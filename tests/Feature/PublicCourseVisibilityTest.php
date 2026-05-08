<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCourseVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_course_without_new_category_relation_still_shows_publicly(): void
    {
        Course::factory()->create([
            'name' => 'Legacy IELTS Course',
            'slug' => 'legacy-ielts-course',
            'category_id' => null,
            'category' => 'language classes',
            'category_slug' => 'language-classes',
            'status' => 'active',
        ]);

        $response = $this->get('/courses-all');

        $response->assertStatus(200);
        $response->assertSee('Legacy IELTS Course');
        $response->assertSee('Ask for Course Help');
    }

    public function test_active_course_in_inactive_category_is_hidden_publicly(): void
    {
        $category = CourseCategory::factory()->create(['status' => 'inactive']);

        $course = Course::factory()->create([
            'name' => 'Hidden Category Course',
            'slug' => 'hidden-category-course',
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $response = $this->get('/courses-all');

        $response->assertStatus(200);
        $response->assertDontSee('Hidden Category Course');

        $this->get(route('courses-detail', $course->slug))->assertNotFound();
        $this->get(route('join-now'))->assertDontSee('Hidden Category Course');
        $this->get(route('sitemap'))->assertDontSee(route('courses-detail', $course->slug), false);
    }

    public function test_courses_page_uses_cms_display_order_and_filters_by_category_and_search(): void
    {
        $category = CourseCategory::factory()->create([
            'name' => 'Study Abroad Test Prep',
            'slug' => 'study-abroad-test-prep',
            'status' => 'active',
            'order_priority' => 1,
        ]);

        $lowPriority = Course::factory()->create([
            'name' => 'Regular IELTS Course',
            'slug' => 'regular-ielts-course',
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'is_featured' => true,
            'display_order' => 50,
            'status' => 'active',
        ]);

        $hotCourse = Course::factory()->create([
            'name' => 'Seasonal Hot IELTS Course',
            'slug' => 'seasonal-hot-ielts-course',
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'is_featured' => true,
            'display_order' => 1,
            'status' => 'active',
        ]);

        $response = $this->get(route('courses-all'));

        $response->assertOk()
            ->assertSee('Popular right now', false)
            ->assertSeeInOrder([$hotCourse->name, $lowPriority->name])
            ->assertSee('Find Courses', false)
            ->assertSee('Ask What Fits Me', false);

        $this->get(route('courses-all', ['category' => $category->slug, 'search' => 'Seasonal']))
            ->assertOk()
            ->assertSee($hotCourse->name, false)
            ->assertDontSee($lowPriority->name, false);
    }

    public function test_legacy_course_and_category_urls_redirect_to_course_finder(): void
    {
        $category = CourseCategory::factory()->create([
            'slug' => 'language-classes',
            'status' => 'active',
        ]);

        $this->get(route('courses'))
            ->assertRedirect(route('courses-all'));

        $this->get(route('course-category', $category->slug))
            ->assertRedirect(route('courses-all', ['category' => $category->slug]));
    }
}
