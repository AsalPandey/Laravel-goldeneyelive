<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Models\Testimonial;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Signature('goldeneye:normalize-brand-copy {--dry-run : Preview changes without saving}')]
#[Description('Normalize stale Golden Eye Academy public brand and academy-positioning copy.')]
class NormalizeBrandCopy extends Command
{
    /**
     * @var array<int, string>
     */
    private const SiteSettingKeys = [
        'site_name',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'aeo_summary',
        'schema_markup',
        'google_maps_embed',
        'hero_badge_text',
        'hero_title',
        'hero_hook_headline',
        'hero_hook_body',
        'hero_subtitle',
        'stat_1_lab',
        'courses_title',
        'courses_subtitle',
        'about_title',
        'about_text',
        'about_point_1',
        'about_point_3',
        'about_content_title',
        'about_content',
        'about_section_title',
        'about_header_title',
        'about_page_content',
        'about_feat_1_title',
        'about_feat_1_desc',
        'founder_message',
        'founder_section_title',
        'teachers_title',
        'teachers_subtitle',
        'blog_subtitle',
        'blog_section_title',
        'blog_cta_title',
        'blog_cta_desc',
        'faq_lead_title',
        'faq_page_content',
        'contact_header_title',
        'contact_page_content',
        'enroll_section_title',
        'privacy_policy_content',
        'popup_title',
        'popup_subtitle',
        'whatsapp_prefill_message',
        'sticky_cta_desc',
        'inquiry_tab_text',
        'inquiry_title',
        'inquiry_subtitle',
        'footer_about_text',
        'footer_newsletter_desc',
        'career_highlight_1',
        'career_highlight_2',
        'career_highlight_3',
        'career_highlight_4',
        'newsletter_success_message',
    ];

    /**
     * @var array<string, string>
     */
    private const ExactReplacements = [
        'GoldenEye Academy' => 'Golden Eye Academy',
        'GoldenEye%20Academy' => 'Golden%20Eye%20Academy',
        'GoldenEye' => 'Golden Eye',
        'Study Abroad Test Prep' => 'IELTS, PTE and Language Preparation',
        'Study Abroad Guidance' => 'IELTS, PTE and Language Preparation',
        'Study abroad guidance' => 'IELTS, PTE and language preparation',
        'study abroad guidance' => 'IELTS, PTE and language preparation',
        'study abroad applicants' => 'students preparing for international study goals',
        'Study abroad applicants' => 'Students preparing for international study goals',
        'abroad applicants' => 'learners preparing for international study goals',
        'Abroad applicants' => 'Learners preparing for international study goals',
        'study-abroad preparation' => 'exam and language preparation',
        'study abroad preparation' => 'exam and language preparation',
        'study abroad journey' => 'academic preparation',
        'Study abroad preparation' => 'Exam and language preparation',
        'global admission pathways' => 'exam and language preparation goals',
        'admission pathways' => 'exam preparation goals',
        'migration pathways' => 'language and exam preparation goals',
        'migration goals' => 'language and exam preparation goals',
        'documentation preparation' => 'academic preparation',
        'applications or documents' => 'enrollment steps',
        'Destination and timeline planning' => 'Exam goal and batch planning',
        'destination and timeline' => 'exam goal and batch timing',
        'destination, university requirement, timeline' => 'exam requirement, timeline',
        'destination, requirement, timeline' => 'exam requirement, timeline',
        'destination and test preference' => 'exam requirement and test preference',
        'admissions help' => 'enrollment support',
        'Admissions help' => 'Enrollment support',
        'admissions guidance' => 'enrollment support',
        'Admissions guidance' => 'Enrollment support',
        'career guidance' => 'career support',
        'Career guidance' => 'Career support',
        'Course Guidance Before Enrollment' => 'Course Information Before Enrollment',
        'Course Guidance and Events' => 'Academic Support and Events',
        'Course Guidance and Admissions' => 'Admissions and Academic Support',
        'Course Help Team' => 'Academic Support Team',
        'Guidance First' => 'Academy Support',
        'Years of Guidance' => 'Years of Learning',
        '15+ years of guidance' => 'Established in 2008',
        'Guidance before enrollment' => 'Academic support before enrollment',
        'guidance before enrollment' => 'academic support before enrollment',
        'course guidance before enrollment' => 'course information before enrollment',
        'Quick course guidance' => 'Course information',
        'quick course guidance' => 'course information',
        'Practical course guidance' => 'Practical classes and academic support',
        'practical course guidance' => 'practical classes and academic support',
        'simple guidance' => 'course information',
        'guidance session' => 'class-information session',
        'Guidance from experienced teachers' => 'Experienced faculty for practical classes',
        'Confirm current profile with the academy team' => 'Practical lessons, student questions, and progress feedback',
        'course guidance Nepal' => 'course information Pokhara',
        'Course guidance' => 'Course information',
        'course guidance' => 'course information',
        'course-specific guidance and classroom support' => 'course-specific academic support and classroom practice',
        'Course-specific guidance and classroom support' => 'Course-specific academic support and classroom practice',
        'progress guidance' => 'progress support',
        'next-step guidance' => 'next-step support',
        'guidance quality' => 'faculty experience',
        'Good guidance reduces wrong enrollment' => 'Clear enrollment support helps students understand class options',
        'a Training Institute' => 'an Academy',
        'a training institute' => 'an academy',
        'an institute' => 'an academy',
        'An institute' => 'An academy',
        'training institute' => 'academy',
        'Training Institute' => 'Academy',
        'institute' => 'academy',
        'Institute' => 'Academy',
        'Practical training' => 'Practical classes',
        'practical training' => 'practical classes',
    ];

