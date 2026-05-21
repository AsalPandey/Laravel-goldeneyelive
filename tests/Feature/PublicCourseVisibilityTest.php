<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
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
            ->assertSee('Popular courses', false)
            ->assertSeeInOrder([$hotCourse->name, $lowPriority->name])
            ->assertSee('View Course Details', false)
            ->assertSee('Ask for Course Help', false)
            ->assertDontSee('Find Courses', false)
            ->assertDontSee('Ask What Fits Me', false);

        $this->get(route('courses-all', ['category' => $category->slug, 'search' => 'Seasonal']))
            ->assertOk()
            ->assertSee($hotCourse->name, false)
            ->assertDontSee($lowPriority->name, false);
    }

    public function test_course_detail_page_renders_deep_decision_sections_and_contextual_ctas(): void
    {
        $this->seed(DatabaseSeeder::class);

        SiteSetting::where('key', 'whatsapp_number')->update(['value' => '+977 980-000-0000']);

        $response = $this->get(route('courses-detail', 'ielts-masterclass'));

        $response->assertOk()
            ->assertSee('IELTS Preparation for Band 7 Goal', false)
            ->assertSee('Planning abroad and unsure how to prepare for IELTS?', false)
            ->assertSee('Best for:', false)
            ->assertSee('Duration: 6 Weeks', false)
            ->assertSee('Fee: Rs. 7,000', false)
            ->assertSee('Next batch: Ask for current intake and available seats', false)
            ->assertSee('Ask for Course Help', false)
            ->assertSee('Message on WhatsApp', false)
            ->assertSee('selected_course=ielts-masterclass', false)
            ->assertSee('source_page=course-detail', false)
            ->assertSee('source_section=course-detail-hero', false)
            ->assertSee('inquiry_intent=course_guidance', false)
            ->assertSee('data-cta="course-detail-whatsapp"', false)
            ->assertSee('https://wa.me/9779800000000?text=', false)
            ->assertSee('Quick facts', false)
            ->assertSee('Who this course is for', false)
            ->assertSee('Local trust', false)
            ->assertSee('Srijana Chowk, Pokhara, Nepal', false)
            ->assertSee('Phone', false)
            ->assertSee('061-572599', false)
            ->assertSee('Morning, day, and evening batches', false)
            ->assertSee('Student View', false)
            ->assertSee('Parent View', false)
            ->assertSee('data-bs-toggle="pill"', false)
            ->assertSee('Curriculum', false)
            ->assertSee('Practical assignments', false)
            ->assertSee('Mock tests/practice', false)
            ->assertSee('What you will learn', false)
            ->assertSee('Week-by-week curriculum', false)
            ->assertSee('Week 1', false)
            ->assertSee('IELTS format and scoring', false)
            ->assertSee('Batch timing and fee', false)
            ->assertSee('Instructor profile', false)
            ->assertSee('Aakash Subedi', false)
            ->assertSee('IELTS and PTE Specialist', false)
            ->assertSee('Years of experience', false)
            ->assertSee('Courses taught:', false)
            ->assertSee('Credibility note:', false)
            ->assertSee('Student result', false)
            ->assertSee('Course completed:', false)
            ->assertSee('Specific progress/result:', false)
            ->assertSee('External social proof', false)
            ->assertSee('verified Google review proof', false)
            ->assertSee('FAQs', false)
            ->assertSee('Inquiry CTA', false)
            ->assertSee('Not guaranteed', false)
            ->assertDontSee('4.9/5', false)
            ->assertDontSee('Placement 92%', false)
            ->assertDontSee('<small class="text-muted d-block text-uppercase fw-bold" style="font-size: 8px;">Placement</small>', false);
    }

    public function test_course_detail_breadcrumb_uses_clickable_actual_category_name(): void
    {
        $category = CourseCategory::factory()->create([
            'name' => 'Study Abroad Test Prep',
            'slug' => 'study-abroad-test-prep',
            'status' => 'active',
        ]);

        $course = Course::factory()->create([
            'name' => 'PTE Academic Preparation',
            'slug' => 'pte-academic-preparation',
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'status' => 'active',
        ]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('href="'.route('courses-all').'"', false)
            ->assertSee('href="'.route('courses-all', ['category' => $category->slug]).'"', false)
            ->assertSee('Study Abroad Test Prep', false)
            ->assertDontSee('Generic Category', false);
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
