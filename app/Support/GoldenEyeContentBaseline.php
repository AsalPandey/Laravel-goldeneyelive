<?php

namespace App\Support;

final class GoldenEyeContentBaseline
{
    public const FOUNDING_YEAR = '2008';

    /**
     * Public CMS values that form the finished fresh-site baseline.
     *
     * @return array<string, array{value: string, type: string}>
     */
    public static function siteSettings(): array
    {
        $settings = [
            'founding_year' => '2008',
            'logo_subtitle' => 'Pokhara, Nepal - Since 2008',
            'meta_title' => 'Golden Eye Academy | Established in Pokhara Since 2008',
            'meta_description' => 'Golden Eye Academy is an established academy in Pokhara offering computer, language, test-preparation and academic-support classes since 2008.',
            'aeo_summary' => 'Golden Eye Academy has provided practical courses, classes and academic support in Pokhara since 2008.',

            'hero_badge_text' => 'Established in Pokhara since 2008',
            'hero_title' => 'Practical courses for your next step.',
            'hero_subtitle' => 'Golden Eye Academy offers computer and digital-skills courses in Pokhara, alongside language and IELTS/PTE preparation. Compare courses or ask where to begin.',
            'hero_cta_1_text' => 'Ask for Course Help',
            'hero_cta_2_text' => 'View Course Details',
            'homepage_social_image' => '',

            'stat_1_val' => '',
            'stat_1_lab' => '',
            'stat_2_val' => '',
            'stat_2_lab' => '',
            'stat_3_val' => '',
            'stat_3_lab' => '',
            'stat_4_val' => '',
            'stat_4_lab' => '',

            'pathway_tagline' => 'Choose by goal',
            'pathway_title' => 'Find a course that fits what you want to learn.',
            'courses_title' => 'Computer, Language and IELTS/PTE Courses in Pokhara',
            'courses_header_title' => 'Courses and Classes',
            'courses_subtitle' => 'Explore practical computer, office and web-development courses, alongside language and IELTS/PTE classes. Compare learning areas, fees and durations.',
            'courses_all_tagline' => 'Find your course',
            'courses_all_title' => 'Explore practical classes by subject.',
            'home_courses_batch_note' => 'Ask about current batch timings',

            'about_point_1' => 'Established in Pokhara since 2008',
            'about_point_2' => 'Computer, language and test-preparation classes',
            'about_point_3' => 'Academic support for students and families',
            'about_point_4' => 'Practical learning connected to real goals',
            'about_content_title' => 'Learning with a clear purpose',
            'about_content' => 'Golden Eye Academy helps learners understand a course, choose a suitable starting point and build skills through structured classes and practical learning.',
            'about_header_title' => 'About Golden Eye Academy',
            'about_page_content' => <<<'HTML'
<h2>An established academy in Pokhara</h2>
<p>Golden Eye Academy has served learners in Pokhara since 2008. The academy provides courses, classes and academic support for students, parents and other learners who want to make a thoughtful education or skills decision.</p>
<h2>Learning connected to a real goal</h2>
<p>Our course areas include computer and IT skills, language and test preparation, English communication and academic support. Active course pages show the options currently listed by the academy.</p>
<p>We believe a useful course decision begins with a clear goal. Students can compare the learning focus, duration and current fee, then speak with the academy team about the batch that suits their level and timetable.</p>
<h2>Support for students and parents</h2>
<p>Students can ask where to begin, while parents can discuss course suitability, learning focus, faculty information and realistic expectations. Our role is to make the next step easier to understand and support steady learning once classes begin.</p>
HTML,
            'about_feat_1_title' => 'Established in Pokhara',
            'about_feat_1_desc' => 'Golden Eye Academy has supported learning in Pokhara since 2008.',
            'about_feat_2_title' => 'Practical Learning',
            'about_feat_2_desc' => 'Classes connect published learning areas with guided practice and useful skills.',
            'about_feat_3_title' => 'Student and Parent Support',
            'about_feat_3_desc' => 'Students and families can discuss course suitability, fees, timing and learning goals.',
            'about_feat_4_title' => 'Clear Course Information',
            'about_feat_4_desc' => 'Course pages and the academy team help learners compare the details that shape a good decision.',

            'blog_title' => 'Golden Eye Academy Course and Learning Guides',
            'blog_header_title' => 'Course and Learning Guides | Golden Eye Academy',
            'blog_subtitle' => 'Practical guides for choosing computer, language and test-preparation courses, building study routines and asking better questions before enrollment.',
            'blog_tagline' => 'Useful guidance from the academy',
            'blog_section_title' => 'Read the Latest Learning Guides',
            'blog_cta_title' => 'Want help choosing your next course?',
            'blog_cta_desc' => 'Tell us what you want to learn and we will help you compare relevant classes and current batches.',
            'blog_cta_btn' => 'Ask for Course Help',

            'faq_header_title' => 'Golden Eye Academy FAQs',
            'faq_lead_title' => 'Still deciding which course fits?',
            'faq_btn_text' => 'Show More FAQs',
            'faq_btn_text_expanded' => 'Show Fewer FAQs',
            'faq_page_content' => '<h2>Questions students and parents ask</h2><p>Find clear answers about choosing a course, practical learning, fees, current batches, faculty and visiting Golden Eye Academy.</p>',

            'contact_header_title' => 'Contact Golden Eye Academy',
            'contact_header_subtitle' => 'Courses, current batches and academy visits',
            'contact_intro_title' => 'Talk with our academy team',
            'contact_form_title' => 'Send Your Course Question',
            'contact_page_content' => '<p>Ask about course suitability, current batches, fees, duration, faculty, academic support or planning a visit. Share your goal and the subject you are considering so our team can give you a useful reply.</p>',
            'enroll_header_title' => 'Ask for Course Help',
            'enroll_section_title' => 'Tell us what you want to learn and where you would like to begin.',

            'catalogue_meta_title' => 'Compare Courses, Fees & Durations | Golden Eye Academy Pokhara',
            'catalogue_meta_description' => 'Compare Golden Eye Academy computer, office, web-development, language and IELTS/PTE courses by fee, duration and learning focus before choosing.',
            'catalogue_badge' => 'Golden Eye Academy learning options',
            'catalogue_title' => 'Explore courses, classes and academic support.',
            'catalogue_description' => 'Browse the academy’s learning areas, then open a course page to see what it covers, how long it takes and what it costs.',
            'catalogue_services_tagline' => 'Ways we support learning',
            'catalogue_services_title' => 'Choose the support area that matches your goal.',
            'catalogue_categories_tagline' => 'Browse by subject',
            'catalogue_categories_title' => 'Explore active course categories.',
            'catalogue_courses_tagline' => 'Current course options',
            'catalogue_courses_title' => 'Open a course to understand what it covers.',
            'catalogue_final_title' => 'Need help choosing between courses?',
            'catalogue_final_description' => 'Share your current level, goal and preferred timing. Our academy team will help you compare the relevant options.',

            'inquiry_tab_text' => 'Need Course Help?',
            'inquiry_title' => 'Ask about courses and current batches',
            'inquiry_subtitle' => 'Share your goal, current level and preferred subject so our academy team can help you compare relevant classes.',
            'sticky_cta_text' => 'Ask for Course Help',
            'sticky_cta_desc' => 'Ask about courses, batches or visiting the academy.',

            'footer_about_text' => 'Golden Eye Academy has provided courses, classes, academic support and practical learning in Pokhara since 2008.',
            'footer_faq_title' => 'Student and Parent Help',
            'footer_quick_link_title' => 'Academy Links',
            'footer_contact_title' => 'Visit or Contact Us',
            'footer_social_title' => 'Follow Golden Eye Academy',
            'footer_newsletter_desc' => 'Receive course and batch updates from Golden Eye Academy.',
            'course_confirmation_note' => 'Contact the academy for current batch timings and availability.',

            'home_hero_status' => 'active',
            'home_trust_status' => 'active',
            'home_audience_status' => 'active',
            'home_courses_status' => 'active',
            'home_categories_status' => 'active',
            'home_why_status' => 'active',
            'home_testimonials_status' => 'active',
            'home_faculty_status' => 'active',
            'home_reviews_status' => 'active',
            'home_parent_status' => 'active',
            'home_faq_status' => 'active',
            'home_final_status' => 'active',
            'home_meta_title' => 'Golden Eye Academy | Computer, Language & IELTS/PTE in Pokhara',
            'home_meta_description' => 'Learn practical computer, office and web-development skills in Pokhara, alongside language and IELTS/PTE preparation. Compare courses, fees and durations.',
            'home_trust_items' => "Established in Pokhara since 2008\nLanguage, test-preparation, computer and IT classes\nPractical learning for students and other learners\nCourse guidance for students and parents\nVisit us at Srijana Chowk, Pokhara",
            'home_audience_tagline' => 'What do you want to learn?',
            'home_audience_title' => 'Choose the path that fits your goal.',
            'home_courses_tagline' => 'Popular course choices',
            'home_courses_title' => 'Explore courses students ask about most',
            'home_courses_cta_text' => 'View Course Details',
            'home_courses_batch_note' => 'Ask about current batch timings',
            'home_categories_tagline' => 'Browse by learning area',
            'home_categories_title' => 'Find classes by subject and skill.',
            'home_why_tagline' => 'Why Golden Eye Academy',
            'home_why_title' => 'Know what you are joining.',
            'home_why_description' => 'See the course focus, fee, duration and instructor, then talk with our team about whether the class suits your goal.',
            'home_why_items' => "Established in Pokhara since 2008\nCourses organized by subject and learning goal\nGuided practice based on each course outline\nCourse guidance for students and parents",
            'home_testimonials_tagline' => 'Student experiences',
            'home_testimonials_title' => 'Hear from learners who studied at Golden Eye Academy',
            'home_faculty_tagline' => 'Golden Eye faculty',
            'home_faculty_title' => 'Meet the people behind current classes',
            'home_reviews_tagline' => 'Independent review information',
            'home_reviews_title' => 'Explore verified external feedback',
            'home_parent_tagline' => 'For parents',
            'home_parent_title' => 'Clear information for parents.',
            'home_parent_description' => 'Review what the course covers, its fee and duration, who teaches it and what students are expected to practise.',
            'home_parent_items' => "Understand what the course covers\nCheck the fee, duration and instructor\nAsk about current timings and attendance expectations\nDiscuss a suitable starting point together",
            'home_faq_tagline' => 'Before you choose',
            'home_faq_title' => 'Questions students and parents ask most',
            'home_final_tagline' => 'Choose with confidence',
            'home_final_title' => 'Need help choosing a course?',
            'home_final_description' => 'Tell us what you want to learn and your current level. We will explain the relevant courses and available timings.',
            'home_final_primary_cta_text' => 'Ask for Course Help',
            'home_final_secondary_cta_text' => 'Message on WhatsApp',

            'home_audience_students_status' => 'active',
            'home_audience_students_title' => 'I am a Student',
            'home_audience_students_problem' => 'Not sure which course matches your goal or current level?',
            'home_audience_students_benefit' => 'Compare beginner, language, test-preparation, computer and IT options.',
            'home_audience_students_cta_text' => 'View Course Details',
            'home_audience_parents_status' => 'active',
            'home_audience_parents_title' => 'I am a Parent',
            'home_audience_parents_problem' => 'Want clear information before your child enrolls?',
            'home_audience_parents_benefit' => 'Review the course focus, fee, duration, instructor and questions to ask.',
            'home_audience_parents_cta_text' => 'View Course Details',
            'home_audience_study_abroad_status' => 'active',
            'home_audience_study_abroad_title' => 'I need IELTS or PTE classes',
            'home_audience_study_abroad_problem' => 'Preparing for an English-language test or an international study goal?',
            'home_audience_study_abroad_benefit' => 'Explore IELTS, PTE and language classes for your preparation goal.',
            'home_audience_study_abroad_cta_text' => 'View Course Details',
            'home_audience_job_computer_skills_status' => 'active',
            'home_audience_job_computer_skills_title' => 'I want Computer or IT Skills',
            'home_audience_job_computer_skills_problem' => 'Want practical skills for study, office work or an IT pathway?',
            'home_audience_job_computer_skills_benefit' => 'Compare office, computer and web-development courses by practical skills.',
            'home_audience_job_computer_skills_cta_text' => 'View Course Details',
        ];

        foreach (self::audienceSettings() as $key => $value) {
            $settings[$key] = $value;
        }

        $normalized = [];

        foreach ($settings as $key => $value) {
            $normalized[$key] = [
                'value' => $value,
                'type' => str_ends_with($key, '_image') ? 'image' : 'text',
            ];
        }

        return $normalized;
    }

