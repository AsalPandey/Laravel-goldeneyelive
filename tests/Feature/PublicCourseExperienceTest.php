<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicCourseExperienceTest extends TestCase
{
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
}
