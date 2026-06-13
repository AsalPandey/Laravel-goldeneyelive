<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'site_name' => ['Golden Eye', 'text'],
            'site_name_suffix' => ['Academy', 'text'],
            'site_logo' => ['site/img/logo.png', 'image'],
            'site_favicon' => ['site/img/logo.png', 'image'],
            'logo_subtitle' => ['Pokhara - Est. 2008', 'text'],
            'site_email' => ['goldeneyeacademy2008@gmail.com', 'text'],
            'site_phone' => ['061-572599', 'text'],
            'whatsapp_number' => ['9779856058599', 'text'],
            'site_address' => ['Srijana Chowk, Pokhara, Nepal', 'text'],
            'opening_hours' => ['Sun-Fri: 7:00 AM - 6:00 PM', 'text'],
            'facebook_url' => ['https://www.facebook.com/goldeneyeacademy', 'text'],
            'instagram_url' => ['https://www.instagram.com/goldeneye.academy/', 'text'],
            'linkedin_url' => ['https://www.linkedin.com/company/golden-eye-academy/', 'text'],
            'youtube_url' => ['https://youtube.com', 'text'],
            'twitter_url' => ['', 'text'],

            'meta_title' => ['Golden Eye Academy | Established Academy in Pokhara Since 2008', 'text'],
            'meta_keywords' => ['Golden Eye Academy, IELTS Pokhara, PTE Pokhara, Korean class, Japanese class, English class, computer course, web development Pokhara, IT classes Pokhara', 'text'],
            'meta_description' => ['Established in 2008, Golden Eye Academy offers IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes in Pokhara, Nepal.', 'text'],
            'aeo_summary' => ['Golden Eye Academy is an established academy in Pokhara, Nepal, serving learners since 2008 through IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes.', 'text'],
            'schema_markup' => [json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'EducationalOrganization',
                'name' => 'Golden Eye Academy',
                'address' => 'Srijana Chowk, Pokhara, Nepal',
                'email' => 'goldeneyeacademy2008@gmail.com',
                'telephone' => '061-572599',
            ]), 'text'],
            'google_analytics_id' => ['', 'text'],
            'analytics_tracking_enabled' => ['active', 'text'],
            'google_search_console_id' => ['', 'text'],
            'bing_webmaster_id' => ['', 'text'],
            'google_business_profile_url' => ['', 'text'],
            'external_review_screenshot' => ['', 'image'],
            'external_review_proof_note' => ['Ask the academy team for current Google review proof or verified review screenshots before enrollment.', 'text'],
            'speakable_selectors' => ['.hero-hook-title, .section-title, h1, h2', 'text'],
            'robots_txt' => ["User-agent: *\nDisallow: /admin\nDisallow: /login\n\nSitemap: https://goldeneye.edu.np/sitemap.xml", 'text'],
            'geo_latitude' => ['28.2126', 'text'],
            'geo_longitude' => ['83.9786', 'text'],
            'google_maps_embed' => ['https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3515.823438031535!2d83.97858907530514!3d28.212555675898857!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399595ab361d716d%3A0xcf953250b7312903!2sGolden%20Eye%20Academy!5e0!3m2!1sen!2snp!4v1714100000000!5m2!1sen!2snp', 'text'],
            'recaptcha_site_key' => ['', 'text'],
            'recaptcha_secret_key' => ['', 'text'],
            'image_size_limit' => ['2048', 'text'],

            'hero_image' => ['site/img/carousel-1.png', 'image'],
            'hero_badge_text' => ['Golden Eye Academy, Pokhara', 'text'],
            'hero_title' => ['Established academy in Pokhara since 2008.', 'text'],
            'hero_hook_headline' => ['Established academy in Pokhara since 2008.', 'text'],
            'hero_hook_body' => ['Golden Eye Academy offers practical classes and skill-based batches for IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT learners in Pokhara.', 'text'],
            'hero_subtitle' => ['Explore practical classes, batch options, faculty support, and enrollment information before choosing your course.', 'text'],
            'hero_cta_text' => ['Ask for Course Help', 'text'],
            'hero_cta_1_text' => ['Ask for Course Help', 'text'],
            'hero_cta_2_text' => ['View Course Details', 'text'],

            'stat_1_val' => ['15+', 'text'],
            'stat_1_lab' => ['Years of Learning', 'text'],
            'stat_2_val' => ['5,000+', 'text'],
            'stat_2_lab' => ['Learners Supported', 'text'],
            'stat_3_val' => ['Regular', 'text'],
            'stat_3_lab' => ['Mock Test & Feedback', 'text'],
            'stat_4_val' => ['2 hr', 'text'],
            'stat_4_lab' => ['Typical Response', 'text'],

            'pathway_tagline' => ['Choose by goal', 'text'],
            'pathway_title' => ['Start with the path that sounds like you.', 'text'],
            'courses_title' => ['Explore Courses and Batches', 'text'],
            'courses_header_title' => ['Professional Courses', 'text'],
            'courses_subtitle' => ['Browse courses by subject, compare class options, and contact the academy before enrollment.', 'text'],
            'courses_all_tagline' => ['Course options', 'text'],
            'courses_all_title' => ['Choose Your', 'text'],
            'category_header_badge' => ['Program Category', 'text'],
            'category_title_prefix' => ['Explore', 'text'],
            'category_tagline' => ['Focused Pathways', 'text'],

            'about_image' => ['site/img/about.jpg', 'image'],
            'about_title' => ['Practical learning since 2008', 'text'],
            'about_text' => ['Golden Eye Academy is an established academy in Pokhara focused on structured classes, practical learning, student support, and clear enrollment information.', 'text'],
            'about_point_1' => ['Academic support before enrollment', 'text'],
            'about_point_2' => ['Language, test prep, IT, and office skills', 'text'],
            'about_point_3' => ['Support for students, parents, and learners', 'text'],
            'about_point_4' => ['Practical classes with clear outcomes', 'text'],
            'about_content_title' => ['Why Golden Eye Academy works', 'text'],
            'about_content' => ['We combine practical classes, faculty support, batch information, and follow-up so learners understand the course before enrollment.', 'text'],
            'about_section_tagline' => ['15+ Years of Trust', 'text'],
            'about_section_title' => ['Pokhara learners have trusted our academy since 2008', 'text'],
            'about_header_title' => ['About Golden Eye Academy', 'text'],
            'about_page_content' => ['<h2>Our Approach</h2><p>Golden Eye Academy has served Pokhara learners since 2008 through practical classes, experienced faculty, clear batch information, and student-focused academic support.</p><p>Our courses are built around structured lessons, practice, and clear next steps.</p>', 'text'],
            'about_feat_1_title' => ['Enrollment Support', 'text'],
            'about_feat_1_desc' => ['Learners understand class options, timing, and course expectations before enrollment.', 'text'],
            'about_feat_2_title' => ['Practical Learning', 'text'],
            'about_feat_2_desc' => ['Courses focus on usable skills, mock tests, projects, and workplace confidence.', 'text'],
            'about_feat_3_title' => ['Parent-Friendly Decisions', 'text'],
            'about_feat_3_desc' => ['Families can ask about outcomes, fees, timing, and student readiness.', 'text'],
            'about_feat_4_title' => ['Follow-Up Support', 'text'],
            'about_feat_4_desc' => ['The team supports learners beyond the first inquiry.', 'text'],

            'founder_name' => ['Shankar Pokharel', 'text'],
            'founder_position' => ['Founder and Director', 'text'],
            'founder_message' => ['Our goal is simple: help learners join the right class, study with discipline, and build practical confidence.', 'text'],
            'founder_image' => ['site/img/message-chairperson.jpg', 'image'],
            'founder_section_tagline' => ["Director's Message", 'text'],
            'founder_section_title' => ['Learn with clarity and discipline', 'text'],

            'teachers_title' => ['Experienced Faculty for Practical Classes', 'text'],
            'teachers_subtitle' => ['Meet the instructors and academic support team behind our classes.', 'text'],
            'testimonials_title' => ['Student Results and Feedback', 'text'],
            'blog_title' => ['Academy Blog', 'text'],
            'blog_header_title' => ['Academy Blog', 'text'],
            'blog_subtitle' => ['Class updates, exam preparation notes, and practical learning articles.', 'text'],
            'blog_tagline' => ['Academy Insights', 'text'],
            'blog_section_title' => ['Latest From Golden Eye Academy', 'text'],
            'blog_cta_title' => ['Need class information before enrollment?', 'text'],
            'blog_cta_desc' => ['Tell us your subject goal and our academy team will explain suitable course and batch options.', 'text'],
            'blog_cta_btn' => ['Ask for Course Help', 'text'],
            'recent_posts_title' => ['Recent Guides', 'text'],

            'faq_header_title' => ['Frequently Asked Questions', 'text'],
            'faq_lead_title' => ['Need class information before enrollment?', 'text'],
            'faq_btn_text' => ['Ask for Course Help', 'text'],
            'faq_page_content' => ['<h2>Frequently Asked Questions</h2><p>Use these answers to understand courses, class timing, certificates, academic support, and enrollment. If you are unsure, send a quick course-help request.</p>', 'text'],
            'contact_header_title' => ['Contact Golden Eye Academy', 'text'],
            'contact_page_content' => ['<h3>Message our academy team</h3><p>Share your current class interest and goal. We will explain suitable courses, batch options, and enrollment support.</p>', 'text'],
            'enroll_header_title' => ['Ask for Course Help', 'text'],
            'enroll_section_title' => ['Tell us your goal. We will explain suitable classes and batch options.', 'text'],
            'privacy_header_title' => ['Privacy Policy', 'text'],
            'privacy_policy_content' => ['<h2>Privacy Commitment</h2><p>We use inquiry and enrollment details only to respond, support students, manage admissions, and improve academy communication.</p>', 'text'],
            'terms_header_title' => ['Terms and Conditions', 'text'],
            'terms_and_conditions_content' => ['<h2>Enrollment Terms</h2><p>Course availability, timing, fees, and batch details should be confirmed with the academy team before final enrollment.</p>', 'text'],

            'popup_status' => ['active', 'text'],
            'popup_image' => ['site/img/carousel-1.png', 'image'],
            'popup_title' => ['Need course information before enrollment?', 'text'],
            'popup_subtitle' => ['Tell us your subject goal and we will explain suitable classes, batch options, and academic support.', 'text'],
            'popup_button_text' => ['Ask for Course Help', 'text'],
            'popup_register_link' => ['/join-now?course=undecided&selected_course=undecided&source_page=popup&source_section=notice-popup&inquiry_intent=course_guidance', 'text'],
            'notice_badge_text' => ['Official Update', 'text'],
            'notice_dismiss_text' => ['Dismiss Notice', 'text'],

            'whatsapp_cta_text' => ['Message on WhatsApp', 'text'],
            'whatsapp_cta_subtext' => ['', 'text'],
            'whatsapp_button_text' => ['Message on WhatsApp', 'text'],
            'whatsapp_prefill_message' => ['Hi Golden Eye Academy, I have a question about classes and enrollment.', 'text'],
            'sticky_cta_text' => ['Ask for Course Help', 'text'],
            'sticky_cta_badge' => ['Quick Help', 'text'],
            'sticky_cta_desc' => ['Message us with your class or enrollment question.', 'text'],
            'inquiry_tab_text' => ['Need Class Info?', 'text'],
            'inquiry_title' => ['Get course and batch information', 'text'],
            'inquiry_subtitle' => ['Share your goal. We will explain suitable courses, class timing, and enrollment support.', 'text'],
            'navbar_menu_label' => ['Navigate', 'text'],

            'footer_about_text' => ['Golden Eye Academy is an established academy in Pokhara offering practical classes in language, test preparation, computer, office, web development, and IT subjects since 2008.', 'text'],
            'footer_faq_title' => ['Student Support', 'text'],
            'footer_quick_link_title' => 'Academy Links',
            'footer_contact_title' => ['Find Us', 'text'],
            'footer_social_title' => ['Follow Our Journey', 'text'],
            'footer_newsletter_desc' => ['Get course updates, batch reminders, and practical learning notes.', 'text'],

            'career_highlight_1' => ['Enrollment support before classes', 'text'],
            'career_highlight_2' => ['Practical classes and academic support', 'text'],
            'career_highlight_3' => ['Job, study, and language goals', 'text'],
            'career_highlight_4' => ['Follow-up from the academy team', 'text'],
            'contact_success_message' => ['Your inquiry has been received. Our team will contact you shortly.', 'text'],
            'newsletter_success_message' => ['You are subscribed. We will send relevant course and class updates.', 'text'],
            'enroll_success_message' => ['Thank you! We received your inquiry. Our team will contact you soon.', 'text'],
        ];

        foreach ($settings as $key => $setting) {
            [$value, $type] = is_array($setting) ? $setting : [$setting, 'text'];

            SiteSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => $type,
                ],
            );

            cache()->forget("setting_{$key}");
        }

        cache()->forget('site_settings');
    }
}
