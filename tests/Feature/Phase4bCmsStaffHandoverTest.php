<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use App\Support\CmsDateTime;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class Phase4bCmsStaffHandoverTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staff;

    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('Staff');

        $this->student = User::factory()->create();
        $this->student->assignRole('Student');
    }

    public function test_admin_can_manage_homepage_copy_cards_and_default_visible_section_controls(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertSeeText('Homepage Sections & Cards')
            ->assertSee('name="home_audience_status"', false)
            ->assertSee('name="home_audience_students_title"', false)
            ->assertSee('name="audience_parents_headline"', false)
            ->assertSee('name="audience_students_image_path"', false)
            ->assertSee("openPicker('input_audience_students_image')", false)
            ->assertSee('Main Campaign Popup')
            ->assertSeeText('Popup-style notices')
            ->assertSeeText('top announcement bar');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Choose the path that fits your goal.')
            ->assertSee('Know what you are joining.');

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'home_audience_status' => 'inactive',
                'home_audience_title' => 'Unique retained audience heading',
                'home_audience_students_title' => 'Unique student pathway',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'home_audience_title',
            'value' => 'Unique retained audience heading',
        ]);
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Unique retained audience heading')
            ->assertDontSee('Unique student pathway');

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), ['home_audience_status' => 'active'])
            ->assertRedirect();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Unique retained audience heading')
            ->assertSee('Unique student pathway');

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'audience_students_secondary_action' => 'unsafe-destination',
            ])
            ->assertSessionHasErrors('audience_students_secondary_action');
    }

    public function test_audience_page_content_sections_visibility_and_sitemap_follow_cms_state(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'audience_students_status' => 'active',
                'audience_students_hero_status' => 'inactive',
                'audience_students_guidance_status' => 'inactive',
                'audience_students_support_status' => 'inactive',
                'audience_students_final_status' => 'inactive',
            ])
            ->assertSessionHasErrors('audience_students_status');

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'audience_students_status' => 'active',
                'audience_students_hero_status' => 'active',
                'audience_students_guidance_status' => 'inactive',
                'audience_students_support_status' => 'active',
                'audience_students_final_status' => 'active',
                'audience_students_headline' => 'Unique student CMS headline',
                'audience_students_problem' => 'Retained guidance content marker',
                'audience_students_primary_cta_text' => 'Ask our course team',
                'audience_students_secondary_action' => 'courses',
                'audience_students_secondary_cta_text' => 'Compare all courses',
            ])
            ->assertRedirect();

        $this->get(route('for-students'))
            ->assertOk()
            ->assertSee('Unique student CMS headline')
            ->assertSee('Ask our course team')
            ->assertSee('Compare all courses')
            ->assertDontSee('Retained guidance content marker');

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), ['audience_students_status' => 'inactive'])
            ->assertRedirect();

        $this->get(route('for-students'))->assertNotFound();
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertDontSee(route('for-students'));

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'audience_students_status' => 'active',
                'audience_students_guidance_status' => 'active',
            ])
            ->assertRedirect();

        $this->get(route('for-students'))
            ->assertOk()
            ->assertSee('Retained guidance content marker');
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('for-students'));
    }

    public function test_structured_homepage_and_audience_fields_render_at_their_exact_public_consumers(): void
    {
        $course = Course::factory()->create(['status' => 'active']);
        CourseCategory::factory()->create(['status' => 'active']);
        Teacher::factory()->create(['status' => 'active']);
        Testimonial::factory()->create([
            'course_name' => $course->name,
            'course_id' => $course->id,
            'status' => 'active',
        ]);
        FAQ::factory()->create(['status' => 'active']);
        SiteSetting::create([
            'key' => 'google_business_profile_url',
            'value' => 'https://example.test/reviews',
            'type' => 'text',
        ]);

        $homepageFields = [
            'home_meta_title' => 'CMS4B homepage meta title',
            'home_meta_description' => 'CMS4B homepage meta description',
            'home_trust_items' => 'CMS4B trust item',
            'home_audience_tagline' => 'CMS4B audience tagline',
            'home_audience_title' => 'CMS4B audience title',
            'home_courses_tagline' => 'CMS4B courses tagline',
            'home_courses_title' => 'CMS4B courses title',
            'home_courses_cta_text' => 'CMS4B courses action',
            'home_categories_tagline' => 'CMS4B categories tagline',
            'home_categories_title' => 'CMS4B categories title',
            'home_why_tagline' => 'CMS4B why tagline',
            'home_why_title' => 'CMS4B why title',
            'home_why_description' => 'CMS4B why description',
            'home_why_items' => 'CMS4B why item',
            'home_testimonials_tagline' => 'CMS4B testimonial tagline',
            'home_testimonials_title' => 'CMS4B testimonial title',
            'home_faculty_tagline' => 'CMS4B faculty tagline',
            'home_faculty_title' => 'CMS4B faculty title',
            'home_reviews_tagline' => 'CMS4B review tagline',
            'home_reviews_title' => 'CMS4B review title',
            'home_parent_tagline' => 'CMS4B parent tagline',
            'home_parent_title' => 'CMS4B parent title',
            'home_parent_description' => 'CMS4B parent description',
            'home_parent_items' => 'CMS4B parent item',
            'home_faq_tagline' => 'CMS4B FAQ tagline',
            'home_faq_title' => 'CMS4B FAQ title',
            'home_final_tagline' => 'CMS4B final tagline',
            'home_final_title' => 'CMS4B final title',
            'home_final_description' => 'CMS4B final description',
            'home_final_primary_cta_text' => 'CMS4B primary action',
            'home_final_secondary_cta_text' => 'CMS4B WhatsApp action',
            'home_audience_students_title' => 'CMS4B student card title',
            'home_audience_students_problem' => 'CMS4B student card problem',
            'home_audience_students_benefit' => 'CMS4B student card benefit',
            'home_audience_students_cta_text' => 'CMS4B student card action',
        ];
        $audienceFields = [
            'audience_students_page_title' => 'CMS4B audience meta title',
            'audience_students_meta_description' => 'CMS4B audience meta description',
            'audience_students_image_path' => 'site/img/cat-1.jpg',
            'audience_students_badge' => 'CMS4B badge',
            'audience_students_headline' => 'CMS4B headline',
            'audience_students_subheadline' => 'CMS4B subheadline',
            'audience_students_problem_tagline' => 'CMS4B problem tagline',
            'audience_students_problem_title' => 'CMS4B problem title',
            'audience_students_problem' => 'CMS4B problem copy',
            'audience_students_paths_tagline' => 'CMS4B paths tagline',
            'audience_students_paths_title' => 'CMS4B paths title',
            'audience_students_paths' => 'CMS4B path item',
            'audience_students_support_tagline' => 'CMS4B support tagline',
            'audience_students_support_title' => 'CMS4B support title',
            'audience_students_why' => 'CMS4B support copy',
            'audience_students_proof_tagline' => 'CMS4B proof tagline',
            'audience_students_proof' => 'CMS4B proof item',
            'audience_students_final_tagline' => 'CMS4B audience final tagline',
            'audience_students_final_headline' => 'CMS4B audience final headline',
            'audience_students_primary_cta_text' => 'CMS4B audience primary action',
            'audience_students_secondary_action' => 'courses',
            'audience_students_secondary_cta_text' => 'CMS4B audience secondary action',
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [...$homepageFields, ...$audienceFields])
            ->assertRedirect();

        $homepage = $this->get(route('home'))->assertOk();
        foreach ($homepageFields as $key => $value) {
            str_contains($key, 'meta_description')
                ? $homepage->assertSee($value)
                : $homepage->assertSeeText($value);
        }

        $audience = $this->get(route('for-students'))->assertOk();
        foreach ($audienceFields as $key => $value) {
            if (in_array($key, ['audience_students_image_path', 'audience_students_secondary_action'], true)) {
                continue;
            }

            str_contains($key, 'meta_description')
                ? $audience->assertSee($value)
                : $audience->assertSeeText($value);
        }
        $audience->assertSee('site/img/cat-1.jpg', false);
    }

    public function test_staff_preview_uses_real_templates_without_publishing_draft_or_inactive_content(): void
    {
        $draft = BlogPost::factory()->draft()->create([
            'title' => 'Private Draft Preview Article',
            'slug' => 'private-draft-preview-article',
            'content' => '<p>Draft preview content marker.</p>',
            'published_at' => null,
        ]);
        $course = Course::factory()->create([
            'name' => 'Inactive Preview Course',
            'slug' => 'inactive-preview-course',
            'status' => 'inactive',
        ]);

        $this->get(route('blog-detail', $draft->slug))->assertNotFound();
        $this->get(route('courses-detail', $course->slug))->assertNotFound();
        $this->get(route('admin.blog.preview', $draft))->assertRedirect(route('login'));
        $this->actingAs($this->student)
            ->get(route('admin.blog.preview', $draft))
            ->assertForbidden();

        $this->actingAs($this->staff)
            ->get(route('admin.blog.preview', $draft))
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive')
            ->assertSee('Staff preview:')
            ->assertSee('Draft preview content marker.')
            ->assertSee('<meta name="robots" content="noindex, nofollow, noarchive">', false)
            ->assertSee('href="'.route('blog-detail', $draft->slug).'"', false);

        $this->actingAs($this->staff)
            ->get(route('admin.courses.preview', $course))
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive')
            ->assertSee('Staff preview:')
            ->assertSee('Inactive Preview Course')
            ->assertSee('<meta name="robots" content="noindex, nofollow, noarchive">', false);
    }

    public function test_staff_cannot_permanently_delete_cms_content_and_admin_can_delete(): void
    {
        $records = [
            [Course::factory()->create(), 'admin.courses.destroy'],
            [CourseCategory::factory()->create(), 'admin.categories.destroy'],
            [ServicePillar::factory()->create(), 'admin.service-pillars.destroy'],
            [BlogPost::factory()->draft()->create(), 'admin.blog.destroy'],
            [FAQ::factory()->create(), 'admin.faq.destroy'],
            [Teacher::factory()->create(), 'admin.teachers.destroy'],
            [Testimonial::factory()->create(), 'admin.testimonials.destroy'],
            [Notice::factory()->create(), 'admin.notices.destroy'],
        ];

        foreach ($records as [$record, $routeName]) {
            $this->actingAs($this->staff)
                ->delete(route($routeName, $record))
                ->assertForbidden();
            $this->assertDatabaseHas($record->getTable(), ['id' => $record->getKey()]);
        }

        $adminFaq = FAQ::factory()->create();
        $this->actingAs($this->admin)
            ->delete(route('admin.faq.destroy', $adminFaq))
            ->assertRedirect();
        $this->assertDatabaseMissing($adminFaq->getTable(), ['id' => $adminFaq->getKey()]);

        $staffIndex = $this->actingAs($this->staff)
            ->get(route('admin.blog.index'))
            ->assertOk();
        $staffIndex->assertDontSee('action="'.route('admin.blog.destroy', $records[3][0]).'"', false);
    }

    public function test_nepal_admin_schedules_are_stored_in_utc_and_respect_public_boundaries(): void
    {
        $this->travelTo(Carbon::parse('2025-12-31 18:29:00', 'UTC'));

        $this->actingAs($this->admin)
            ->post(route('admin.blog.store'), [
                'title' => 'Kathmandu Boundary Article',
                'slug' => 'kathmandu-boundary-article',
                'content' => '<p>Timezone boundary content.</p>',
                'status' => 'published',
                'published_at' => '2026-01-01T00:15',
            ])
            ->assertRedirect(route('admin.blog.index'));

        $post = BlogPost::where('slug', 'kathmandu-boundary-article')->firstOrFail();
        $this->assertSame('2025-12-31 18:30:00', $post->published_at->utc()->format('Y-m-d H:i:s'));
        $this->get(route('blog-detail', $post->slug))->assertNotFound();

        $this->actingAs($this->admin)
            ->post(route('admin.notices.store'), [
                'title' => 'Kathmandu Boundary Notice',
                'status' => 'active',
                'display_type' => 'bar',
                'button_text' => 'View Course Details',
                'link' => '/courses-all',
                'starts_at' => '2026-01-01T00:15',
                'expires_at' => '2026-01-01T00:20',
            ])
            ->assertRedirect(route('admin.notices.index'));

        $notice = Notice::where('title', 'Kathmandu Boundary Notice')->firstOrFail();
        $this->assertSame('2025-12-31 18:30:00', $notice->starts_at->utc()->format('Y-m-d H:i:s'));
        $this->assertSame('2025-12-31 18:35:00', $notice->expires_at->utc()->format('Y-m-d H:i:s'));
        $this->get(route('home'))->assertDontSee('Kathmandu Boundary Notice');

        $this->travelTo(Carbon::parse('2025-12-31 18:30:00', 'UTC'));
        $this->get(route('blog-detail', $post->slug))->assertOk();
        cache()->flush();
        $this->get(route('home'))->assertSee('Kathmandu Boundary Notice');

        $this->travelTo(Carbon::parse('2025-12-31 18:35:01', 'UTC'));
        cache()->flush();
        $this->get(route('home'))->assertDontSee('Kathmandu Boundary Notice');

        $historicalPost = BlogPost::factory()->create([
            'status' => 'published',
            'published_at' => Carbon::parse('2025-06-01 04:15:37', 'UTC'),
        ]);
        $this->actingAs($this->admin)
            ->put(route('admin.blog.update', $historicalPost), [
                'title' => $historicalPost->title,
                'slug' => $historicalPost->slug,
                'content' => $historicalPost->content,
                'status' => $historicalPost->status,
                'published_at' => CmsDateTime::forStaffInput($historicalPost->published_at),
            ])
            ->assertRedirect(route('admin.blog.index'));
        $this->assertSame(
            '2025-06-01 04:15:37',
            $historicalPost->fresh()->published_at->utc()->format('Y-m-d H:i:s'),
        );

        $historicalNotice = Notice::factory()->create([
            'status' => 'inactive',
            'starts_at' => Carbon::parse('2025-06-01 04:15:37', 'UTC'),
            'expires_at' => Carbon::parse('2025-06-02 04:15:49', 'UTC'),
        ]);
        $this->actingAs($this->admin)
            ->put(route('admin.notices.update', $historicalNotice), [
                'title' => $historicalNotice->title,
                'status' => $historicalNotice->status,
                'display_type' => $historicalNotice->display_type,
                'starts_at' => CmsDateTime::forStaffInput($historicalNotice->starts_at),
                'expires_at' => CmsDateTime::forStaffInput($historicalNotice->expires_at),
            ])
            ->assertRedirect(route('admin.notices.index'));
        $this->assertSame(
            '2025-06-01 04:15:37',
            $historicalNotice->fresh()->starts_at->utc()->format('Y-m-d H:i:s'),
        );
        $this->assertSame(
            '2025-06-02 04:15:49',
            $historicalNotice->fresh()->expires_at->utc()->format('Y-m-d H:i:s'),
        );
    }

    public function test_unmatched_course_relationships_do_not_publish_internal_confirmation_placeholders(): void
    {
        Teacher::factory()->create(['name' => 'Different Active Faculty', 'status' => 'active']);
        Testimonial::factory()->create([
            'student_name' => 'Different Course Student',
            'course_name' => 'Another Course',
            'status' => 'active',
        ]);
        $course = Course::factory()->create([
            'name' => 'Course With Legacy Relationship Values',
            'slug' => 'course-with-legacy-relationship-values',
            'instructor' => 'Legacy Unmatched Faculty',
            'status' => 'active',
        ]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertDontSee('confirmation pending', false)
            ->assertDontSee('Ask the academy to confirm the faculty assigned to the current batch.')
            ->assertDontSee('Instructor credentials')
            ->assertDontSee('Different Active Faculty')
            ->assertDontSee('Different Course Student');
    }

    public function test_cms_module_permissions_allow_staff_content_work_and_deny_students_and_sensitive_settings(): void
    {
        $staffRoutes = [
            'admin.courses.index',
            'admin.categories.index',
            'admin.service-pillars.index',
            'admin.blog.index',
            'admin.faq.index',
            'admin.teachers.index',
            'admin.testimonials.index',
            'admin.notices.index',
            'admin.media.index',
        ];

        foreach ($staffRoutes as $routeName) {
            $this->actingAs($this->staff)->get(route($routeName))->assertOk();
            $this->actingAs($this->student)->get(route($routeName))->assertForbidden();
        }

        $this->actingAs($this->staff)
            ->get(route('admin.branding.index'))
            ->assertForbidden();
        $this->actingAs($this->staff)->get(route('admin.seo.index'))->assertForbidden();
        $this->actingAs($this->admin)->get(route('admin.seo.index'))->assertOk();
    }
}
