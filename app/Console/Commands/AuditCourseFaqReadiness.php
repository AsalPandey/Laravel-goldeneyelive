<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\FAQ;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'Audit active courses for Course-FAQ explicit CMS assignments.';

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
        $totalFaqs = FAQ::count();

        $this->info('================================================================');
        $this->info('COURSE-FAQ CMS ASSIGNMENT AUDIT REPORT');
        $this->info('================================================================');

        $tableRows = [];
        $coursesWithAssignments = 0;
        $coursesWithZeroAssignments = 0;

        $totalPivotCount = 0;
        $totalInactiveAttached = 0;
        $duplicatePivotCount = 0;

        foreach ($courses as $course) {
            $explicitFaqs = $course->faqs;
            $totalAssigned = $explicitFaqs->count();
            $activeAssigned = $explicitFaqs->where('status', 'active')->count();
            $inactiveAssigned = $explicitFaqs->where('status', 'inactive')->count();

            $totalPivotCount += $totalAssigned;
            $totalInactiveAttached += $inactiveAssigned;

            if ($totalAssigned > 0) {
                $coursesWithAssignments++;
            } else {
                $coursesWithZeroAssignments++;
            }

            // Check for duplicates
            // Since course->faqs is a belongsToMany, duplicates can happen if pivot has duplicates
            $faqIds = $explicitFaqs->pluck('id')->toArray();
            $uniqueFaqIds = array_unique($faqIds);
            if (count($faqIds) !== count($uniqueFaqIds)) {
                $duplicatePivotCount += (count($faqIds) - count($uniqueFaqIds));
            }

            $explicitIds = $explicitFaqs->pluck('id')->implode(', ') ?: 'None';

            $statusText = $totalAssigned === 0 ? 'NO PUBLIC FAQs' : 'ASSIGNED';

            $tableRows[] = [
                $course->id,
                $course->slug,
                $totalAssigned,
                $activeAssigned,
                $inactiveAssigned,
                $explicitIds,
                $statusText,
            ];
        }

        $this->table(
            ['ID', 'Slug', 'Total Assigned', 'Active Assigned', 'Inactive Assigned', 'Assigned FAQ IDs', 'Public Status'],
            $tableRows
        );

        $this->info("\n----------------------------------------------------------------");
        $this->info('AUDIT SUMMARY');
        $this->info('----------------------------------------------------------------');
        $this->info("Total Active Courses: {$totalCourses}");
        $this->info("Total FAQ Records: {$totalFaqs}");
        $this->info("Total Pivot Assignments: {$totalPivotCount}");
        $this->info("Courses with Assignments: {$coursesWithAssignments}");

        if ($coursesWithZeroAssignments > 0) {
            $this->warn("Courses with Zero Assignments: {$coursesWithZeroAssignments}");
        } else {
            $this->info("Courses with Zero Assignments: {$coursesWithZeroAssignments}");
        }

        if ($totalInactiveAttached > 0) {
            $this->warn("Inactive but Attached FAQs: {$totalInactiveAttached}");
        } else {
            $this->info("Inactive but Attached FAQs: {$totalInactiveAttached}");
        }

        if ($duplicatePivotCount > 0) {
            $this->error("Duplicate Pivot Assignments: {$duplicatePivotCount}");
        } else {
            $this->info("Duplicate Pivot Assignments: {$duplicatePivotCount}");
        }

        return self::SUCCESS;
    }
}
