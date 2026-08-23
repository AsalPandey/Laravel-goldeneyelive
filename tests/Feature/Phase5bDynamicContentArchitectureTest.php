<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Phase5bDynamicContentArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_course_schema_relationships_and_uniqueness_are_stable(): void
    {
        $blog = BlogPost::factory()->create();
        $course = Course::factory()->create();

        $blog->courses()->attach($course);

        $this->assertTrue(Schema::hasTable('blog_course'));
        $this->assertTrue($blog->courses->first()->is($course));
        $this->assertTrue($course->blogs->first()->is($blog));
        $this->assertSame(1, DB::table('blog_course')->count());

        $this->expectException(QueryException::class);
        DB::table('blog_course')->insert([
            'blog_id' => $blog->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_staff_can_attach_and_detach_all_related_courses_while_preserving_blog_category(): void
    {
        $staff = $this->staffUser();
        $firstCourse = Course::factory()->create(['name' => 'First Related Course']);
        $secondCourse = Course::factory()->create(['name' => 'Second Related Course']);
        BlogPost::factory()->create(['category' => 'Student Guides']);

        $this->actingAs($staff)
            ->get(route('admin.blog.create'))
            ->assertOk()
            ->assertSee('Related Courses')
            ->assertSee('Search courses')
            ->assertSee('Student Guides')
            ->assertSee('name="courses[]"', false)
            ->assertDontSee('name="schema_markup"', false);

        $this->actingAs($staff)
            ->post(route('admin.blog.store'), $this->blogPayload([
                'title' => 'Relationship CMS Article',
                'category' => 'Career & Skills',
                'courses_present' => '1',
                'courses' => [$firstCourse->id, $secondCourse->id],
            ]))
            ->assertRedirect(route('admin.blog.index'))
            ->assertSessionHasNoErrors();

        $blog = BlogPost::where('title', 'Relationship CMS Article')->firstOrFail();
        $this->assertSame('Career & Skills', $blog->category);
        $this->assertEqualsCanonicalizing(
            [$firstCourse->id, $secondCourse->id],
            $blog->courses()->pluck('courses.id')->all(),
        );

        $this->from(route('admin.blog.edit', $blog))
            ->put(route('admin.blog.update', $blog), $this->blogPayload([
                'title' => '',
                'category' => 'Career & Skills',
                'courses_present' => '1',
                'courses' => [$secondCourse->id],
            ]))
            ->assertRedirect(route('admin.blog.edit', $blog))
            ->assertSessionHasErrors('title')
            ->assertSessionHasInput('courses', [$secondCourse->id]);

        $this->assertEqualsCanonicalizing(
            [$firstCourse->id, $secondCourse->id],
            $blog->courses()->pluck('courses.id')->all(),
        );

        $this->put(route('admin.blog.update', $blog), $this->blogPayload([
            'title' => 'Relationship CMS Article',
            'category' => 'Career & Skills',
            'courses_present' => '1',
        ]))
            ->assertRedirect(route('admin.blog.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(0, $blog->courses()->count());
        $this->assertSame('Career & Skills', $blog->fresh()->category);
    }

    public function test_failed_blog_update_rolls_back_blog_and_pivot_changes_together(): void
    {
        $staff = $this->staffUser();
        $originalCourse = Course::factory()->create();
        $rejectedCourse = Course::factory()->create();
        $blog = BlogPost::factory()->create(['title' => 'Original Transactional Title']);
        $blog->courses()->attach($originalCourse);

        DB::statement('CREATE TRIGGER reject_phase5b_pivot BEFORE INSERT ON blog_course '
            .'WHEN NEW.course_id = '.$rejectedCourse->id.' BEGIN '
            ."SELECT RAISE(ABORT, 'phase5b rollback proof'); END");

        $this->actingAs($staff)->withoutExceptionHandling();

        try {
            $this->put(route('admin.blog.update', $blog), $this->blogPayload([
                'title' => 'Partially Updated Title',
                'courses_present' => '1',
                'courses' => [$rejectedCourse->id],
            ]));
            $this->fail('The forced pivot failure did not occur.');
        } catch (QueryException $exception) {
            $this->assertStringContainsString('phase5b rollback proof', $exception->getMessage());
        }

        $this->assertSame('Original Transactional Title', $blog->fresh()->title);
        $this->assertSame([$originalCourse->id], $blog->courses()->pluck('courses.id')->all());
    }

    public function test_blog_related_courses_use_current_public_course_data_and_hide_inactive_courses(): void
    {
        $activeCourse = Course::factory()->create([
            'name' => 'Current Course Name',
            'slug' => 'current-course-name',
            'price' => 'Current verified fee',
            'duration' => 'Current verified duration',
            'instructor' => 'Current instructor snapshot',
            'status' => 'active',
        ]);
        $inactiveCourse = Course::factory()->inactive()->create([
            'name' => 'Private Inactive Course',
            'slug' => 'private-inactive-course',
        ]);
        $blog = BlogPost::factory()->create([
            'title' => 'Public Relationship Guide',
            'slug' => 'public-relationship-guide',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);
        $blog->courses()->attach([$activeCourse->id, $inactiveCourse->id]);

        $this->get(route('blog-detail', $blog->slug))
            ->assertOk()
            ->assertSee('Related Courses')
            ->assertSee('Current Course Name')
            ->assertSee('Current verified fee')
            ->assertSee('Current verified duration')
            ->assertSee('Current instructor snapshot')
            ->assertSee('data-cta="blog-related-course"', false)
            ->assertSee('data-track-event="blog_related_course_click"', false)
            ->assertDontSee('Private Inactive Course');

        $activeCourse->update(['name' => 'Live CMS Course Name', 'price' => 'Updated verified fee']);
        cache()->flush();

        $this->get(route('blog-detail', $blog->slug))
            ->assertOk()
            ->assertSee('Live CMS Course Name')
            ->assertSee('Updated verified fee')
            ->assertDontSee('Current Course Name');
    }

    public function test_course_helpful_guides_are_published_deterministic_and_draft_safe(): void
    {
        $course = Course::factory()->create(['slug' => 'guide-linked-course', 'status' => 'active']);
        $olderPublished = BlogPost::factory()->create([
            'title' => 'Older Published Guide',
            'slug' => 'older-published-guide',
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);
        $newerPublished = BlogPost::factory()->create([
            'title' => 'Newer Published Guide',
            'slug' => 'newer-published-guide',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        $draft = BlogPost::factory()->draft()->create([
            'title' => 'Private Draft Guide',
            'slug' => 'private-draft-guide',
        ]);
        $course->blogs()->attach([$olderPublished->id, $newerPublished->id, $draft->id]);

        $html = $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Helpful Guides')
            ->assertSee('data-cta="helpful-guide-from-course"', false)
            ->assertSee('data-track-event="course_helpful_guide_click"', false)
            ->assertSee('Older Published Guide')
            ->assertSee('Newer Published Guide')
            ->assertDontSee('Private Draft Guide')
            ->getContent();

        $this->assertLessThan(
            strpos($html, 'Older Published Guide'),
            strpos($html, 'Newer Published Guide'),
        );
        $this->get(route('blog-detail', $draft->slug))->assertNotFound();
    }

    public function test_dynamic_metadata_and_laravel_generated_structured_data_ignore_legacy_raw_schema(): void
    {
        config()->set('app.url', 'https://phase5b.example');
        SiteSetting::insert([
            ['key' => 'site_name', 'value' => 'Golden Eye', 'type' => 'text'],
            ['key' => 'site_name_suffix', 'value' => 'Academy', 'type' => 'text'],
            ['key' => 'site_phone', 'value' => '061-572599', 'type' => 'text'],
            ['key' => 'site_address', 'value' => 'Verified CMS Address', 'type' => 'text'],
            ['key' => 'schema_markup', 'value' => '{"@type":"Thing","phaseMarker":"unsafe-legacy-schema"}', 'type' => 'text'],
            ['key' => 'speakable_selectors', 'value' => '.unsafe-speakable', 'type' => 'text'],
        ]);
        $blog = BlogPost::factory()->create([
            'title' => 'Structured Editorial Guide',
            'slug' => 'structured-editorial-guide',
            'meta_title' => 'Verified Social Title',
            'meta_description' => 'Verified social description.',
            'schema_markup' => '{"@type":"BlogPosting","phaseMarker":"unsafe-blog-schema"}',
        ]);

        $html = $this->get(route('blog-detail', $blog->slug))->assertOk()->getContent();
        $nodes = collect($this->jsonLdNodes($html));
        $article = $nodes->firstWhere('@type', 'BlogPosting');
        $breadcrumbs = $nodes->firstWhere('@type', 'BreadcrumbList');
        $organization = $nodes->firstWhere('@type', 'EducationalOrganization');

        $this->assertSame('Verified Social Title - Golden Eye Academy', $this->metaValue($html, 'og:title', 'property'));
        $this->assertSame('Verified social description.', $this->metaValue($html, 'og:description', 'property'));
        $this->assertSame('https://phase5b.example/blog/structured-editorial-guide', $this->metaValue($html, 'og:url', 'property'));
        $this->assertSame('Structured Editorial Guide', $article['headline']);
        $this->assertArrayNotHasKey('phaseMarker', $article);
        $this->assertSame('Structured Editorial Guide', $breadcrumbs['itemListElement'][2]['name']);
        $this->assertSame('Golden Eye Academy', $organization['name']);
        $this->assertSame('061-572599', $organization['telephone']);
        $this->assertSame('Verified CMS Address', $organization['address']['streetAddress']);
        $this->assertSame(1, $nodes->where('@type', 'EducationalOrganization')->count());
        $this->assertStringNotContainsString('unsafe-legacy-schema', $html);
        $this->assertStringNotContainsString('unsafe-blog-schema', $html);
        $this->assertStringNotContainsString('SpeakableSpecification', $html);
    }

    public function test_course_schema_and_breadcrumb_match_visible_course_hierarchy_without_claims(): void
    {
        config()->set('app.url', 'https://phase5b.example');
        $category = CourseCategory::factory()->create([
            'name' => 'Verified Category',
            'slug' => 'verified-category',
            'status' => 'active',
        ]);
        $course = Course::factory()->create([
            'name' => 'Verified Course',
            'slug' => 'verified-course',
            'category_id' => $category->id,
            'status' => 'active',
            'schema_markup' => '{"@type":"Course","offers":{"price":"0"},"aggregateRating":{"ratingValue":"5"}}',
        ]);

        $html = $this->get(route('courses-detail', $course->slug))->assertOk()->getContent();
        $nodes = collect($this->jsonLdNodes($html));
        $courseSchema = $nodes->firstWhere('@type', 'Course');
        $breadcrumbs = $nodes->firstWhere('@type', 'BreadcrumbList');

        $this->assertSame('Verified Course', $courseSchema['name']);
        $this->assertArrayNotHasKey('offers', $courseSchema);
        $this->assertArrayNotHasKey('aggregateRating', $courseSchema);
        $this->assertSame(['Home', 'Courses', 'Verified Category', 'Verified Course'], collect($breadcrumbs['itemListElement'])->pluck('name')->all());
        $this->assertStringContainsString('Verified Category', $html);
    }

    public function test_teacher_and_testimonial_relationships_retain_factual_snapshots_when_links_clear(): void
    {
        $staff = $this->staffUser();
        $teacher = Teacher::factory()->create(['name' => 'Verified Teacher Profile', 'status' => 'active']);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'instructor' => 'Approved Instructor Snapshot',
        ]);
        $testimonial = Testimonial::factory()->create([
            'course_id' => $course->id,
            'course_name' => $course->name,
            'status' => 'active',
        ]);

        $this->actingAs($staff)
            ->put(route('admin.testimonials.update', $testimonial), [
                'student_name' => $testimonial->student_name,
                'course_id' => '',
                'content' => $testimonial->content,
                'rating' => $testimonial->rating,
                'status' => 'active',
                'is_featured' => '0',
            ])
            ->assertRedirect(route('admin.testimonials.index'))
            ->assertSessionHasNoErrors();

        $this->assertNull($testimonial->fresh()->course_id);
        $this->assertSame($course->name, $testimonial->fresh()->course_name);

        $teacher->delete();
        $course->refresh();

        $this->assertNull($course->teacher_id);
        $this->assertSame('Approved Instructor Snapshot', $course->instructor);

        cache()->flush();
        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Approved Instructor Snapshot')
            ->assertDontSee('Verified Teacher Profile');
    }

    public function test_blog_permissions_and_fixed_robots_and_sitemap_publication_rules_hold(): void
    {
        config()->set('app.url', 'https://phase5b.example');
        $ordinaryUser = User::factory()->create();
        $activeCourse = Course::factory()->create(['slug' => 'sitemap-active-course', 'status' => 'active']);
        $inactiveCourse = Course::factory()->inactive()->create(['slug' => 'sitemap-inactive-course']);
        $publishedBlog = BlogPost::factory()->create(['slug' => 'sitemap-published-blog', 'status' => 'published']);
        $draftBlog = BlogPost::factory()->draft()->create(['slug' => 'sitemap-draft-blog']);

        $this->actingAs($ordinaryUser)->get(route('admin.blog.create'))->assertForbidden();

        $robots = $this->get('/robots.txt')->assertOk()->getContent();
        $this->assertStringContainsString("User-agent: *\nAllow: /", $robots);
        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringContainsString('Sitemap: https://phase5b.example/sitemap.xml', $robots);
        $this->assertStringNotContainsString('GPTBot', $robots);
        $this->assertStringNotContainsString('Google-Extended', $robots);

        $sitemap = $this->get(route('sitemap'))->assertOk()->getContent();
        $this->assertStringContainsString($activeCourse->slug, $sitemap);
        $this->assertStringContainsString($publishedBlog->slug, $sitemap);
        $this->assertStringNotContainsString($inactiveCourse->slug, $sitemap);
        $this->assertStringNotContainsString($draftBlog->slug, $sitemap);
        $this->assertStringNotContainsString('/admin', $sitemap);
        $this->assertStringNotContainsString('/preview', $sitemap);
    }

    public function test_relationship_click_events_use_the_existing_safe_analytics_pipeline(): void
    {
        foreach (['blog_related_course_click', 'course_helpful_guide_click'] as $eventName) {
            $this->postJson(route('analytics.events.store'), [
                'event_name' => $eventName,
                'source_page' => 'phase5b-browser',
                'source_section' => 'relationship-links',
                'cta_label' => 'Relationship link',
                'selected_course' => 'verified-course',
                'metadata' => [
                    'cta_id' => $eventName,
                    'message' => 'sensitive free text must be excluded',
                ],
            ])->assertStatus(202)->assertJson(['tracked' => true]);

            $this->assertDatabaseHas('analytics_events', [
                'event_name' => $eventName,
                'selected_course' => 'verified-course',
            ]);
        }

        $events = AnalyticsEvent::whereIn('event_name', [
            'blog_related_course_click',
            'course_helpful_guide_click',
        ])->get();

        $this->assertCount(2, $events);
        $this->assertTrue($events->every(fn ($event): bool => ! array_key_exists('message', $event->metadata ?? [])));
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function blogPayload(array $overrides = []): array
    {
        return array_replace([
            'title' => 'Phase 5B Blog',
            'slug' => 'phase-5b-blog',
            'author' => 'Golden Eye Academy',
            'category' => 'Student Guides',
            'content' => '<h2>Verified heading</h2><p><strong>Verified</strong> content.</p><blockquote>Verified note.</blockquote><table><tbody><tr><td>Verified comparison</td></tr></tbody></table>',
            'status' => 'draft',
            'published_at' => null,
        ], $overrides);
    }

    private function staffUser(): User
    {
        $this->seed(RoleSeeder::class);
        $staff = User::factory()->create();
        $staff->assignRole('Staff');

        return $staff;
    }

    private function metaValue(string $html, string $key, string $attribute = 'name'): ?string
    {
        preg_match('/<meta[^>]+'.preg_quote($attribute, '/').'=["\']'.preg_quote($key, '/').'["\'][^>]+content=["\']([^"\']*)["\']/i', $html, $matches);

        return isset($matches[1]) ? html_entity_decode($matches[1]) : null;
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
