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
            'hero_title' => 'Build skills you can use with confidence.',
            'hero_subtitle' => 'Explore focused classes, compare the learning areas and choose a suitable current batch.',
            'hero_cta_1_text' => 'Ask for Course Help',
            'hero_cta_2_text' => 'View Course Details',

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
            'courses_title' => 'Courses and Classes at Golden Eye Academy',
            'courses_header_title' => 'Courses and Classes',
            'courses_subtitle' => 'Explore active courses by subject, compare the learning focus and ask about the current batch that fits your goal.',
            'courses_all_tagline' => 'Courses and current batches',
            'courses_all_title' => 'Explore practical classes by subject.',
            'home_courses_batch_note' => 'Ask about the current batch',

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

            'blog_title' => 'Golden Eye Academy Learning Guides',
            'blog_header_title' => 'Learning Guides',
            'blog_subtitle' => 'Practical articles for choosing courses, preparing for classes and making informed learning decisions.',
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

            'catalogue_meta_title' => 'Golden Eye Academy Courses and Learning Support in Pokhara',
            'catalogue_meta_description' => 'Explore Golden Eye Academy courses, classes and learning-support areas in Pokhara, then ask for help choosing a suitable current batch.',
            'catalogue_badge' => 'Golden Eye Academy learning options',
            'catalogue_title' => 'Explore courses, classes and academic support.',
            'catalogue_description' => 'Browse the academyâ€™s active learning areas, then open individual course pages to compare their focus, duration and current fee.',
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
            'footer_newsletter_desc' => 'Receive useful course updates, batch notices and academy learning guides.',
            'course_confirmation_note' => 'Ask the academy team for the current batch timing, seats and faculty information.',

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
            'home_meta_title' => 'Golden Eye Academy | Courses and Classes in Pokhara',
            'home_meta_description' => 'Build practical skills at Golden Eye Academy, established in Pokhara since 2008. Explore computer, language, test-preparation and academic-support classes.',
            'home_trust_items' => "Established in Pokhara since 2008\nClear course information\nPractical learning connected to student goals\nSupport for students and parents\nCall or message before planning your visit",
            'home_audience_tagline' => 'Start with your goal',
            'home_audience_title' => 'Which learning path are you looking for?',
            'home_courses_tagline' => 'Popular course choices',
            'home_courses_title' => 'Explore courses students ask about most',
            'home_courses_cta_text' => 'View Course Details',
            'home_courses_batch_note' => 'Ask about the current batch',
            'home_categories_tagline' => 'Browse by learning area',
            'home_categories_title' => 'Find classes by subject and skill.',
            'home_why_tagline' => 'Why Golden Eye Academy',
            'home_why_title' => 'Choose a course with a clear reason.',
            'home_why_description' => 'Golden Eye Academy combines structured teaching, published course information and support for students and families.',
            'home_why_items' => "Established in Pokhara since 2008\nActive courses organized by subject and learning goal\nPractical learning supported by published course outlines\nCourse Help for students and parents",
            'home_testimonials_tagline' => 'Student experiences',
            'home_testimonials_title' => 'Hear from learners who studied at Golden Eye Academy',
            'home_faculty_tagline' => 'Golden Eye faculty',
            'home_faculty_title' => 'Meet the people behind current classes',
            'home_reviews_tagline' => 'Independent review information',
            'home_reviews_title' => 'Explore verified external feedback',
            'home_parent_tagline' => 'For parents',
            'home_parent_title' => 'Make the course decision with confidence.',
            'home_parent_description' => 'Review the learning focus, duration, current fee, batch timing and faculty information before your child enrolls.',
            'home_parent_items' => "Understand what the course covers\nCompare the duration and current fee\nDiscuss batch timing and faculty information\nSet realistic expectations together",
            'home_faq_tagline' => 'Before you choose',
            'home_faq_title' => 'Questions students and parents ask most',
            'home_final_tagline' => 'Choose with confidence',
            'home_final_title' => 'Ready to find a suitable course?',
            'home_final_description' => 'Tell us your goal, current level and preferred timing. We will help you compare relevant classes and current batches.',
            'home_final_primary_cta_text' => 'Ask for Course Help',
            'home_final_secondary_cta_text' => 'Message on WhatsApp',

            'home_audience_students_status' => 'active',
            'home_audience_students_title' => 'I am a Student',
            'home_audience_students_problem' => 'Want to build a useful skill but unsure which course to choose?',
            'home_audience_students_benefit' => 'Compare classes by subject, starting point and learning goal.',
            'home_audience_students_cta_text' => 'View Course Details',
            'home_audience_parents_status' => 'active',
            'home_audience_parents_title' => 'I am a Parent',
            'home_audience_parents_problem' => 'Want clear course information before your child enrolls?',
            'home_audience_parents_benefit' => 'Review the learning focus, fee, duration and current batch information.',
            'home_audience_parents_cta_text' => 'View Course Details',
            'home_audience_study_abroad_status' => 'active',
            'home_audience_study_abroad_title' => 'I need IELTS or PTE classes',
            'home_audience_study_abroad_problem' => 'Preparing for an English-language test or an international study goal?',
            'home_audience_study_abroad_benefit' => 'Explore relevant preparation classes and understand the next learning step.',
            'home_audience_study_abroad_cta_text' => 'View Course Details',
            'home_audience_job_computer_skills_status' => 'active',
            'home_audience_job_computer_skills_title' => 'I want Computer or IT Skills',
            'home_audience_job_computer_skills_problem' => 'Want practical skills for study, office work or an IT pathway?',
            'home_audience_job_computer_skills_benefit' => 'Start with a course that matches your current experience and goal.',
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
            'hero_title' => 'd070f38a56c378676d2d4a82af1799efa382260847b55b611f621db9f8614ccf',
            'hero_subtitle' => '48324edad9173939bd857a394052fb8f8122b8b43c8d1c1bfc610bcb20c841a1',
            'about_text' => '47454625a40fee6d73f08aee3299cea25fb9a450cc0d82991625349e4d740118',
            'catalogue_description' => '304b9860145087d10f7e52f119052c0aec49474260671eb3b6fd752deacb01b8',
            'inquiry_subtitle' => '0f1f7ec372d66fdd7eb0803726e72b9ae3f25c05aa9edfc5d948e58ddda0bcdc',
            'home_why_description' => '46bcd29d54caf0c127d7813c9fefe97feb92fba2f175ac1dbbf3cd3b642979a5',
            'home_final_tagline' => 'd6c92918d662ca9fd4a1bfb10feb380a491fb563b97529d9d761a5cf8386476b',
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
            ['id' => 6, 'question' => 'What practical activities are included in classes?', 'answer' => 'Practical work depends on the subject. Classes may use guided exercises, speaking or mock activities, completed files, and practical projects in relevant computer and web-development courses. Published course outlines describe the main learning areas.', 'order_priority' => 60],
            ['id' => 7, 'question' => 'How do I find the current batch timing?', 'answer' => 'Send the course name and your preferred study time through Course Help, telephone or WhatsApp. The academy team will share the current batch options for that course.', 'order_priority' => 70],
            ['id' => 8, 'question' => 'What information should I provide during an inquiry?', 'answer' => 'Share the subject or course you are considering, your current level, your main goal and the time you prefer to study. This helps the academy team give you a more useful reply.', 'order_priority' => 80],
            ['id' => 9, 'question' => 'How do I choose a suitable batch?', 'answer' => 'Choose a batch that matches your current level, timetable and ability to practise consistently. Ask about the learning pace and faculty for the current batch before making your decision.', 'order_priority' => 90],
            ['id' => 10, 'question' => 'Do Golden Eye Academy courses include certificates?', 'answer' => 'Golden Eye Academy provides a certificate after completion of each course.', 'order_priority' => 100],
            ['id' => 11, 'question' => 'What teaching approach does Golden Eye Academy use?', 'answer' => 'Golden Eye Academy connects clear instruction with practical learning and regular participation. The exact classroom approach depends on the subject and the learning areas listed for that course.', 'order_priority' => 110],
            ['id' => 12, 'question' => 'Who will teach my class?', 'answer' => 'Course pages show the faculty member or teaching team currently associated with each course. Ask the academy to identify the faculty for the batch you plan to join.', 'order_priority' => 120],
            ['id' => 13, 'question' => 'How is student progress reviewed?', 'answer' => 'Progress review varies by course and may include class participation, guided practice, feedback or subject-specific activities. Ask how progress is reviewed in the course you are considering.', 'order_priority' => 130],
            ['id' => 14, 'question' => 'Are the courses suitable for school or college students?', 'answer' => 'Suitability depends on the student’s current level, timetable and goal. Students and parents can compare the published course information and discuss the most appropriate starting point with the academy.', 'order_priority' => 140],
            ['id' => 15, 'question' => 'How much do courses cost and how long do they take?', 'answer' => 'Each active course page shows its listed fee and duration. Ask the academy team for the current payment and batch information for the course you want to join.', 'order_priority' => 150],
            ['id' => 16, 'question' => 'What happens after I submit Course Help?', 'answer' => 'The academy receives your contact details, learning goal and course interest. A team member can then respond with relevant course or current-batch information.', 'order_priority' => 160],
            ['id' => 17, 'question' => 'Can I visit Golden Eye Academy before enrolling?', 'answer' => 'Yes. Contact the academy before travelling so you can plan a suitable visit and use the time to discuss courses, current batches, fees and academic support.', 'order_priority' => 170],
            ['id' => 18, 'question' => 'Where should I ask about study-abroad preparation?', 'answer' => 'Golden Eye Academy provides relevant language and test-preparation classes. Visit the study-abroad guidance page for the academyâ€™s class information and separate contextual guidance.', 'order_priority' => 180],
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
            6 => '7cb9fe3a2923d2251e53a85d4c9d3ecefc7fddc92d119e4008efe0b60f5d85a3',
            10 => 'ee3ac28533395c5cf526542714ea551c98f50e85ef8369f355dfc5e4360ffc47',
            18 => '6c1c8bab83de03dced9677375b8dd499f90055c30f24b28f7ab791a1c47c61ba',
        ];
    }

    /**
     * Fresh-site product copy overrides. Product identities and commercial facts
     * are deliberately included only so the checkpoint matches the protected
     * operational catalogue.
     *
     * @return array<string, array{name: string, badge_text: string, description: string}>
     */
    public static function courseOverrides(): array
    {
        return [
            'ielts-masterclass' => [
                'name' => 'IELTS Masterclass for Band 7+',
                'badge_text' => 'IELTS Preparation',
                'description' => 'Designed for learners preparing for IELTS, this course covers the listening, reading, writing and speaking areas listed in the published outline. It may suit students who want a structured class routine, guided practice and feedback while working toward their own test requirement. Review the current fee and duration on this page, then ask Golden Eye Academy which batch matches your present English level and preferred study time.',
            ],
            'pte-elite-training' => [
                'name' => 'PTE Elite Academic Training',
                'badge_text' => 'PTE Preparation',
                'description' => 'This PTE preparation course is for learners who want structured practice with the computer-based test areas listed in the course outline. Classes focus on understanding the test interface, timing, speaking fluency, writing, reading and listening practice. Compare the published fee and duration, then discuss your current English level and batch options with the academy team.',
            ],
            'jlpt-n5-elite' => [
                'name' => 'Japanese Proficiency JLPT N5',
                'badge_text' => 'JLPT N5',
                'description' => 'A foundation Japanese course for learners beginning with Hiragana, Katakana, basic Kanji, vocabulary, grammar, listening and simple conversation. The published outline shows the main N5 learning areas and may suit students who want a structured starting point before progressing further. Ask the academy about the current batch and the study routine expected between classes.',
            ],
            'jlpt-n4' => [
                'name' => 'Japanese Proficiency JLPT N4',
                'badge_text' => 'JLPT N4',
                'description' => 'This course develops the N4-level vocabulary, Kanji, grammar, reading, listening and conversation areas listed in the outline. It may suit learners who already have a Japanese foundation and want to continue with a more advanced learning routine. Share your previous Japanese study with the academy team when asking whether the current batch is a suitable next step.',
            ],
            'professional-korean-eps' => [
                'name' => 'Professional Korean EPS-TOPIK',
                'badge_text' => 'EPS-TOPIK',
                'description' => 'This Korean course covers Hangul, vocabulary, listening, reading and EPS-TOPIK-oriented practice from the published outline. It is an exam-focused language course for learners who want a structured Korean study routine with regular review and practice.',
            ],
            'basic-korean-course' => [
                'name' => 'Basic Korean Course',
                'badge_text' => 'Korean Foundation',
                'description' => 'A starting course for learners who want to read and write Hangul, build everyday vocabulary, form simple sentences and practise basic listening and conversation. The course can provide a foundation for continued Korean learning. Compare the published duration and fee, then ask which current batch matches your starting level.',
            ],
            'global-english-pro' => [
                'name' => 'Global English Professional Track',
                'badge_text' => 'English Communication',
                'description' => 'This English course focuses on the speaking, writing, grammar, presentation and interview-practice areas listed in the outline. It may suit students and other learners who want to communicate more clearly in study, workplace or everyday situations. Tell the academy team which area of English is most difficult for you when asking about the current class.',
            ],
            'basic-english-course' => [
                'name' => 'Basic English Foundation',
                'badge_text' => 'English Foundation',
                'description' => 'A foundation course covering basic grammar, vocabulary, reading, writing, listening and everyday speaking. It may suit learners who want to rebuild core English skills through a structured class routine. Share your current comfort with English so the academy team can help you understand whether this is the right starting point.',
            ],
            'professional-web-development' => [
                'name' => 'Professional Web Development',
                'badge_text' => 'Web Development',
                'description' => 'Build web-development foundations through practical project work covering HTML, CSS, responsive interfaces, Laravel, databases, API integration and deployment concepts. The course supports learners in creating and explaining their own technical work while developing a stronger understanding of how websites are built.',
            ],
            'advanced-computer-diploma' => [
                'name' => 'Advanced Diploma in Computer Science',
                'badge_text' => 'Computer Skills',
                'description' => 'This computer course covers documents, spreadsheets, presentations, databases, email and everyday digital workflows through guided tasks and practical activities. It supports greater confidence with common study, office and workplace computer tasks.',
            ],
            'corporate-office-package' => [
                'name' => 'Corporate Office and Admin Package',
                'badge_text' => 'Office Skills',
                'description' => 'An office-skills course covering Word documents, Excel data handling, presentations, email, cloud collaboration and file management. It may suit learners who want guided practice with common administrative and workplace tasks. Compare the published fee and duration, then ask which current batch fits your computer experience and timetable.',
            ],
            'chinese-language-course' => [
                'name' => 'Chinese Language Starter',
                'badge_text' => 'Chinese Starter',
                'description' => 'A beginner Chinese course introducing pronunciation, basic vocabulary, listening, reading, speaking and cultural etiquette from the published outline. It may suit learners who want an organized first step into the language. Ask the academy team about the current class pace and what regular practice will support the course.',
            ],
            'free-course-roadmap-help' => [
                'name' => 'Free Course Roadmap Help',
                'badge_text' => 'Course Help',
                'description' => 'Course Help is for students and parents who want to compare active Golden Eye Academy courses before choosing a class. Share the learner’s current level, main goal, preferred timing and the subjects being considered. The academy team can explain relevant course information and current batch options through the existing inquiry path.',
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
            'professional-korean-eps' => 'ef29b2f1f8d49a95fb18849df0fd1021314e2d66bfadb7e90c6a24647360ab59',
            'professional-web-development' => 'a0daa0ce000776f38b6ef37d133c2dae1ac6eb5de22411cd6764c1bcea7371cb',
            'advanced-computer-diploma' => '29a2de46184569c760ff3d3e1e14fae7ca9e3311ec8cff19aec7c92f859a17c0',
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
                'page_title' => 'Courses and Classes for Students in Pokhara | Golden Eye Academy',
                'meta_description' => 'Explore Golden Eye Academy courses for students in Pokhara and compare computer, language, test-preparation and practical-skill options.',
                'badge' => 'For students',
                'headline' => 'Choose a course around your goal.',
                'subheadline' => 'Compare relevant classes, understand what each course covers and ask for guidance when you are unsure where to begin.',
                'problem_tagline' => 'Start with what matters now',
                'problem_title' => 'A useful course choice begins with a clear goal.',
                'problem' => 'You may want to improve English, prepare for a test, learn a language, build computer confidence or begin an IT skill. Focus first on the result you want from your learning time.',
                'paths_tagline' => 'Explore your options',
                'paths_title' => 'Choose a learning area, then compare the active courses.',
                'paths' => "Language and English classes\nIELTS and PTE preparation\nComputer and office skills\nWeb development and IT learning\nAcademic support",
                'support_tagline' => 'How Golden Eye helps',
                'support_title' => 'Understand the course before you choose the batch.',
                'why' => 'Golden Eye Academy helps students compare the learning focus, duration and current course information, then ask the questions that make the decision clearer.',
                'proof_tagline' => 'Golden Eye Academy, Pokhara',
                'proof' => "Established in Pokhara since 2008\nActive course pages organized by learning area\nCourse Help by form, telephone or WhatsApp",
                'final_tagline' => 'Choose your next step',
                'final_headline' => 'Tell us what you want to learn and where you are starting from.',
                'primary_cta_text' => 'Ask for Course Help',
                'secondary_action' => 'courses',
                'secondary_cta_text' => 'View Course Details',
            ],
            'parents' => [
                'page_title' => 'Course Information for Parents in Pokhara | Golden Eye Academy',
                'meta_description' => 'Golden Eye Academy helps parents compare course suitability, learning focus, current fees, duration, batches and faculty information.',
                'badge' => 'For parents',
                'headline' => 'Make the course decision with confidence.',
                'subheadline' => 'Review the learning focus, duration, current fee, batch timing and faculty information before your child enrolls.',
                'problem_tagline' => 'A clearer family decision',
                'problem_title' => 'Look beyond the course name.',
                'problem' => 'A suitable course should match the student’s current level, interest, timetable and willingness to practise. Clear information helps families discuss those factors together.',
                'paths_tagline' => 'Questions worth asking',
                'paths_title' => 'Compare the details that shape the student experience.',
                'paths' => "What the course covers\nWho the course may suit\nDuration and current fee\nCurrent batch timing\nFaculty information\nLearning and practice expectations",
                'support_tagline' => 'Parent support',
                'support_title' => 'Speak with the academy before deciding.',
                'why' => 'Parents can use Course Help, telephone, WhatsApp or an academy visit to understand the course and discuss realistic expectations with the student.',
                'proof_tagline' => 'Clear, practical information',
                'proof' => "Established in Pokhara since 2008\nPublished course descriptions and learning areas\nDirect contact with the academy team",
                'final_tagline' => 'Discuss the decision',
                'final_headline' => 'Share your child’s learning goal and the course you are considering.',
                'primary_cta_text' => 'Ask for Course Help',
                'secondary_action' => 'whatsapp',
                'secondary_cta_text' => 'Message on WhatsApp',
            ],
            'study_abroad' => [
                'page_title' => 'Test Preparation and Education Guidance in Pokhara | Golden Eye Academy',
                'meta_description' => 'Explore relevant Golden Eye Academy preparation classes and understand when education-consulting guidance from partner Brilliant Education Pokhara may be useful.',
                'badge' => 'Preparation classes and guidance',
                'headline' => 'Prepare for your next education step with the right support.',
                'subheadline' => 'Golden Eye Academy provides relevant language and test-preparation classes. Education-consulting guidance is available contextually through our partner, Brilliant Education Pokhara.',
                'problem_tagline' => 'Begin with the right question',
                'problem_title' => 'Do you need a class, education consulting or both?',
                'problem' => 'A preparation class builds language or test skills. Education consulting helps with broader institution or application decisions. Understanding the difference keeps your next step focused.',
                'paths_tagline' => 'Two connected roles',
                'paths_title' => 'Choose the support that matches your immediate goal.',
                'paths' => "Golden Eye Academy for active language and test-preparation classes\nGolden Eye Academy Course Help for class and batch questions\nBrilliant Education Pokhara for contextual education-consulting guidance",
                'support_tagline' => 'Golden Eye remains your academy',
                'support_title' => 'Preparation classes are provided by Golden Eye Academy.',
                'why' => 'Golden Eye Academy provides courses, classes and academic support. For education-consulting guidance, Golden Eye Academy works with its partner, Brilliant Education Pokhara.',
                'proof_tagline' => 'Start with your goal',
                'proof' => "Explore active preparation courses\nAsk about your current level and preferred batch\nUse the existing Golden Eye inquiry path",
                'final_tagline' => 'Plan the next conversation',
                'final_headline' => 'Tell Golden Eye Academy whether you need preparation classes or broader education guidance.',
                'primary_cta_text' => 'Ask for Course Help',
                'secondary_action' => 'courses',
                'secondary_cta_text' => 'View Course Details',
            ],
            'job_computer_skills' => [
                'page_title' => 'Computer and IT Courses in Pokhara | Golden Eye Academy',
                'meta_description' => 'Explore Golden Eye Academy computer, office and web-development courses for practical study and workplace skills.',
                'badge' => 'Computer and IT skills',
                'headline' => 'Build practical skills you can use and explain.',
                'subheadline' => 'Explore computer, office and web-development courses designed around useful digital and technical learning areas.',
                'problem_tagline' => 'Choose the right starting point',
                'problem_title' => 'Start from your current computer experience.',
                'problem' => 'Some learners need everyday computer confidence, others need office-software practice, and some want to begin web development. The right course depends on what you can already do and what you want to build next.',
                'paths_tagline' => 'Computer and IT pathways',
                'paths_title' => 'Compare active courses by the work you want to practise.',
                'paths' => "Computer foundations and daily digital tasks\nOffice documents, spreadsheets and presentations\nWeb-development foundations\nPractical IT learning connected to study or work",
                'support_tagline' => 'Practical course discovery',
                'support_title' => 'Read the learning areas, then choose your starting point.',
                'why' => 'Golden Eye Academy course pages show the subject focus, duration, current fee and faculty information so learners can compare options before choosing a batch.',
                'proof_tagline' => 'Golden Eye Academy, Pokhara',
                'proof' => "Active computer, office and web-development courses\nPublished course descriptions and outlines\nCourse Help for learners who are unsure where to begin",
                'final_tagline' => 'Build your next skill',
                'final_headline' => 'Tell us what you can do now and what you want to learn next.',
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