    public static function settingValue(string $key, string $fallback = ''): string
    {
        return self::siteSettings()[$key]['value'] ?? $fallback;
    }

    /**
     * Values diagnosed in the operational CMS before this rollout.
     * Missing keys are intentionally omitted and may be created.
     *
     * @return array<string, string>
     */
    public static function legacySiteSettings(): array
    {
        return [
            'logo_subtitle' => 'Pokhara - Est. 2008',
            'meta_title' => 'Golden Eye Academy | Established Academy in Pokhara Since 2008',
            'meta_description' => 'Established in 2008, Golden Eye Academy offers IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes in Pokhara, Nepal.',
            'aeo_summary' => 'Golden Eye Academy is an established academy in Pokhara, Nepal, serving learners since 2008 through IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes.',
            'hero_badge_text' => 'Golden Eye Academy Launchpad',
            'hero_title' => "Don't just study. Build your competitive edge.",
            'hero_subtitle' => 'Send your goal, get a practical roadmap, and choose the course that fits your timeline, budget, and current level.',
            'hero_cta_text' => 'Ask for Course Guidance',
            'hero_cta_1_text' => 'Ask for Course Guidance',
            'hero_cta_2_text' => 'View Course Details',
            'stat_1_val' => '15+',
            'stat_1_lab' => 'Years of Learning',
            'stat_2_val' => '5,000+',
            'stat_2_lab' => 'Learners Supported',
            'stat_3_val' => '4.9/5',
            'stat_3_lab' => 'Student Rating',
            'stat_4_val' => '2 hr',
            'stat_4_lab' => 'Typical Response',
            'pathway_tagline' => 'Choose by goal',
            'pathway_title' => 'Start with the path that sounds like you.',
            'courses_title' => 'Compare Programs by Goal',
            'courses_header_title' => 'Professional Courses',
            'courses_subtitle' => 'Shortlist courses by outcome, then talk to us before choosing your batch.',
            'courses_all_tagline' => 'Expert-Led Training',
            'courses_all_title' => 'Choose Your',
            'about_title' => 'Practical learning since 2008',
            'about_text' => 'Golden Eye Academy is an established academy in Pokhara focused on structured classes, practical learning, student support, and clear enrollment information.',
            'about_point_1' => 'Quick course roadmap help',
            'about_point_2' => 'Language, test prep, IT, and office skills',
            'about_point_3' => 'Support for students, parents, and job seekers',
            'about_point_4' => 'Practical classes with clear outcomes',
            'about_content_title' => 'Why Golden Eye Academy works',
            'about_content' => 'We combine course information, practical teaching, and follow-up so learners choose courses with a clear reason.',
            'about_section_tagline' => '15+ Years of Trust',
            'about_section_title' => 'Pokhara trusted us because guidance comes first',
            'about_header_title' => 'About Golden Eye Academy',
            'about_page_content' => '<h2>Our Approach</h2><p>Golden Eye Academy starts with course information because students should not choose courses randomly. We help learners compare goals, timeline, current level, and practical outcomes before enrollment.</p><p>After that, training is built around structured classes, practice, and clear next steps.</p>',
            'about_feat_1_title' => 'Guidance Before Enrollment',
            'about_feat_1_desc' => 'Learners understand the best route before committing to a course.',
            'about_feat_2_title' => 'Practical Learning',
            'about_feat_2_desc' => 'Courses focus on usable skills, mock tests, projects, and workplace confidence.',
            'about_feat_3_title' => 'Parent-Friendly Decisions',
            'about_feat_3_desc' => 'Families can ask about outcomes, fees, timing, and student readiness.',
            'about_feat_4_title' => 'Follow-Up Support',
            'about_feat_4_desc' => 'The team supports learners beyond the first inquiry.',
            'teachers_title' => 'Faculty and Academic Support Team',
            'teachers_subtitle' => 'Meet the people who guide your next step.',
            'testimonials_title' => 'Student Results and Feedback',
            'blog_title' => 'Academy Blog',
            'blog_header_title' => 'Academy Blog',
            'blog_subtitle' => 'Guides, updates, and decisions that help you move faster.',
            'blog_tagline' => 'Academy Insights',
            'blog_section_title' => 'Latest From Golden Eye',
            'blog_cta_title' => 'Need advice before choosing?',
            'blog_cta_desc' => 'Tell us your goal and our team will suggest the right path.',
            'blog_cta_btn' => 'Ask for Course Guidance',
            'faq_header_title' => 'Frequently Asked Questions',
            'faq_lead_title' => 'Still deciding? Ask first.',
            'faq_btn_text' => 'Ask for Course Guidance',
            'faq_page_content' => '<h2>Frequently Asked Questions</h2><p>Use these answers to understand course selection, class timing, and enrollment. If you are unsure, send a quick course-help request.</p>',
            'contact_header_title' => 'Contact Golden Eye Academy',
            'contact_page_content' => '<h3>Message our team</h3><p>Share your current situation and goal. We will help you compare the right course or next step before enrollment.</p>',
            'enroll_header_title' => 'Ask for Course Guidance',
            'enroll_section_title' => 'Tell us your goal. We will help map the next step.',
            'inquiry_tab_text' => 'Need Guidance?',
            'inquiry_title' => 'Get your course recommendation',
            'inquiry_subtitle' => 'Share your goal. We will recommend the right next step within 2 hours.',
            'sticky_cta_text' => 'Ask Now',
            'sticky_cta_desc' => 'Text us your goal.',
            'footer_about_text' => 'Golden Eye Academy helps students, parents, learners preparing for international study goals, and job seekers choose practical learning paths through course information.',
            'footer_faq_title' => 'Student Support',
            'footer_quick_link_title' => 'Academy Links',
            'footer_contact_title' => 'Find Us',
            'footer_social_title' => 'Follow Our Journey',
            'footer_newsletter_desc' => 'Get course updates, quick reminders, and practical career support.',
        ];
    }

