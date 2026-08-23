<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\FAQ;
use App\Support\ApprovedCourseFaqDeploymentData;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

#[Signature('course-faq:apply-deployment-data
    {--apply : Apply the approved deployment data; the command is a dry run without this flag}')]
#[Description('Preview or apply the owner-approved course metadata and Course-FAQ deployment data.')]
class ApplyApprovedCourseFaqDeploymentData extends Command
{
    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $plan = $this->buildPlan();

        $this->renderPlan($plan, $apply);

        if ($plan['fatal_errors'] !== []) {
            foreach ($plan['fatal_errors'] as $error) {
                $this->error("PREFLIGHT FAILED: {$error}");
            }

            $this->error('No database changes were made.');

            return self::FAILURE;
        }

        if ($apply && $plan['unexpected_assignments'] !== []) {
            $this->error('Apply aborted: unexpected target-course assignments require separate owner approval.');
            $this->error('No database changes were made.');

            return self::FAILURE;
        }

        if (! $apply) {
            if ($plan['unexpected_assignments'] !== []) {
                $this->warn('Dry run complete with unexpected assignments. --apply will abort until they are reviewed.');
            } elseif ($this->hasChanges($plan)) {
                $this->info('Dry run complete. Re-run with --apply after reviewing every proposed change.');
            } else {
                $this->info('NO CHANGES REQUIRED');
            }

            return self::SUCCESS;
        }

        $protectedState = $this->captureProtectedState($plan);

        try {
            DB::transaction(function () use ($protectedState): void {
                $lockedPlan = $this->buildPlan(lockRecords: true);

                if ($lockedPlan['fatal_errors'] !== []) {
                    throw new RuntimeException('Transactional preflight failed: '.implode(' ', $lockedPlan['fatal_errors']));
                }

                if ($lockedPlan['unexpected_assignments'] !== []) {
                    throw new RuntimeException('Transactional preflight found an unexpected target-course assignment.');
                }

                $this->applyApprovedChanges($lockedPlan);
                $this->assertPostApplyState($protectedState);
            });
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Apply failed and rolled back: '.$exception->getMessage());

            return self::FAILURE;
        }

        if ($this->hasChanges($plan)) {
            $this->info('APPROVED DEPLOYMENT DATA APPLIED');
        } else {
            $this->info('NO CHANGES REQUIRED; APPLY COMPLETED AS AN IDEMPOTENT NO-OP');
        }

        $this->info('Post-apply audit passed: 68 target Course-FAQ assignments verified.');

