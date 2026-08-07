<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Course;
use App\Models\FAQ;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditCourseFaqReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_reports_explicit_pivot_assignments()
    {
        $course = Course::factory()->create(['slug' => 'test-course', 'status' => 'active']);
        $faq = FAQ::create(['question' => 'Q?', 'answer' => 'A', 'status' => 'active']);
        $course->faqs()->attach($faq->id);

        $this->artisan('course-faq:audit')
            ->expectsOutputToContain('Total Active Courses: 1')
            ->expectsOutputToContain('Total FAQ Records: 1')
            ->expectsOutputToContain('Total Pivot Assignments: 1')
            ->expectsOutputToContain('Courses with Assignments: 1')
            ->expectsOutputToContain('Courses with Zero Assignments: 0')
            ->expectsOutputToContain($faq->id)
            ->assertExitCode(0);
    }

    public function test_unassigned_keyword_matching_faq_is_not_treated_as_assignment()
    {
        $course = Course::factory()->create(['slug' => 'test-course', 'status' => 'active', 'category' => 'Web']);
        // Create an FAQ that legacy would match (has "%course%" and "%duration%") but don't attach it
        FAQ::create(['question' => 'course duration?', 'answer' => 'Web', 'status' => 'active']);

        $this->artisan('course-faq:audit')
            ->expectsOutputToContain('Total Pivot Assignments: 0')
            ->expectsOutputToContain('Courses with Zero Assignments: 1')
            ->expectsOutputToContain('Courses with Assignments: 0')
            ->assertExitCode(0);
    }

    public function test_inactive_attached_faq_is_reported_as_attached_inactive()
    {
        $course = Course::factory()->create(['slug' => 'test-course', 'status' => 'active']);
        $faq = FAQ::create(['question' => 'Q?', 'answer' => 'A', 'status' => 'inactive']);
        $course->faqs()->attach($faq->id);

        $this->artisan('course-faq:audit')
            ->expectsOutputToContain('Total Pivot Assignments: 1')
            ->expectsOutputToContain('Inactive but Attached FAQs: 1')
            ->assertExitCode(0);
    }

    public function test_course_with_zero_assignments_is_reported_accurately()
    {
        $course = Course::factory()->create(['slug' => 'test-course', 'status' => 'active']);

        $this->artisan('course-faq:audit')
            ->expectsOutputToContain('Total Pivot Assignments: 0')
            ->expectsOutputToContain('Courses with Zero Assignments: 1')
            ->assertExitCode(0);
    }
}
