<?php

namespace Tests\Feature;

use App\Mail\ContactMail;
use App\Models\Contact;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\JoinNowQuery;
use App\Models\NewsLetter;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_submits_without_recaptcha_when_recaptcha_is_not_configured(): void
    {
        Mail::fake();

        $response = $this->from(route('contact'))->post(route('contact-submit'), [
            'name' => 'Asha Sharma',
            'phone' => '9823456780',
            'email' => 'asha@example.com',
            'subject' => 'Course question',
            'message' => 'I want to know more about IELTS classes.',
        ]);

        $response->assertRedirect(route('contact'));

        $this->assertDatabaseHas(Contact::class, [
            'email' => 'asha@example.com',
            'subject' => 'Course question',
        ]);

        Mail::assertQueued(ContactMail::class);
    }

    public function test_contact_form_rejects_invalid_phone_number(): void
    {
        $response = $this->from(route('contact'))->post(route('contact-submit'), [
            'name' => 'Asha Sharma',
            'phone' => 'not-a-phone',
            'email' => 'asha@example.com',
            'subject' => 'Course question',
            'message' => 'I want to know more about IELTS classes.',
        ]);

        $response
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseMissing(Contact::class, [
            'email' => 'asha@example.com',
        ]);
    }

    public function test_join_now_form_accepts_visible_required_fields_only(): void
    {
        Mail::fake();

        $response = $this->from(route('join-now'))->post(route('join-now-submit'), [
            'full_name' => 'Asha Sharma',
            'phone' => '9823456780',
            'help_topic' => 'Choosing a course',
        ]);

        $response
            ->assertRedirect(route('join-now'))
            ->assertSessionHas('success', 'Thank you! We received your inquiry. Our team will contact you soon.');

        $this->assertDatabaseHas(JoinNowQuery::class, [
            'firstName' => 'Asha',
            'lastName' => 'Sharma',
            'email' => '',
            'phone' => '9823456780',
            'address' => '',
            'course_id' => null,
            'course_slug' => null,
            'course' => 'Need help choosing a program',
            'help_topic' => 'Choosing a course',
            'lead_score' => 5,
            'lead_status' => 'Basic',
        ]);

        Mail::assertQueued(ContactMail::class);
    }

    public function test_join_now_form_stores_optional_context_and_lead_score_for_admin(): void
    {
        Mail::fake();

        $course = Course::create([
            'name' => 'IELTS Preparation',
            'slug' => 'ielts-preparation',
            'category' => 'Language',
            'category_slug' => 'language',
            'price' => '10000',
            'duration' => '8 weeks',
            'instructor' => 'GoldenEye Team',
            'capacity' => '20',
            'description' => 'IELTS course',
            'course_outline' => 'Speaking, writing, reading, listening',
            'photo' => 'site/img/carousel-1.jpg',
            'rating_star' => '5',
            'rating_count' => '0',
            'status' => 'active',
        ]);

        $response = $this->from(route('courses-detail', $course->slug))->post(route('join-now-submit'), [
            'full_name' => 'Asha Sharma',
            'email' => 'asha@example.com',
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
            'contactMethod' => 'WhatsApp',
        ]);

        $response
            ->assertRedirect(route('courses-detail', $course->slug))
            ->assertSessionHas('success', 'Thank you! We received your inquiry. Our team will contact you soon.');

        $this->assertDatabaseHas(JoinNowQuery::class, [
            'email' => 'asha@example.com',
            'phone' => '9823456789',
            'course_id' => $course->id,
            'course_slug' => $course->slug,
            'course' => $course->name,
            'help_topic' => 'Fees and timing',
            'selected_course' => $course->slug,
            'source_page' => 'course-detail',
            'source_section' => 'course-detail-hero',
            'audience_type' => 'parent',
            'inquiry_intent' => 'course_guidance',
            'preferred_batch_time' => 'Evening',
            'lead_score' => 23,
            'lead_status' => 'Hot',
        ]);
    }

    public function test_join_now_form_accepts_supported_nepal_phone_formats(): void
    {
        Mail::fake();

        foreach (['9823456789', '9723456789', '+9779823456789', '+9779723456789', '0615725999'] as $index => $phone) {
            $response = $this->from(route('join-now'))->post(route('join-now-submit'), [
                'firstName' => 'Asha'.$index,
                'lastName' => 'Sharma',
                'email' => "asha{$index}@example.com",
                'phone' => $phone,
                'course' => 'undecided',
            ]);

            $response->assertRedirect(route('join-now'));

            $this->assertDatabaseHas(JoinNowQuery::class, [
                'email' => "asha{$index}@example.com",
                'phone' => $phone,
                'course' => 'Need help choosing a program',
            ]);
        }
    }

    public function test_join_now_form_rejects_invalid_phone_number(): void
    {
        Mail::fake();

        foreach (['abc123', '98000', '9800000000123', '9800000000'] as $index => $phone) {
            $response = $this->from(route('join-now'))->post(route('join-now-submit'), [
                'firstName' => 'Asha',
                'lastName' => 'Sharma',
                'email' => "bad-phone{$index}@example.com",
                'phone' => $phone,
                'course' => 'undecided',
            ]);

            $response
                ->assertRedirect(route('join-now'))
                ->assertSessionHasErrors('phone');

            $this->assertDatabaseMissing(JoinNowQuery::class, [
                'email' => "bad-phone{$index}@example.com",
            ]);
        }

        Mail::assertNothingQueued();
    }

    public function test_newsletter_submits_successfully(): void
    {
        $response = $this->from(route('home'))->post(route('newsletter'), [
            'email' => 'news@example.com',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas(NewsLetter::class, ['email' => 'news@example.com']);
    }

    public function test_contact_form_fails_if_recaptcha_configured_but_missing(): void
    {
        // Mock site setting to require recaptcha
        SiteSetting::create([
            'key' => 'recaptcha_secret_key',
            'value' => 'some_secret',
            'type' => 'text',
        ]);

        $response = $this->from(route('contact'))->post(route('contact-submit'), [
            'name' => 'Asha Sharma',
            'phone' => '9823456780',
            'email' => 'asha@example.com',
            'subject' => 'Course question',
            'message' => 'I want to know more.',
        ]);

        $response->assertSessionHasErrors('g-recaptcha-response');
    }

    public function test_join_now_form_fails_with_invalid_course_slug(): void
    {
        Mail::fake();

        $response = $this->from(route('join-now'))->post(route('join-now-submit'), [
            'firstName' => 'Asha',
            'lastName' => 'Sharma',
            'email' => 'asha@example.com',
            'phone' => '9823456780',
            'course' => 'invalid-course-slug',
        ]);

        $response->assertSessionHasErrors('course');
        Mail::assertNothingQueued();
    }

    public function test_join_now_form_rejects_course_from_inactive_category(): void
    {
        Mail::fake();

        $category = CourseCategory::factory()->create(['status' => 'inactive']);

        $course = Course::factory()->create([
            'slug' => 'hidden-category-enrollment',
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $response = $this->from(route('join-now'))->post(route('join-now-submit'), [
            'firstName' => 'Asha',
            'lastName' => 'Sharma',
            'email' => 'asha@example.com',
            'phone' => '9823456780',
            'course' => $course->slug,
        ]);

        $response->assertSessionHasErrors('course');
        $this->assertDatabaseMissing(JoinNowQuery::class, [
            'email' => 'asha@example.com',
            'course_slug' => $course->slug,
        ]);
        Mail::assertNothingQueued();
    }
}