    /** @return array<string, string> */
    public static function previousSiteSettingSignatures(): array
    {
        return [
            'hero_title' => 'a2c3e2fd0331ff5b6a513d2199a56d92eb71a9ac864b1f1c9cf51941534b5f5c',
            'hero_subtitle' => '36b55a704b234c880f8c7eb95f55192083ababea69c0c2b35a7c13b7c3323a63',
            'courses_subtitle' => '3e877df29bbe6ff2b023fe89624555b6bd7734c5dc631e9a97d5ded116277b09',
            'courses_all_tagline' => '877f9e07be0a479494d69ad768e85c87973f9a4a4fc0d28e729cee08305ee0d8',
            'home_courses_batch_note' => '92a02a328cee218beace42e23130e42bf85163b3498641437b141913be4b4473',
            'catalogue_description' => '28fe0f77068ebb51a4d4cb955a9c73f5aab070ff7b0b98f8070618cf47d8031b',
            'footer_newsletter_desc' => '36cb391cd87ca76499f423d75d0bf508aae09106824ab6fdae220e6ec1f86d9d',
            'course_confirmation_note' => '42ee8f5b61a9fc0d0f0d475bd91dc559225fdb33fe9230a605e205051c74b5af',
            'home_trust_items' => '9ffb26571134e2926f51b6c848baad513a1810cc9de9eefca85434efb368ea02',
            'home_audience_tagline' => 'b05e1d49848382170c9af82456da36161061423f7840eb57cf8a6e2bd7ca4723',
            'home_audience_title' => '048ccbf3a617b0fc227c8b31f6288c3bd1b97933366052f6ba09098c97a1a112',
            'home_why_title' => '8444e86cad8e1e9df2a00669b3dc6431dc423820f5c69d948617194018652764',
            'home_why_description' => '48fe34c266c16f2405256f909596a35a520fa6d724eb9eb2c28cebcf6b9ca036',
            'home_why_items' => '52b7f3557afae3a22bbec1d78adc43b403f68136929475c8b0282e1c6c8af0a3',
            'home_parent_title' => '74ddd1d999b21113256154c349900eb3c5ca1b60164f33610ea015c12f3befe7',
            'home_parent_description' => '1c3529152ff3780e5f1c14be612d3f29efa6007a6ca840b4a9a09554426073e9',
            'home_parent_items' => '8a2f17b53f38130117600ae33e358a57b34a2beea211c7b111c5a615e07d3d1c',
            'home_final_title' => '0272817b8a19dae9944fdf4307c156f48ea4fdf16f3a5bd87583e0c955301bf7',
            'home_final_description' => '1f660ddf84691a55bff0c3595d77504270d8b14af8ba5da501946ba8a0bed589',
            'home_audience_students_problem' => 'ca75ca02da76b89d79ffca5d8e175fda9e340571ed463f08631f5e6a2a9c3ec0',
            'home_audience_students_benefit' => '6b29df0bf7bbceb8a45852845149303cd593942df4f3a04098ab017c23bdd572',
            'home_audience_parents_problem' => '9df6e046604bf271c1f842d586eb8d03c9f0803d30e2beedce6b5400d865760f',
            'home_audience_parents_benefit' => '7ff2d9209898f077f5ba07ff326c80f5bb8d321b03a5efc7c9585c5c3e1e344b',
            'home_audience_study_abroad_benefit' => '36d8bb0bd857c8abfbfaef83b29f42b36af28a6d4a028a3b3480e7d946adfca9',
            'home_audience_job_computer_skills_benefit' => '42c5227374fd66fdd0afacf0ea8dc66b2f57f6bd3f63598f47344defd4163147',
            'audience_students_headline' => '6b9bbabbea868a187fd8c6abd2103997744fcc47e409bd2c8b62f8c54df68476',
            'audience_students_subheadline' => '85e3bb255f7c0bd1f08a4d13956a1e579cfe5d7205d657395a26fa9801a0365c',
            'audience_students_problem_tagline' => '1244392a75b989796cc74ce085bd85b60153ae6609766be291cb89f5d29e568a',
            'audience_students_problem_title' => 'abd5f169e27627e2674095a102de09f58ac7fdd7711ef67345dd534d6510458c',
            'audience_students_problem' => '974877aa510876747c1c361b333efc361742685afa49b8c980d1214ab4a25a9c',
            'audience_students_paths_tagline' => '298cd6a0007f2d71326c94a0c362d370fa0a87654f39564f324f82d7895e7cfc',
            'audience_students_paths_title' => '7c7e5cdc1c2ca1ff499ec4c232c6519d5334c5d0258698539f19ec821c360c2f',
            'audience_students_paths' => '9b2b108beb0fabea8bdf72b4edb1b9022e99679c1ae5cf71fe6eaf4c7ec795b6',
            'audience_students_support_tagline' => 'e75c9e49b8d4626ceb1c63ef5ae6ecfe40828112d69b99f26bc65b51b33cf1bb',
            'audience_students_support_title' => '660cc503e24d0b4a29b1c4089578c96ed4f4e723b9eaf9f88e5169206c4c6b99',
            'audience_students_why' => '2b845bbb38d24ba19021ec26514fc84d3bf1a5547bf0724ca4c3295ff0d10cd6',
            'audience_students_final_headline' => '4be0e0247a5044489c82663c25bd5d5d610124ea2a77c9c61930bf7549f20316',
            'audience_parents_headline' => '74ddd1d999b21113256154c349900eb3c5ca1b60164f33610ea015c12f3befe7',
            'audience_parents_subheadline' => '1c3529152ff3780e5f1c14be612d3f29efa6007a6ca840b4a9a09554426073e9',
            'audience_parents_problem_tagline' => '8aaff8e1529c64a53a6c124060bed6f91fd754b452d3fb3e99d2a591a3c47437',
            'audience_parents_problem_title' => 'b60dc9b97dd3e6f5b588621fb112b354d49f46621edc010bfdc576449b4493a9',
            'audience_parents_problem' => '181d51ce978d95adca98bcad0b2fae0e8ecc38e392b27205fb0ea60031c64d89',
            'audience_parents_paths_tagline' => '20fde0d0f2133f0c90d43374ca3616305e6269ad92c135a1f5f79c4a9e6b8027',
            'audience_parents_paths_title' => '0674527e56204eec15de5c519a4d4b281fae636847c0182c67aad9ff3e1c9ae7',
            'audience_parents_paths' => '29a60ca5a926fb94997de62712e64dd28e6d78e79e9f6f128799f8a51c36edfc',
            'audience_parents_support_tagline' => '529148daa330aa2c59566591e9a045e43bd45e7ee2fb3a8c582e67250d5f8268',
            'audience_parents_support_title' => '8334aa6a55bb36532078f5f8dd081988789eb4c8798728fa1bc350d9e6623ee9',
            'audience_parents_why' => 'c21bbe731e24bb2403378cc9265f4d8419757e69cdf5aad593e94791dede1d3e',
            'audience_parents_final_headline' => '750661b22a9c766c1268df2f97088b164fb48a6cb0189a924b75f88bf3541053',
            'audience_study_abroad_meta_description' => 'a857f386ecfebd8595e20c8e467a7b1e45775384388a4bfc64c70095b2f303f5',
            'audience_study_abroad_headline' => 'a9b9050ddf4f12811d8cd27309422ba794592a5c32e8807762c8fed54c510106',
            'audience_study_abroad_subheadline' => '1f9c9fc15ec86d911c58d4fd7cc985a525660f9b5c47230e93d4545983355234',
            'audience_study_abroad_problem_tagline' => '5fe41aef555e6a117a00c9be0d9c0df70eff91f30ba3c56916a57d02e179afbc',
            'audience_study_abroad_problem_title' => '2835fb37ddc0dfbd110b98202ad0d5b9d07347506351d858c60037ab31c76f18',
            'audience_study_abroad_problem' => 'c027590291145bb6ff5307295cffaff3fe9db1074d171523120c1bf6e3209e78',
            'audience_study_abroad_paths_tagline' => 'c54d73191459676780772dcad9fb0e118c2579ceea3864a62004884474896080',
            'audience_study_abroad_paths_title' => '2f4f687d4ffbf957d1bf1649b8b50297711bd29970e6b29664695d0851e79736',
            'audience_study_abroad_paths' => '27eb512161eae90d3626f00fd67062cb858b56e805cd00b454772843131eed69',
            'audience_study_abroad_support_tagline' => 'f85913a5ce3d5f6e201d35dfe3a47f2190292f9926d1d443c2753567129376bb',
            'audience_study_abroad_support_title' => '4ba339bd56610b815d6f88c29e952903f9c152ac37d05e649f03e6283d231dbf',
            'audience_study_abroad_why' => 'b0d20a912d8e97ca7d5880fdc62ad776aaec619072b650bd0503c2e07aa90560',
            'audience_study_abroad_proof_tagline' => 'b05e1d49848382170c9af82456da36161061423f7840eb57cf8a6e2bd7ca4723',
            'audience_study_abroad_proof' => '74e781cb68be35053060c27976d4392178a448b7d9073e516ca33b24fb9ad681',
            'audience_study_abroad_final_headline' => '297f29f4567d0e84a83995f732dede7268c369417d1ede96a6f6642220b2a67a',
            'audience_job_computer_skills_subheadline' => '60aab2daecc460cbea8da15d01714e758ae0bcf28e696c4d5c1cd1929ddebbf9',
            'audience_job_computer_skills_problem' => 'c2073bc223475e3060751c1894c8d5247128b5f042d013fe5720039ed5a15ae5',
            'audience_job_computer_skills_paths_title' => '905773db42bd2749e632bc06888af736aeae8fb42272bfa8aba281cc0dfe0b44',
            'audience_job_computer_skills_paths' => '0c393c7168a3196626317c811966d07a130bba2ea84e28039fd86ca42371724d',
            'audience_job_computer_skills_support_tagline' => '136a81d8c5c79bfa180e95c68a78ef2f8f81aa7c3a45375aea1f0de46e066d54',
            'audience_job_computer_skills_support_title' => 'e21a467d365fc4fb95e118fc1abef9043091ba4b76253dfd9595ee55de4a1d71',
            'audience_job_computer_skills_why' => 'bce6cac483a45ca09b168fe73f85ff8eefefb782cd47d3c7d1ac8232cb3b4c42',
            'audience_job_computer_skills_final_headline' => 'aa80f830f4c9eda45d29c2bf5b0383816f8406c07ef888e34f3f43c808debde4',
        ];
    }

