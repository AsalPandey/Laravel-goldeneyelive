<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\Contact;
use App\Models\Course;
use App\Models\JoinNowQuery;
use App\Models\NewsLetter;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSettingsCleanupCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_dry_run_reports_changes_without_updating_database(): void
    {
        SiteSetting::create([
            'key' => 'hero_cta_1_text',
            'value' => 'Ask for Course Guidance',
            'type' => 'text',
        ]);

        $this->artisan('goldeneye:normalize-public-settings', ['--dry-run' => true])
            ->expectsOutputToContain('would change')
            ->expectsOutputToContain('Dry-run total changes: 1')
            ->assertExitCode(0);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'hero_cta_1_text',
            'value' => 'Ask for Course Guidance',
        ]);
    }

    public function test_real_run_normalizes_stale_site_setting_values(): void
    {
        SiteSetting::insert([
            ['key' => 'hero_cta_1_text', 'value' => 'Ask for Course Guidance', 'type' => 'text'],
            ['key' => 'hero_cta_2_text', 'value' => 'See Courses', 'type' => 'text'],
            ['key' => 'whatsapp_cta_text', 'value' => 'Message us on WhatsApp', 'type' => 'text'],
            ['key' => 'whatsapp_button_text', 'value' => 'Message us on WhatsApp', 'type' => 'text'],
            ['key' => 'faq_btn_text', 'value' => 'Ask for Course Guidance', 'type' => 'text'],
            ['key' => 'whatsapp_cta_subtext', 'value' => 'CASUAL QUESTIONS. QUICK REPLY.', 'type' => 'text'],
            ['key' => 'whatsapp_prefill_message', 'value' => 'CASUAL QUESTIONS. QUICK REPLY.', 'type' => 'text'],
            ['key' => 'sticky_cta_text', 'value' => 'Custom Safe Label', 'type' => 'text'],
        ]);

        $this->artisan('goldeneye:normalize-public-settings')
            ->expectsOutputToContain('Total changes: 7')
            ->assertExitCode(0);

        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'hero_cta_1_text', 'value' => 'Ask for Course Help']);
        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'hero_cta_2_text', 'value' => 'View Course Details']);
        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'whatsapp_cta_text', 'value' => 'Message on WhatsApp']);
        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'whatsapp_button_text', 'value' => 'Message on WhatsApp']);
        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'faq_btn_text', 'value' => 'Ask for Course Help']);
        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'whatsapp_cta_subtext', 'value' => '']);
        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'whatsapp_prefill_message',
            'value' => 'Hi Golden Eye Academy, I have a question about classes and enrollment.',
        ]);
        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'sticky_cta_text', 'value' => 'Custom Safe Label']);
    }

    public function test_real_run_normalizes_notice_button_text_based_on_link_intent(): void
    {
        Notice::factory()->create([
            'link' => '/courses-all',
            'button_text' => 'Explore Programs',
        ]);
        Notice::factory()->create([
            'link' => '/join-now?course=undecided',
            'button_text' => 'Ask for Course Guidance',
        ]);
        Notice::factory()->create([
            'link' => 'https://wa.me/9779800000000',
            'button_text' => 'Message us on WhatsApp',
        ]);

        $this->artisan('goldeneye:normalize-public-settings')
            ->expectsOutputToContain('Total changes: 3')
            ->assertExitCode(0);

        $this->assertDatabaseHas(Notice::class, ['link' => '/courses-all', 'button_text' => 'View Course Details']);
        $this->assertDatabaseHas(Notice::class, ['link' => '/join-now?course=undecided', 'button_text' => 'Ask for Course Help']);
        $this->assertDatabaseHas(Notice::class, ['link' => 'https://wa.me/9779800000000', 'button_text' => 'Message on WhatsApp']);
    }

    public function test_real_run_normalizes_service_pillar_cta_label_based_on_url_intent(): void
    {
        ServicePillar::factory()->create([
            'title' => 'Course Pillar',
            'slug' => 'course-pillar',
            'cta_url' => '/courses/ielts-masterclass',
            'cta_label' => 'See Courses',
        ]);
        ServicePillar::factory()->create([
            'title' => 'Help Pillar',
            'slug' => 'help-pillar',
            'cta_url' => '/contact',
            'cta_label' => 'Contact Us',
        ]);
        ServicePillar::factory()->create([
            'title' => 'WhatsApp Pillar',
            'slug' => 'whatsapp-pillar',
            'cta_url' => 'https://wa.me/9779800000000',
            'cta_label' => 'Message us on WhatsApp',
        ]);

        $this->artisan('goldeneye:normalize-public-settings')
            ->expectsOutputToContain('Total changes: 3')
            ->assertExitCode(0);

        $this->assertDatabaseHas(ServicePillar::class, ['slug' => 'course-pillar', 'cta_label' => 'View Course Details']);
        $this->assertDatabaseHas(ServicePillar::class, ['slug' => 'help-pillar', 'cta_label' => 'Ask for Course Help']);
        $this->assertDatabaseHas(ServicePillar::class, ['slug' => 'whatsapp-pillar', 'cta_label' => 'Message on WhatsApp']);
    }

    public function test_running_command_twice_causes_no_additional_changes(): void
    {
        SiteSetting::create([
            'key' => 'hero_cta_text',
            'value' => 'Ask for Course Guidance',
            'type' => 'text',
        ]);

        $this->artisan('goldeneye:normalize-public-settings')
            ->expectsOutputToContain('Total changes: 1')
            ->assertExitCode(0);

        $this->artisan('goldeneye:normalize-public-settings')
            ->expectsOutputToContain('Total changes: 0')
            ->assertExitCode(0);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'hero_cta_text',
            'value' => 'Ask for Course Help',
        ]);
    }

    public function test_command_does_not_modify_protected_business_data(): void
    {
        $contact = Contact::factory()->create(['subject' => 'Original contact subject']);
        $enrollment = JoinNowQuery::factory()->create(['course' => 'Original enrollment course']);
        $course = Course::factory()->create(['name' => 'Original Course Name']);
        $user = User::factory()->create(['name' => 'Original User Name']);
        $testimonial = Testimonial::factory()->create(['content' => 'Original testimonial content']);
        $teacher = Teacher::factory()->create(['name' => 'Original Teacher Name']);
        $newsletter = NewsLetter::factory()->create(['email' => 'original@example.com']);
        $analyticsEvent = AnalyticsEvent::create([
            'event_name' => 'cta_click',
            'source_page' => 'home',
            'source_section' => 'hero',
            'cta_label' => 'Ask for Course Guidance',
            'occurred_at' => now(),
            'metadata' => ['kept' => 'yes'],
        ]);

        SiteSetting::create([
            'key' => 'hero_cta_text',
            'value' => 'Ask for Course Guidance',
            'type' => 'text',
        ]);

        $this->artisan('goldeneye:normalize-public-settings')
            ->assertExitCode(0);

        $this->assertDatabaseHas(Contact::class, ['id' => $contact->id, 'subject' => 'Original contact subject']);
        $this->assertDatabaseHas(JoinNowQuery::class, ['id' => $enrollment->id, 'course' => 'Original enrollment course']);
        $this->assertDatabaseHas(Course::class, ['id' => $course->id, 'name' => 'Original Course Name']);
        $this->assertDatabaseHas(User::class, ['id' => $user->id, 'name' => 'Original User Name']);
        $this->assertDatabaseHas(Testimonial::class, ['id' => $testimonial->id, 'content' => 'Original testimonial content']);
        $this->assertDatabaseHas(Teacher::class, ['id' => $teacher->id, 'name' => 'Original Teacher Name']);
        $this->assertDatabaseHas(NewsLetter::class, ['id' => $newsletter->id, 'email' => 'original@example.com']);
        $this->assertDatabaseHas(AnalyticsEvent::class, ['id' => $analyticsEvent->id, 'cta_label' => 'Ask for Course Guidance']);
    }

    public function test_public_pages_do_not_show_stale_guidance_label_after_command(): void
    {
        SiteSetting::insert([
            ['key' => 'hero_cta_1_text', 'value' => 'Ask for Course Guidance', 'type' => 'text'],
            ['key' => 'hero_cta_text', 'value' => 'Ask for Course Guidance', 'type' => 'text'],
            ['key' => 'popup_button_text', 'value' => 'Ask for Course Guidance', 'type' => 'text'],
            ['key' => 'popup_status', 'value' => 'active', 'type' => 'text'],
        ]);

        Notice::factory()->create([
            'title' => 'Stale Notice',
            'link' => '/join-now?course=undecided',
            'button_text' => 'Ask for Course Guidance',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        $this->artisan('goldeneye:normalize-public-settings')
            ->assertExitCode(0);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Ask for Course Help', false)
            ->assertDontSee('Ask for Course Guidance', false);
    }
}
