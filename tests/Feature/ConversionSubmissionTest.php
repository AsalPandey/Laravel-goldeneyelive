<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversionSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_submission_tracks_conversion_source(): void
    {
        $response = $this->post(route('contact-submit'), [
            'name' => 'Asha Sharma',
            'email' => 'asha@example.com',
            'phone' => '9800000000',
            'subject' => 'Course Help',
            'message' => 'I want help choosing a course.',
            'lead_source' => 'homepage_quick_inquiry',
            'landing_page' => 'https://goldeneye.edu.np/',
            'cta_id' => 'homepage-final-contact',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('contacts', [
            'email' => 'asha@example.com',
            'lead_source' => 'homepage_quick_inquiry',
            'landing_page' => 'https://goldeneye.edu.np/',
            'cta_id' => 'homepage-final-contact',
        ]);
    }

    public function test_join_now_submission_tracks_conversion_source(): void
    {
        $course = Course::factory()->create([
            'name' => 'IELTS Masterclass',
            'slug' => 'ielts-masterclass',
            'status' => 'active',
        ]);

        $response = $this->post(route('join-now-submit'), [
            'firstName' => 'Niraj',
            'lastName' => 'Gurung',
            'email' => 'niraj@example.com',
            'phone' => '9811111111',
            'course' => $course->slug,
            'contactMethod' => 'WhatsApp',
            'lead_source' => 'service_pillar',
            'landing_page' => 'https://goldeneye.edu.np/#service-pillars',
            'cta_id' => 'service-pillar-global-launchpad',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('join_now_queries', [
            'email' => 'niraj@example.com',
            'course' => 'IELTS Masterclass',
            'lead_source' => 'service_pillar',
            'landing_page' => 'https://goldeneye.edu.np/#service-pillars',
            'cta_id' => 'service-pillar-global-launchpad',
        ]);
    }
}