    /**
     * @return array<int, array{id: int, question: string, answer: string, order_priority: int}>
     */
    public static function faqs(): array
    {
        return [
            ['id' => 1, 'question' => 'What courses does Golden Eye Academy offer?', 'answer' => 'Golden Eye Academy offers active courses across computer and IT skills, language learning, English and test preparation, and academic support. Browse the current course list to see the subjects available now.', 'order_priority' => 10],
            ['id' => 2, 'question' => 'Can I ask for help before choosing a course?', 'answer' => 'Yes. Tell us what you want to learn, your current level and the time you can study. Course Help is designed to make relevant class options easier to compare.', 'order_priority' => 20],
            ['id' => 3, 'question' => 'How should I choose a course after SEE or Plus Two?', 'answer' => 'Begin with the skill, subject or exam goal that matters most to you now. Compare the course focus, duration and starting point, then discuss the decision with your family or the academy team.', 'order_priority' => 30],
            ['id' => 4, 'question' => 'Can parents discuss course options with the academy?', 'answer' => 'Yes. Parents can ask about the learning focus, duration, current fee, batch timing, faculty information and the level of commitment expected from the student.', 'order_priority' => 40],
            ['id' => 5, 'question' => 'Can beginners join Golden Eye Academy courses?', 'answer' => 'Several listed courses begin with foundation-level learning. Read the course description and share your current experience so the academy team can help you identify a suitable starting point.', 'order_priority' => 50],
            ['id' => 6, 'question' => 'What practical activities are included in classes?', 'answer' => 'Practical work depends on the subject. Classes may use guided exercises, speaking or mock activities, completed files, and practical projects in relevant computer and web-development courses. Each course page shows its main learning areas.', 'order_priority' => 60],
            ['id' => 7, 'question' => 'How do I find the current batch timing?', 'answer' => 'Send the course name and your preferred study time through Course Help, telephone or WhatsApp. The academy team will share the current batch options for that course.', 'order_priority' => 70],
            ['id' => 8, 'question' => 'What information should I provide during an inquiry?', 'answer' => 'Share the subject or course you are considering, your current level, your main goal and the time you prefer to study. This helps the academy team give you a more useful reply.', 'order_priority' => 80],
            ['id' => 9, 'question' => 'How do I choose a suitable batch?', 'answer' => 'Choose a batch that matches your current level, timetable and ability to practise consistently. Ask about the learning pace and faculty for the current batch before making your decision.', 'order_priority' => 90],
            ['id' => 10, 'question' => 'Do Golden Eye Academy courses include certificates?', 'answer' => 'Golden Eye Academy provides a certificate after completion of each course. Course-specific completion requirements may be confirmed with the academy team.', 'order_priority' => 100],
            ['id' => 11, 'question' => 'What teaching approach does Golden Eye Academy use?', 'answer' => 'Golden Eye Academy connects clear instruction with practical learning and regular participation. The exact classroom approach depends on the subject and the learning areas listed for that course.', 'order_priority' => 110],
            ['id' => 12, 'question' => 'Who will teach my class?', 'answer' => 'Course pages show the faculty member or teaching team currently associated with each course. Ask the academy to identify the faculty for the batch you plan to join.', 'order_priority' => 120],
            ['id' => 13, 'question' => 'How is student progress reviewed?', 'answer' => 'Progress review varies by course and may include class participation, guided practice, feedback or subject-specific activities. Ask how progress is reviewed in the course you are considering.', 'order_priority' => 130],
            ['id' => 14, 'question' => 'Are the courses suitable for school or college students?', 'answer' => 'Suitability depends on the student’s current level, timetable and goal. Students and parents can compare the published course information and discuss the most appropriate starting point with the academy.', 'order_priority' => 140],
            ['id' => 15, 'question' => 'How much do courses cost and how long do they take?', 'answer' => 'Each active course page shows its listed fee and duration. Ask the academy team for the current payment and batch information for the course you want to join.', 'order_priority' => 150],
            ['id' => 16, 'question' => 'What happens after I submit Course Help?', 'answer' => 'The academy receives your contact details, learning goal and course interest. A team member can then respond with relevant course or current-batch information.', 'order_priority' => 160],
            ['id' => 17, 'question' => 'Can I visit Golden Eye Academy before enrolling?', 'answer' => 'Yes. Contact the academy before travelling so you can plan a suitable visit and use the time to discuss courses, current batches, fees and academic support.', 'order_priority' => 170],
            ['id' => 18, 'question' => 'Where should I ask about study-abroad preparation?', 'answer' => 'Golden Eye Academy provides relevant language and test-preparation classes. Visit the study-abroad guidance page for the academy’s class information and separate contextual guidance.', 'order_priority' => 180],
            ['id' => 19, 'question' => 'What results can I expect from a course?', 'answer' => 'A course can provide instruction, practice, feedback and academic support. Individual progress depends on the learner’s starting point, attendance, practice and continued effort.', 'order_priority' => 190],
            ['id' => 20, 'question' => 'Where is Golden Eye Academy located?', 'answer' => 'Golden Eye Academy is at Srijana Chowk, Pokhara. Use the contact page, telephone or WhatsApp if you would like help planning your visit.', 'order_priority' => 200],
        ];
    }

