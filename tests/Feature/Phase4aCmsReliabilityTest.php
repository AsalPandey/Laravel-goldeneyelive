<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\FAQ;
use App\Models\Notice;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class Phase4aCmsReliabilityTest extends TestCase
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

    public function test_primary_about_page_uses_full_cms_content_and_legacy_route_redirects(): void
    {
        SiteSetting::upsert([
            ['key' => 'about_content_title', 'value' => 'Structured About Heading', 'type' => 'text'],
            ['key' => 'about_content', 'value' => '<p>Short structured summary.</p>', 'type' => 'text'],
            ['key' => 'about_page_content', 'value' => '<h3>Unique CMS About Story</h3><p>Managed full story body.</p>', 'type' => 'text'],
        ], ['key'], ['value', 'type']);
        Cache::flush();

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Structured About Heading')
            ->assertSee('Unique CMS About Story')
            ->assertSee('Managed full story body.')
            ->assertSee('Questions we help you answer');

        $this->get('/about-detail')
            ->assertRedirect('/about')
            ->assertStatus(301);
    }

    public function test_course_page_uses_only_explicit_active_faculty_and_testimonial_relationships(): void
    {
        $teacher = Teacher::factory()->create([
            'name' => 'Verified Course Faculty',
            'designation' => 'IELTS Faculty',
            'bio' => 'Verified course-specific faculty biography.',
            'status' => 'active',
        ]);
        Teacher::factory()->create([
            'name' => 'Unrelated Global Faculty',
            'bio' => 'This unrelated biography must not appear.',
            'status' => 'active',
            'is_featured' => true,
        ]);

        $course = Course::factory()->create([
            'name' => 'Exact Relationship Course',
            'slug' => 'exact-relationship-course',
            'instructor' => $teacher->name,
            'teacher_id' => $teacher->id,
            'status' => 'active',
        ]);
        Testimonial::factory()->create([
            'student_name' => 'Exact Course Student',
            'course_name' => $course->name,
            'course_id' => $course->id,
            'content' => 'Exact course testimonial proof.',
            'status' => 'active',
        ]);
        Testimonial::factory()->create([
            'student_name' => 'Unrelated Global Student',
            'course_name' => 'Different Course',
            'content' => 'Unrelated testimonial proof.',
            'status' => 'active',
            'is_featured' => true,
        ]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Verified Course Faculty')
            ->assertSee('Verified course-specific faculty biography.')
            ->assertSee('Exact Course Student')
            ->assertSee('Exact course testimonial proof.')
            ->assertDontSee('Unrelated Global Faculty')
            ->assertDontSee('Unrelated Global Student');
    }

    public function test_course_page_uses_an_honest_neutral_state_when_no_verified_faculty_exists(): void
    {
        Teacher::factory()->create([
            'name' => 'Unrelated Faculty Profile',
            'status' => 'active',
            'is_featured' => true,
        ]);
        Testimonial::factory()->create([
            'student_name' => 'Unrelated Testimonial Student',
            'course_name' => 'Another Course',
            'status' => 'active',
            'is_featured' => true,
        ]);
        $course = Course::factory()->create([
            'name' => 'Course Without Verified Associations',
            'slug' => 'course-without-verified-associations',
            'instructor' => 'Unverified Legacy Name',
            'status' => 'active',
        ]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertDontSee('Ask the academy to confirm the faculty assigned to the current batch.')
            ->assertDontSee('Instructor credentials')
            ->assertDontSee('Unrelated Faculty Profile')
            ->assertDontSee('Unrelated Testimonial Student')
            ->assertDontSee('Instructor profile');
    }

    public function test_course_and_testimonial_forms_offer_existing_records_for_association(): void
    {
        $teacher = Teacher::factory()->create(['name' => 'Selectable Faculty']);
        $course = Course::factory()->create(['name' => 'Selectable Course']);

        $this->actingAs($this->staff)
            ->get(route('admin.courses.create'))
            ->assertOk()
            ->assertSee('Linked Faculty Profile')
            ->assertSee('value="'.$teacher->id.'"', false);

        $this->actingAs($this->staff)
            ->get(route('admin.testimonials.create'))
            ->assertOk()
            ->assertSee('Related Course')
            ->assertSee('value="'.$course->id.'"', false);
    }

    public function test_public_blog_queries_obey_draft_immediate_scheduled_and_unpublished_states(): void
    {
        $visible = BlogPost::factory()->create([
            'title' => 'Visible Published Article',
            'slug' => 'visible-published-article',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);
        $draft = BlogPost::factory()->draft()->create([
            'title' => 'Hidden Draft Article',
            'slug' => 'hidden-draft-article',
            'published_at' => now()->subDay(),
        ]);
        $scheduled = BlogPost::factory()->create([
            'title' => 'Future Scheduled Article',
            'slug' => 'future-scheduled-article',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);
        $missingDate = BlogPost::factory()->create([
            'title' => 'Published Without Date',
            'slug' => 'published-without-date',
            'status' => 'published',
            'published_at' => null,
        ]);

        $this->get(route('blog'))
            ->assertOk()
            ->assertSee($visible->title)
            ->assertDontSee($draft->title)
            ->assertDontSee($scheduled->title)
            ->assertDontSee($missingDate->title);

        $this->get(route('blog-detail', $visible->slug))->assertOk();
        $this->get(route('blog-detail', $draft->slug))->assertNotFound();
        $this->get(route('blog-detail', $scheduled->slug))->assertNotFound();
        $this->get(route('blog-detail', $missingDate->slug))->assertNotFound();
    }

    public function test_admin_blog_actions_preserve_schedule_and_assign_immediate_publication_time(): void
    {
        $now = Carbon::parse('2026-07-29 08:00:00');
        $future = $now->copy()->addDays(2);
        $expectedStoredFuture = Carbon::parse($future->format('Y-m-d H:i:s'), 'Asia/Kathmandu')->utc();
        $this->travelTo($now);

        $this->actingAs($this->admin)
            ->post(route('admin.blog.store'), [
                'title' => 'Scheduled CMS Article',
                'slug' => 'scheduled-cms-article',
                'content' => '<p>Scheduled content.</p>',
                'status' => 'published',
                'published_at' => $future->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect(route('admin.blog.index'));

        $scheduled = BlogPost::where('slug', 'scheduled-cms-article')->firstOrFail();
        $this->assertTrue($scheduled->published_at->equalTo($expectedStoredFuture));

        $this->actingAs($this->admin)
            ->patch(route('admin.blog.toggle-status', $scheduled))
            ->assertRedirect();
        $scheduled->refresh();
        $this->assertSame('draft', $scheduled->status);
        $this->assertTrue($scheduled->published_at->equalTo($expectedStoredFuture));

        $this->actingAs($this->admin)
            ->patch(route('admin.blog.toggle-status', $scheduled))
            ->assertRedirect();
        $scheduled->refresh();
        $this->assertSame('published', $scheduled->status);
        $this->assertTrue($scheduled->published_at->equalTo($expectedStoredFuture));

        $this->actingAs($this->admin)
            ->post(route('admin.blog.store'), [
                'title' => 'Immediate CMS Article',
                'slug' => 'immediate-cms-article',
                'content' => '<p>Immediate content.</p>',
                'status' => 'published',
            ])
            ->assertRedirect(route('admin.blog.index'));

        $this->assertTrue(
            BlogPost::where('slug', 'immediate-cms-article')->firstOrFail()->published_at->equalTo($now),
        );
    }

    public function test_faq_admin_and_public_use_lower_numbers_first_without_exposing_inactive_records(): void
    {
        SiteSetting::upsert([
            ['key' => 'faq_btn_text', 'value' => 'Reveal Phase 4A Answers', 'type' => 'text'],
            ['key' => 'faq_btn_text_expanded', 'value' => 'Hide Phase 4A Answers', 'type' => 'text'],
        ], ['key'], ['value', 'type']);
        FAQ::factory()->create(['question' => 'Second Ordered Question', 'order_priority' => 20, 'status' => 'active']);
        FAQ::factory()->create(['question' => 'First Ordered Question', 'order_priority' => 5, 'status' => 'active']);
        FAQ::factory()->create(['question' => 'Inactive Ordered Question', 'order_priority' => 0, 'status' => 'inactive']);
        FAQ::factory()->count(9)->sequence(
            fn ($sequence): array => [
                'question' => 'Additional Ordered Question '.($sequence->index + 1),
                'order_priority' => 30 + $sequence->index,
                'status' => 'active',
            ],
        )->create();
        Cache::flush();

        $html = $this->get(route('faq'))
            ->assertOk()
            ->assertSee('Reveal Phase 4A Answers')
            ->assertSee('Hide Phase 4A Answers', false)
            ->assertDontSee('Inactive Ordered Question')
            ->getContent();

        $this->assertLessThan(
            strpos($html, 'Second Ordered Question'),
            strpos($html, 'First Ordered Question'),
        );

        $this->actingAs($this->staff)
            ->get(route('admin.faq.create'))
            ->assertOk()
            ->assertSee('Lower numbers appear first');

        $this->actingAs($this->staff)
            ->post(route('admin.faq.store'), [
                'question' => 'Invalid negative order',
                'answer' => 'This should be rejected.',
                'status' => 'active',
                'order_priority' => -1,
            ])
            ->assertSessionHasErrors('order_priority');
    }

    public function test_admin_can_manage_popup_contact_notice_and_media_fields_and_technical_controls(): void
    {
        $branding = $this->actingAs($this->admin)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertSee('Website Content')
            ->assertSee('name="popup_status"', false)
            ->assertSee('name="popup_image_path"', false)
            ->assertSee('name="site_phone"', false)
            ->assertSee('name="whatsapp_number"', false)
            ->assertDontSee('name="recaptcha_secret_key"', false)
            ->assertSee('name="google_analytics_id"', false)
            ->assertSee('Permanently Delete Unused');

        $this->assertStringContainsString('openPicker', $branding->getContent());

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'popup_status' => 'active',
                'popup_title' => 'Staff Managed Popup',
                'popup_subtitle' => 'Staff managed popup description.',
                'popup_button_text' => 'View Course Details',
                'popup_register_link' => '/courses-all',
                'site_phone' => '061-500000',
                'whatsapp_number' => '9779800000000',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'popup_status', 'value' => 'active']);
        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'popup_title', 'value' => 'Staff Managed Popup']);
        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'site_phone', 'value' => '061-500000']);
        $this->assertDatabaseMissing(SiteSetting::class, ['key' => 'google_analytics_id']);
        $this->assertDatabaseMissing(SiteSetting::class, ['key' => 'recaptcha_secret_key']);

        $this->actingAs($this->admin)
            ->post(route('admin.notices.store'), [
                'badge' => 'Batch Update',
                'title' => 'Staff Managed Announcement',
                'subtitle' => 'Current batch information.',
                'button_text' => 'View Course Details',
                'link' => '/courses-all',
                'status' => 'active',
                'display_type' => 'bar',
                'is_urgent' => true,
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->assertDatabaseHas(Notice::class, [
            'badge' => 'Batch Update',
            'title' => 'Staff Managed Announcement',
            'status' => 'active',
            'display_type' => 'bar',
        ]);

        Cache::flush();
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Staff Managed Popup')
            ->assertSee('Staff Managed Announcement')
            ->assertSee('siteNoticePopup', false);
    }

    public function test_guest_and_student_are_denied_while_admin_only_routes_remain_restricted_to_staff(): void
    {
        $this->get(route('admin.branding.index'))->assertRedirect(route('login'));

        $this->actingAs($this->student)
            ->get(route('admin.branding.index'))
            ->assertForbidden();

        $this->actingAs($this->staff)
            ->get(route('admin.seo.index'))
            ->assertForbidden();

        $this->actingAs($this->staff)
            ->post(route('admin.branding.asset.purge'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('admin.seo.index'))
            ->assertOk();
    }
}
