<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\JoinNowQuery;
use App\Models\Notice;
use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicCtaRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_navigation_is_simplified_for_desktop_and_mobile(): void
    {
        $this->seed(DatabaseSeeder::class);

        SiteSetting::where('key', 'whatsapp_number')->update(['value' => '+977 980-000-0000']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('site-desktop-nav', false)
            ->assertSee('Home', false)
            ->assertSee('Courses', false)
            ->assertSee('About', false)
            ->assertSee('Blog', false)
            ->assertSee('Contact', false)
            ->assertSee('data-cta="navbar-course-help"', false)
            ->assertSee('Ask for Course Help', false)
            ->assertSee('All Courses', false)
            ->assertSee('IELTS / PTE', false)
            ->assertSee('Japanese / Korean', false)
            ->assertSee('Computer Skills', false)
            ->assertSee('Web Development', false)
            ->assertSee('site-mobile-nav', false)
            ->assertSee('IELTS / PTE', false)
            ->assertSee('Languages', false)
            ->assertSee('data-cta="mobile-menu-whatsapp"', false)
            ->assertSee('Message on WhatsApp', false)
            ->assertDontSee('All Career Paths', false)
            ->assertDontSee('Ask for Course Guidance', false);
    }

    public function test_notice_popup_markup_uses_controlled_course_help_behavior(): void
    {
        cache()->flush();

        Notice::create([
            'title' => 'Admissions Guidance',
            'subtitle' => 'Choose the right course before enrolling.',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('siteNoticePopup', false)
            ->assertSee('openSiteNoticePopup', false)
            ->assertSee('course_help_popup_closed_', false)
            ->assertSee('window.setTimeout(maybeAutoOpen, autoDelayMs)', false)
            ->assertSee('Ask for Course Help', false);
    }

    public function test_homepage_renders_sales_ctas_and_whatsapp_chat_prompt(): void
    {
        $this->seed(DatabaseSeeder::class);

        SiteSetting::where('key', 'whatsapp_number')->update(['value' => '+977 980-000-0000']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('home-hero', false)
            ->assertSee('data-cta="hero-course-help"', false)
            ->assertSee('Ask for Course Help', false)
            ->assertSee('View Course Details', false)
            ->assertSee('Message on WhatsApp', false)
            ->assertSee('https://wa.me/9779800000000?text=', false)
            ->assertSee('data-cta="hero-course-details"', false)
            ->assertSee('source_page=home', false)
            ->assertSee('source_section=hero', false)
            ->assertSee('inquiry_intent=course_guidance', false)
            ->assertDontSee('data-cta="mobile-sticky-quick-help"', false)
            ->assertDontSee('Text us your goal.', false)
            ->assertSee('Trusted by students in Pokhara since 2008', false)
            ->assertSee('Established in 2008', false)
            ->assertSee('Srijana Chowk, Pokhara, Nepal', false)
            ->assertSee('Phone: 061-572599', false)
            ->assertSee('Email: goldeneyeacademy2008@gmail.com', false)
            ->assertSee('Morning, day, and evening batches', false)
            ->assertSee('I am a Student', false)
            ->assertSee('I am a Parent', false)
            ->assertSee('I need IELTS / PTE', false)
            ->assertSee('I want Job/Computer Skills', false)
            ->assertSee('IELTS / PTE', false)
            ->assertSee('Popular courses', false)
            ->assertSee('Course categories', false)
            ->assertSee('Why Golden Eye Academy', false)
            ->assertSee('Student results', false)
            ->assertSee('Course completed:', false)
            ->assertSee('Specific progress/result:', false)
            ->assertSee('Instructors', false)
            ->assertSee('Subject expertise:', false)
            ->assertSee('Class support:', false)
            ->assertSee('Courses taught:', false)
            ->assertSee('Credibility note:', false)
            ->assertSee('External social proof', false)
            ->assertSee('verified Google review proof', false)
            ->assertSee('For parents', false)
            ->assertSee('FAQ', false)
            ->assertSee('Need course and batch information?', false)
            ->assertDontSee('Start with the path that sounds like you.', false)
            ->assertDontSee('Ask What Fits Me', false)
            ->assertDontSee('Ask About This Course', false)
            ->assertDontSee('Ask for Course Guidance', false)
            ->assertDontSee('Message us on WhatsApp', false)
            ->assertDontSee('Casual questions. Quick reply.', false)
            ->assertDontSee('Book Free Counseling', false)
            ->assertDontSee('Free course guidance', false)
            ->assertDontSee('data-cta="homepage-fallback-course-counseling"', false)
            ->assertDontSee('data-cta="fallback-category-counseling"', false);
    }

    public function test_join_now_page_uses_guidance_first_conversion_copy(): void
    {
        Course::factory()->create([
            'name' => 'IELTS Preparation',
            'slug' => 'ielts-masterclass',
            'status' => 'active',
        ]);

        $this->get(route('join-now', [
            'course' => 'ielts-masterclass',
            'selected_course' => 'ielts-masterclass',
            'source_page' => 'home',
            'source_section' => 'homepage-course-card',
            'inquiry_intent' => 'course_guidance',
        ]))
            ->assertOk()
            ->assertSee('Ask for Course Help', false)
            ->assertSee('course-help-page-header', false)
            ->assertSee('course-help-card', false)
            ->assertSee('Find the right course before you enroll', false)
            ->assertSee('I need help choosing the right program', false)
            ->assertSee('name="selected_course" value="ielts-masterclass"', false)
            ->assertSee('name="source_page" value="home"', false)
            ->assertSee('name="source_section" value="homepage-course-card"', false)
            ->assertSee('name="inquiry_intent" value="course_guidance"', false)
            ->assertSee('class="btn btn-link text-primary small fw-bold text-decoration-none p-0 optional-details-toggle"', false)
            ->assertSee('aria-controls="extraDetails"', false)
            ->assertSee('aria-invalid="false"', false)
            ->assertSee('aria-describedby="phoneHelp phoneClientError', false)
            ->assertSee('id="phoneHelp"', false)
            ->assertSee('id="phoneClientError"', false)
            ->assertSee('Add more details', false)
            ->assertSee('pattern="(?:\\+977(?:97|98)[0-9]{8}|(?:97|98)[0-9]{8}|0[0-9]{9})"', false)
            ->assertSee('data-cta="join-now-form-submit"', false);
    }

    public function test_contact_page_forms_have_accessible_labels_errors_and_phone_help(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('<label for="name">Full Name</label>', false)
            ->assertSee('<label for="phone">Phone Number</label>', false)
            ->assertSee('<label for="subject">Interested Pathway</label>', false)
            ->assertSee('<label for="email">Email Address</label>', false)
            ->assertSee('<label for="message">What goal or course question can we help with?</label>', false)
            ->assertSee('aria-describedby="phoneHelp', false)
            ->assertSee('id="phoneHelp"', false)
            ->assertSee('aria-invalid="false"', false)
            ->assertSee('pattern="(?:\\+977(?:97|98)[0-9]{8}|(?:97|98)[0-9]{8}|0[0-9]{9})"', false)
            ->assertSee('Ask for Course Help', false);
    }

    public function test_public_css_has_accessible_focus_form_and_mobile_widget_rules(): void
    {
        $css = file_get_contents(public_path('site/css/style.css'));

        $this->assertStringContainsString('a:focus-visible', $css);
        $this->assertStringContainsString('min-height: 44px', $css);
        $this->assertStringContainsString('.form-conversational .form-select', $css);
        $this->assertStringContainsString('.form-conversational .form-floating > .form-control::placeholder', $css);
        $this->assertStringContainsString('color: transparent', $css);
        $this->assertStringContainsString('scroll-margin-top: 88px', $css);
        $this->assertStringContainsString('border: 1px solid #cbd5e1 !important', $css);
        $this->assertStringContainsString('.form-conversational .form-control.is-invalid', $css);
        $this->assertStringContainsString('.whatsapp-btn-container', $css);
        $this->assertStringContainsString('bottom: max(82px, env(safe-area-inset-bottom))', $css);
    }

    public function test_undecided_course_help_request_can_capture_a_lead(): void
    {
        Mail::fake();

        $this->withoutMiddleware();

        $this->from(route('join-now', ['course' => 'undecided']))
            ->post(route('join-now-submit'), [
                'firstName' => 'Asha',
                'lastName' => 'Sharma',
                'email' => 'asha@example.com',
                'phone' => '9823456780',
                'course' => 'undecided',
                'lead_source' => 'audience-students',
                'landing_page' => route('home'),
                'cta_id' => 'audience-students',
            ])
            ->assertRedirect(route('join-now', ['course' => 'undecided']));

        $this->assertDatabaseHas(JoinNowQuery::class, [
            'email' => 'asha@example.com',
            'course' => 'Need help choosing a program',
            'course_id' => null,
            'course_slug' => null,
            'lead_source' => 'audience-students',
            'cta_id' => 'audience-students',
        ]);
    }
}