    /**
     * @return array<int, array{id: int, question: string, answer: string, order_priority: int}>
     */
    public static function legacyFaqs(): array
    {
        return [
            ['id' => 1, 'question' => 'What courses does Golden Eye Academy offer?', 'answer' => 'Golden Eye Academy offers IELTS, PTE, Japanese, Korean, English, computer office skills, web development, and free course-roadmap help.', 'order_priority' => 10],
            ['id' => 2, 'question' => 'Can I ask for help before choosing a course?', 'answer' => 'Yes. Students, parents, job seekers, and students preparing for international study goals can ask for quick course help before choosing a course.', 'order_priority' => 20],
            ['id' => 3, 'question' => 'What should I choose after SEE or Plus Two?', 'answer' => 'The right path depends on your goal, timeline, budget, interest, and current level. Our team compares study abroad, language, IT, and job-skill options with you.', 'order_priority' => 30],
            ['id' => 4, 'question' => 'Do you help parents understand course options?', 'answer' => 'Yes. Parents can talk with the team about course outcomes, class timing, fees, student readiness, and next-step planning.', 'order_priority' => 40],
            ['id' => 5, 'question' => 'Are IELTS and PTE both available?', 'answer' => 'Yes. Golden Eye Academy offers both IELTS and PTE preparation. The team helps students choose based on exam requirement, timeline, and test preference.', 'order_priority' => 50],
            ['id' => 6, 'question' => 'Do you provide Japanese and Korean classes?', 'answer' => 'Yes. We provide Japanese JLPT preparation and Korean language or EPS-TOPIK preparation for learners planning study, work, or language and exam preparation goals.', 'order_priority' => 60],
            ['id' => 7, 'question' => 'Can beginners join computer courses?', 'answer' => 'Yes. Beginners can join foundation computer, office package, and digital productivity courses. No advanced technical background is required.', 'order_priority' => 70],
            ['id' => 8, 'question' => 'Do I need coding experience for web development?', 'answer' => 'No. The web development course starts with foundations and moves toward practical projects, Laravel, databases, and deployment concepts.', 'order_priority' => 80],
            ['id' => 9, 'question' => 'Are classes practical or only theoretical?', 'answer' => 'Courses are designed around practical learning, guided exercises, mock tests, assignments, and project-based outputs where relevant.', 'order_priority' => 90],
            ['id' => 10, 'question' => 'Do you provide certificates?', 'answer' => 'Yes. Students who complete course requirements receive certificates that can support job applications, academic profiles, and skill proof.', 'order_priority' => 100],
            ['id' => 11, 'question' => 'Are flexible class timings available?', 'answer' => 'Yes. Morning, day, and evening timing options may be available depending on the program and batch schedule.', 'order_priority' => 110],
            ['id' => 12, 'question' => 'Can working professionals join?', 'answer' => 'Yes. Working professionals can choose short courses, office skills, English communication, or flexible language/test-prep batches.', 'order_priority' => 120],
            ['id' => 13, 'question' => 'How much do courses cost?', 'answer' => 'Fees vary by program, duration, and batch. The team can explain the current fee, timing, and available options before enrollment.', 'order_priority' => 130],
            ['id' => 14, 'question' => 'Is there an online learning option?', 'answer' => 'Some programs may support online or hybrid guidance depending on the course structure. Contact the team for the current batch format.', 'order_priority' => 140],
            ['id' => 15, 'question' => 'How do I enroll?', 'answer' => 'You can submit the course information form, contact the academy, or use the WhatsApp chat CTA. The team will confirm your goal and guide the next step.', 'order_priority' => 150],
            ['id' => 16, 'question' => 'What happens after I submit the form?', 'answer' => 'The team receives your details, reviews the selected course or course-help request, and contacts you for confirmation and guidance.', 'order_priority' => 160],
            ['id' => 17, 'question' => 'Can I ask for a course recommendation?', 'answer' => 'Yes. Select the option for help choosing the right program, and the team will contact you with a roadmap recommendation.', 'order_priority' => 170],
            ['id' => 18, 'question' => 'Do you run events and workshops?', 'answer' => 'Yes. Golden Eye Academy can run workshops, skill sessions, course-help events, and career-focused activities based on schedule and demand.', 'order_priority' => 180],
            ['id' => 19, 'question' => 'Can I switch course after getting guidance?', 'answer' => 'Course changes depend on batch status and availability. The team will help you avoid wrong enrollment before payment whenever possible.', 'order_priority' => 190],
            ['id' => 20, 'question' => 'Where is Golden Eye Academy located?', 'answer' => 'Golden Eye Academy is based around Srijana Chowk, Pokhara. Contact the team for exact visit timing and location support.', 'order_priority' => 200],
        ];
    }

    /** @return array<int, string> */
    public static function previousFaqSignatures(): array
    {
        return [
            6 => 'ab12dccb68a7b5bf31828fcbe582890ab49b962cd149da60b78afca2bcdce191',
            10 => 'aa50a36c45158af32243c2c96f1356ab4419a7323df62a94b307f4512185f82b',
            18 => 'd7d431f91415d06a69320a57db7710fc16262beeea3e82df71cbd9312c4761d2',
        ];
    }

