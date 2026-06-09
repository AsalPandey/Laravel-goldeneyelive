<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoLaunchFixTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_static_file_and_route_use_valid_sitemap_directives(): void
    {
        $staticRobots = file_get_contents(public_path('robots.txt'));

        $this->assertIsString($staticRobots);
        $this->assertStringNotContainsString('{{', $staticRobots);
        $this->assertStringContainsString('Sitemap: https://goldeneye.edu.np/sitemap.xml', $staticRobots);
        $this->assertDoesNotMatchRegularExpression('/^Disallow:\s*\/\s*$/mi', $staticRobots);

        $response = $this->get('/robots.txt')->assertOk();
        $content = $response->getContent();

        $this->assertStringNotContainsString('{{', $content);
        $this->assertMatchesRegularExpression('/^Sitemap:\s*https?:\/\/[^\s]+\/sitemap\.xml\s*$/mi', $content);
        $this->assertDoesNotMatchRegularExpression('/^Disallow:\s*\/\s*$/mi', $content);
    }

    public function test_faq_page_normalizes_stale_dynamic_button_text_without_removing_cms_control(): void
    {
        cache()->flush();

        SiteSetting::create([
            'key' => 'faq_btn_text',
            'value' => 'Ask for Course Guidance',
            'type' => 'text',
        ]);

        FAQ::factory()->count(11)->create(['status' => 'active']);

        $this->get(route('faq'))
            ->assertOk()
            ->assertSee('Ask for Course Help', false)
            ->assertDontSee('Ask for Course Guidance', false);
    }

    public function test_normalize_public_settings_command_cleans_faq_button_text(): void
    {
        SiteSetting::create([
            'key' => 'faq_btn_text',
            'value' => 'Ask for Course Guidance',
            'type' => 'text',
        ]);

        $this->artisan('goldeneye:normalize-public-settings')
            ->expectsOutputToContain('faq_btn_text')
            ->expectsOutputToContain('Total changes: 1')
            ->assertExitCode(0);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'faq_btn_text',
            'value' => 'Ask for Course Help',
        ]);
    }

    public function test_sitemap_lists_only_canonical_course_index_and_detail_urls(): void
    {
        $category = CourseCategory::factory()->create([
            'slug' => 'study-abroad-test-prep',
            'status' => 'active',
        ]);

        $course = Course::factory()->create([
            'name' => 'IELTS Masterclass',
            'slug' => 'ielts-masterclass',
            'status' => 'active',
            'category_id' => $category->id,
        ]);

        $post = BlogPost::factory()->create([
            'slug' => 'career-guide',
            'status' => 'published',
        ]);

        cache()->forget('sitemap_xml');

        $response = $this->get(route('sitemap'))->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('<loc>'.route('home').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.route('courses-all').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.route('courses-detail', $course->slug).'</loc>', $content);
        $this->assertStringContainsString('<loc>'.route('blog-detail', $post->slug).'</loc>', $content);
        $this->assertStringContainsString('<loc>'.route('for-students').'</loc>', $content);
        $this->assertStringNotContainsString('courses-all?category=', $content);
        $this->assertStringNotContainsString('/admin', $content);
        $this->assertStringNotContainsString('/login', $content);

        $this->get(route('courses-all', ['category' => $category->slug]))
            ->assertOk()
            ->assertSee('IELTS Masterclass', false);
    }

    public function test_homepage_outputs_one_dynamic_organization_schema_entity(): void
    {
        SiteSetting::insert([
            ['key' => 'schema_markup', 'value' => '{"@context":"https://schema.org","@type":"EducationalOrganization","name":"GoldenEye Academy","telephone":"061-572599"}', 'type' => 'text'],
            ['key' => 'site_name', 'value' => 'GoldenEye', 'type' => 'text'],
            ['key' => 'site_name_suffix', 'value' => 'Academy', 'type' => 'text'],
            ['key' => 'site_phone', 'value' => '061-572599', 'type' => 'text'],
        ]);

        $nodes = $this->jsonLdNodes($this->get(route('home'))->assertOk()->getContent());

        $this->assertSame(1, $this->countSchemaType($nodes, 'EducationalOrganization'));
        $this->assertTrue(collect($nodes)->contains(fn (array $node): bool => ($node['name'] ?? null) === 'GoldenEye Academy'));
    }

    public function test_course_page_outputs_one_dynamic_course_schema_entity(): void
    {
        $course = Course::factory()->create([
            'name' => 'Dynamic IELTS Masterclass',
            'slug' => 'dynamic-ielts-masterclass',
            'status' => 'active',
            'schema_markup' => '{"@context":"https://schema.org","@type":"Course","name":"Old Schema Course Name","educationalLevel":"Advanced"}',
        ]);

        $nodes = $this->jsonLdNodes($this->get(route('courses-detail', $course->slug))->assertOk()->getContent());
        $courseNodes = collect($nodes)->filter(fn (array $node): bool => $this->schemaHasType($node, 'Course'))->values();

        $this->assertCount(1, $courseNodes);
        $this->assertSame('Dynamic IELTS Masterclass', $courseNodes[0]['name'] ?? null);
        $this->assertSame('Advanced', $courseNodes[0]['educationalLevel'] ?? null);
        $this->assertSame(1, $this->countSchemaType($nodes, 'EducationalOrganization'));
    }

    public function test_course_titles_do_not_duplicate_brand_and_fallback_metadata_is_safe(): void
    {
        $brandedCourse = Course::factory()->create([
            'name' => 'IELTS Masterclass',
            'slug' => 'ielts-masterclass',
            'status' => 'active',
            'meta_title' => 'IELTS Masterclass for Band 7+ | GoldenEye Academy',
        ]);

        $this->get(route('courses-detail', $brandedCourse->slug))
            ->assertOk()
            ->assertSee('<title>IELTS Masterclass for Band 7+ | GoldenEye Academy</title>', false)
            ->assertDontSee('GoldenEye Academy - GoldenEye Academy', false);

        $fallbackCourse = Course::factory()->create([
            'name' => 'Office Skills Starter',
            'slug' => 'office-skills-starter',
            'status' => 'active',
            'meta_title' => null,
            'meta_description' => null,
            'description' => str_repeat('Practical office skills for study and work. ', 12),
        ]);

        $html = $this->get(route('courses-detail', $fallbackCourse->slug))
            ->assertOk()
            ->assertSee('<title>Office Skills Starter - GoldenEye Academy</title>', false)
            ->getContent();

        $this->assertNotNull($this->metaDescription($html));
        $this->assertLessThanOrEqual(155, strlen((string) $this->metaDescription($html)));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function jsonLdNodes(string $html): array
    {
        preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches);

        $nodes = [];

        foreach ($matches[1] as $json) {
            $decoded = json_decode(html_entity_decode(trim($json)), true);

            $this->assertIsArray($decoded);

            if (isset($decoded['@graph']) && is_array($decoded['@graph'])) {
                foreach ($decoded['@graph'] as $graphNode) {
                    if (is_array($graphNode)) {
                        $nodes[] = $graphNode;
                    }
                }

                continue;
            }

            $nodes[] = $decoded;
        }

        return $nodes;
    }

    /**
     * @param  array<int, array<string, mixed>>  $nodes
     */
    private function countSchemaType(array $nodes, string $type): int
    {
        return collect($nodes)
            ->filter(fn (array $node): bool => $this->schemaHasType($node, $type))
            ->count();
    }

    /**
     * @param  array<string, mixed>  $node
     */
    private function schemaHasType(array $node, string $type): bool
    {
        $schemaType = $node['@type'] ?? null;

        return $schemaType === $type || (is_array($schemaType) && in_array($type, $schemaType, true));
    }

    private function metaDescription(string $html): ?string
    {
        preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']*)["\']/i', $html, $matches);

        return isset($matches[1]) ? html_entity_decode($matches[1]) : null;
    }
}
