<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Support\GoldenEyeContentBaseline;
use Database\Seeders\Concerns\PreventsProductionBaselineSeeding;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    use PreventsProductionBaselineSeeding;

    /**
     * @var array<int, string>
     */
    private const ENVIRONMENT_BOUND_KEYS = [
        'bing_webmaster_id',
        'external_review_screenshot',
        'geo_latitude',
        'geo_longitude',
        'google_analytics_id',
        'google_business_profile_url',
        'google_maps_embed',
        'google_search_console_id',
        'image_size_limit',
        'recaptcha_secret_key',
        'recaptcha_site_key',
        'robots_txt',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->preventProductionBaselineSeeding();

        $settings = [
            'site_name' => ['Golden Eye', 'text'],
            'site_name_suffix' => ['Academy', 'text'],
            'site_logo' => ['site/img/logo.png', 'image'],
            'site_favicon' => ['site/img/logo.png', 'image'],
            'logo_subtitle' => ['Pokhara, Nepal', 'text'],
            'site_email' => ['goldeneyeacademy2008@gmail.com', 'text'],
            'site_phone' => ['061-572599', 'text'],
            'whatsapp_number' => ['9779856058599', 'text'],
            'site_address' => ['Srijana Chowk, Pokhara, Nepal', 'text'],
            'opening_hours' => ['Sun-Fri: 7:00 AM - 6:00 PM', 'text'],
            'facebook_url' => ['https://www.facebook.com/goldeneyeacademy', 'text'],
            'instagram_url' => ['https://www.instagram.com/goldeneye.academy/', 'text'],
            'linkedin_url' => ['https://www.linkedin.com/company/golden-eye-academy/', 'text'],
            'youtube_url' => ['', 'text'],
            'tiktok_url' => ['https://www.tiktok.com/@goldeneye.academy', 'text'],
            'twitter_url' => ['', 'text'],

            'meta_title' => ['Golden Eye Academy | Courses and Classes in Pokhara', 'text'],
            'meta_keywords' => ['Golden Eye Academy, IELTS Pokhara, PTE Pokhara, Korean class, Japanese class, English class, computer course, web development Pokhara, IT classes Pokhara', 'text'],
            'meta_description' => ['Golden Eye Academy offers IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes in Pokhara, Nepal.', 'text'],
            'aeo_summary' => ['Golden Eye Academy in Pokhara provides IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes.', 'text'],
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
            'external_review_proof_note' => ['', 'text'],
            'robots_txt' => ["User-agent: *\nDisallow: /admin\nDisallow: /login\n\nSitemap: https://goldeneye.edu.np/sitemap.xml", 'text'],
            'geo_latitude' => ['28.2126', 'text'],
            'geo_longitude' => ['83.9786', 'text'],
            'google_maps_embed' => ['https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3515.823438031535!2d83.97858907530514!3d28.212555675898857!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399595ab361d716d%3A0xcf953250b7312903!2sGolden%20Eye%20Academy!5e0!3m2!1sen!2snp!4v1714100000000!5m2!1sen!2snp', 'text'],
            'recaptcha_site_key' => ['', 'text'],
            'recaptcha_secret_key' => ['', 'text'],
            'image_size_limit' => ['2048', 'text'],

            'hero_image' => ['site/img/carousel-1.png', 'image'],
            'hero_badge_text' => ['Golden Eye Academy, Pokhara', 'text'],
            'hero_title' => ['Practical courses and classes in Pokhara.', 'text'],
            'hero_hook_headline' => ['Practical courses and classes in Pokhara.', 'text'],
            'hero_hook_body' => ['Golden Eye Academy offers practical classes and skill-based batches for IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT learners in Pokhara.', 'text'],
            'hero_subtitle' => ['Compare available course details and ask the academy to confirm current batch information before enrollment.', 'text'],
            'hero_cta_text' => ['Ask for Course Help', 'text'],
            'hero_cta_1_text' => ['Ask for Course Help', 'text'],
            'hero_cta_2_text' => ['View Course Details', 'text'],

            'stat_1_val' => ['', 'text'],
            'stat_1_lab' => ['', 'text'],
            'stat_2_val' => ['', 'text'],
            'stat_2_lab' => ['', 'text'],
            'stat_3_val' => ['', 'text'],
            'stat_3_lab' => ['', 'text'],
            'stat_4_val' => ['', 'text'],
            'stat_4_lab' => ['', 'text'],

            'pathway_tagline' => ['Choose by goal', 'text'],
            'pathway_title' => ['Start with the path that sounds like you.', 'text'],
            'courses_title' => ['Explore Courses and Batches', 'text'],
            'courses_header_title' => ['Professional Courses', 'text'],
            'courses_subtitle' => ['Browse courses by subject, compare class options, and contact the academy before enrollment.', 'text'],
            'courses_all_tagline' => ['Course options', 'text'],
            'courses_all_title' => ['Choose Your', 'text'],
            'home_courses_batch_note' => ['Confirm current batch options with the academy', 'text'],
            'category_header_badge' => ['Program Category', 'text'],
            'category_title_prefix' => ['Explore', 'text'],
            'category_tagline' => ['Focused Pathways', 'text'],

            'about_image' => ['site/img/about.jpg', 'image'],
            'about_title' => ['Practical courses and learning support', 'text'],
            'about_text' => ['Golden Eye Academy provides practical courses, classes, workshops and academic programs for students, professionals, schools and organizations.', 'text'],
            'about_point_1' => ['Academic support before enrollment', 'text'],
            'about_point_2' => ['Language, test prep, IT, and office skills', 'text'],
            'about_point_3' => ['Support for students, parents, and learners', 'text'],
            'about_point_4' => ['Current details confirmed before enrollment', 'text'],
            'about_content_title' => ['Understand the course before enrollment', 'text'],
            'about_content' => ['Review the course focus, fee, duration, outline, and available instructor information, then ask the academy to confirm the current schedule and support.', 'text'],
            'about_section_tagline' => ['Before enrollment', 'text'],
            'about_section_title' => ['Questions students and parents can check', 'text'],
            'about_header_title' => ['About Golden Eye Academy', 'text'],
            'about_page_content' => ['<h2>Our Approach</h2><p>Golden Eye Academy provides courses, classes, workshops, and academic programs in Pokhara.</p><p>Students and parents can review the available course description, outline, fee, duration, and instructor information, then contact the academy to confirm current batch details before enrollment.</p>', 'text'],
            'about_feat_1_title' => ['Enrollment Support', 'text'],
            'about_feat_1_desc' => ['Ask about course fit, current timing, fees, and support before enrollment.', 'text'],
            'about_feat_2_title' => ['Practical Learning', 'text'],
            'about_feat_2_desc' => ['Review each course description and outline to understand its listed learning areas.', 'text'],
            'about_feat_3_title' => ['Parent-Friendly Decisions', 'text'],
            'about_feat_3_desc' => ['Families can ask about fees, timing, course expectations, and student readiness.', 'text'],
            'about_feat_4_title' => ['Current Information', 'text'],
            'about_feat_4_desc' => ['Confirm current batches, availability, and instructor details before enrollment.', 'text'],

            'founder_name' => ['Shankar Pokharel', 'text'],
            'founder_position' => ['Founder and Director', 'text'],
            'founder_message' => ['Our goal is simple: help learners join the right class, study with discipline, and build practical confidence.', 'text'],
            'founder_image' => ['site/img/message-chairperson.jpg', 'image'],
            'founder_section_tagline' => ["Director's Message", 'text'],
            'founder_section_title' => ['Learn with clarity and discipline', 'text'],

            'teachers_title' => ['Faculty and Academic Support Profiles', 'text'],
            'teachers_subtitle' => ['View active profiles and confirm the instructor assigned to the current batch.', 'text'],
            'testimonials_title' => ['Published Student Feedback', 'text'],
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
            'faq_btn_text' => ['Show More FAQs', 'text'],
            'faq_btn_text_expanded' => ['Show Fewer FAQs', 'text'],
            'faq_page_content' => ['<h2>Frequently Asked Questions</h2><p>Use these answers as general guidance, then confirm current fees, schedules, availability, certificates, and other course-specific details with the academy before enrollment.</p>', 'text'],
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

            'footer_about_text' => ['Golden Eye Academy provides practical courses, classes, workshops and academic programs for students, professionals, schools and organizations.', 'text'],
            'footer_faq_title' => ['Student Support', 'text'],
            'footer_quick_link_title' => 'Academy Links',
            'footer_contact_title' => ['Find Us', 'text'],
            'footer_social_title' => ['Follow Our Journey', 'text'],
            'footer_newsletter_desc' => ['Get course updates, batch reminders, and practical learning notes.', 'text'],
            'course_confirmation_note' => ['Confirm current batch timing, seat availability, and instructor details with the academy before enrollment.', 'text'],

            'career_highlight_1' => ['Enrollment support before classes', 'text'],
            'career_highlight_2' => ['Practical classes and academic support', 'text'],
            'career_highlight_3' => ['Job, study, and language goals', 'text'],
            'career_highlight_4' => ['Follow-up from the academy team', 'text'],
            'contact_success_message' => ['Your inquiry has been received. Our team will contact you shortly.', 'text'],
            'newsletter_success_message' => ['You are subscribed. We will send relevant course and class updates.', 'text'],
            'enroll_success_message' => ['Thank you! We received your inquiry. Our team will contact you soon.', 'text'],
        ];

        foreach (GoldenEyeContentBaseline::siteSettings() as $key => $setting) {
            $settings[$key] = [$setting['value'], $setting['type']];
        }

        foreach ($settings as $key => $setting) {
            [$value, $type] = is_array($setting) ? $setting : [$setting, 'text'];

            $attributes = [
                'value' => $value,
                'type' => $type,
            ];

            if (in_array($key, self::ENVIRONMENT_BOUND_KEYS, true)) {
                SiteSetting::firstOrCreate(['key' => $key], $attributes);
            } else {
                SiteSetting::updateOrCreate(['key' => $key], $attributes);
            }

            cache()->forget("setting_{$key}");
        }

        cache()->forget('site_settings');
    }
}