    /**
     * Fresh-site product copy overrides. Product identities and commercial facts
     * are deliberately included only so the checkpoint matches the protected
     * operational catalogue.
     *
     * @return array<string, array{name: string, badge_text: string, description: string, meta_title: string, meta_description: string}>
     */
    public static function courseOverrides(): array
    {
        return [
            'ielts-masterclass' => [
                'name' => 'IELTS Masterclass for Band 7+',
                'badge_text' => 'IELTS Preparation',
                'description' => 'Prepare for all four IELTS skills: listening, reading, writing and speaking. This course suits learners who want a structured study routine with mock practice, writing correction, speaking practice and instructor feedback. Your progress will depend on your starting level, attendance and practice; the academy does not guarantee a particular score.',
                'meta_title' => 'IELTS Preparation in Pokhara | Golden Eye Academy',
                'meta_description' => 'Prepare listening, reading, writing and speaking through structured IELTS practice in Pokhara. Rs. 7,000 for 6 weeks with Saroj Giri.',
            ],
            'pte-elite-training' => [
                'name' => 'PTE Elite Academic Training',
                'badge_text' => 'PTE Preparation',
                'description' => 'Prepare for the computer-based PTE Academic test through practice with the test interface, timing, speaking fluency, writing, reading and listening. Computer-lab activities help learners become familiar with the test format. Progress depends on your starting level and regular practice; no score is guaranteed.',
                'meta_title' => 'PTE Academic Preparation in Pokhara | Golden Eye Academy',
                'meta_description' => 'Practise the computer-based PTE format, speaking, writing, reading, listening and timing in Pokhara. Rs. 7,000 for 6 weeks with Saroj Giri.',
            ],
            'jlpt-n5-elite' => [
                'name' => 'Japanese Proficiency JLPT N5',
                'badge_text' => 'JLPT N5',
                'description' => 'Start Japanese with Hiragana, Katakana, basic Kanji, vocabulary and grammar. Learners also practise listening and simple conversation while building a foundation for JLPT N5. This course is suitable for beginners who are ready to study and review between classes.',
                'meta_title' => 'Beginner Japanese and JLPT N5 in Pokhara | Golden Eye Academy',
                'meta_description' => 'Start Japanese with Hiragana, Katakana, basic Kanji, vocabulary, grammar, listening and conversation. Rs. 15,000 for 6 months with Navaraj Thapa.',
            ],
            'jlpt-n4' => [
                'name' => 'Japanese Proficiency JLPT N4',
                'badge_text' => 'JLPT N4',
                'description' => 'Continue Japanese study with N4-level vocabulary, Kanji, grammar, reading, listening and conversation practice. Learners should complete JLPT N5 first; if you studied equivalent material elsewhere, ask the academy to confirm the right starting level.',
                'meta_title' => 'JLPT N4 Japanese Course in Pokhara | Golden Eye Academy',
                'meta_description' => 'Continue from JLPT N5 into N4 vocabulary, Kanji, grammar, reading, listening and conversation. Rs. 13,000 for 3 months with Navaraj Thapa.',
            ],
            'professional-korean-eps' => [
                'name' => 'Professional Korean EPS-TOPIK',
                'badge_text' => 'EPS-TOPIK',
                'description' => 'Build Korean skills for EPS-TOPIK through Hangul, vocabulary, listening, reading, mock activities and structured review. This exam-focused course suits learners who can commit to regular class attendance and practice. Golden Eye Academy provides preparation, not a guaranteed exam, job or placement outcome.',
                'meta_title' => 'EPS-TOPIK Korean Preparation in Pokhara | Golden Eye Academy',
                'meta_description' => 'Prepare Korean for EPS-TOPIK through Hangul, vocabulary, reading, listening and mock activities. Rs. 18,000 for 6 months with Pradeep Paudel.',
            ],
            'basic-korean-course' => [
                'name' => 'Basic Korean Course',
                'badge_text' => 'Korean Foundation',
                'description' => 'Learn to read and write Hangul, build everyday vocabulary, form simple sentences and practise basic listening and conversation. This course is a suitable starting point for beginners and provides a foundation for continued Korean study.',
                'meta_title' => 'Basic Korean Course in Pokhara | Golden Eye Academy',
                'meta_description' => 'Learn Hangul, everyday vocabulary, simple sentences, listening and conversation from beginner level. Rs. 10,000 for 3 months with Pradeep Paudel.',
            ],
            'global-english-pro' => [
                'name' => 'Global English Professional Track',
                'badge_text' => 'English Communication',
                'description' => 'Develop clearer English for study, work and everyday communication. Learners practise grammar, speaking, professional writing, presentations and interviews. The course may suit people who already know some English and want more confident, practical use.',
                'meta_title' => 'Practical English for Study & Work | Golden Eye Academy',
                'meta_description' => 'Build practical English for study, work and daily communication through grammar, speaking, writing, presentations and interviews. Rs. 7,000 for 45 days.',
            ],
            'basic-english-course' => [
                'name' => 'Basic English Foundation',
                'badge_text' => 'English Foundation',
                'description' => 'Build a foundation in English grammar, vocabulary, reading, writing, listening and everyday speaking. This course suits beginners and learners who want to strengthen the basics before moving to more advanced communication or test preparation.',
                'meta_title' => 'Basic English Course in Pokhara | Golden Eye Academy',
                'meta_description' => 'Build beginner English through grammar, vocabulary, reading, writing, listening and everyday speaking. Rs. 5,000 for 45 days with Saroj Giri.',
            ],
            'professional-web-development' => [
                'name' => 'Professional Web Development',
                'badge_text' => 'Web Development',
                'description' => 'Learn how websites are built through practical project work covering HTML, CSS, responsive interfaces, Laravel, databases, API integration and deployment concepts. Students create and explain their own technical work and must bring a laptop for the course. The academy does not guarantee a job or placement.',
                'meta_title' => 'Web Development with Laravel in Pokhara | Golden Eye Academy',
                'meta_description' => 'Build practical websites using HTML, CSS, responsive design, Laravel, databases, APIs and deployment concepts. Rs. 24,850 for 3 months with Asal Pandey.',
            ],
            'advanced-computer-diploma' => [
                'name' => 'Advanced Diploma in Computer Science',
                'badge_text' => 'Computer Skills',
                'description' => 'Build confidence with common computer tasks through guided practice in documents, spreadsheets, presentations, databases, email and everyday digital workflows. The course suits learners who want broader computer skills for study, office work or personal use.',
                'meta_title' => 'Advanced Computer Diploma in Pokhara | Golden Eye Academy',
                'meta_description' => 'Practise documents, spreadsheets, presentations, databases, email and digital workplace tasks. Rs. 14,000 for 3 months with Sanju Khanal.',
            ],
            'corporate-office-package' => [
                'name' => 'Corporate Office and Admin Package',
                'badge_text' => 'Office Skills',
                'description' => 'Practise common office tasks using Word documents, Excel data handling, presentations, email, cloud collaboration and file management. This course suits learners who want more confidence with administrative and workplace computer tasks.',
                'meta_title' => 'Office Skills Course in Pokhara | Golden Eye Academy',
                'meta_description' => 'Practise Word documents, Excel, presentations, email, cloud collaboration and file management. Rs. 7,000 for 3 months with Sanju Khanal.',
            ],
            'chinese-language-course' => [
                'name' => 'Chinese Language Starter',
                'badge_text' => 'Chinese Starter',
                'description' => 'Begin Chinese with pronunciation, basic vocabulary, listening, reading, speaking and cultural etiquette. The course suits beginners who want a structured introduction and are ready to practise regularly between classes.',
                'meta_title' => 'Beginner Chinese Course in Pokhara | Golden Eye Academy',
                'meta_description' => 'Begin Chinese through pronunciation, vocabulary, listening, reading, speaking and cultural etiquette. Rs. 15,000 for 1 month with Chham Maya Rai.',
            ],
            'free-course-roadmap-help' => [
                'name' => 'Free Course Roadmap Help',
                'badge_text' => 'Course Help',
                'description' => 'A free 30-minute conversation for students or parents who are unsure which Golden Eye Academy course to choose. Share the learner’s current level, goal, preferred timing and subjects of interest. The team will explain relevant courses and current availability without promising a particular result.',
                'meta_title' => 'Free Course Guidance in Pokhara | Golden Eye Academy',
                'meta_description' => 'Get a free 30-minute conversation with the Academic Support Team to compare Golden Eye courses by your goal, level, timing and subject interests.',
            ],
        ];
    }

