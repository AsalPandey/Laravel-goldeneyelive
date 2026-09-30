<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicCourseExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_cards_present_one_primary_decision_action(): void
    {
        $templates = [
            resource_path('views/site/index.blade.php'),
            resource_path('views/site/courses/courses.blade.php'),
            resource_path('views/site/courses/courses-category.blade.php'),
        ];

        foreach ($templates as $template) {
            $contents = file_get_contents($template);

            $this->assertStringContainsString('course-card-cta', $contents);
            $this->assertStringNotContainsString('homepage-course-guidance', $contents);
            $this->assertStringNotContainsString('featured-course-guidance', $contents);
            $this->assertStringNotContainsString('category-course-guidance', $contents);
        }
    }

    public function test_course_search_and_category_shortcuts_do_not_duplicate_category_controls(): void
    {
        $template = file_get_contents(resource_path('views/site/courses/course-all.blade.php'));

        $this->assertStringNotContainsString('id="course-category"', $template);
        $this->assertStringContainsString('class="course-category-shortcuts', $template);
        $this->assertStringContainsString('type="hidden" name="category"', $template);
        $this->assertStringContainsString('course-card-cta', $template);
    }

    public function test_course_detail_has_a_contextual_conversion_action_near_fee_and_timing(): void
    {
        $template = file_get_contents(resource_path('views/site/courses/course-detail.blade.php'));

        $this->assertStringContainsString('course-detail-fee-guidance', $template);
        $this->assertStringContainsString('Ask About This Batch', $template);
        $this->assertStringContainsString('course-detail-fee-whatsapp', $template);
    }

    public function test_design_system_does_not_override_radius_utilities_globally(): void
    {
        $css = file_get_contents(public_path('site/css/style.css'));

        $this->assertStringContainsString('.course-card-cta', $css);
        $this->assertDoesNotMatchRegularExpression('/\.rounded-xl\s*,|\.rounded-2xl\s*\{[^}]*border-radius:\s*8px\s*!important/s', $css);
    }

    public function test_every_public_course_card_exposes_its_details_link_across_the_full_card(): void
    {
        $templates = [
            resource_path('views/site/index.blade.php'),
            resource_path('views/site/courses/courses.blade.php'),
            resource_path('views/site/courses/courses-category.blade.php'),
            resource_path('views/site/courses/course-all.blade.php'),
            resource_path('views/site/catalogue/index.blade.php'),
            resource_path('views/site/blog/show.blade.php'),
        ];

        foreach ($templates as $template) {
            $contents = file_get_contents($template);

            $this->assertStringContainsString('course-card-clickable position-relative', $contents);
            $this->assertStringContainsString('stretched-link', $contents);
            $this->assertStringContainsString('aria-label="View details for {{', $contents);
        }

        $category = CourseCategory::factory()->create(['status' => 'active']);
        $course = Course::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'status' => 'active',
        ]);

        $this->get(route('courses-all'))
            ->assertOk()
            ->assertSee('aria-label="View details for '.e($course->name).'"', false)
            ->assertSee(route('courses-detail', $course->slug), false);
    }

    public function test_course_fit_section_always_has_four_cards_and_uses_builder_content(): void
    {
        $category = CourseCategory::factory()->create(['status' => 'active']);
        $course = Course::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'status' => 'active',
            'fit_note' => 'Bring your current learning goals for a short placement discussion.',
        ]);

        $html = $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Bring your current learning goals for a short placement discussion.')
            ->getContent();

        preg_match('/<section[^>]+aria-labelledby="who-for-heading".*?<\/section>/s', $html, $section);

        $this->assertNotEmpty($section);
        $this->assertSame(4, substr_count($section[0], 'class="col-md-6"'));
        $this->assertTrue(Schema::hasColumn('courses', 'fit_note'));

        $course->update(['fit_note' => null]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Ask what preparation or materials are recommended before class.');
    }

    public function test_course_builder_exposes_the_fourth_fit_card_field(): void
    {
        $create = file_get_contents(resource_path('views/admin/courses/create.blade.php'));
        $edit = file_get_contents(resource_path('views/admin/courses/edit.blade.php'));
        $request = file_get_contents(app_path('Http/Requests/Admin/CourseRequest.php'));

        $this->assertStringContainsString('name="fit_note"', $create);
        $this->assertStringContainsString('name="fit_note"', $edit);
        $this->assertStringContainsString("'fit_note' => ['nullable', 'string', 'max:255']", $request);
    }
}
