<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Phase5PublicAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_course_trust_uses_stable_explicit_relationships(): void
    {
        $linkedTeacher = Teacher::factory()->create([
            'name' => 'Verified Faculty Profile',
            'bio' => 'Verified profile biography.',
            'status' => 'active',
        ]);
        $sameNameButUnlinkedTeacher = Teacher::factory()->create([
            'name' => 'Published Instructor Name',
            'bio' => 'This mutable-name match must not create a relationship.',
            'status' => 'active',
        ]);
        $course = Course::factory()->create([
            'name' => 'Stable Relationship Course',
            'slug' => 'stable-relationship-course',
            'instructor' => $sameNameButUnlinkedTeacher->name,
            'teacher_id' => $linkedTeacher->id,
            'status' => 'active',
        ]);
        $linkedTestimonial = Testimonial::factory()->create([
            'student_name' => 'Linked Learner',
            'course_name' => 'Legacy mutable title',
            'course_id' => $course->id,
            'content' => 'Explicitly linked course feedback.',
            'status' => 'active',
        ]);
        Testimonial::factory()->create([
            'student_name' => 'General Learner',
            'course_name' => $course->name,
            'course_id' => null,
            'content' => 'General academy feedback.',
            'status' => 'active',
        ]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee($linkedTeacher->name)
            ->assertSee('Verified profile biography.')
            ->assertDontSee('This mutable-name match must not create a relationship.')
            ->assertSee($linkedTestimonial->student_name)
            ->assertDontSee('General Learner');

        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Linked Learner')
            ->assertSee('Verified course: '.$course->name)
            ->assertSee('General Learner')
            ->assertSee('Academy experience');
    }

    public function test_final_visible_course_selection_controls_submission_and_analytics(): void
    {
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

        $this->from(route('join-now', ['course' => $courseA->slug]))
            ->post(route('join-now-submit'), [
                'full_name' => 'Phase Five Student',
                'phone' => '+977 984-123-4567',
                'help_topic' => 'Choosing a course',
                'course' => $courseB->slug,
                'selected_course' => $courseA->slug,
                'source_page' => 'course-detail',
                'source_section' => 'course-detail-hero',
                'inquiry_intent' => 'course_guidance',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('join_now_queries', [
            'phone' => '9841234567',
            'course_id' => $courseB->id,
            'course_slug' => $courseB->slug,
            'course' => $courseB->name,
            'selected_course' => $courseB->slug,
        ]);
        $this->assertDatabaseHas('analytics_events', [
            'event_name' => 'course_help_submit',
            'selected_course' => $courseB->slug,
        ]);
    }

    public function test_contact_validation_rejects_placeholder_numbers_and_accepts_formatted_nepal_numbers(): void
    {
        $payload = [
            'name' => 'Phase Five Contact',
            'email' => 'phase5-contact@example.test',
            'subject' => 'General Inquiry',
            'message' => 'Please confirm a current batch.',
        ];

        $this->from(route('contact'))
            ->post(route('contact-submit'), [...$payload, 'phone' => '9812345678'])
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors('phone');

        $this->from(route('contact'))
            ->post(route('contact-submit'), [...$payload, 'phone' => '+977 984 123 4567'])
            ->assertRedirect(route('contact'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contacts', [
            'email' => 'phase5-contact@example.test',
            'phone' => '9841234567',
        ]);
    }

    public function test_phase_five_mobile_popup_and_submit_guards_are_present(): void
    {
        $css = file_get_contents(public_path('site/css/style.css'));
        $tailwindSource = file_get_contents(resource_path('css/app.css'));
        $header = file_get_contents(resource_path('views/site/layout/header.blade.php'));
        $layout = file_get_contents(resource_path('views/site/layout/app.blade.php'));
        $joinForm = file_get_contents(resource_path('views/site/join-now/join-now.blade.php'));
        $courseDetail = file_get_contents(resource_path('views/site/courses/course-detail.blade.php'));

        $this->assertStringContainsString('overflow-x: clip;', $header);
        $this->assertStringContainsString('overflow-x: clip;', $css);
        $this->assertStringContainsString('overflow-x-clip', $tailwindSource);
        $this->assertStringContainsString('.course-contact-copy', $css);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $css);
        $this->assertStringContainsString('max-height: calc(100dvh - 32px);', $css);
        $this->assertStringContainsString('const autoDelayMs = 12000;', $layout);
        $this->assertStringContainsString('! delayElapsed || ! scrollTriggered', $layout);
        $this->assertStringContainsString("addEventListener('hidden.bs.modal'", $layout);
        $this->assertStringContainsString('rememberDismissal(popupId);', $layout);
        $this->assertStringContainsString('submitButton.disabled = true;', $joinForm);
        $this->assertStringContainsString('selectedCourse.value = course.value;', $joinForm);
        $this->assertStringContainsString('form.dataset.selectedCourse = course.value;', $joinForm);
        $this->assertStringContainsString('course-contact-row', $courseDetail);
    }

    public function test_stable_relationship_columns_and_foreign_keys_are_available(): void
    {
        $this->assertTrue(Schema::hasColumn('courses', 'teacher_id'));
        $this->assertTrue(Schema::hasColumn('testimonials', 'course_id'));

        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create(['teacher_id' => $teacher->id]);
        $testimonial = Testimonial::factory()->create(['course_id' => $course->id]);

        $this->assertTrue($course->teacher->is($teacher));
        $this->assertTrue($testimonial->course->is($course));
        $this->assertSame(1, DB::table('testimonials')->where('course_id', $course->id)->count());
    }
}
