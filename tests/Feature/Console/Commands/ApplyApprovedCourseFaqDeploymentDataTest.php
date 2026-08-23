<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Course;
use App\Models\FAQ;
use App\Support\ApprovedCourseFaqDeploymentData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ApplyApprovedCourseFaqDeploymentDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_dry_run_performs_zero_writes(): void
    {
        $this->seedOperationalState();
        $before = $this->databaseFingerprint();

        $exitCode = Artisan::call('course-faq:apply-deployment-data');
        $output = Artisan::output();

        $this->assertSame(0, $exitCode, $output);
        $this->assertStringContainsString('APPROVED DEPLOYMENT DATA DRY RUN', $output);
        $this->assertSame($before, $this->databaseFingerprint());
    }

    public function test_dry_run_displays_approved_metadata_diff(): void
    {
        $this->seedOperationalState();

        $exitCode = Artisan::call('course-faq:apply-deployment-data');
        $output = Artisan::output();

        $this->assertSame(0, $exitCode, $output);
        $this->assertStringContainsString('ielts-masterclass', $output);
        $this->assertStringContainsString('Legacy instructor', $output);
        $this->assertStringContainsString('Saroj Giri', $output);
        $this->assertStringContainsString('Dry run complete', $output);
    }

    public function test_apply_updates_only_the_approved_twelve_courses(): void
    {
        $this->seedOperationalState();
        $unrelated = Course::factory()->create([
            'name' => 'Unrelated Staff Course',
            'slug' => 'unrelated-staff-course',
            'instructor' => 'Staff Instructor',
            'price' => 'Rs. 99',
            'duration' => '9 Days',
        ]);

        $this->assertSuccessfulApply();

        foreach (ApprovedCourseFaqDeploymentData::courseMetadata() as $slug => $target) {
            $course = Course::query()->where('slug', $slug)->firstOrFail();
            $this->assertSame($target['instructor'], $course->instructor, $slug);
            $this->assertSame($target['price'], $course->price, $slug);
            $this->assertSame($target['duration'], $course->duration, $slug);
        }

        $this->assertSame('Staff Instructor', $unrelated->fresh()->instructor);
        $this->assertSame('Rs. 99', $unrelated->fresh()->price);
        $this->assertSame('9 Days', $unrelated->fresh()->duration);
    }

    public function test_free_course_roadmap_help_metadata_remains_unchanged(): void
    {
        $this->seedOperationalState();
        $freeCourse = Course::query()->where('slug', 'free-course-roadmap-help')->firstOrFail();
        $fields = array_flip(['instructor', 'price', 'duration', 'updated_at']);
        $before = array_intersect_key($freeCourse->getRawOriginal(), $fields);

        $this->assertSuccessfulApply();

        $this->assertSame($before, array_intersect_key($freeCourse->fresh()->getRawOriginal(), $fields));
    }

    public function test_german_a1_is_not_created(): void
    {
        $this->seedOperationalState();

        $this->assertSuccessfulApply();

        $this->assertDatabaseMissing('courses', ['slug' => 'german-a1']);
        $this->assertSame(0, Course::query()->whereRaw('LOWER(name) = ?', ['german a1'])->count());
    }

    public function test_certificate_faq_is_corrected_by_exact_question(): void
    {
        $this->seedOperationalState();
        $certificate = FAQ::query()->where('question', ApprovedCourseFaqDeploymentData::CERTIFICATE_QUESTION)->firstOrFail();
        $originalId = $certificate->id;

        $this->assertSuccessfulApply();

        $certificate->refresh();
        $this->assertSame($originalId, $certificate->id);
        $this->assertSame(ApprovedCourseFaqDeploymentData::CERTIFICATE_ANSWER, $certificate->answer);
    }

    public function test_four_missing_approved_faqs_are_created_exactly_once(): void
    {
        $this->seedOperationalState();

        $this->assertSuccessfulApply();

        foreach (ApprovedCourseFaqDeploymentData::approvedFaqs() as $question => $target) {
            $this->assertSame(1, FAQ::query()->where('question', $question)->count(), $question);
            $faq = FAQ::query()->where('question', $question)->firstOrFail();
            $this->assertSame($target['answer'], $faq->answer);
            $this->assertSame($target['order_priority'], $faq->order_priority);
            $this->assertSame('active', $faq->status);
        }
    }

    public function test_existing_approved_faq_is_reused_instead_of_duplicated(): void
    {
        $this->seedOperationalState();
        $question = ApprovedCourseFaqDeploymentData::IELTS_FEEDBACK_QUESTION;
        $existing = FAQ::query()->create([
            'question' => $question,
            'answer' => 'Outdated answer',
            'status' => 'inactive',
            'order_priority' => 1,
        ]);

        $this->assertSuccessfulApply();

        $this->assertSame(1, FAQ::query()->where('question', $question)->count());
        $this->assertSame($existing->id, FAQ::query()->where('question', $question)->value('id'));
        $this->assertSame(ApprovedCourseFaqDeploymentData::approvedFaqs()[$question]['answer'], $existing->fresh()->answer);
    }

    public function test_duplicate_exact_faq_identity_aborts_before_writes(): void
    {
        $this->seedOperationalState();
        FAQ::query()->create([
            'question' => ApprovedCourseFaqDeploymentData::CERTIFICATE_QUESTION,
            'answer' => 'Duplicate certificate answer',
            'status' => 'active',
            'order_priority' => 999,
        ]);
        $before = $this->databaseFingerprint();

        $exitCode = Artisan::call('course-faq:apply-deployment-data', ['--apply' => true]);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Duplicate exact FAQ identity', Artisan::output());
        $this->assertSame($before, $this->databaseFingerprint());
    }

    public function test_missing_required_course_aborts_before_writes(): void
    {
        $this->seedOperationalState();
        Course::query()->where('slug', 'ielts-masterclass')->delete();
        $before = $this->databaseFingerprint();

        $exitCode = Artisan::call('course-faq:apply-deployment-data', ['--apply' => true]);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Required course slug is missing: ielts-masterclass', Artisan::output());
        $this->assertSame($before, $this->databaseFingerprint());
    }

    public function test_ambiguous_course_slug_aborts_before_writes(): void
    {
        $this->seedOperationalState();
        DB::statement('DROP INDEX courses_slug_unique');
        $course = Course::query()->where('slug', 'ielts-masterclass')->firstOrFail();
        $attributes = $course->getAttributes();
        unset($attributes['id']);
        DB::table('courses')->insert($attributes);
        $before = $this->databaseFingerprint();

        $exitCode = Artisan::call('course-faq:apply-deployment-data', ['--apply' => true]);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Required course slug is ambiguous: ielts-masterclass', Artisan::output());
        $this->assertSame($before, $this->databaseFingerprint());
    }

    public function test_unexpected_target_course_assignment_is_reported_and_aborts_apply(): void
    {
        $this->seedOperationalState();
        $unexpectedFaq = FAQ::query()->create([
            'question' => 'Unapproved target-course assignment?',
            'answer' => 'Must be reviewed.',
            'status' => 'active',
            'order_priority' => 999,
        ]);
        $course = Course::query()->where('slug', 'ielts-masterclass')->firstOrFail();
        $course->faqs()->attach($unexpectedFaq->id);

        $dryRunExitCode = Artisan::call('course-faq:apply-deployment-data');
        $dryRunOutput = Artisan::output();
        $this->assertSame(0, $dryRunExitCode, $dryRunOutput);
        $this->assertStringContainsString('UNEXPECTED EXISTING ASSIGNMENT', $dryRunOutput);
        $beforeApply = $this->databaseFingerprint();

        $applyExitCode = Artisan::call('course-faq:apply-deployment-data', ['--apply' => true]);

        $this->assertSame(1, $applyExitCode);
        $this->assertStringContainsString('Apply aborted', Artisan::output());
        $this->assertSame($beforeApply, $this->databaseFingerprint());
    }

    public function test_unrelated_course_faq_relationships_remain_untouched(): void
    {
        $this->seedOperationalState();
        $course = Course::factory()->create(['name' => 'Unrelated Course', 'slug' => 'unrelated-course']);
        $faq = FAQ::query()->create([
            'question' => 'Unrelated staff FAQ?',
            'answer' => 'Staff content.',
            'status' => 'active',
            'order_priority' => 500,
        ]);
        $course->faqs()->attach($faq->id);

        $this->assertSuccessfulApply();

        $this->assertDatabaseHas('course_faq', ['course_id' => $course->id, 'faq_id' => $faq->id]);
        $this->assertSame(1, DB::table('course_faq')->where('course_id', $course->id)->count());
    }

    public function test_unrelated_faq_records_remain_untouched(): void
    {
        $this->seedOperationalState();
        $faq = FAQ::query()->create([
            'question' => 'Unrelated owner-authored FAQ?',
            'answer' => 'Do not change this answer.',
            'status' => 'inactive',
            'order_priority' => 777,
        ]);
        $before = $faq->fresh()->getRawOriginal();

        $this->assertSuccessfulApply();

        $this->assertSame($before, $faq->fresh()->getRawOriginal());
    }

    public function test_apply_results_in_exactly_sixty_eight_target_assignments(): void
    {
        $this->seedOperationalState();

        $this->assertSuccessfulApply();

        $targetIds = Course::query()->whereIn('slug', array_keys(ApprovedCourseFaqDeploymentData::targetCourses()))->pluck('id');
        $this->assertSame(68, DB::table('course_faq')->whereIn('course_id', $targetIds)->count());

        foreach (ApprovedCourseFaqDeploymentData::matrix() as $slug => $approvedQuestions) {
            $actual = Course::query()->where('slug', $slug)->firstOrFail()->faqs()->orderBy('question')->pluck('question')->all();
            sort($approvedQuestions);
            $this->assertSame($approvedQuestions, $actual, $slug);
        }
    }

    public function test_second_apply_is_idempotent(): void
    {
        $this->seedOperationalState();
        $this->assertSuccessfulApply();
        $afterFirstApply = $this->databaseFingerprint();

        $dryRunExitCode = Artisan::call('course-faq:apply-deployment-data');
        $dryRunOutput = Artisan::output();
        $this->assertSame(0, $dryRunExitCode, $dryRunOutput);
        $this->assertStringContainsString('NO CHANGES REQUIRED', $dryRunOutput);

        $secondApplyExitCode = Artisan::call('course-faq:apply-deployment-data', ['--apply' => true]);
        $secondApplyOutput = Artisan::output();
        $this->assertSame(0, $secondApplyExitCode, $secondApplyOutput);
        $this->assertStringContainsString('IDEMPOTENT NO-OP', $secondApplyOutput);
        $this->assertSame($afterFirstApply, $this->databaseFingerprint());
    }

    public function test_transaction_rolls_back_all_changes_when_pivot_write_fails(): void
    {
        $this->seedOperationalState();
        DB::statement("CREATE TRIGGER inject_course_faq_failure BEFORE INSERT ON course_faq BEGIN SELECT RAISE(ABORT, 'injected pivot failure'); END");
        $before = $this->databaseFingerprint();

        $exitCode = Artisan::call('course-faq:apply-deployment-data', ['--apply' => true]);

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('Apply failed and rolled back', Artisan::output());
        $this->assertSame($before, $this->databaseFingerprint());
    }

    public function test_deployment_identities_are_questions_and_slugs_not_numeric_faq_ids(): void
    {
        foreach (ApprovedCourseFaqDeploymentData::matrix() as $slug => $questions) {
            $this->assertIsString($slug);
            $this->assertNotSame('', $slug);
            $this->assertNotEmpty($questions);
            foreach ($questions as $question) {
                $this->assertIsString($question);
                $this->assertStringContainsString('?', $question);
            }
        }

        $dataSource = (string) file_get_contents(app_path('Support/ApprovedCourseFaqDeploymentData.php'));
        $commandSource = (string) file_get_contents(app_path('Console/Commands/ApplyApprovedCourseFaqDeploymentData.php'));
        $this->assertStringNotContainsString("'faq_id' =>", $dataSource);
        $this->assertStringNotContainsString('FAQ::find(', $commandSource);
        $this->assertStringNotContainsString('FAQ::findOrFail(', $commandSource);
    }

    private function seedOperationalState(): void
    {
        foreach (ApprovedCourseFaqDeploymentData::targetCourses() as $slug => $name) {
            Course::factory()->create([
                'name' => $name,
                'slug' => $slug,
                'instructor' => $slug === 'free-course-roadmap-help' ? 'Academic Support Team' : 'Legacy instructor',
                'price' => $slug === 'free-course-roadmap-help' ? 'Free' : 'Rs. 1,000',
                'duration' => $slug === 'free-course-roadmap-help' ? '30 Minutes' : '1 Week',
            ]);
        }

        $newQuestions = array_keys(ApprovedCourseFaqDeploymentData::approvedFaqs());
        foreach (ApprovedCourseFaqDeploymentData::requiredFaqQuestions() as $priority => $question) {
            if (in_array($question, $newQuestions, true)) {
                continue;
            }

            FAQ::query()->create([
                'question' => $question,
                'answer' => $question === ApprovedCourseFaqDeploymentData::CERTIFICATE_QUESTION
                    ? 'Certificate details differ by course.'
                    : 'Existing approved reference answer.',
                'status' => 'active',
                'order_priority' => ($priority + 1) * 10,
            ]);
        }
    }

    private function assertSuccessfulApply(): void
    {
        $exitCode = Artisan::call('course-faq:apply-deployment-data', ['--apply' => true]);
        $output = Artisan::output();

        $this->assertSame(0, $exitCode, $output);
        $this->assertStringContainsString('Post-apply audit passed', $output);
    }

    private function databaseFingerprint(): string
    {
        return hash('sha256', json_encode([
            'courses' => Course::query()->orderBy('id')->get()->toArray(),
            'faqs' => FAQ::query()->orderBy('id')->get()->toArray(),
            'pivots' => DB::table('course_faq')->orderBy('course_id')->orderBy('faq_id')->get()->map(fn (object $row): array => (array) $row)->all(),
        ], JSON_THROW_ON_ERROR));
    }
}
