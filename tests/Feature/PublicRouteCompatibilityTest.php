<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicRouteCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_misspelled_course_category_url_redirects_to_course_finder(): void
    {
        $category = CourseCategory::create([
            'name' => 'Language',
            'slug' => 'language',
            'status' => 'active',
            'order_priority' => 0,
        ]);

        $this->get(route('course-catagory', $category->slug))
            ->assertStatus(301)
            ->assertRedirect(route('courses-all', ['category' => $category->slug]));
    }

    public function test_common_course_listing_aliases_redirect_to_canonical_course_finder(): void
    {
        $this->get('/courses')
            ->assertStatus(301)
            ->assertRedirect(route('courses-all'));

        $this->get('/all-courses')
            ->assertStatus(301)
            ->assertRedirect(route('courses-all'));
    }

    public function test_course_detail_uses_courses_slug_and_does_not_render_under_blog(): void
    {
        $course = Course::factory()->create([
            'name' => 'IELTS Route Check',
            'slug' => 'ielts-route-check',
            'status' => 'active',
        ]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('IELTS Route Check');

        $this->get('/blog/'.$course->slug)
            ->assertNotFound()
            ->assertDontSee('IELTS Route Check');
    }
}
