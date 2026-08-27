<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\FAQ;
use App\Models\JoinNowQuery;
use App\Models\Notice;
use App\Models\SiteSetting;
use App\Models\Teacher;
use Database\Seeders\LiveSiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Phase6cConversionRemediationTest extends TestCase
{
    use RefreshDatabase;

    public function test_navigation_and_explicit_course_help_links_are_not_inferred_as_campaign_popup_triggers(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('href="'.route('for-students').'" data-cta="audience-student"', $html);
        $this->assertStringContainsString('href="'.route('for-parents').'" data-cta="audience-parent"', $html);
        $this->assertStringContainsString('href="'.route('study-abroad-guidance').'" data-cta="audience-study-abroad"', $html);
        $this->assertStringContainsString('href="'.route('job-computer-skills').'" data-cta="audience-job-computer-skills"', $html);
        $this->assertStringContainsString('data-cta="hero-course-help"', $html);
        $this->assertStringContainsString("link.dataset.popupTrigger === 'campaign'", $html);
        $this->assertStringNotContainsString("label.includes('course information')", $html);
        $this->assertStringNotContainsString("cta.includes('course-help')", $html);

        $xpath = $this->xpath($html);
        $audienceLinks = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " audience-card-link-wrapper ")]');
        $courseHelpLinks = $xpath->query('//*[@data-cta="hero-course-help"]');

        $this->assertSame(4, $audienceLinks->length);
        $this->assertSame(1, $courseHelpLinks->length);

        foreach ($audienceLinks as $link) {
            $this->assertSame('', $link->getAttribute('data-popup-trigger'));
        }

        $this->assertSame('', $courseHelpLinks->item(0)?->getAttribute('data-popup-trigger'));
    }

    public function test_campaign_popup_cms_controls_and_automatic_behavior_remain_independent(): void
    {
        $this->seed(LiveSiteSeeder::class);
        Notice::query()->delete();

        foreach ([
            'popup_status' => 'active',
            'popup_title' => 'Staff controlled campaign',
            'popup_subtitle' => 'Staff controlled campaign details.',
            'popup_button_text' => 'Ask for Course Help',
            'popup_register_link' => '/join-now?source_page=campaign-test',
        ] as $key => $value) {
            SiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value, 'type' => 'text']);
        }

        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('siteNoticePopup', false)
            ->assertSee('Staff controlled campaign', false)
            ->assertSee('Staff controlled campaign details.', false)
            ->assertSee('/join-now?source_page=campaign-test', false)
            ->assertSee('const autoDelayMs = 12000', false)
            ->assertSee('(window.scrollY / scrollable) >= 0.25', false)
            ->assertSee('course_help_popup_closed_', false)
            ->assertSee('popup_dismissed_', false);

        SiteSetting::query()->where('key', 'popup_status')->update(['value' => 'inactive']);
        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('siteNoticePopup', false)
            ->assertDontSee('Staff controlled campaign', false);
    }

    public function test_course_faqs_render_complete_sanitized_answers_for_one_or_many_assignments(): void
    {
        $course = Course::factory()->create([
            'name' => 'Course FAQ Acceptance',
            'slug' => 'course-faq-acceptance',
            'status' => 'active',
        ]);
        $fullAnswer = '<p>'.str_repeat('Complete course FAQ wording remains readable. ', 5).'<strong>Final decision detail.</strong></p><ul><li>Bring your questions</li></ul>';
        $firstFaq = FAQ::factory()->create([
            'question' => 'Can I read the complete answer?',
            'answer' => $fullAnswer,
            'status' => 'active',
            'order_priority' => 1,
        ]);
        $secondFaq = FAQ::factory()->create([
            'question' => 'Is another assigned answer shown?',
            'answer' => '<p>Yes, every active assigned answer is available.</p>',
            'status' => 'active',
            'order_priority' => 2,
        ]);
        $inactiveFaq = FAQ::factory()->create([
            'question' => 'Inactive internal question',
            'answer' => '<p>This must stay private.</p>',
            'status' => 'inactive',
            'order_priority' => 3,
        ]);

        $course->faqs()->sync([$firstFaq->id]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Can I read the complete answer?', false)
            ->assertSee('Final decision detail.', false)
            ->assertSee('<strong>Final decision detail.</strong>', false)
            ->assertSee('<ul><li>Bring your questions</li></ul>', false)
            ->assertSee('data-bs-toggle="collapse"', false);

        $course->faqs()->sync([$firstFaq->id, $secondFaq->id, $inactiveFaq->id]);

        $response = $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Can I read the complete answer?', false)
            ->assertSee('Is another assigned answer shown?', false)
            ->assertDontSee('Inactive internal question', false)
            ->assertDontSee('This must stay private.', false);

        $this->assertSame(2, substr_count($response->getContent(), 'class="faq-premium-item'));

        $this->get(route('faq'))
            ->assertOk()
            ->assertSee('Can I read the complete answer?', false)
            ->assertSee('Final decision detail.', false);
    }

    public function test_course_with_no_assigned_faqs_does_not_render_an_empty_course_faq_section(): void
    {
        $course = Course::factory()->create([
            'slug' => 'course-without-faqs',
            'status' => 'active',
        ]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertDontSee('Questions before joining this course', false)
            ->assertDontSee('courseFaqAccordion', false);
    }

    public function test_course_help_shows_one_authoritative_course_context_and_allows_switching(): void
    {
        Mail::fake();

        $courseA = Course::factory()->create([
            'name' => 'Course A',
            'slug' => 'course-a',
            'status' => 'active',
        ]);
        $courseB = Course::factory()->create([
            'name' => 'Course B',
            'slug' => 'course-b',
            'status' => 'active',
        ]);

        $this->get(route('join-now', [
            'course' => $courseA->slug,
            'selected_course' => $courseA->slug,
            'source_page' => 'course-detail',
            'source_section' => 'course-detail-hero',
            'inquiry_intent' => 'course_guidance',
        ]))
            ->assertOk()
            ->assertSee('You are asking about:', false)
            ->assertSee('Course A', false)
            ->assertSee('data-selected-course-summary', false)
            ->assertSee('value="course-a" selected', false)
            ->assertSee('value="course-b"', false)
            ->assertSee('Privacy Policy', false)
            ->assertSee('href="'.route('privacy-policy').'"', false);

        $this->post(route('join-now-submit'), [
            'full_name' => 'Phase Six C Buyer',
            'phone' => '9823456780',
            'help_topic' => 'Choosing a course',
            'course' => $courseB->slug,
            'selected_course' => $courseA->slug,
            'source_page' => 'course-detail',
            'source_section' => 'course-detail-hero',
            'inquiry_intent' => 'course_guidance',
        ])->assertRedirect();

        $this->assertDatabaseHas(JoinNowQuery::class, [
            'firstName' => 'Phase',
            'lastName' => 'Six C Buyer',
            'course_id' => $courseB->id,
            'course_slug' => $courseB->slug,
            'selected_course' => $courseB->slug,
            'source_section' => 'course-detail-hero',
        ]);
    }

    public function test_contact_uses_the_existing_opening_hours_setting_without_an_invented_fallback(): void
    {
        SiteSetting::query()->create([
            'key' => 'opening_hours',
            'value' => 'Sun-Fri: 7:00 AM - 6:00 PM',
            'type' => 'text',
        ]);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Opening hours', false)
            ->assertSee('Sun-Fri: 7:00 AM - 6:00 PM', false);

        SiteSetting::query()->where('key', 'opening_hours')->update(['value' => '']);
        cache()->flush();

        $this->get(route('contact'))
            ->assertOk()
            ->assertDontSee('Opening hours', false)
            ->assertDontSee('Sun-Fri: 7:00 AM - 6:00 PM', false);
    }

    public function test_course_outline_and_faculty_copy_are_customer_facing_without_duplicate_detail(): void
    {
        $teacher = Teacher::factory()->create([
            'name' => 'Course Teacher',
            'designation' => 'Web Development Instructor',
            'bio' => 'Teaches practical web development.',
            'status' => 'active',
        ]);
        $course = Course::factory()->create([
            'name' => 'Customer Friendly Course',
            'slug' => 'customer-friendly-course',
            'course_outline' => "HTML foundations\nCSS foundations\nJavaScript foundations",
            'instructor' => $teacher->name,
            'teacher_id' => $teacher->id,
            'status' => 'active',
        ]);

        $response = $this->get(route('courses-detail', $course->slug))->assertOk();
        $html = $response->getContent();

        $response
            ->assertSee('Published course outline', false)
            ->assertSee('Listed learning areas', false)
            ->assertSee('Courses taught:', false)
            ->assertSee('Faculty profile', false)
            ->assertDontSee('Course outline order', false)
            ->assertDontSee('Profile note:', false)
            ->assertDontSee('Matched courses:', false)
            ->assertDontSee('Matched faculty profile', false)
            ->assertDontSee('details that are not stored on this page', false);

        $this->assertSame(1, substr_count($html, 'HTML foundations'));
        $this->assertSame(1, substr_count($html, 'CSS foundations'));
        $this->assertSame(1, substr_count($html, 'JavaScript foundations'));
    }

    public function test_whatsapp_contract_remains_unchanged(): void
    {
        $this->seed(LiveSiteSeeder::class);
        SiteSetting::query()->where('key', 'whatsapp_number')->update(['value' => '9779856058599']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('class="whatsapp-chat-cta"', false)
            ->assertSee('data-cta="whatsapp-chat"', false)
            ->assertSee('https://wa.me/9779856058599?text=', false);
    }

    private function xpath(string $html): \DOMXPath
    {
        $document = new \DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();

        return new \DOMXPath($document);
    }
}
