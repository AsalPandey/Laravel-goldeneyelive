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
            'site_name' => ['GoldenEye', 'text'],
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

            'meta_title' => ['GoldenEye Academy - Career, College, Skills and Test Prep in Pokhara', 'text'],
            'meta_keywords' => ['GoldenEye Academy, IELTS Pokhara, PTE Pokhara, Korean class, Japanese class, computer course, web development Pokhara, course guidance Nepal', 'text'],
            'meta_description' => ['GoldenEye Academy helps students, parents, study abroad applicants, and job seekers choose practical courses through quick course guidance in Pokhara.', 'text'],
            'aeo_summary' => ['GoldenEye Academy provides course guidance, test preparation, language classes, computer skills, web development, and practical support for learners in Pokhara.', 'text'],
            'schema_markup' => [json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'EducationalOrganization',
                'name' => 'GoldenEye Academy',
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
            'robots_txt' => ["User-agent: *\nDisallow: /admin\nDisallow: /login\n\nSitemap: /sitemap.xml", 'text'],
            'geo_latitude' => ['28.2126', 'text'],
            'geo_longitude' => ['83.9786', 'text'],
            'google_maps_embed' => ['https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3515.823438031535!2d83.97858907530514!3d28.212555675898857!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399595ab361d716d%3A0xcf953250b7312903!2sGoldenEye%20Academy!5e0!3m2!1sen!2snp!4v1714100000000!5m2!1sen!2snp', 'text'],
            'recaptcha_site_key' => ['', 'text'],
            'recaptcha_secret_key' => ['', 'text'],
            'image_size_limit' => ['2048', 'text'],

            'hero_image' => ['site/img/carousel-1.png', 'image'],
            'hero_badge_text' => ['GoldenEye Academy, Pokhara', 'text'],
            'hero_title' => ['Choose the right course with clear guidance.', 'text'],
            'hero_hook_headline' => ['Choose the right course with clear guidance.', 'text'],
            'hero_hook_body' => ['Confused about what to study next? Do not choose a course randomly. GoldenEye Academy helps students, parents, study abroad applicants, and job seekers compare the right path before enrollment.', 'text'],
            'hero_subtitle' => ['Tell us your goal. We will help you compare course options by timing, fee, current level, and next step.', 'text'],
            'hero_cta_text' => ['Ask for Course Help', 'text'],
            'hero_cta_1_text' => ['Ask for Course Help', 'text'],
            'hero_cta_2_text' => ['View Course Details', 'text'],

            'stat_1_val' => ['15+', 'text'],
            'stat_1_lab' => ['Years of Guidance', 'text'],
            'stat_2_val' => ['5,000+', 'text'],
            'stat_2_lab' => ['Learners Supported', 'text'],
            'stat_3_val' => ['Regular', 'text'],
            'stat_3_lab' => ['Mock Test & Feedback', 'text'],
            'stat_4_val' => ['2 hr', 'text'],
            'stat_4_lab' => ['Typical Response', 'text'],

            'pathway_tagline' => ['Choose by goal', 'text'],
            'pathway_title' => ['Start with the path that sounds like you.', 'text'],
            'courses_title' => ['Compare Programs by Goal', 'text'],
            'courses_header_title' => ['Professional Courses', 'text'],
            'courses_subtitle' => ['Shortlist courses by outcome, then talk to us before choosing your batch.', 'text'],
            'courses_all_tagline' => ['Course options', 'text'],
            'courses_all_title' => ['Choose Your', 'text'],
            'category_header_badge' => ['Program Category', 'text'],
            'category_title_prefix' => ['Explore', 'text'],
            'category_tagline' => ['Focused Pathways', 'text'],

            'about_image' => ['site/img/about.jpg', 'image'],
            'about_title' => ['Guidance-first learning since 2008', 'text'],
            'about_text' => ['GoldenEye Academy exists for one reason: learners should not waste time, money, or confidence on the wrong course. We help you decide first, then train with structure.', 'text'],
            'about_point_1' => ['Quick course guidance', 'text'],
            'about_point_2' => ['Language, test prep, IT, and office skills', 'text'],
            'about_point_3' => ['Support for students, parents, and job seekers', 'text'],
            'about_point_4' => ['Practical classes with clear outcomes', 'text'],
            'about_content_title' => ['Why GoldenEye Academy works', 'text'],
            'about_content' => ['We combine course guidance, practical teaching, and follow-up so learners choose courses with a clear reason.', 'text'],
            'about_section_tagline' => ['15+ Years of Trust', 'text'],
            'about_section_title' => ['Pokhara trusted us because guidance comes first', 'text'],
            'about_header_title' => ['About GoldenEye Academy', 'text'],
            'about_page_content' => ['<h2>Our Approach</h2><p>GoldenEye Academy starts with simple guidance because students should not choose courses randomly. We help learners compare goals, timeline, current level, and practical outcomes before enrollment.</p><p>After that, training is built around structured classes, practice, and clear next steps.</p>', 'text'],
            'about_feat_1_title' => ['Guidance Before Enrollment', 'text'],
            'about_feat_1_desc' => ['Learners understand the right route before committing to a course.', 'text'],
            'about_feat_2_title' => ['Practical Learning', 'text'],
            'about_feat_2_desc' => ['Courses focus on usable skills, mock tests, projects, and workplace confidence.', 'text'],
            'about_feat_3_title' => ['Parent-Friendly Decisions', 'text'],
            'about_feat_3_desc' => ['Families can ask about outcomes, fees, timing, and student readiness.', 'text'],
            'about_feat_4_title' => ['Follow-Up Support', 'text'],
            'about_feat_4_desc' => ['The team supports learners beyond the first inquiry.', 'text'],

            'founder_name' => ['Shankar Pokharel', 'text'],
            'founder_position' => ['Founder and Director', 'text'],
            'founder_message' => ['Our goal is simple: help learners make the right decision first, then train them with practical discipline and confidence.', 'text'],
            'founder_image' => ['site/img/message-chairperson.jpg', 'image'],
            'founder_section_tagline' => ["Director's Message", 'text'],
            'founder_section_title' => ['Choose the path with clarity', 'text'],

            'teachers_title' => ['Faculty and Course Help Team', 'text'],
            'teachers_subtitle' => ['Meet the people who guide your next step.', 'text'],
            'testimonials_title' => ['Student Results and Feedback', 'text'],
            'blog_title' => ['Academy Blog', 'text'],
            'blog_header_title' => ['Academy Blog', 'text'],
            'blog_subtitle' => ['Guides, updates, and decisions that help you move faster.', 'text'],
            'blog_tagline' => ['Academy Insights', 'text'],
            'blog_section_title' => ['Latest From GoldenEye', 'text'],
            'blog_cta_title' => ['Need advice before choosing?', 'text'],
            'blog_cta_desc' => ['Tell us your goal and our team will suggest the right path.', 'text'],
            'blog_cta_btn' => ['Ask for Course Help', 'text'],
            'recent_posts_title' => ['Recent Guides', 'text'],

            'faq_header_title' => ['Frequently Asked Questions', 'text'],
            'faq_lead_title' => ['Still deciding? Ask first.', 'text'],
            'faq_btn_text' => ['Ask for Course Help', 'text'],
            'faq_page_content' => ['<h2>Frequently Asked Questions</h2><p>Use these answers to understand course selection, class timing, and enrollment. If you are unsure, send a quick course-help request.</p>', 'text'],
            'contact_header_title' => ['Contact GoldenEye Academy', 'text'],
            'contact_page_content' => ['<h3>Message our team</h3><p>Share your current situation and goal. We will help you compare the right course or next step before enrollment.</p>', 'text'],
            'enroll_header_title' => ['Ask for Course Help', 'text'],
            'enroll_section_title' => ['Tell us your goal. We will help map the next step.', 'text'],
            'privacy_header_title' => ['Privacy Policy', 'text'],
            'privacy_policy_content' => ['<h2>Privacy Commitment</h2><p>We use inquiry and enrollment details only to respond, guide students, manage admissions, and improve academy communication.</p>', 'text'],
            'terms_header_title' => ['Terms and Conditions', 'text'],
            'terms_and_conditions_content' => ['<h2>Enrollment Terms</h2><p>Course availability, timing, fees, and batch details should be confirmed with the academy team before final enrollment.</p>', 'text'],

            'popup_status' => ['active', 'text'],
            'popup_image' => ['site/img/carousel-1.png', 'image'],
            'popup_title' => ['Still deciding? Ask before enrollment.', 'text'],
            'popup_subtitle' => ['Tell us your goal and we will recommend the right course path before you spend money on the wrong option.', 'text'],
            'popup_button_text' => ['Ask for Course Help', 'text'],
            'popup_register_link' => ['/join-now?course=undecided&selected_course=undecided&source_page=popup&source_section=notice-popup&inquiry_intent=course_guidance', 'text'],
            'notice_badge_text' => ['Official Update', 'text'],
            'notice_dismiss_text' => ['Dismiss Notice', 'text'],

            'whatsapp_cta_text' => ['Message on WhatsApp', 'text'],
            'whatsapp_cta_subtext' => ['', 'text'],
            'whatsapp_button_text' => ['Message on WhatsApp', 'text'],
            'whatsapp_prefill_message' => ['Hi GoldenEye Academy, I have a quick question. Can you help me choose the right course?', 'text'],
            'sticky_cta_text' => ['Ask for Course Help', 'text'],
            'sticky_cta_badge' => ['Quick Help', 'text'],
            'sticky_cta_desc' => ['Message us with your course question.', 'text'],
            'inquiry_tab_text' => ['Need Guidance?', 'text'],
            'inquiry_title' => ['Get your course recommendation', 'text'],
            'inquiry_subtitle' => ['Share your goal. We will help you compare the right next step before enrollment.', 'text'],
            'navbar_menu_label' => ['Navigate', 'text'],

            'footer_about_text' => ['GoldenEye Academy helps students, parents, abroad applicants, and job seekers choose practical learning paths through quick course guidance.', 'text'],
            'footer_faq_title' => ['Student Support', 'text'],
            'footer_quick_link_title' => 'Academy Links',
            'footer_contact_title' => ['Find Us', 'text'],
            'footer_social_title' => ['Follow Our Journey', 'text'],
            'footer_newsletter_desc' => ['Get course updates, batch reminders, and practical guidance notes.', 'text'],

            'career_highlight_1' => ['Guidance before enrollment', 'text'],
            'career_highlight_2' => ['Practical course guidance', 'text'],
            'career_highlight_3' => ['Job, study, and language pathways', 'text'],
            'career_highlight_4' => ['Follow-up from the academy team', 'text'],
            'contact_success_message' => ['Your inquiry has been received. Our team will contact you shortly.', 'text'],
            'newsletter_success_message' => ['You are subscribed. We will send relevant course and guidance updates.', 'text'],
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
