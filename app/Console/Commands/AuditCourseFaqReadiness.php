<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\FAQ;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuditCourseFaqReadiness extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course-faq:audit {--snapshot= : Optional path to snapshot sqlite database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit active courses for Course-FAQ relationship readiness and generate candidate keyword suggestions.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $snapshotPath = $this->option('snapshot');

        if ($snapshotPath) {
            if (! file_exists($snapshotPath)) {
                $this->error("Snapshot database file not found at: {$snapshotPath}");

                return self::FAILURE;
            }

            config([
                'database.connections.snapshot_audit' => [
                    'driver' => 'sqlite',
                    'database' => $snapshotPath,
                    'prefix' => '',
                    'foreign_key_constraints' => true,
                ],
            ]);

            DB::setDefaultConnection('snapshot_audit');
            $this->info("Using database snapshot at: {$snapshotPath}");
        }

        $courses = Course::publiclyVisible()->orderBy('id')->get();
        $totalCourses = $courses->count();

        $this->info('================================================================');
        $this->info('COURSE-FAQ CONTENT-READINESS AUDIT REPORT');
        $this->info('================================================================');
        $this->info("Total Active Courses Audited: {$totalCourses}\n");

        $readyCount = 0;
        $unreadyCount = 0;
        $candidateMappings = [];

        $tableRows = [];

        foreach ($courses as $course) {
            $explicitFaqs = $course->faqs;
            $activeAssignedFaqs = $explicitFaqs->where('status', 'active');
            $isReady = $activeAssignedFaqs->count() > 0;

            if ($isReady) {
                $readyCount++;
            } else {
                $unreadyCount++;
            }

            $explicitIds = $explicitFaqs->pluck('id')->implode(', ') ?: 'None';

            $tableRows[] = [
                $course->id,
                $course->name,
                $course->slug,
                $explicitFaqs->count(),
                $activeAssignedFaqs->count(),
                $explicitIds,
                $isReady ? 'READY' : 'NOT READY',
            ];

            // Candidate keyword suggestions (old query algorithm)
            $suggestedFaqs = FAQ::where('status', 'active')
                ->where(function ($query) use ($course) {
                    $query->where('question', 'like', '%course%')
                        ->orWhere('question', 'like', '%fee%')
                        ->orWhere('question', 'like', '%duration%')
                        ->orWhere('answer', 'like', '%'.$course->category.'%')
                        ->orWhere('answer', 'like', '%'.$course->name.'%');
                })
                ->orderBy('order_priority')
                ->latest()
                ->limit(4)
                ->get();

            foreach ($suggestedFaqs as $faq) {
                $candidateMappings[] = [
                    'course_id' => $course->id,
                    'course_slug' => $course->slug,
                    'faq_id' => $faq->id,
                    'faq_question' => $faq->question,
                    'note' => 'Requires human verification',
                ];
            }
        }

        $this->table(
            ['ID', 'Name', 'Slug', 'Total Explicit', 'Active Assigned', 'Explicit FAQ IDs', 'Strict Cutover Readiness'],
            $tableRows
        );

        $this->info("\n----------------------------------------------------------------");
        $this->info('AUDIT SUMMARY');
        $this->info('----------------------------------------------------------------');
        $this->info("Total Active Courses: {$totalCourses}");
        $this->info("Courses Ready for Strict Relationship Cutover: {$readyCount}");
        $this->info("Courses Still Missing Assignments: {$unreadyCount}");

        $this->info("\n----------------------------------------------------------------");
        $this->info('CANDIDATE FAQ-TO-COURSE SUGGESTIONS (UNCOMMITTED)');
        $this->info('----------------------------------------------------------------');
        $this->warn('NOTE: Every suggested relationship below is marked [Requires human verification] and HAS NOT been inserted into course_faq.');

        $suggestionRows = array_map(function ($mapping) {
            return [
                $mapping['course_id'],
                $mapping['course_slug'],
                $mapping['faq_id'],
                Str::limit($mapping['faq_question'], 50),
                $mapping['note'],
            ];
        }, $candidateMappings);

        $this->table(
            ['Course ID', 'Course Slug', 'Suggested FAQ ID', 'Suggested Question', 'Verification Policy'],
            $suggestionRows
        );

        return self::SUCCESS;
    }
}
