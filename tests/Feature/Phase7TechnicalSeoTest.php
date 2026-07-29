<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\CanonicalUrl;
use Database\Seeders\RoleSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase7TechnicalSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_canonical_and_social_urls_use_configured_environment_host_not_request_host(): void
    {
        config()->set('app.url', 'https://phase-seven.example');

        $html = $this->withServerVariables(['HTTP_HOST' => 'untrusted-host.example'])
            ->get('/about')
            ->assertOk()
            ->getContent();

        $this->assertSame(['https://phase-seven.example/about'], $this->linkValues($html, 'canonical'));
        $this->assertSame('https://phase-seven.example/about', $this->metaValue($html, 'og:url', 'property'));
        $this->assertSame('https://phase-seven.example/about', $this->metaValue($html, 'twitter:url'));
        $this->assertSame('summary_large_image', $this->metaValue($html, 'twitter:card'));
        $this->assertStringNotContainsString('property="twitter:', $html);
    }

    public function test_pagination_is_self_canonical_but_internal_course_filters_are_noindex(): void
    {
        config()->set('app.url', 'https://phase-seven.example');

        $blogHtml = $this->get('/blog?page=2')->assertOk()->getContent();
        $this->assertSame(
            ['https://phase-seven.example/blog?page=2'],
            $this->linkValues($blogHtml, 'canonical'),
        );

        $filteredHtml = $this->get('/courses-all?search=ielts&category=language')
            ->assertOk()
            ->getContent();

        $this->assertSame('noindex, follow', $this->metaValue($filteredHtml, 'robots'));
        $this->assertSame(
            ['https://phase-seven.example/courses-all'],
            $this->linkValues($filteredHtml, 'canonical'),
        );
    }

    public function test_inquiry_auth_and_admin_surfaces_have_indexing_protection(): void
    {
        $this->get(route('join-now'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false);

        $this->get('/login')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow, noarchive" />', false);

        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_empty_course_metadata_uses_a_factual_nonempty_fallback(): void
    {
        $course = Course::factory()->create([
            'name' => 'Verified Course Name',
            'slug' => 'verified-course-name',
            'status' => 'active',
            'meta_title' => '',
            'meta_description' => '',
            'description' => '',
        ]);

        $html = $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->getContent();

        $this->assertSame('Verified Course Name - Golden Eye Academy', $this->title($html));
        $this->assertSame(
            'Verified Course Name at Golden Eye Academy in Pokhara. Ask the academy team for current class and enrollment details.',
            $this->metaValue($html, 'description'),
        );
    }

    public function test_indexable_public_pages_have_one_canonical_and_nonempty_metadata(): void
    {
        config()->set('app.url', 'https://phase-seven.example');
        $category = CourseCategory::factory()->create(['status' => 'active']);
        $course = Course::factory()->create([
            'slug' => 'public-seo-course',
            'status' => 'active',
            'category_id' => $category->id,
        ]);
        $post = BlogPost::factory()->create([
            'slug' => 'public-seo-guide',
            'status' => 'published',
        ]);
        $urls = [
            route('home'),
            route('about'),
            route('catalogue'),
            route('courses-all'),
            route('courses-detail', $course->slug),
            route('blog'),
            route('blog-detail', $post->slug),
            route('faq'),
            route('contact'),
            route('for-students'),
            route('for-parents'),
            route('study-abroad-guidance'),
            route('job-computer-skills'),
            route('privacy-policy'),
            route('terms-and-conditions'),
        ];

        foreach ($urls as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertCount(1, $this->linkValues($html, 'canonical'), $url);
            $this->assertNotSame('', $this->title($html), $url);
            $this->assertNotSame('', trim((string) $this->metaValue($html, 'description')), $url);
            $this->jsonLdNodes($html);
        }
    }

    public function test_robots_route_is_authoritative_and_normalizes_sitemap_to_configured_host(): void
    {
        config()->set('app.url', 'https://phase-seven.example');
        SiteSetting::create([
            'key' => 'robots_txt',
            'value' => "User-agent: *\nDisallow: /admin\nSitemap: http://stale.example/sitemap.xml\nSitemap: http://duplicate.example/sitemap.xml",
            'type' => 'text',
        ]);

        $content = $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->getContent();

        $this->assertFileDoesNotExist(public_path('robots.txt'));
        $this->assertStringContainsString('Disallow: /admin', $content);
        $this->assertStringContainsString('Sitemap: https://phase-seven.example/sitemap.xml', $content);
        $this->assertStringNotContainsString('stale.example', $content);
        $this->assertSame(1, preg_match_all('/^Sitemap\s*:/mi', $content));
    }

    public function test_sitemap_uses_canonical_host_and_only_public_canonical_records(): void
    {
        config()->set('app.url', 'https://phase-seven.example');

        $category = CourseCategory::factory()->create(['status' => 'active']);
        $activeCourse = Course::factory()->create([
            'slug' => 'active-course',
            'status' => 'active',
            'category_id' => $category->id,
        ]);
        $inactiveCourse = Course::factory()->create([
            'slug' => 'inactive-course',
            'status' => 'inactive',
            'category_id' => $category->id,
        ]);
        $publishedPost = BlogPost::factory()->create([
            'slug' => 'published-guide',
            'status' => 'published',
        ]);
        $draftPost = BlogPost::factory()->create([
            'slug' => 'draft-guide',
            'status' => 'draft',
        ]);
        SiteSetting::create([
            'key' => 'audience_students_status',
            'value' => 'inactive',
            'type' => 'text',
        ]);

        $xml = $this->get(route('sitemap'))->assertOk()->getContent();

        $this->assertStringContainsString('<loc>'.CanonicalUrl::route('catalogue').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.CanonicalUrl::route('courses-detail', ['slug' => $activeCourse->slug]).'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.CanonicalUrl::route('blog-detail', ['slug' => $publishedPost->slug]).'</loc>', $xml);
        $this->assertStringNotContainsString($inactiveCourse->slug, $xml);
        $this->assertStringNotContainsString($draftPost->slug, $xml);
        $this->assertStringNotContainsString('/for-students', $xml);
        $this->assertStringNotContainsString('<changefreq>', $xml);
        $this->assertStringNotContainsString('<priority>', $xml);
        $this->assertStringNotContainsString('untrusted-host', $xml);
    }

    public function test_structured_data_is_parseable_unique_and_does_not_invent_course_or_local_facts(): void
    {
        config()->set('app.url', 'https://phase-seven.example');
        $course = Course::factory()->create([
            'name' => 'Schema Safe Course',
            'slug' => 'schema-safe-course',
            'status' => 'active',
            'schema_markup' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Course',
                'name' => 'Stale Name',
                'offers' => ['@type' => 'Offer', 'price' => '0'],
                'hasCourseInstance' => ['@type' => 'CourseInstance', 'courseMode' => 'Onsite'],
            ], JSON_THROW_ON_ERROR),
        ]);

        $nodes = $this->jsonLdNodes(
            $this->get(route('courses-detail', $course->slug))->assertOk()->getContent(),
        );
        $courseNodes = collect($nodes)->filter(fn (array $node): bool => ($node['@type'] ?? null) === 'Course');
        $organization = collect($nodes)->first(fn (array $node): bool => ($node['@type'] ?? null) === 'EducationalOrganization');

        $this->assertCount(1, $courseNodes);
        $this->assertSame('Schema Safe Course', $courseNodes->first()['name']);
        $this->assertArrayNotHasKey('offers', $courseNodes->first());
        $this->assertArrayNotHasKey('hasCourseInstance', $courseNodes->first());
        $this->assertNotNull($organization);
        $this->assertArrayNotHasKey('address', $organization);
        $this->assertArrayNotHasKey('geo', $organization);
        $this->assertArrayNotHasKey('sameAs', $organization);
        $this->assertArrayNotHasKey('areaServed', $organization);
        $this->assertSame(1, collect($nodes)->where('@type', 'WebSite')->count());
        $this->assertSame(1, collect($nodes)->where('@type', 'WebPage')->count());
    }

    public function test_blog_emits_one_factual_blog_posting_and_preserves_safe_encoding(): void
    {
        $marker = '</script><script id="unsafe-marker">unsafe</script>';
        $post = BlogPost::factory()->create([
            'title' => 'Verified Blog Title',
            'slug' => 'verified-blog-title',
            'status' => 'published',
            'schema_markup' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'phaseMarker' => $marker,
            ], JSON_THROW_ON_ERROR),
        ]);

        $html = $this->get(route('blog-detail', $post->slug))->assertOk()->getContent();
        $nodes = $this->jsonLdNodes($html);
        $articles = collect($nodes)->filter(
            fn (array $node): bool => in_array($node['@type'] ?? null, ['Article', 'BlogPosting'], true),
        );

        $this->assertCount(1, $articles);
        $this->assertSame('Verified Blog Title', $articles->first()['headline']);
        $this->assertSame($marker, $articles->first()['phaseMarker']);
        $this->assertStringNotContainsString('<script id="unsafe-marker">', $html);
        $this->assertStringContainsString('\u003C\/script\u003E', $html);
    }

    public function test_staff_can_edit_ordinary_metadata_but_cannot_submit_raw_schema(): void
    {
        $this->seed(RoleSeeder::class);
        $staff = User::factory()->create();
        $staff->assignRole('Staff');
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($staff)
            ->get(route('admin.blog.create'))
            ->assertOk()
            ->assertSee('name="meta_title"', false)
            ->assertSee('name="meta_description"', false)
            ->assertDontSee('name="schema_markup"', false);

        $this->actingAs($staff)
            ->post(route('admin.blog.store'), [
                'title' => 'Staff Metadata Article',
                'content' => '<p>Verified article content.</p>',
                'status' => 'draft',
                'meta_title' => 'Staff Managed Metadata',
                'meta_description' => 'A factual staff-managed description.',
                'schema_markup' => '{"@context":"https://schema.org","@type":"Thing"}',
            ])
            ->assertSessionHasErrors('schema_markup');

        $this->actingAs($admin)
            ->get(route('admin.blog.create'))
            ->assertOk()
            ->assertSee('name="schema_markup"', false);
    }

    /**
     * @return array<int, string>
     */
    private function linkValues(string $html, string $rel): array
    {
        $xpath = $this->xpath($html);

        return collect($xpath->query("//link[@rel='{$rel}']") ?: [])
            ->map(fn ($node): string => $node->attributes?->getNamedItem('href')?->nodeValue ?? '')
            ->all();
    }

    private function metaValue(string $html, string $key, string $attribute = 'name'): ?string
    {
        $node = $this->xpath($html)->query("//meta[@{$attribute}='{$key}']")?->item(0);

        return $node?->attributes?->getNamedItem('content')?->nodeValue;
    }

    private function title(string $html): string
    {
        return trim($this->xpath($html)->query('//title')?->item(0)?->textContent ?? '');
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        @$document->loadHTML($html);

        return new DOMXPath($document);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function jsonLdNodes(string $html): array
    {
        preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches);
        $nodes = [];

        foreach ($matches[1] as $json) {
            $decoded = json_decode(html_entity_decode(trim($json)), true, 512, JSON_THROW_ON_ERROR);

            if (isset($decoded['@graph']) && is_array($decoded['@graph'])) {
                array_push($nodes, ...array_values(array_filter($decoded['@graph'], 'is_array')));

                continue;
            }

            $nodes[] = $decoded;
        }

        return $nodes;
    }
}
