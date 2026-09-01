<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\CourseCategorySeeder;
use Database\Seeders\CourseSeeder;
use Database\Seeders\NoticeSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Phase3b3CmsSeoPopupAuthorityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    public function test_seeded_course_metadata_renders_on_the_public_course_page(): void
    {
        $this->seed([CourseCategorySeeder::class, CourseSeeder::class]);

        $course = Course::query()->whereNotNull('meta_title')->firstOrFail();

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('<title>'.e($course->meta_title).'</title>', false)
            ->assertSee('name="description" content="'.e($course->meta_description).'"', false)
            ->assertSee('property="og:title" content="'.e($course->meta_title).'"', false)
            ->assertSee('name="aeo-summary" content="'.e(strip_tags((string) $course->aeo_summary)).'"', false);

        $this->actingAs($this->admin)
            ->put(route('admin.courses.update', $course), $this->coursePayload($course, $course->courseCategory, [
                'meta_title' => $course->meta_title,
                'meta_description' => $course->meta_description,
                'meta_keywords' => $course->meta_keywords,
                'aeo_summary' => $course->aeo_summary,
            ]))
            ->assertRedirect(route('admin.courses.index'))
            ->assertSessionHasNoErrors();
    }

    public function test_course_editor_persists_safe_metadata_and_changes_public_outputs(): void
    {
        $category = CourseCategory::factory()->create();
        $course = Course::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'schema_markup' => '{"preserved":true}',
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.courses.create'))
            ->assertOk()
            ->assertSee('name="meta_title"', false)
            ->assertSee('maxlength="70"', false)
            ->assertSee('name="meta_description"', false)
            ->assertSee('maxlength="500"', false)
            ->assertSee('name="meta_keywords"', false)
            ->assertSee('name="aeo_summary"', false)
            ->assertSee('Leave fields empty to use the public fallback.', false)
            ->assertDontSee('name="schema_markup"', false)
            ->assertDontSee('name="canonical_url"', false);

        $this->actingAs($this->admin)
            ->put(route('admin.courses.update', $course), $this->coursePayload($course, $category, [
                'meta_title' => 'Staff Managed Course Search Title',
                'meta_description' => 'Staff managed course description for search, sharing, and structured data.',
                'meta_keywords' => 'course, Pokhara, academy',
                'aeo_summary' => 'This is the factual staff-managed course answer summary.',
                'schema_markup' => '<script>untrusted</script>',
            ]))
            ->assertRedirect(route('admin.courses.index'))
            ->assertSessionHasNoErrors();

        $course->refresh();
        $this->assertSame('Staff Managed Course Search Title', $course->meta_title);
        $this->assertSame('Staff managed course description for search, sharing, and structured data.', $course->meta_description);
        $this->assertSame('course, Pokhara, academy', $course->meta_keywords);
        $this->assertSame('This is the factual staff-managed course answer summary.', $course->aeo_summary);
        $this->assertSame('{"preserved":true}', $course->schema_markup);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('<title>Staff Managed Course Search Title - Golden Eye Academy</title>', false)
            ->assertSee('name="description" content="Staff managed course description for search, sharing, and structured data."', false)
            ->assertSee('property="og:title" content="Staff Managed Course Search Title - Golden Eye Academy"', false)
            ->assertSee('property="og:description" content="Staff managed course description for search, sharing, and structured data."', false)
            ->assertSee('name="aeo-summary" content="This is the factual staff-managed course answer summary."', false)
            ->assertSee('"description":"Staff managed course description for search, sharing, and structured data."', false)
            ->assertDontSee('<script>untrusted</script>', false);
    }

    public function test_blank_course_metadata_uses_nonempty_public_fallbacks(): void
    {
        $category = CourseCategory::factory()->create();
        $course = Course::factory()->create([
            'name' => 'Fallback Course Name',
            'slug' => 'fallback-course-name',
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'description' => '<p>Visible course copy supplies the safe fallback description.</p>',
            'meta_title' => 'Old title',
            'meta_description' => 'Old description',
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.courses.update', $course), $this->coursePayload($course, $category, [
                'meta_title' => '',
                'meta_description' => '',
                'meta_keywords' => '',
                'aeo_summary' => '',
            ]))
            ->assertRedirect(route('admin.courses.index'));

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('<title>Fallback Course Name - Golden Eye Academy</title>', false)
            ->assertSee('name="description" content="Visible course copy supplies the safe fallback description."', false)
            ->assertDontSee('<title></title>', false)
            ->assertDontSee('name="description" content=""', false);
    }

    public function test_blog_metadata_remains_publicly_wired_without_changing_status_or_relationships(): void
    {
        $relatedCourse = Course::factory()->create(['status' => 'active']);
        $post = BlogPost::factory()->create([
            'title' => 'Public SEO Article',
            'slug' => 'public-seo-article',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);
        $post->courses()->sync([$relatedCourse->id]);

        $this->actingAs($this->admin)
            ->put(route('admin.blog.update', $post), $this->blogPayload($post, [
                'meta_title' => 'Edited Blog Search Title',
                'meta_description' => 'Edited blog description that reaches search, sharing, and article data.',
                'meta_keywords' => 'blog, course, academy',
                'aeo_summary' => 'Edited factual blog answer summary.',
                'courses_present' => '1',
                'courses' => [$relatedCourse->id],
            ]))
            ->assertRedirect(route('admin.blog.index'))
            ->assertSessionHasNoErrors();

        $post->refresh();
        $this->assertSame('published', $post->status);
        $this->assertSame([$relatedCourse->id], $post->courses()->pluck('courses.id')->all());

        $this->get(route('blog-detail', $post->slug))
            ->assertOk()
            ->assertSee('<title>Edited Blog Search Title - Golden Eye Academy</title>', false)
            ->assertSee('name="description" content="Edited blog description that reaches search, sharing, and article data."', false)
            ->assertSee('property="og:title" content="Edited Blog Search Title - Golden Eye Academy"', false)
            ->assertSee('name="aeo-summary" content="Edited factual blog answer summary."', false)
            ->assertSee('"description":"Edited blog description that reaches search, sharing, and article data."', false);

        $draft = BlogPost::factory()->draft()->create(['slug' => 'private-draft-seo']);
        $this->get(route('blog-detail', $draft->slug))->assertNotFound();

        $this->actingAs($this->admin)
            ->get(route('admin.blog.edit', $post))
            ->assertOk()
            ->assertDontSee('name="schema_markup"', false)
            ->assertDontSee('name="canonical_url"', false);
    }

    public function test_notice_popup_precedence_is_visible_in_cms_and_returns_to_branding_when_disabled(): void
    {
        SiteSetting::query()->insert([
            ['key' => 'popup_status', 'value' => 'active', 'type' => 'text'],
            ['key' => 'popup_title', 'value' => 'Branding Campaign Authority', 'type' => 'text'],
            ['key' => 'popup_subtitle', 'value' => 'Branding campaign subtitle.', 'type' => 'text'],
            ['key' => 'popup_button_text', 'value' => 'Ask for Course Help', 'type' => 'text'],
        ]);

        Cache::forget('site_shared_data');
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Branding Campaign Authority')
            ->assertSee('data-cta="navbar-course-help"', false)
            ->assertSee("return link.dataset.popupTrigger === 'campaign';", false);

        $notice = Notice::factory()->create([
            'title' => 'Notice Popup Authority',
            'subtitle' => 'Notice popup subtitle.',
            'status' => 'active',
            'display_type' => 'popup',
            'starts_at' => null,
            'expires_at' => null,
        ]);

        Cache::forget('site_shared_data');
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Notice Popup Authority')
            ->assertDontSee('Branding Campaign Authority');

        $this->actingAs($this->admin)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertSee('Main Campaign Popup is currently overridden')
            ->assertSee('Notice Popup Authority')
            ->assertSee(route('admin.notices.edit', $notice), false);

        $this->actingAs($this->admin)
            ->get(route('admin.notices.create'))
            ->assertOk()
            ->assertSee('An active popup-style Notice temporarily overrides the Main Campaign Popup.');

        $this->actingAs($this->admin)
            ->patch(route('admin.notices.toggle', $notice))
            ->assertRedirect();

        $this->assertSame('inactive', $notice->fresh()->status);
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Branding Campaign Authority')
            ->assertDontSee('Notice Popup Authority');
        $this->get(route('join-now'))->assertOk()->assertSee('Ask for Course Help');
    }

    public function test_fresh_popup_seed_has_one_unambiguous_popup_authority_and_is_idempotent(): void
    {
        $this->seed([NoticeSeeder::class, SiteSettingSeeder::class]);
        $this->seed([NoticeSeeder::class, SiteSettingSeeder::class]);

        $this->assertSame('active', SiteSetting::getValue('popup_status'));
        $this->assertSame(0, Notice::query()->where('status', 'active')->whereIn('display_type', ['popup', 'standard'])->count());
        $this->assertSame(1, Notice::query()->where('status', 'active')->where('display_type', 'bar')->count());
        $this->assertSame(3, Notice::query()->count());

        Cache::forget('site_shared_data');
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Need course information before enrollment?')
            ->assertDontSee('Course Information Before Enrollment');
    }

    public function test_service_pillar_cms_no_longer_promises_homepage_feature_and_preserves_legacy_value(): void
    {
        $pillar = ServicePillar::factory()->create(['is_featured' => true]);

        $this->actingAs($this->admin)
            ->get(route('admin.service-pillars.edit', $pillar))
            ->assertOk()
            ->assertDontSee('Feature on Homepage')
            ->assertDontSee('name="is_featured"', false)
            ->assertSee('Service Pillars are ordered catalogue content. They are not displayed on the homepage.');

        $this->actingAs($this->admin)
            ->get(route('admin.service-pillars.index'))
            ->assertOk()
            ->assertDontSee('Control the homepage catalogue')
            ->assertDontSee('>Featured<', false);

        $this->actingAs($this->admin)
            ->put(route('admin.service-pillars.update', $pillar), [
                'title' => 'Edited Catalogue Pillar',
                'slug' => $pillar->slug,
                'summary' => $pillar->summary,
                'bullets' => $pillar->bullets,
                'cta_label' => $pillar->cta_label,
                'cta_url' => $pillar->cta_url,
                'status' => $pillar->status,
                'sort_order' => $pillar->sort_order,
            ])
            ->assertRedirect(route('admin.service-pillars.index'));

        $this->assertTrue($pillar->fresh()->is_featured);
        $this->get(route('home'))->assertOk()->assertDontSee('Edited Catalogue Pillar');
    }

    public function test_business_success_messages_are_editable_in_existing_branding_content_cms(): void
    {
        foreach (['contact_success_message', 'newsletter_success_message', 'enroll_success_message'] as $key) {
            SiteSetting::query()->create(['key' => $key, 'value' => 'Original message', 'type' => 'text']);
        }

        $this->actingAs($this->admin)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertSee('name="contact_success_message"', false)
            ->assertSee('name="newsletter_success_message"', false)
            ->assertSee('name="enroll_success_message"', false);

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'contact_success_message' => 'Contact confirmation managed by staff.',
                'newsletter_success_message' => 'Newsletter confirmation managed by staff.',
                'enroll_success_message' => 'Course Help confirmation managed by staff.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('Contact confirmation managed by staff.', SiteSetting::getValue('contact_success_message'));
        $this->assertSame('Newsletter confirmation managed by staff.', SiteSetting::getValue('newsletter_success_message'));
        $this->assertSame('Course Help confirmation managed by staff.', SiteSetting::getValue('enroll_success_message'));
    }

    /** @param  array<string, mixed>  $overrides */
    private function coursePayload(Course $course, CourseCategory $category, array $overrides = []): array
    {
        return array_merge([
            'name' => $course->name,
            'slug' => $course->slug,
            'category_id' => $category->id,
            'price' => $course->price,
            'duration' => $course->duration,
            'instructor' => $course->instructor,
            'teacher_id' => $course->teacher_id,
            'capacity' => $course->capacity,
            'description' => $course->description,
            'course_outline' => $course->course_outline,
            'status' => $course->status,
            'is_featured' => (int) $course->is_featured,
            'display_order' => $course->display_order,
        ], $overrides);
    }

    /** @param  array<string, mixed>  $overrides */
    private function blogPayload(BlogPost $post, array $overrides = []): array
    {
        return array_merge([
            'title' => $post->title,
            'slug' => $post->slug,
            'author' => $post->author,
            'category' => $post->category,
            'content' => $post->content,
            'status' => $post->status,
        ], $overrides);
    }
}
