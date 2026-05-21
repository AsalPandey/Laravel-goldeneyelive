<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\Course;
use App\Models\JoinNowQuery;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AnalyticsTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_public_analytics_endpoint_stores_safe_event_payload(): void
    {
        $response = $this->postJson(route('analytics.events.store'), [
            'event_name' => 'cta_click',
            'source_page' => 'home',
            'source_section' => 'hero',
            'cta_label' => 'Ask for Course Help',
            'selected_course' => 'undecided',
            'audience_type' => 'student',
            'inquiry_intent' => 'course_guidance',
            'device_type' => 'mobile',
            'timestamp' => now()->toISOString(),
            'metadata' => [
                'cta_id' => 'hero-course-guidance',
                'phone' => '9823456780',
            ],
        ]);

        $response
            ->assertAccepted()
            ->assertJson(['ok' => true, 'tracked' => true]);

        $event = AnalyticsEvent::firstOrFail();

        $this->assertSame('cta_click', $event->event_name);
        $this->assertSame('home', $event->source_page);
        $this->assertSame('hero', $event->source_section);
        $this->assertSame('Ask for Course Help', $event->cta_label);
        $this->assertSame('undecided', $event->selected_course);
        $this->assertSame('student', $event->audience_type);
        $this->assertSame('course_guidance', $event->inquiry_intent);
        $this->assertSame('mobile', $event->device_type);
        $this->assertSame('hero-course-guidance', $event->metadata['cta_id']);
        $this->assertArrayNotHasKey('phone', $event->metadata);
    }

    public function test_analytics_can_be_disabled_without_breaking_requests(): void
    {
        SiteSetting::create([
            'key' => 'analytics_tracking_enabled',
            'value' => 'disabled',
            'type' => 'text',
        ]);

        Cache::flush();

        $this->postJson(route('analytics.events.store'), [
            'event_name' => 'cta_click',
            'source_page' => 'home',
        ])
            ->assertAccepted()
            ->assertJson(['ok' => true, 'tracked' => false]);

        $this->assertDatabaseCount('analytics_events', 0);
    }

    public function test_course_detail_page_renders_course_view_tracking_context(): void
    {
        $course = Course::factory()->create([
            'slug' => 'ielts-preparation',
            'status' => 'active',
        ]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('data-tracking-event="course_detail_view"', false)
            ->assertSee('data-selected-course="ielts-preparation"', false)
            ->assertSee('analytics\\/events', false);
    }

    public function test_course_help_submission_stores_lead_score_status_and_success_event(): void
    {
        Mail::fake();

        $course = Course::factory()->create([
            'name' => 'IELTS Preparation',
            'slug' => 'ielts-preparation',
            'status' => 'active',
        ]);

        $this->post(route('join-now-submit'), [
            'full_name' => 'Asha Sharma',
            'phone' => '9823456789',
            'help_topic' => 'Fees and timing',
            'course' => $course->slug,
            'selected_course' => $course->slug,
            'source_page' => 'course-detail',
            'source_section' => 'course-detail-hero',
            'audience_type' => 'parent',
            'inquiry_intent' => 'course_guidance',
            'preferred_batch_time' => 'Evening',
            'goal' => 'I want IELTS preparation for abroad study and need fee timing details before enrollment.',
            'queries' => 'Please call my parent after 5 PM.',
        ])->assertRedirect();

        $this->assertDatabaseHas('join_now_queries', [
            'phone' => '9823456789',
            'selected_course' => $course->slug,
            'lead_score' => 23,
            'lead_status' => 'Hot',
        ]);

        $this->assertDatabaseHas('analytics_events', [
            'event_name' => 'course_help_submit',
            'source_page' => 'course-detail',
            'source_section' => 'course-detail-hero',
            'cta_label' => 'Ask for Course Help',
            'selected_course' => $course->slug,
            'audience_type' => 'parent',
            'inquiry_intent' => 'course_guidance',
            'device_type' => 'server',
        ]);
    }

    public function test_invalid_phone_validation_attempt_is_tracked_without_phone_value(): void
    {
        $this->from(route('join-now'))->post(route('join-now-submit'), [
            'full_name' => 'Asha Sharma',
            'phone' => 'bad-phone-value',
            'help_topic' => 'Choosing a course',
            'source_page' => 'home',
            'source_section' => 'hero',
            'selected_course' => 'undecided',
            'inquiry_intent' => 'course_guidance',
        ])
            ->assertRedirect(route('join-now'))
            ->assertSessionHasErrors('phone');

        $event = AnalyticsEvent::where('event_name', 'phone_validation_error')->firstOrFail();

        $this->assertSame('home', $event->source_page);
        $this->assertSame('hero', $event->source_section);
        $this->assertSame('phone', $event->metadata['field']);
        $this->assertSame('server', $event->metadata['validation']);
        $this->assertArrayNotHasKey('phone', $event->metadata);
    }

    public function test_admin_lead_status_change_is_tracked(): void
    {
        Role::firstOrCreate(['name' => 'Admin']);

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $lead = JoinNowQuery::factory()->create([
            'status' => 'new',
            'selected_course' => 'ielts-preparation',
            'audience_type' => 'parent',
            'inquiry_intent' => 'course_guidance',
            'lead_status' => 'Hot',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.submissions.join_now.status.update', $lead), [
                'status' => 'contacted',
                'admin_notes' => 'Called parent.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('analytics_events', [
            'event_name' => 'admin_lead_status_change',
            'source_page' => 'admin',
            'source_section' => 'enrollments',
            'cta_label' => 'Update Audit Log',
            'selected_course' => 'ielts-preparation',
            'audience_type' => 'parent',
            'inquiry_intent' => 'course_guidance',
            'device_type' => 'server',
        ]);
    }
}