    /**
     * @var array<string, string>
     */
    private const ExactWholeValueReplacements = [
        'GoldenEye' => 'Golden Eye',
        'GoldenEye Academy - Career, College, Skills and Test Prep in Pokhara' => 'Golden Eye Academy | Established Academy in Pokhara Since 2008',
        'GoldenEye Academy helps students, parents, study abroad applicants, and job seekers choose practical courses through quick course guidance in Pokhara.' => 'Established in 2008, Golden Eye Academy offers IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes in Pokhara, Nepal.',
        'GoldenEye Academy provides course guidance, test preparation, language classes, computer skills, web development, and practical support for learners in Pokhara.' => 'Golden Eye Academy is an established academy in Pokhara, Nepal, serving learners since 2008 through IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes.',
        'Choose the right course with clear guidance.' => 'Established academy in Pokhara since 2008.',
        'Confused about what to study next? Do not choose a course randomly. GoldenEye Academy helps students, parents, study abroad applicants, and job seekers compare the right path before enrollment.' => 'Golden Eye Academy offers practical classes and skill-based batches for IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT learners in Pokhara.',
        'Tell us your goal. We will help you compare course options by timing, fee, current level, and next step.' => 'Explore practical classes, batch options, faculty support, and enrollment information before choosing your course.',
        'Guidance-first learning since 2008' => 'Practical learning since 2008',
        'GoldenEye Academy exists for one reason: learners should not waste time, money, or confidence on the wrong course. We help you decide first, then train with structure.' => 'Golden Eye Academy is an established academy in Pokhara focused on structured classes, practical learning, student support, and clear enrollment information.',
        'Course Guidance Before Enrollment' => 'Course Information Before Enrollment',
        'Still deciding? Ask before enrollment.' => 'Need course information before enrollment?',
        'Tell us your goal and we will recommend the right course path before you spend money on the wrong option.' => 'Tell us your subject goal and we will explain suitable classes, batch options, and academic support.',
        'Hi GoldenEye Academy, I have a quick question. Can you help me choose the right course?' => 'Hi Golden Eye Academy, I have a question about classes and enrollment.',
        'Hi GoldenEye Academy, I need help choosing the right course.' => 'Hi Golden Eye Academy, I have a question about classes and enrollment.',
        'Parents Guide: How to Evaluate a Training Institute' => 'Parents Guide: How to Evaluate an Academy',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $rows = [];
        $totalChanges = 0;

        [$siteRows, $siteChanges] = $this->normalizeSiteSettings($dryRun);
        $rows = array_merge($rows, $siteRows);
        $totalChanges += $siteChanges;

        foreach ($this->normalizableModels() as $definition) {
            [$modelRows, $modelChanges] = $this->normalizeModelFields($definition['class'], $definition['table'], $definition['fields'], $dryRun);
            $rows = array_merge($rows, $modelRows);
            $totalChanges += $modelChanges;
        }

        if ($rows === []) {
            $this->info('No public brand copy records were found to inspect.');
        } else {
            $this->table(['Table', 'Key / ID', 'Old value', 'New value', 'Status'], $rows);
        }

        if (! $dryRun && $totalChanges > 0) {
            $this->clearAffectedCaches();
        }

        $this->info(($dryRun ? 'Dry-run total changes: ' : 'Total changes: ').$totalChanges);

        return self::SUCCESS;
    }

