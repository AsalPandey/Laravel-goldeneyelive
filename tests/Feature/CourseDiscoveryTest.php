<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CourseDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        cache()->flush();

        $this->createCategoryWithCourses(
            'IELTS, PTE and Language Preparation',
            'study-abroad-test-prep',
            ['IELTS Masterclass', 'PTE Elite Academic Training'],
        );
        $this->createCategoryWithCourses(
            'Global Language Academy',
            'language-classes',
            ['Japanese Proficiency JLPT N5', 'Professional Korean EPS-TOPIK'],
        );
        $this->createCategoryWithCourses(
            'Computer and Office Skills',
            'computer-classes',
            ['Advanced Diploma in Computer Science', 'Corporate Office and Admin Package'],
        );
        $this->createCategoryWithCourses(
            'Web Development and IT Career',
            'web-development-it-career',
            ['Professional Web Development'],
        );
    }

    /**
     * @param  array<int, string>  $expectedCourses
     */
    #[DataProvider('combinedSearchQueries')]
    public function test_previous_combined_shortcut_searches_return_relevant_courses(
        string $search,
        array $expectedCourses,
    ): void {
        $response = $this->get(route('courses-all', ['search' => $search]))
            ->assertOk()
            ->assertDontSee('0 courses found', false)
            ->assertDontSee('No course matched that search.', false);

        foreach ($expectedCourses as $expectedCourse) {
            $response->assertSee($expectedCourse, false);
        }
    }

    public static function combinedSearchQueries(): array
    {
        return [
            'IELTS and PTE' => [
                'IELTS PTE',
                ['IELTS Masterclass', 'PTE Elite Academic Training'],
            ],
            'Japanese and Korean' => [
                'Japanese Korean Language',
                ['Japanese Proficiency JLPT N5', 'Professional Korean EPS-TOPIK'],
            ],
            'computer and office' => [
                'Computer Office Skills',
                ['Advanced Diploma in Computer Science', 'Corporate Office and Admin Package'],
            ],
            'web development' => [
                'Web Development',
                ['Professional Web Development'],
            ],
        ];
    }

    public function test_current_category_slug_filters_to_current_courses(): void
    {
        $this->get(route('courses-all', ['category' => 'computer-classes']))
            ->assertOk()
            ->assertSee('Advanced Diploma in Computer Science', false)
            ->assertSee('Corporate Office and Admin Package', false)
            ->assertDontSee('Professional Web Development', false);
    }

    public function test_desktop_and_mobile_course_shortcuts_use_the_same_current_category_urls(): void
    {
        $response = $this->get(route('home'))->assertOk();
        $html = $response->getContent();

        foreach (CourseCategory::query()->orderBy('order_priority')->get() as $category) {
            $url = e(route('courses-all', ['category' => $category->slug]));

            $this->assertGreaterThanOrEqual(
                2,
                substr_count($html, $url),
                "Expected the {$category->name} shortcut in both desktop and mobile navigation.",
            );
        }

        $this->assertStringNotContainsString('search=IELTS%20PTE', $html);
        $this->assertStringNotContainsString('search=Computer%20Office%20Skills', $html);
    }

    public function test_unmatched_search_has_clear_recovery_actions(): void
    {
        $this->get(route('courses-all', ['search' => 'nonexistent-subject']))
            ->assertOk()
            ->assertSee('No course matched that search.', false)
            ->assertSee('Clear Search', false)
            ->assertSee('Ask for Course Help', false)
            ->assertSee(route('courses-all'), false);
    }

    /**
     * @param  array<int, string>  $courseNames
     */
    private function createCategoryWithCourses(string $name, string $slug, array $courseNames): void
    {
        $category = CourseCategory::factory()->create([
            'name' => $name,
            'slug' => $slug,
            'status' => 'active',
            'order_priority' => CourseCategory::query()->count(),
        ]);

        foreach ($courseNames as $index => $courseName) {
            Course::factory()->create([
                'name' => $courseName,
                'slug' => str($courseName)->slug()->toString(),
                'category' => $name,
                'category_slug' => $slug,
                'category_id' => $category->id,
                'status' => 'active',
                'display_order' => $index,
            ]);
        }
    }
}