    /**
     * SHA-256 signatures of the diagnosed badge and description fields.
     *
     * @return array<string, string>
     */
    public static function legacyCourseSignatures(): array
    {
        return [
            'ielts-masterclass' => 'a4703f97f1243e60d6489110d85b2ef04ab7f063ede7bb76b6e8c3fa524d11d8',
            'pte-elite-training' => 'c4d581d1adb303f494348e7b2c8102e99247c1913a9dd77bab80850abdaf5ca8',
            'jlpt-n5-elite' => 'a5d71d90f492003f5695a6819ef08c6cf200903de9a7675f1feee71faaafbb53',
            'jlpt-n4' => '78baa4387a9c0027c9d6434c49c64ef1fda1121e6b146c2b980179e213ad9a80',
            'professional-korean-eps' => '7fc69e4f3259b6d7d89d84ae71ca418f6f9e422e295fa0f5e86a190967f0b283',
            'basic-korean-course' => 'ee5ea21a48c3eeaa934e2eeb305872e9a0ae8596ee3ebae3d4e1a955442dfaa7',
            'global-english-pro' => 'fca4f1a595580c96e43b71a038ef864959de5547650cfec5ea4561bb18035544',
            'basic-english-course' => 'a782ab682744f5bf9deeb800432859a93fc3202ecf63c3ef87f2002dd50ae86f',
            'professional-web-development' => '984aaa947144305b572b36a776161dbc2634005997ca713b0f38e40e6ba4e527',
            'advanced-computer-diploma' => 'b6075eb2eff2b5b02ade1467f263be0aa33e9d7ccad22b5701f255829695febb',
            'corporate-office-package' => '508d1f9c6666098a461d71fa96ad629bcf893f11a96313f75620a96eef386f8a',
            'chinese-language-course' => '53de971b5fbbd5eea5cb8618f267687bb192f4aaab1c00b9fce79ef6de0f824e',
            'free-course-roadmap-help' => '0a846086ca0eb7c0a365369ca9a8b5b81a9007b5589df58c6bf2c13a13c4032c',
        ];
    }

    /** @return array<string, string> */
    public static function previousCourseSignatures(): array
    {
        return [
            'ielts-masterclass' => '2cc70a19eaa2824414b13e6e6bcb2c5033eca428fe0d69de4858e805f4f185f9',
            'pte-elite-training' => 'c180b33a054f2928d9db2de3df71c1711522f4cb1fb5676f23ef388547998d06',
            'jlpt-n5-elite' => '5b549cc3217f8ff4c1b21ea0ddd162fcff9de16ea528c57e83e1b1568a804827',
            'jlpt-n4' => '02ebcae15042be29265fd1f632a508be4dc711af0a39ce4054dccacfc19ce62f',
            'professional-korean-eps' => 'b1c41265d6af49a8f8b1a06f90007b0f3363b7009c10bd521598b77e25974760',
            'basic-korean-course' => 'e930cfabd60a6e68b9e99adc15ea725bc9e0769a953a9f3c98d978d60a1b6fbf',
            'global-english-pro' => '3d3fff963ef0b57fdc702340a53a87da0955d076e92fc362f02acd3e1ca0ed9d',
            'basic-english-course' => 'b15c9e743441fd5b423c1fe283f1a248eeba628f47809b08bdfe45aee9d5835d',
            'professional-web-development' => 'b859261712ac6129d96f28953c313a5784e04a2d9f010f3b0904313365acdbcc',
            'advanced-computer-diploma' => '477c40f84c454d077f1fc313b8556548980ad7e572a1274f9c6052caec97ed5b',
            'corporate-office-package' => '035e316dcadc751f7d19541da8e637ceba3f6d4fa26c5c3169fca6f8ea4aea2a',
            'chinese-language-course' => '3a0f5854a37306d3db4058ed79915379885a644f1fce5538ea557060d7a0b46e',
            'free-course-roadmap-help' => '8e4c7784d9fa67970dc764bbb109628fcd4827b3802aac25f1fa9da1f205873f',
        ];
    }

    /**
     * @return array<string, array{title: string, summary: string, bullets: array<int, string>}>
     */
    public static function servicePillarUpdates(): array
    {
        return [
            'learn-your-way-digital-first-flexibility' => [
                'title' => 'Course and Batch Guidance',
                'summary' => 'Start with your goal, compare relevant classes and understand the current batch options before choosing.',
                'bullets' => ['Share your current level and learning goal.', 'Compare active courses by subject, duration and learning focus.', 'Use Course Help to discuss the current batch that fits your timetable.'],
            ],
            'the-network-your-unfair-advantage' => [
                'title' => 'Practical Learning for Study and Work',
                'summary' => 'Build useful language, computer and technical skills through the learning areas published for each course.',
                'bullets' => ['Connect the course to a clear study or work goal.', 'Practise the subject areas described in the course outline.', 'Continue building confidence through regular participation and review.'],
            ],
            'career-and-college-blueprint-zero-guesswork' => [
                'title' => 'Academic Support for Students',
                'summary' => 'Course guidance and academic support help students and families make a clearer learning decision.',
                'bullets' => ['Discuss the student’s current level and immediate goal.', 'Compare the learning focus, duration and current fee.', 'Use a parent conversation or academy visit to clarify the next step.'],
            ],
            'real-world-skills-become-un-ignorable' => [
                'title' => 'Computer, Office and IT Skills',
                'summary' => 'Explore practical computer, office and web-development learning for study, personal projects and workplace tasks.',
                'bullets' => ['Build everyday computer and office-software confidence.', 'Use practical projects in relevant web-development learning.', 'Explore Laravel, APIs and deployment concepts in the web-development course.'],
            ],
            'global-launchpad-languages-and-test-prep' => [
                'title' => 'Language and Test Preparation',
                'summary' => 'Explore active English, test-preparation and language courses at Golden Eye Academy.',
                'bullets' => ['Compare current IELTS and PTE preparation options.', 'Explore active Japanese, Korean, English and other language classes.', 'Choose a class around your present level and learning goal.'],
            ],
            'academic-powerhouse-grades-8-to-masters' => [
                'title' => 'Academic Support and Tuition',
                'summary' => 'Academic support helps learners strengthen subject understanding and build a more consistent study routine.',
                'bullets' => ['Start with the subject or learning difficulty that needs attention.', 'Discuss the student’s current level and timetable.', 'Ask about the academic-support options currently available.'],
            ],
            'events-learn-meet-move' => [
                'title' => 'Workshops and Short Programs',
                'summary' => 'Golden Eye Academy uses notices and campaigns to share current workshops, short programs and academy events.',
                'bullets' => ['Check the current announcement or notice for active events.', 'Review the topic, audience and timing before registering.', 'Contact the academy when you need event or workshop information.'],
            ],
        ];
    }

    /**
     * SHA-256 signatures of the diagnosed title, summary and bullets fields.
     *
     * @return array<string, string>
     */
    public static function legacyServicePillarSignatures(): array
    {
        return [
            'learn-your-way-digital-first-flexibility' => '5accbeb15c73f7d92c9da22ea026614181e6f97c255bc6bf383022990de0adc5',
            'the-network-your-unfair-advantage' => '258b207d8a3c9872f0bc16c70cb8e702a7d66ff3f3b432cd79c28f754d17a604',
            'career-and-college-blueprint-zero-guesswork' => '76df645c2a7a6cedcd8b3f4c7ccf473a544a67e3d69e4505f35dd9b629e62f41',
            'real-world-skills-become-un-ignorable' => '9cd63c481c091882137aa018558f163aaaf135cbd81598b35e67159b8e275ebd',
            'global-launchpad-languages-and-test-prep' => '5b1643d867f0b9f65381651cafc46bc3a6ada8af723cbb1b7eff4b05f860ce9d',
            'academic-powerhouse-grades-8-to-masters' => 'd2878061947914ce20591ef8489ede3010dadd237e7697c38263021169c54f15',
            'events-learn-meet-move' => 'fa27d51c300655e7701113c5c14a06d19c597db56d5e7dea6e80304d18f3fea4',
        ];
    }

