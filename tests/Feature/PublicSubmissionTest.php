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
            'phone' => '9800000000',
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

    public function test_join_now_form_accepts_visible_required_fields_only(): void
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

        $response = $this->from(route('join-now'))->post(route('join-now-submit'), [
            'firstName' => 'Asha',
            'lastName' => 'Sharma',
            'email' => 'asha@example.com',
            'phone' => '9800000000',
            'course' => $course->slug,
        ]);

        $response->assertRedirect(route('join-now'));

        $this->assertDatabaseHas(JoinNowQuery::class, [
            'firstName' => 'Asha',
            'lastName' => 'Sharma',
            'address' => '',
            'course_id' => $course->id,
            'course_slug' => $course->slug,
            'course' => $course->name,
        ]);

        Mail::assertQueued(ContactMail::class);
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
            'phone' => '9800000000',
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
            'phone' => '9800000000',
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
            'phone' => '9800000000',
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