    /**
     * @return array{0: array<int, array<int, string>>, 1: int}
     */
    private function normalizeSiteSettings(bool $dryRun): array
    {
        $rows = [];
        $changes = 0;

        foreach (self::SiteSettingKeys as $key) {
            $setting = SiteSetting::query()->where('key', $key)->first();

            if (! $setting) {
                continue;
            }

            $oldValue = (string) ($setting->value ?? '');
            $newValue = $this->normalizeCopy($oldValue);

            if ($newValue === $oldValue) {
                continue;
            }

            $changes++;

            if (! $dryRun) {
                $setting->forceFill(['value' => $newValue])->save();
            }

            $rows[] = $this->row('site_settings', $key, $oldValue, $newValue, $dryRun ? 'would change' : 'changed');
        }

        return [$rows, $changes];
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<int, string>  $fields
     * @return array{0: array<int, array<int, string>>, 1: int}
     */
    private function normalizeModelFields(string $modelClass, string $table, array $fields, bool $dryRun): array
    {
        $rows = [];
        $changes = 0;

        foreach ($modelClass::query()->orderBy('id')->cursor() as $model) {
            $updates = [];

            foreach ($fields as $field) {
                $oldValue = $model->getAttribute($field);

                if ($oldValue === null) {
                    continue;
                }

                $newValue = is_array($oldValue)
                    ? $this->normalizeArray($oldValue)
                    : $this->normalizeCopy((string) $oldValue);

                if ($newValue === $oldValue) {
                    continue;
                }

                $updates[$field] = $newValue;
                $rows[] = $this->row($table, $model->getKey().'.'.$field, $this->displayable($oldValue), $this->displayable($newValue), $dryRun ? 'would change' : 'changed');
            }

            if ($updates === []) {
                continue;
            }

            $changes += count($updates);

            if (! $dryRun) {
                $model->forceFill($updates)->save();
            }
        }

        return [$rows, $changes];
    }

    /**
     * @return array<int, array{class: class-string<Model>, table: string, fields: array<int, string>}>
     */
    private function normalizableModels(): array
    {
        return [
            ['class' => Notice::class, 'table' => 'notices', 'fields' => ['title', 'subtitle', 'badge', 'button_text']],
            ['class' => ServicePillar::class, 'table' => 'service_pillars', 'fields' => ['title', 'summary', 'bullets', 'cta_label', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup']],
            ['class' => CourseCategory::class, 'table' => 'course_categories', 'fields' => ['name', 'description', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup']],
            ['class' => Course::class, 'table' => 'courses', 'fields' => ['name', 'badge_text', 'category', 'description', 'course_outline', 'instructor', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup']],
            ['class' => FAQ::class, 'table' => 'f_a_q_s', 'fields' => ['question', 'answer', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup']],
            ['class' => BlogPost::class, 'table' => 'blog_posts', 'fields' => ['title', 'content', 'author', 'category', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup']],
            ['class' => Teacher::class, 'table' => 'teachers', 'fields' => ['name', 'designation', 'bio', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup']],
            ['class' => Testimonial::class, 'table' => 'testimonials', 'fields' => ['student_name', 'course_name', 'content', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup']],
        ];
    }

    private function normalizeCopy(string $value): string
    {
        if (array_key_exists($value, self::ExactWholeValueReplacements)) {
            return self::ExactWholeValueReplacements[$value];
        }

        return str_replace(
            array_keys(self::ExactReplacements),
            array_values(self::ExactReplacements),
            $value,
        );
    }

    /**
     * @param  array<int|string, mixed>  $values
     * @return array<int|string, mixed>
     */
    private function normalizeArray(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $values[$key] = $this->normalizeArray($value);

                continue;
            }

            if (is_string($value)) {
                $values[$key] = $this->normalizeCopy($value);
            }
        }

        return $values;
    }

    private function displayable(mixed $value): string
    {
        if (is_array($value)) {
            return json_encode($value) ?: '';
        }

        return (string) $value;
    }

    /**
     * @return array<int, string>
     */
    private function row(string $table, string $key, string $oldValue, string $newValue, string $status): array
    {
        return [
            $table,
            $key,
            $this->displayValue($oldValue),
            $this->displayValue($newValue),
            $status,
        ];
    }

    private function displayValue(string $value): string
    {
        if ($value === '') {
            return '(blank)';
        }

        return str($value)->limit(90, '...')->toString();
    }

    private function clearAffectedCaches(): void
    {
        foreach (self::SiteSettingKeys as $settingKey) {
            Cache::forget("setting_{$settingKey}");
        }

        foreach ([
            'homepage_data',
            'homepage_data_v2',
            'site_settings',
            'site_shared_data',
            'site_active_notice',
            'service_pillars',
            'sitemap_xml',
        ] as $cacheKey) {
            Cache::forget($cacheKey);
        }
    }
}