        return self::SUCCESS;
    }

    /**
     * @return array{
     *     fatal_errors: array<int, string>,
     *     courses: Collection<string, Course>,
     *     course_rows: array<int, array<string, mixed>>,
     *     certificate: array<string, mixed>|null,
     *     approved_faq_rows: array<int, array<string, mixed>>,
     *     matrix_rows: array<string, array<string, array<int, string>>>,
     *     unexpected_assignments: array<int, array{slug: string, question: string}>,
     *     counts: array<string, int>
     * }
     */
    private function buildPlan(bool $lockRecords = false): array
    {
        $fatalErrors = $this->definitionErrors();
        $fatalErrors = [...$fatalErrors, ...$this->schemaErrors()];
        $courses = collect();
        $courseRows = [];
        $certificate = null;
        $approvedFaqRows = [];
        $matrixRows = [];
        $unexpectedAssignments = [];
        $counts = [
            'required_courses_found' => 0,
            'required_courses_total' => count(ApprovedCourseFaqDeploymentData::targetCourses()),
            'required_faqs_found' => 0,
            'required_faqs_total' => count(ApprovedCourseFaqDeploymentData::requiredFaqQuestions()),
            'unrelated_courses' => 0,
            'unrelated_faqs' => 0,
            'target_pivots' => 0,
            'unrelated_pivots' => 0,
        ];

        if ($fatalErrors !== []) {
            return [
                'fatal_errors' => $fatalErrors,
                'courses' => $courses,
                'course_rows' => $courseRows,
                'certificate' => $certificate,
                'approved_faq_rows' => $approvedFaqRows,
                'matrix_rows' => $matrixRows,
                'unexpected_assignments' => $unexpectedAssignments,
                'counts' => $counts,
            ];
        }

        foreach (ApprovedCourseFaqDeploymentData::targetCourses() as $slug => $approvedName) {
            $query = Course::query()->where('slug', $slug);
            if ($lockRecords) {
                $query->lockForUpdate();
            }

            /** @var EloquentCollection<int, Course> $matches */
            $matches = $query->get();
            if ($matches->count() !== 1) {
                $fatalErrors[] = $matches->isEmpty()
                    ? "Required course slug is missing: {$slug}."
                    : "Required course slug is ambiguous: {$slug} resolved {$matches->count()} times.";

                continue;
            }

            $course = $matches->first();
            $courses->put($slug, $course);
            $counts['required_courses_found']++;

            if (! isset(ApprovedCourseFaqDeploymentData::courseMetadata()[$slug])) {
                continue;
            }

            $target = ApprovedCourseFaqDeploymentData::courseMetadata()[$slug];
            $current = [
                'instructor' => (string) $course->instructor,
                'price' => (string) $course->price,
                'duration' => (string) $course->duration,
            ];
            $targetFields = array_intersect_key($target, $current);
            $courseRows[] = [
                'slug' => $slug,
                'approved_name' => $approvedName,
                'course' => $course,
                'current' => $current,
                'target' => $targetFields,
                'change_required' => $current !== $targetFields,
            ];
        }

        if ($this->germanCourseCount() > 0) {
            $fatalErrors[] = 'German A1 exists unexpectedly; it is excluded from this approved deployment plan.';
        }

        $approvedNewQuestions = array_keys(ApprovedCourseFaqDeploymentData::approvedFaqs());
        $faqsByQuestion = collect();

        foreach (ApprovedCourseFaqDeploymentData::requiredFaqQuestions() as $question) {
            $query = FAQ::query()->where('question', $question);
            if ($lockRecords) {
                $query->lockForUpdate();
            }

            /** @var EloquentCollection<int, FAQ> $matches */
            $matches = $query->get();
            $isApprovedNewFaq = in_array($question, $approvedNewQuestions, true);

            if ($matches->count() > 1) {
                $fatalErrors[] = "Duplicate exact FAQ identity: {$question}";

                continue;
            }

            if ($matches->isEmpty()) {
                if (! $isApprovedNewFaq) {
                    $fatalErrors[] = "Required exact FAQ identity is missing: {$question}";
                }

                continue;
            }

            $faq = $matches->first();
            $faqsByQuestion->put($question, $faq);
            $counts['required_faqs_found']++;

            if (! $isApprovedNewFaq && $faq->status !== 'active') {
                $fatalErrors[] = "Required existing FAQ is inactive: {$question}";
            }
        }

        if ($faqsByQuestion->has(ApprovedCourseFaqDeploymentData::CERTIFICATE_QUESTION)) {
            /** @var FAQ $faq */
            $faq = $faqsByQuestion->get(ApprovedCourseFaqDeploymentData::CERTIFICATE_QUESTION);
            $certificate = [
                'faq' => $faq,
                'current_question' => (string) $faq->question,
                'current_answer' => (string) $faq->answer,
                'target_answer' => ApprovedCourseFaqDeploymentData::CERTIFICATE_ANSWER,
                'change_required' => $faq->answer !== ApprovedCourseFaqDeploymentData::CERTIFICATE_ANSWER,
            ];
        }

        foreach (ApprovedCourseFaqDeploymentData::approvedFaqs() as $question => $target) {
            /** @var FAQ|null $faq */
            $faq = $faqsByQuestion->get($question);
            $current = $faq ? [
                'answer' => (string) $faq->answer,
                'order_priority' => (int) $faq->order_priority,
                'status' => (string) $faq->status,
            ] : null;
            $approvedFaqRows[] = [
                'question' => $question,
                'faq' => $faq,
                'exists' => $faq !== null,
                'current' => $current,
                'target' => $target,
                'action' => $faq === null ? 'CREATE' : ($current === $target ? 'NONE' : 'UPDATE'),
            ];
        }

        if ($fatalErrors === []) {
            foreach (ApprovedCourseFaqDeploymentData::matrix() as $slug => $approvedQuestions) {
                /** @var Course $course */
                $course = $courses->get($slug);
                $currentQuestions = DB::table('course_faq')
                    ->join('f_a_q_s', 'course_faq.faq_id', '=', 'f_a_q_s.id')
                    ->where('course_faq.course_id', $course->id)
                    ->orderBy('f_a_q_s.question')
                    ->pluck('f_a_q_s.question')
                    ->map(fn (mixed $question): string => (string) $question)
                    ->all();
                $approvedQuestions = array_values($approvedQuestions);
                $additions = array_values(array_diff($approvedQuestions, $currentQuestions));
                $unexpected = array_values(array_diff($currentQuestions, $approvedQuestions));

                foreach ($unexpected as $question) {
                    $unexpectedAssignments[] = compact('slug', 'question');
                }

                $matrixRows[$slug] = [
                    'current' => $currentQuestions,
                    'approved' => $approvedQuestions,
                    'additions' => $additions,
                    'unexpected' => $unexpected,
                    'removals' => $unexpected,
                ];
            }
        }

        if (Schema::hasTable('course_faq') && Schema::hasTable('courses') && Schema::hasTable('f_a_q_s')) {
            $targetCourseIds = $courses->pluck('id')->all();
            $counts['unrelated_courses'] = Course::query()->whereNotIn('slug', array_keys(ApprovedCourseFaqDeploymentData::targetCourses()))->count();
            $counts['unrelated_faqs'] = FAQ::query()->whereNotIn('question', ApprovedCourseFaqDeploymentData::requiredFaqQuestions())->count();
            $counts['target_pivots'] = $targetCourseIds === [] ? 0 : DB::table('course_faq')->whereIn('course_id', $targetCourseIds)->count();
            $counts['unrelated_pivots'] = $targetCourseIds === []
                ? DB::table('course_faq')->count()
                : DB::table('course_faq')->whereNotIn('course_id', $targetCourseIds)->count();
        }

        return [
            'fatal_errors' => $fatalErrors,
            'courses' => $courses,
            'course_rows' => $courseRows,
            'certificate' => $certificate,
            'approved_faq_rows' => $approvedFaqRows,
            'matrix_rows' => $matrixRows,
            'unexpected_assignments' => $unexpectedAssignments,
            'counts' => $counts,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function definitionErrors(): array
    {
        $errors = [];
        $metadata = ApprovedCourseFaqDeploymentData::courseMetadata();
        $targetCourses = ApprovedCourseFaqDeploymentData::targetCourses();
        $matrix = ApprovedCourseFaqDeploymentData::matrix();

        if (count($metadata) !== 12) {
            $errors[] = 'Approved metadata must contain exactly 12 courses.';
        }

        if (array_keys($targetCourses) !== array_keys($matrix)) {
            $errors[] = 'Target-course identities do not exactly match matrix identities.';
        }

        if (array_sum(array_map('count', $matrix)) !== 68) {
            $errors[] = 'The approved Course-FAQ matrix does not contain exactly 68 assignments.';
        }

        if (isset($metadata['free-course-roadmap-help'])) {
            $errors[] = 'Free Course Roadmap Help must be excluded from metadata changes.';
        }

        foreach (array_keys($targetCourses) as $slug) {
            if (str_contains($slug, 'german')) {
                $errors[] = 'German A1 must not be present in the application plan.';
            }
        }

        foreach ($metadata as $slug => $course) {
            foreach (['instructor', 'price', 'duration'] as $field) {
                if (mb_strlen($course[$field]) > 255) {
                    $errors[] = "Approved {$field} exceeds the course column contract for {$slug}.";
                }
            }
        }

        foreach (ApprovedCourseFaqDeploymentData::approvedFaqs() as $question => $faq) {
            if (mb_strlen($question) > 255 || strlen($faq['answer']) > 65535) {
                $errors[] = "Approved FAQ exceeds the production column contract: {$question}";
            }
        }

        return $errors;
    }

    /**
     * @return array<int, string>
     */
    private function schemaErrors(): array
    {
        $errors = [];

        foreach (['courses', 'f_a_q_s', 'course_faq', 'migrations'] as $table) {
            if (! Schema::hasTable($table)) {
                $errors[] = "Required table is missing: {$table}.";
            }
        }

        if ($errors !== []) {
            return $errors;
        }

        if (Schema::hasColumn('course_faq', 'id')) {
            $errors[] = 'course_faq still has the legacy id column; the composite-key migration is required.';
        }

        if (! Schema::hasColumns('course_faq', ['course_id', 'faq_id'])) {
            $errors[] = 'course_faq is missing course_id or faq_id.';
        }

        $hasCompositePrimary = collect(Schema::getIndexes('course_faq'))->contains(function (array $index): bool {
            $columns = $index['columns'] ?? [];
            sort($columns);

            return ($index['primary'] ?? false) === true && $columns === ['course_id', 'faq_id'];
        });
        if (! $hasCompositePrimary) {
            $errors[] = 'course_faq does not have the required composite primary key.';
        }

        if (! DB::table('migrations')->where('migration', '2026_08_06_230629_rebuild_course_faq_table')->exists()) {
            $errors[] = 'The final course_faq rebuild migration is not recorded.';
        }

        $duplicateCount = DB::query()->fromSub(
            DB::table('course_faq')
                ->select('course_id', 'faq_id')
                ->groupBy('course_id', 'faq_id')
                ->havingRaw('COUNT(*) > 1'),
            'duplicate_course_faq',
        )->count();
        if ($duplicateCount > 0) {
            $errors[] = "course_faq contains {$duplicateCount} duplicate assignment group(s).";
        }

        $orphanedCourses = DB::table('course_faq')
            ->leftJoin('courses', 'course_faq.course_id', '=', 'courses.id')
            ->whereNull('courses.id')
            ->count();
        $orphanedFaqs = DB::table('course_faq')
            ->leftJoin('f_a_q_s', 'course_faq.faq_id', '=', 'f_a_q_s.id')
            ->whereNull('f_a_q_s.id')
            ->count();
        if ($orphanedCourses > 0 || $orphanedFaqs > 0) {
            $errors[] = "course_faq contains orphaned rows (courses: {$orphanedCourses}, FAQs: {$orphanedFaqs}).";
        }

        return $errors;
    }

    /**
     * @param  array<string, mixed>  $plan
     */
    private function renderPlan(array $plan, bool $apply): void
    {
        $this->newLine();
        $this->info($apply ? 'APPROVED DEPLOYMENT DATA APPLY PREFLIGHT' : 'APPROVED DEPLOYMENT DATA DRY RUN');

        $this->newLine();
        $this->info('COURSE METADATA');
        $this->table(
            ['Slug', 'Current instructor', 'Target instructor', 'Current fee', 'Target fee', 'Current duration', 'Target duration', 'Change'],
            array_map(fn (array $row): array => [
                $row['slug'],
                $row['current']['instructor'],
                $row['target']['instructor'],
                $row['current']['price'],
                $row['target']['price'],
                $row['current']['duration'],
                $row['target']['duration'],
                $row['change_required'] ? 'YES' : 'NO',
            ], $plan['course_rows']),
        );

        $this->newLine();
        $this->info('CERTIFICATE FAQ');
        if ($plan['certificate'] === null) {
            $this->warn('Certificate FAQ could not be resolved uniquely.');
        } else {
            $this->line('Current question: '.$plan['certificate']['current_question']);
            $this->line('Current answer: '.$plan['certificate']['current_answer']);
            $this->line('Target answer: '.$plan['certificate']['target_answer']);
            $this->line('Change required: '.($plan['certificate']['change_required'] ? 'YES' : 'NO'));
        }

        $this->newLine();
        $this->info('FOUR APPROVED FAQS');
        foreach ($plan['approved_faq_rows'] as $row) {
            $this->line('Question: '.$row['question']);
            $this->line('Exists: '.($row['exists'] ? 'YES' : 'NO'));
            $this->line('Current: '.$this->display($row['current']));
            $this->line('Target: '.$this->display($row['target']));
            $this->line('Action: '.$row['action']);
            $this->newLine();
        }

        $this->info('COURSE-FAQ MATRIX');
        foreach ($plan['matrix_rows'] as $slug => $row) {
            $this->line("Course: {$slug}");
            $this->line('Current assigned exact questions: '.$this->displayList($row['current']));
            $this->line('Approved assigned exact questions: '.$this->displayList($row['approved']));
            $this->line('Additions: '.$this->displayList($row['additions']));
            if ($row['unexpected'] !== []) {
                foreach ($row['unexpected'] as $question) {
                    $this->warn("UNEXPECTED EXISTING ASSIGNMENT: {$slug} -> {$question}");
                }
            } else {
                $this->line('Unexpected existing assignments: (none)');
            }
            $this->line('Removals required for authoritative synchronization: '.$this->displayList($row['removals']));
            $this->newLine();
        }

        $counts = $plan['counts'];
        $this->info('PREFLIGHT COUNTS');
        $this->line("Required target courses found: {$counts['required_courses_found']} / {$counts['required_courses_total']}");
        $this->line("Required target FAQs found: {$counts['required_faqs_found']} / {$counts['required_faqs_total']}");
        $this->line("Unrelated courses: {$counts['unrelated_courses']}");
        $this->line("Unrelated FAQs: {$counts['unrelated_faqs']}");
        $this->line("Target pivot count: {$counts['target_pivots']}");
        $this->line("Unrelated pivot count: {$counts['unrelated_pivots']}");
    }

    /**
     * @param  array<string, mixed>  $plan
     */
    private function hasChanges(array $plan): bool
    {
        if (collect($plan['course_rows'])->contains('change_required', true)) {
            return true;
        }

        if (($plan['certificate']['change_required'] ?? false) === true) {
            return true;
        }

        if (collect($plan['approved_faq_rows'])->contains(fn (array $row): bool => $row['action'] !== 'NONE')) {
            return true;
        }

        return collect($plan['matrix_rows'])->contains(
            fn (array $row): bool => $row['additions'] !== [] || $row['removals'] !== [],
        );
    }

    /**
     * @param  array<string, mixed>  $plan
     * @return array<string, mixed>
     */
    private function captureProtectedState(array $plan): array
    {
        $targetCourseIds = $plan['courses']->pluck('id')->all();
        $freeCourse = $plan['courses']->get('free-course-roadmap-help');

        return [
            'unrelated_courses' => Course::query()->whereNotIn('id', $targetCourseIds)->orderBy('id')->get()->toArray(),
            'unrelated_faqs' => FAQ::query()->whereNotIn('question', ApprovedCourseFaqDeploymentData::requiredFaqQuestions())->orderBy('id')->get()->toArray(),
            'unrelated_pivots' => DB::table('course_faq')->whereNotIn('course_id', $targetCourseIds)->orderBy('course_id')->orderBy('faq_id')->get()->map(fn (object $row): array => (array) $row)->all(),
            'free_metadata' => [
                'instructor' => (string) $freeCourse->instructor,
                'price' => (string) $freeCourse->price,
                'duration' => (string) $freeCourse->duration,
            ],
            'german_count' => $this->germanCourseCount(),
        ];
    }

    /**
     * @param  array<string, mixed>  $plan
     */
    private function applyApprovedChanges(array $plan): void
    {
        foreach ($plan['course_rows'] as $row) {
            if (! $row['change_required']) {
                continue;
            }

            /** @var Course $course */
            $course = $row['course'];
            $course->forceFill($row['target'])->save();
        }

        if ($plan['certificate']['change_required']) {
            /** @var FAQ $certificate */
            $certificate = $plan['certificate']['faq'];
            $certificate->forceFill(['answer' => ApprovedCourseFaqDeploymentData::CERTIFICATE_ANSWER])->save();
        }

        foreach ($plan['approved_faq_rows'] as $row) {
            if ($row['action'] === 'NONE') {
                continue;
            }

            if ($row['faq'] instanceof FAQ) {
                $row['faq']->forceFill($row['target'])->save();

                continue;
            }

            FAQ::query()->create([
                'question' => $row['question'],
                ...$row['target'],
            ]);
        }

        $resolvedFaqs = collect();
        foreach (ApprovedCourseFaqDeploymentData::requiredFaqQuestions() as $question) {
            $matches = FAQ::query()->where('question', $question)->lockForUpdate()->get();
            if ($matches->count() !== 1) {
                throw new RuntimeException("FAQ identity changed during apply: {$question}");
            }

            $resolvedFaqs->put($question, $matches->first());
        }

        foreach (ApprovedCourseFaqDeploymentData::matrix() as $slug => $questions) {
            /** @var Course $course */
            $course = $plan['courses']->get($slug);
            $faqIds = array_map(
                fn (string $question): int => (int) $resolvedFaqs->get($question)->getKey(),
                $questions,
            );
            $course->faqs()->sync($faqIds);
        }
    }

    /**
     * @param  array<string, mixed>  $protectedState
     */
    private function assertPostApplyState(array $protectedState): void
    {
        $errors = [];

        foreach (ApprovedCourseFaqDeploymentData::courseMetadata() as $slug => $target) {
            $course = Course::query()->where('slug', $slug)->first();
            if (! $course || [
                'instructor' => (string) $course->instructor,
                'price' => (string) $course->price,
                'duration' => (string) $course->duration,
            ] !== array_intersect_key($target, array_flip(['instructor', 'price', 'duration']))) {
                $errors[] = "Course metadata mismatch: {$slug}.";
            }
        }

        $certificateMatches = FAQ::query()
            ->where('question', ApprovedCourseFaqDeploymentData::CERTIFICATE_QUESTION)
            ->where('answer', ApprovedCourseFaqDeploymentData::CERTIFICATE_ANSWER)
            ->count();
        if ($certificateMatches !== 1) {
            $errors[] = 'Certificate FAQ post-apply verification failed.';
        }

        foreach (ApprovedCourseFaqDeploymentData::approvedFaqs() as $question => $target) {
            $matches = FAQ::query()->where('question', $question)->get();
            if ($matches->count() !== 1) {
                $errors[] = "Approved FAQ identity count is not one: {$question}";

                continue;
            }

            $faq = $matches->first();
            if ([
                'answer' => (string) $faq->answer,
                'order_priority' => (int) $faq->order_priority,
                'status' => (string) $faq->status,
            ] !== $target) {
                $errors[] = "Approved FAQ content mismatch: {$question}";
            }
        }

        foreach (ApprovedCourseFaqDeploymentData::requiredFaqQuestions() as $question) {
            $matches = FAQ::query()->where('question', $question)->get();
            if ($matches->count() !== 1 || $matches->first()->status !== 'active') {
                $errors[] = "Required FAQ is missing, duplicated, or inactive: {$question}";
            }
        }

        $targetCourseIds = [];
        foreach (ApprovedCourseFaqDeploymentData::matrix() as $slug => $approvedQuestions) {
            $course = Course::query()->where('slug', $slug)->first();
            if (! $course) {
                $errors[] = "Target course disappeared: {$slug}.";

                continue;
            }

            $targetCourseIds[] = $course->id;
            $actualQuestions = $course->faqs()->orderBy('question')->pluck('question')->all();
            sort($approvedQuestions);
            if ($actualQuestions !== $approvedQuestions) {
                $errors[] = "Course-FAQ matrix mismatch: {$slug}.";
            }
        }

        if (DB::table('course_faq')->whereIn('course_id', $targetCourseIds)->count() !== 68) {
            $errors[] = 'Target Course-FAQ assignment count is not 68.';
        }

        $duplicateCount = DB::query()->fromSub(
            DB::table('course_faq')
                ->whereIn('course_id', $targetCourseIds)
                ->select('course_id', 'faq_id')
                ->groupBy('course_id', 'faq_id')
                ->havingRaw('COUNT(*) > 1'),
            'duplicate_target_course_faq',
        )->count();
        if ($duplicateCount > 0) {
            $errors[] = 'Duplicate target Course-FAQ rows exist.';
        }

        $inactiveAttachedCount = DB::table('course_faq')
            ->join('f_a_q_s', 'course_faq.faq_id', '=', 'f_a_q_s.id')
            ->whereIn('course_faq.course_id', $targetCourseIds)
            ->where('f_a_q_s.status', '!=', 'active')
            ->count();
        if ($inactiveAttachedCount > 0) {
            $errors[] = 'An inactive FAQ is attached to a target course.';
        }

        if (Course::query()->whereNotIn('id', $targetCourseIds)->orderBy('id')->get()->toArray() !== $protectedState['unrelated_courses']) {
            $errors[] = 'Unrelated course records changed.';
        }

        if (FAQ::query()->whereNotIn('question', ApprovedCourseFaqDeploymentData::requiredFaqQuestions())->orderBy('id')->get()->toArray() !== $protectedState['unrelated_faqs']) {
            $errors[] = 'Unrelated FAQ records changed.';
        }

        $unrelatedPivots = DB::table('course_faq')->whereNotIn('course_id', $targetCourseIds)->orderBy('course_id')->orderBy('faq_id')->get()->map(fn (object $row): array => (array) $row)->all();
        if ($unrelatedPivots !== $protectedState['unrelated_pivots']) {
            $errors[] = 'Unrelated course relationships changed.';
        }

        $freeCourse = Course::query()->where('slug', 'free-course-roadmap-help')->first();
        if (! $freeCourse || [
            'instructor' => (string) $freeCourse->instructor,
            'price' => (string) $freeCourse->price,
            'duration' => (string) $freeCourse->duration,
        ] !== $protectedState['free_metadata']) {
            $errors[] = 'Free Course Roadmap Help metadata changed.';
        }

        if ($this->germanCourseCount() !== $protectedState['german_count'] || $this->germanCourseCount() !== 0) {
            $errors[] = 'German A1 was created or already exists unexpectedly.';
        }

        if ($errors !== []) {
            throw new RuntimeException('Post-apply audit failed: '.implode(' ', $errors));
        }
    }

    private function germanCourseCount(): int
    {
        return Course::query()
            ->where(function ($query): void {
                $query->where('slug', 'german-a1')
                    ->orWhereRaw('LOWER(name) = ?', ['german a1']);
            })
            ->count();
    }

    private function display(mixed $value): string
    {
        if ($value === null) {
            return '(missing)';
        }

        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    /**
     * @param  array<int, string>  $values
     */
    private function displayList(array $values): string
    {
        return $values === [] ? '(none)' : implode(' | ', $values);
    }
}
