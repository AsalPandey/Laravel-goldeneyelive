<?php

namespace Tests\Feature;

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
}
