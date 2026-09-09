<?php

namespace App\Support;

final class CmsPublicContent
{
    /**
     * @return array<string, array{key: string, label: string, description: string}>
     */
    public static function homepageSectionDefinitions(): array
    {
        return [
            'hero' => ['key' => 'home_hero_status', 'label' => 'Homepage hero', 'description' => 'Main headline, hero image and primary actions.'],
            'trust' => ['key' => 'home_trust_status', 'label' => 'Trust strip', 'description' => 'Compact academy facts directly below the hero.'],
            'audience' => ['key' => 'home_audience_status', 'label' => 'Audience cards', 'description' => 'Student, parent, study and job-skill pathways.'],
            'courses' => ['key' => 'home_courses_status', 'label' => 'Popular courses', 'description' => 'Active courses selected by the existing course ordering.'],
            'categories' => ['key' => 'home_categories_status', 'label' => 'Course categories', 'description' => 'Active course categories and their course counts.'],
            'why' => ['key' => 'home_why_status', 'label' => 'Why choose the academy', 'description' => 'Academic support summary and local advantages.'],
            'testimonials' => ['key' => 'home_testimonials_status', 'label' => 'Student testimonials', 'description' => 'Active testimonial cards.'],
            'faculty' => ['key' => 'home_faculty_status', 'label' => 'Faculty', 'description' => 'Active faculty profiles.'],
            'reviews' => ['key' => 'home_reviews_status', 'label' => 'External review proof', 'description' => 'Google profile link, screenshot and proof note.'],
            'parents' => ['key' => 'home_parent_status', 'label' => 'Parent information', 'description' => 'Parent-focused enrollment clarity.'],
            'faq' => ['key' => 'home_faq_status', 'label' => 'Homepage FAQs', 'description' => 'Active frequently asked questions.'],
            'final' => ['key' => 'home_final_status', 'label' => 'Final inquiry section', 'description' => 'Final course-help and WhatsApp actions.'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function audienceDefinitions(): array
    {
        return [
            'students' => [
                'label' => 'Students',
                'route' => 'for-students',
                'page_title' => 'Courses for Students in Pokhara | Golden Eye Academy',
                'meta_description' => 'Compare practical computer, language and IELTS/PTE courses in Pokhara by learning focus, fee and duration, then ask which starting point fits your goal.',
                'source_page' => 'for_students',
                'audience_type' => 'student',
                'inquiry_intent' => 'course_selection_help',
                'selected_course' => 'undecided',
                'badge' => 'For students',
                'headline' => 'Not sure which course to choose after school or college?',
                'subheadline' => 'Golden Eye Academy helps students compare IELTS/PTE, language classes, computer skills, office classes, and web development before choosing a course.',
                'problem' => 'Many students choose courses randomly because friends joined, parents suggested something, or the course sounds popular. But the right course depends on your goal, current level, time, budget, and future plan.',
                'paths' => [
                    'IELTS / PTE if you are preparing for English exam goals',
                    'Japanese / Korean if you want language preparation',
                    'Computer & Office Skills if you want practical office and computer skills',
                    'Web Development if you want to start an IT skill path',
                ],
                'why' => 'We help you compare course information before enrollment and ask which current batch options fit your availability.',
                'proof' => [
                    'Srijana Chowk, Pokhara, Nepal',
                    'Course pages list available fee, duration, outline, and instructor information',
                    'Ask the academy to confirm current batch timing before enrollment',
                ],
                'final_headline' => 'Tell us your goal and current level. We will explain relevant course information before enrollment.',
                'secondary_action' => 'courses',
            ],
            'parents' => [
                'label' => 'Parents',
                'route' => 'for-parents',
                'page_title' => 'Course Guide for Parents in Pokhara | Golden Eye Academy',
                'meta_description' => 'Compare course suitability, fees, duration, instructors, current timings and completion certificates before helping your son or daughter choose.',
                'source_page' => 'for_parents',
                'audience_type' => 'parent',
                'inquiry_intent' => 'parent_course_guidance',
                'selected_course' => 'undecided',
                'badge' => 'For parents',
                'headline' => 'Need clear course information for your child?',
                'subheadline' => 'Golden Eye Academy helps parents understand course fit, fees, timing, academic support, and realistic outcomes before enrollment.',
                'problem' => 'Parents often want to know whether a course is useful, who teaches it, what it costs, how long it takes, and whether it fits the student\'s future plan. We explain these details clearly before enrollment.',
                'paths' => [
                    'Course fit',
                    'Fee and duration',
                    'Batch timing',
                    'Instructor support',
                    'Expected learning outcome',
                    'What support is included',
                    'What is not guaranteed',
                ],
                'why' => 'We focus on practical classes, transparent communication, and helping students choose a course based on their actual goal.',
                'proof' => [
                    'Srijana Chowk, Pokhara, Nepal',
                    'Course pages show available fee, duration, outline, and instructor information',
                    'Phone and WhatsApp follow-up available',
                ],
                'final_headline' => 'Share your child\'s current level and goal. Our team will explain relevant course options and details to confirm.',
                'secondary_action' => 'whatsapp',
            ],
            'study_abroad' => [
                'label' => 'IELTS, PTE and language preparation',
                'route' => 'study-abroad-guidance',
                'page_title' => 'IELTS, PTE & Language Preparation in Pokhara | Golden Eye Academy',
                'meta_description' => 'See Golden Eye Academy preparation classes for IELTS, PTE and languages. For wider study-abroad consulting questions, contact Brilliant Education Pokhara.',
                'source_page' => 'study_abroad_guidance',
                'audience_type' => 'study_abroad_applicant',
                'inquiry_intent' => 'study_abroad_course_guidance',
                'selected_course' => 'exam and language preparation',
                'badge' => 'IELTS, PTE and language preparation',
                'headline' => 'Preparing for international study goals?',
                'subheadline' => 'Explore IELTS, PTE, Japanese, Korean, English, and practical classes that support your academic preparation.',
                'problem' => 'Exam and language preparation can feel unclear at first. Students often need help understanding whether to start with IELTS, PTE, Japanese, Korean, English, or basic computer skills.',
                'paths' => [
                    'IELTS preparation for English test goals',
                    'PTE preparation for computer-based English testing',
                    'Japanese / Korean language courses for language pathway learners',
                    'Computer skills for academic and workplace readiness',
                ],
                'support_tagline' => 'Clear service roles',
                'support_title' => 'Preparation classes and consulting are handled separately',
                'why' => 'Language and test-preparation classes are provided by Golden Eye Academy. Education, career and study-abroad consulting services are presented separately through Brilliant Education Pokhara.',
                'proof_tagline' => 'Preparation and guidance',
                'proof' => [
                    'Golden Eye Academy provides relevant language and test-preparation classes.',
                    'Brilliant Education Pokhara is the contextual brand for broader education-consulting questions.',
                    'Neither preparation nor consulting guarantees a score, admission or visa outcome.',
                ],
                'final_headline' => 'Tell us your exam goal, current education level, and timeline. We will explain suitable preparation classes.',
                'secondary_action' => 'courses',
            ],
            'job_computer_skills' => [
                'label' => 'Computer and job skills',
                'route' => 'job-computer-skills',
                'page_title' => 'Computer & Digital Skills Courses in Pokhara | Golden Eye Academy',
                'meta_description' => 'Build practical computer and workplace skills through documents, spreadsheets, presentations, email and web development with Laravel, APIs and deployment concepts.',
                'source_page' => 'job_computer_skills',
                'audience_type' => 'job_skill_learner',
                'inquiry_intent' => 'computer_skill_guidance',
                'selected_course' => 'computer skills',
                'badge' => 'Computer and job skills',
                'headline' => 'Want practical computer or workplace skills?',
                'subheadline' => 'Golden Eye Academy provides computer, office, web development, and IT classes for study and workplace tasks.',
                'problem' => 'Many students and job seekers want practical skills but do not know where to start. Some need basic computer confidence, some need office skills, and some want to begin web development.',
                'paths' => [
                    'Basic Computer Skills for beginners',
                    'Office Package / Computer Skills for common workplace tasks',
                    'Web Development for IT skill-building',
                    'Guided exercises based on the published course outline',
                ],
                'why' => 'We help you choose a course based on your current level and future goal, not random trends.',
                'proof' => [
                    'Computer, office, and web development tracks',
                    'Course learning areas, durations, fees and instructor information',
                    'Classes and contact support based in Pokhara',
                ],
                'final_headline' => 'Tell us your current skill level and goal. We will explain the available starting points.',
                'secondary_action' => 'courses',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public static function homepage(array $settings): array
    {
        $sections = [];
        foreach (self::homepageSectionDefinitions() as $name => $definition) {
            $sections[$name] = self::isActive($settings[$definition['key']] ?? null);
        }

        $audienceCards = [
            'students' => [
                'icon' => 'fa fa-graduation-cap',
                'route' => 'for-students',
                'source' => 'audience-student',
                'title' => 'I am a Student',
                'problem' => 'Not sure which course to choose after school or college?',
                'benefit' => 'Compare options by goal, timing, and current level.',
            ],
            'parents' => [
                'icon' => 'fa fa-users',
                'route' => 'for-parents',
                'source' => 'audience-parent',
                'title' => 'I am a Parent',
                'problem' => 'Need clarity on fees, timing, safety, and course fit?',
                'benefit' => 'We explain course fit, fees, timing, expected outcomes, and next steps.',
            ],
            'study_abroad' => [
                'icon' => 'fa fa-plane',
                'route' => 'study-abroad-guidance',
                'source' => 'audience-study-abroad',
                'title' => 'I need IELTS / PTE',
                'problem' => 'Comparing IELTS, PTE, Japanese, or Korean classes?',
                'benefit' => 'Match exam preparation with your goal and batch timing.',
            ],
            'job_computer_skills' => [
                'icon' => 'fa fa-laptop-code',
                'route' => 'job-computer-skills',
                'source' => 'audience-job-computer-skills',
                'title' => 'I want Job/Computer Skills',
                'problem' => 'Want practical skills for work, office, or IT?',
                'benefit' => 'Start with a skill path that fits your level.',
            ],
        ];

        foreach ($audienceCards as $key => &$card) {
            $prefix = "home_audience_{$key}";
            $card['is_active'] = self::isActive($settings["{$prefix}_status"] ?? null)
                && self::isActive($settings["audience_{$key}_status"] ?? null);
            $card['title'] = self::value($settings, "{$prefix}_title", $card['title']);
            $card['problem'] = self::value($settings, "{$prefix}_problem", $card['problem']);
            $card['benefit'] = self::value($settings, "{$prefix}_benefit", $card['benefit']);
            $card['cta_text'] = self::value($settings, "{$prefix}_cta_text", 'View Course Details');
        }
        unset($card);

        $trustDefaults = [
            'Golden Eye Academy, Pokhara',
            self::value($settings, 'site_address', 'Srijana Chowk, Pokhara, Nepal'),
            'Phone: '.self::value($settings, 'site_phone', '061-572599'),
            'Email: '.config('goldeneye.official_email', 'contact@goldeneye.edu.np'),
            'Current batch timing confirmed before enrollment',
        ];
        $advantageDefaults = [
            self::value($settings, 'site_address', 'Srijana Chowk, Pokhara, Nepal'),
            'Course descriptions, outlines, fees, and durations available for comparison',
            'Language, test preparation, computer, office, web, and IT course options',
            'Phone and WhatsApp contact: '.self::value($settings, 'site_phone', '061-572599'),
        ];
        $parentDefaults = [
            'Review course fit before enrollment',
            'Check the published fee and duration',
            'Confirm current batch timing',
            'Ask what depends on attendance and practice',
        ];

        return [
            'sections' => $sections,
            'meta_title' => self::value($settings, 'home_meta_title', 'Golden Eye Academy | Computer, Language & IELTS/PTE in Pokhara'),
            'meta_description' => self::value($settings, 'home_meta_description', 'Learn practical computer, office and web-development skills in Pokhara, alongside language and IELTS/PTE preparation. Compare courses, fees and durations.'),
            'trust_items_text' => self::value($settings, 'home_trust_items', implode("\n", $trustDefaults)),
            'trust_items' => self::lines($settings, 'home_trust_items', $trustDefaults),
            'audience_tagline' => self::value($settings, 'home_audience_tagline', 'Start here'),
            'audience_title' => self::value($settings, 'home_audience_title', 'Which class are you interested in?'),
            'audience_cards' => $audienceCards,
            'courses_tagline' => self::value($settings, 'home_courses_tagline', 'Popular courses'),
            'courses_title' => self::value($settings, 'home_courses_title', 'Courses students ask about most'),
            'courses_cta_text' => self::value($settings, 'home_courses_cta_text', 'View Course Details'),
            'courses_batch_note' => self::value($settings, 'home_courses_batch_note', 'Confirm current batch options with the academy'),
            'categories_tagline' => self::value($settings, 'home_categories_tagline', 'Course categories'),
            'categories_title' => self::value($settings, 'home_categories_title', 'Browse by learning goal'),
            'why_tagline' => self::value($settings, 'home_why_tagline', 'Why Golden Eye Academy'),
            'why_title' => self::value($settings, 'home_why_title', 'Course information before enrollment'),
            'why_description' => self::value($settings, 'home_why_description', 'Review available course descriptions, outlines, fees, durations, and instructor information. Contact the academy to confirm the current batch before enrolling.'),
            'why_items_text' => self::value($settings, 'home_why_items', implode("\n", $advantageDefaults)),
            'why_items' => self::lines($settings, 'home_why_items', $advantageDefaults),
            'testimonials_tagline' => self::value($settings, 'home_testimonials_tagline', 'Student experiences'),
            'testimonials_title' => self::value($settings, 'home_testimonials_title', 'Hear from learners who studied at Golden Eye Academy'),
            'faculty_tagline' => self::value($settings, 'home_faculty_tagline', 'Instructors'),
            'faculty_title' => self::value($settings, 'home_faculty_title', 'Faculty profiles and course information'),
            'reviews_tagline' => self::value($settings, 'home_reviews_tagline', 'Verified external reviews'),
            'reviews_title' => self::value($settings, 'home_reviews_title', 'Check independent review information'),
            'parent_tagline' => self::value($settings, 'home_parent_tagline', 'For parents'),
            'parent_title' => self::value($settings, 'home_parent_title', 'Clear answers before your child enrolls'),
            'parent_description' => self::value($settings, 'home_parent_description', 'For parents, we explain course fit, fees, timing, expected outcomes, and realistic next steps before enrollment. No pressure. Visit, call, or message us to understand the right option for your child.'),
            'parent_items_text' => self::value($settings, 'home_parent_items', implode("\n", $parentDefaults)),
            'parent_items' => self::lines($settings, 'home_parent_items', $parentDefaults),
            'faq_tagline' => self::value($settings, 'home_faq_tagline', 'FAQ'),
            'faq_title' => self::value($settings, 'home_faq_title', 'Common questions before enrollment'),
            'final_tagline' => self::value($settings, 'home_final_tagline', 'Next step'),
            'final_title' => self::value($settings, 'home_final_title', 'Need course and batch information?'),
            'final_description' => self::value($settings, 'home_final_description', 'Send your goal. Our academy team will explain suitable classes before enrollment.'),
            'final_primary_cta_text' => self::value($settings, 'home_final_primary_cta_text', 'Ask for Course Help'),
            'final_secondary_cta_text' => self::value($settings, 'home_final_secondary_cta_text', 'Message on WhatsApp'),
        ];
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, array<string, mixed>>
     */
    public static function audiencePages(array $settings): array
    {
        $pages = [];

        foreach (self::audienceDefinitions() as $key => $defaults) {
            $prefix = "audience_{$key}";
            $secondaryAction = self::value($settings, "{$prefix}_secondary_action", $defaults['secondary_action']);
            $resolvedSecondaryAction = in_array($secondaryAction, ['courses', 'whatsapp'], true)
                ? $secondaryAction
                : $defaults['secondary_action'];
            $pages[$key] = [
                ...$defaults,
                'key' => $key,
                'prefix' => $prefix,
                'is_active' => self::isActive($settings["{$prefix}_status"] ?? null),
                'sections' => [
                    'hero' => self::isActive($settings["{$prefix}_hero_status"] ?? null),
                    'guidance' => self::isActive($settings["{$prefix}_guidance_status"] ?? null),
                    'support' => self::isActive($settings["{$prefix}_support_status"] ?? null),
                    'final' => self::isActive($settings["{$prefix}_final_status"] ?? null),
                ],
                'page_title' => self::value($settings, "{$prefix}_page_title", $defaults['page_title']),
                'meta_description' => self::value($settings, "{$prefix}_meta_description", $defaults['meta_description']),
                'image' => self::value($settings, "{$prefix}_image", self::value($settings, 'hero_image', 'site/img/carousel-1.png')),
                'social_image' => trim((string) ($settings["{$prefix}_image"] ?? '')),
                'badge' => self::value($settings, "{$prefix}_badge", $defaults['badge']),
                'headline' => self::value($settings, "{$prefix}_headline", $defaults['headline']),
                'subheadline' => self::value($settings, "{$prefix}_subheadline", $defaults['subheadline']),
                'problem_tagline' => self::value($settings, "{$prefix}_problem_tagline", 'What usually happens'),
                'problem_title' => self::value($settings, "{$prefix}_problem_title", 'The choice needs context first'),
                'problem' => self::value($settings, "{$prefix}_problem", $defaults['problem']),
                'paths_tagline' => self::value($settings, "{$prefix}_paths_tagline", 'Recommended paths'),
                'paths_title' => self::value($settings, "{$prefix}_paths_title", 'Start with the path that fits your goal'),
                'paths_text' => self::value($settings, "{$prefix}_paths", implode("\n", $defaults['paths'])),
                'paths' => self::lines($settings, "{$prefix}_paths", $defaults['paths']),
                'support_tagline' => self::value($settings, "{$prefix}_support_tagline", $defaults['support_tagline'] ?? 'Why Golden Eye Academy helps'),
                'support_title' => self::value($settings, "{$prefix}_support_title", $defaults['support_title'] ?? 'Academic support before enrollment'),
                'why' => self::value($settings, "{$prefix}_why", $defaults['why']),
                'proof_tagline' => self::value($settings, "{$prefix}_proof_tagline", $defaults['proof_tagline'] ?? 'Trust markers'),
                'proof_text' => self::value($settings, "{$prefix}_proof", implode("\n", $defaults['proof'])),
                'proof' => self::lines($settings, "{$prefix}_proof", $defaults['proof']),
                'final_tagline' => self::value($settings, "{$prefix}_final_tagline", 'Next step'),
                'final_headline' => self::value($settings, "{$prefix}_final_headline", $defaults['final_headline']),
                'primary_cta_text' => self::value($settings, "{$prefix}_primary_cta_text", 'Ask for Course Help'),
                'secondary_action' => $resolvedSecondaryAction,
                'secondary_cta_text' => self::value(
                    $settings,
                    "{$prefix}_secondary_cta_text",
                    $resolvedSecondaryAction === 'whatsapp' ? 'Message on WhatsApp' : 'View Course Details',
                ),
            ];
        }

        return $pages;
    }

    /**
     * @return array<int, string>
     */
    public static function textKeys(): array
    {
        $keys = [
            'home_meta_title',
            'home_meta_description',
            'home_trust_items',
            'home_audience_tagline',
            'home_audience_title',
            'home_courses_tagline',
            'home_courses_title',
            'home_courses_cta_text',
            'home_courses_batch_note',
            'home_categories_tagline',
            'home_categories_title',
            'home_why_tagline',
            'home_why_title',
            'home_why_description',
            'home_why_items',
            'home_testimonials_tagline',
            'home_testimonials_title',
            'home_faculty_tagline',
            'home_faculty_title',
            'home_reviews_tagline',
            'home_reviews_title',
            'home_parent_tagline',
            'home_parent_title',
            'home_parent_description',
            'home_parent_items',
            'home_faq_tagline',
            'home_faq_title',
            'home_final_tagline',
            'home_final_title',
            'home_final_description',
            'home_final_primary_cta_text',
            'home_final_secondary_cta_text',
        ];

        foreach (self::homepageSectionDefinitions() as $definition) {
            $keys[] = $definition['key'];
        }

        foreach (array_keys(self::audienceDefinitions()) as $key) {
            $homePrefix = "home_audience_{$key}";
            array_push(
                $keys,
                "{$homePrefix}_status",
                "{$homePrefix}_title",
                "{$homePrefix}_problem",
                "{$homePrefix}_benefit",
                "{$homePrefix}_cta_text",
            );

            $prefix = "audience_{$key}";
            foreach ([
                'status',
                'hero_status',
                'guidance_status',
                'support_status',
                'final_status',
                'page_title',
                'meta_description',
                'badge',
                'headline',
                'subheadline',
                'problem_tagline',
                'problem_title',
                'problem',
                'paths_tagline',
                'paths_title',
                'paths',
                'support_tagline',
                'support_title',
                'why',
                'proof_tagline',
                'proof',
                'final_tagline',
                'final_headline',
                'primary_cta_text',
                'secondary_action',
                'secondary_cta_text',
            ] as $suffix) {
                $keys[] = "{$prefix}_{$suffix}";
            }
        }

        return array_values(array_unique($keys));
    }

    /**
     * @return array<int, string>
     */
    public static function imageKeys(): array
    {
        return array_map(
            fn (string $key): string => "audience_{$key}_image",
            array_keys(self::audienceDefinitions()),
        );
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function validationRules(): array
    {
        $rules = [];
        $statusKeys = array_map(
            fn (array $definition): string => $definition['key'],
            self::homepageSectionDefinitions(),
        );

        foreach (array_keys(self::audienceDefinitions()) as $key) {
            $homePrefix = "home_audience_{$key}";
            $statusKeys[] = "{$homePrefix}_status";
            $prefix = "audience_{$key}";
            foreach (['status', 'hero_status', 'guidance_status', 'support_status', 'final_status'] as $suffix) {
                $statusKeys[] = "{$prefix}_{$suffix}";
            }
            $rules["{$prefix}_secondary_action"] = ['nullable', 'in:courses,whatsapp'];
            $rules["{$prefix}_page_title"] = ['nullable', 'string', 'max:255'];
            $rules["{$prefix}_meta_description"] = ['nullable', 'string', 'max:500'];
            $rules["{$prefix}_paths"] = ['nullable', 'string', 'max:2500'];
            $rules["{$prefix}_proof"] = ['nullable', 'string', 'max:1500'];
        }

        foreach ($statusKeys as $key) {
            $rules[$key] = ['nullable', 'in:active,inactive'];
        }

        foreach (self::textKeys() as $key) {
            $rules[$key] ??= ['nullable', 'string', 'max:1000'];
        }

        return $rules;
    }

    public static function isActive(mixed $value): bool
    {
        return $value !== 'inactive';
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    private static function value(array $settings, string $key, string $default): string
    {
        $value = trim((string) ($settings[$key] ?? ''));

        if ($value === '') {
            $default = GoldenEyeContentBaseline::settingValue($key, $default);
        }

        return $value !== '' ? $value : $default;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @param  array<int, string>  $defaults
     * @return array<int, string>
     */
    private static function lines(array $settings, string $key, array $defaults): array
    {
        $value = trim((string) ($settings[$key] ?? ''));
        if ($value === '') {
            $value = GoldenEyeContentBaseline::settingValue($key);
        }

        if ($value === '') {
            return $defaults;
        }

        $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];

        return array_values(array_filter(
            array_map(fn (string $line): string => trim($line), $lines),
        ));
    }
}