    /** @return array<string, string> */
    public static function previousServicePillarSignatures(): array
    {
        return [
            'real-world-skills-become-un-ignorable' => 'fd73a89a0140f9568830908c39652a7cd856cddbb4736401ff618388949abbd0',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function audienceSettings(): array
    {
        $pages = [
            'students' => [
                'page_title' => 'Courses for Students in Pokhara | Golden Eye Academy',
                'meta_description' => 'Compare practical computer, language and IELTS/PTE courses in Pokhara by learning focus, fee and duration, then ask which starting point fits your goal.',
                'badge' => 'For students',
                'headline' => 'Find a course that fits your starting point.',
                'subheadline' => 'Whether you are a beginner or building on existing skills, compare what each course covers and ask us where to begin.',
                'problem_tagline' => 'Start with your goal',
                'problem_title' => 'What do you want to be able to do?',
                'problem' => 'You might want to improve English, prepare for IELTS or PTE, learn Japanese, Korean or Chinese, use office software with confidence or begin web development. Your goal and current level will help narrow the choice.',
                'paths_tagline' => 'Ways to begin',
                'paths_title' => 'Choose the kind of practice you need.',
                'paths' => "Start with a foundation course if you are new to the subject\nPractise speaking, listening, reading and writing in language classes\nUse mock activities in IELTS, PTE and exam-focused courses\nCreate documents, spreadsheets and presentations in computer courses\nBuild practical projects in Web Development",
                'support_tagline' => 'Before you join',
                'support_title' => 'Ask the questions that affect your learning.',
                'why' => 'Check the course level, learning areas, fee, duration and instructor. Ask about current timings, what to bring and how much practice is expected outside class.',
                'proof_tagline' => 'Golden Eye Academy, Pokhara',
                'proof' => "Established in Pokhara since 2008\nActive course pages organized by learning area\nCourse Help by form, telephone or WhatsApp",
                'final_tagline' => 'Choose your next step',
                'final_headline' => 'Tell us your goal, current level and preferred time to study.',
                'primary_cta_text' => 'Ask for Course Help',
                'secondary_action' => 'courses',
                'secondary_cta_text' => 'View Course Details',
            ],
            'parents' => [
                'page_title' => 'Course Guide for Parents in Pokhara | Golden Eye Academy',
                'meta_description' => 'Compare course suitability, fees, duration, instructors, current timings and completion certificates before helping your son or daughter choose.',
                'badge' => 'For parents',
                'headline' => 'Clear course information for parents.',
                'subheadline' => 'See what your child will learn, what the course costs, how long it takes and which questions to ask before enrollment.',
                'problem_tagline' => 'Choose with your child',
                'problem_title' => 'A suitable course needs more than a familiar name.',
                'problem' => 'Consider the student’s current level, interest, timetable and willingness to practise. Then compare the course content, fee, duration and instructor together.',
                'paths_tagline' => 'Before enrollment',
                'paths_title' => 'Check the details that matter to your family.',
                'paths' => "Course suitability and starting level\nFee, duration and instructor\nCurrent batch timing and availability\nAttendance and practice expectations\nHow progress can be discussed\nCertificate and course-completion requirements",
                'support_tagline' => 'Questions are welcome',
                'support_title' => 'Call, message or visit the academy.',
                'why' => 'Ask how attendance and practice affect learning, how progress can be discussed and what completion requirements apply. Golden Eye Academy provides a certificate after completion of each course; course-specific requirements can be confirmed with the team.',
                'proof_tagline' => 'Clear, practical information',
                'proof' => "Established in Pokhara since 2008\nPublished course descriptions and learning areas\nDirect contact with the academy team",
                'final_tagline' => 'Discuss the decision',
                'final_headline' => 'Share your child’s current level, goal and the course you are considering.',
                'primary_cta_text' => 'Ask for Course Help',
                'secondary_action' => 'whatsapp',
                'secondary_cta_text' => 'Message on WhatsApp',
            ],
            'study_abroad' => [
                'page_title' => 'IELTS, PTE & Language Preparation in Pokhara | Golden Eye Academy',
                'meta_description' => 'See Golden Eye Academy preparation classes for IELTS, PTE and languages. For wider study-abroad consulting questions, contact Brilliant Education Pokhara.',
                'badge' => 'Preparation classes and guidance',
                'headline' => 'Build the language and test skills your plan requires.',
                'subheadline' => 'Golden Eye Academy provides IELTS, PTE and relevant language classes in Pokhara. Broader education-consulting questions belong with Brilliant Education Pokhara.',
                'problem_tagline' => 'Know which help you need',
                'problem_title' => 'Preparation classes and education consulting are different.',
                'problem' => 'A preparation class helps you practise language or test skills. Education consulting deals with wider institution and application questions. Neither service can guarantee a score, admission or visa outcome.',
                'paths_tagline' => 'Choose the right conversation',
                'paths_title' => 'Start with your immediate need.',
                'paths' => "Golden Eye Academy for IELTS and PTE preparation\nGolden Eye Academy for active Japanese, Korean, English and Chinese classes\nGolden Eye Academy Course Help for class and timing questions\nBrilliant Education Pokhara for contextual education-consulting guidance",
                'support_tagline' => 'Preparation at Golden Eye Academy',
                'support_title' => 'Focus on the skills you need to practise.',
                'why' => 'Tell the academy your current level, test or language goal and preferred study time. The team can explain relevant preparation classes and current availability without promising a score, admission or visa result.',
                'proof_tagline' => 'Plan your preparation',
                'proof' => "Compare active IELTS, PTE and language courses\nAsk about your starting level and current timings\nUse Course Help for Golden Eye Academy class questions",
                'final_tagline' => 'Plan the next conversation',
                'final_headline' => 'Tell us which language or test skills you want to prepare for.',
                'primary_cta_text' => 'Ask for Course Help',
                'secondary_action' => 'courses',
                'secondary_cta_text' => 'View Course Details',
            ],
            'job_computer_skills' => [
                'page_title' => 'Computer & Digital Skills Courses in Pokhara | Golden Eye Academy',
                'meta_description' => 'Build practical computer and workplace skills through documents, spreadsheets, presentations, email and web development with Laravel, APIs and deployment concepts.',
                'badge' => 'Computer and IT skills',
                'headline' => 'Build practical skills you can use and explain.',
                'subheadline' => 'Practise everyday computer use, office software or web development through a course that matches your experience.',
                'problem_tagline' => 'Choose the right starting point',
                'problem_title' => 'Start from your current computer experience.',
                'problem' => 'Some learners need everyday computer confidence, others want practice with office software, and some are ready to build websites. Start with what you can do now and the tasks you want to handle next.',
                'paths_tagline' => 'Computer and IT pathways',
                'paths_title' => 'Choose the practical work you want to learn.',
                'paths' => "Computer confidence, email and everyday digital tasks\nWord documents and professional formatting\nExcel data handling, formulas and reporting\nPowerPoint presentations and file management\nHTML, CSS, responsive interfaces and Laravel\nDatabases, API integration and deployment concepts in Web Development",
                'support_tagline' => 'Choose your level',
                'support_title' => 'Begin with the course that matches your experience.',
                'why' => 'Course pages explain the practical learning areas, fee, duration and instructor. Ask about current timings and any equipment you need. These courses build skills through practice; they do not guarantee a job or placement.',
                'proof_tagline' => 'Golden Eye Academy, Pokhara',
                'proof' => "Active computer, office and web-development courses\nPublished course descriptions and outlines\nCourse Help for learners who are unsure where to begin",
                'final_tagline' => 'Build your next skill',
                'final_headline' => 'Tell us which computer tasks you can do now and what you want to practise next.',
                'primary_cta_text' => 'Ask for Course Help',
                'secondary_action' => 'courses',
                'secondary_cta_text' => 'View Course Details',
            ],
        ];

        $settings = [];

        foreach ($pages as $page => $values) {
            $prefix = "audience_{$page}";
            $settings["{$prefix}_status"] = 'active';
            $settings["{$prefix}_hero_status"] = 'active';
            $settings["{$prefix}_guidance_status"] = 'active';
            $settings["{$prefix}_support_status"] = 'active';
            $settings["{$prefix}_final_status"] = 'active';
            $settings["{$prefix}_image"] = 'site/img/carousel-1.png';

            foreach ($values as $key => $value) {
                $settings["{$prefix}_{$key}"] = $value;
            }
        }

        return $settings;
    }
}
