<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\NewsLetter;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use App\Traits\InteractsWithAssets;
use Database\Seeders\RoleSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Tests\TestCase;
use Throwable;

class Phase2bCmsSafetyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
        $this->staff = User::factory()->create();
        $this->staff->assignRole('Staff');
    }

    public function test_branding_forms_are_siblings_and_buttons_target_the_intended_routes(): void
    {
        $html = $this->actingAs($this->admin)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->getContent();

        $xpath = $this->xpath($html);

        $this->assertSame(0, $xpath->query('//form//form')->length);
        $this->assertSame(route('admin.branding.update'), $xpath->query('//form[@id="brandingForm"]')->item(0)?->getAttribute('action'));
        $this->assertSame(route('admin.branding.asset.purge'), $xpath->query('//form[@id="brandingPurgeForm"]')->item(0)?->getAttribute('action'));
        $this->assertSame(route('admin.branding.asset.store'), $xpath->query('//form[@id="brandingAssetUploadForm"]')->item(0)?->getAttribute('action'));
        $this->assertSame('brandingForm', $xpath->query('//button[@id="brandingSubmitBtn"]')->item(0)?->getAttribute('form'));
        $this->assertSame('brandingPurgeForm', $xpath->query('//button[@form="brandingPurgeForm"]')->item(0)?->getAttribute('form'));
        $this->assertSame('brandingAssetUploadForm', $xpath->query('//input[@name="image" and @form="brandingAssetUploadForm"]')->item(0)?->getAttribute('form'));
    }

    public function test_branding_vault_ignores_files_removed_during_enumeration(): void
    {
        $missingFile = new SplFileInfo(public_path('site/img/missing-vault-image.jpg'), '', 'missing-vault-image.jpg');

        File::partialMock();
        File::shouldReceive('isDirectory')->once()->andReturnTrue();
        File::shouldReceive('allFiles')->once()->andReturn([$missingFile]);

        $this->actingAs($this->admin)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertDontSee('missing-vault-image.jpg');
    }

    public function test_branding_and_seo_forms_preserve_unsaved_input_after_validation_errors(): void
    {
        $branding = $this->actingAs($this->admin)
            ->followingRedirects()
            ->from(route('admin.branding.index'))
            ->post(route('admin.branding.update'), [
                'hero_title' => 'Unsaved Hero Heading',
                'site_email' => 'not-an-email',
                'popup_status' => 'active',
                'popup_title' => 'Unsaved Popup Heading',
                'hero_image_path' => 'site/img/unsaved-hero.png',
            ])
            ->assertOk()
            ->assertSee('value="Unsaved Hero Heading"', false)
            ->assertSee('value="not-an-email"', false)
            ->assertSee('value="site/img/unsaved-hero.png"', false)
            ->assertSee('Unsaved Popup Heading')
            ->assertSee('const firstErrorField = "site_email"', false);

        $this->assertStringContainsString('value="active" selected', $branding->getContent());

        $this->actingAs($this->admin)
            ->followingRedirects()
            ->from(route('admin.seo.index'))
            ->post(route('admin.seo.update'), [
                'meta_title' => 'Unsaved SEO Title',
                'meta_description' => 'Unsaved SEO description',
                'founding_year' => '1800',
            ])
            ->assertOk()
            ->assertSee('value="Unsaved SEO Title"', false)
            ->assertSee('Unsaved SEO description')
            ->assertSee('value="1800"', false);
    }

    public function test_new_faq_and_course_default_inactive_but_can_be_explicitly_published(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.faq.store'), [
                'question' => 'Inactive by default?',
                'answer' => '<p>Yes.</p>',
                'order_priority' => 10,
            ])
            ->assertRedirect(route('admin.faq.index'));

        $this->assertDatabaseHas(FAQ::class, ['question' => 'Inactive by default?', 'status' => 'inactive']);

        $this->actingAs($this->admin)
            ->post(route('admin.faq.store'), [
                'question' => 'Explicitly public?',
                'answer' => '<p>Yes.</p>',
                'status' => 'active',
                'order_priority' => 20,
            ])
            ->assertRedirect(route('admin.faq.index'));

        $this->assertDatabaseHas(FAQ::class, ['question' => 'Explicitly public?', 'status' => 'active']);

        $category = CourseCategory::factory()->create();
        $coursePayload = [
            'name' => 'Safe Draft Course',
            'slug' => 'safe-draft-course',
            'category_id' => $category->id,
            'price' => 'NPR 10,000',
            'duration' => '6 weeks',
            'instructor' => 'Academy Team',
            'capacity' => '20 seats',
            'description' => '<p>Description.</p>',
            'course_outline' => '<ul><li>Outline</li></ul>',
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.courses.store'), $coursePayload)
            ->assertRedirect(route('admin.courses.index'));

        $this->assertDatabaseHas(Course::class, ['slug' => 'safe-draft-course', 'status' => 'inactive']);

        $this->actingAs($this->admin)
            ->post(route('admin.courses.store'), array_merge($coursePayload, [
                'name' => 'Explicit Public Course',
                'slug' => 'explicit-public-course',
                'status' => 'active',
            ]))
            ->assertRedirect(route('admin.courses.index'));

        $this->assertDatabaseHas(Course::class, ['slug' => 'explicit-public-course', 'status' => 'active']);
    }

    public function test_editing_a_published_course_preserves_publication_and_unchecked_state(): void
    {
        $category = CourseCategory::factory()->create();
        $course = Course::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'status' => 'active',
            'is_featured' => true,
        ]);

        $normalEdit = $this->actingAs($this->admin)
            ->get(route('admin.courses.edit', $course))
            ->assertOk();
        $normalXPath = $this->xpath($normalEdit->getContent());
        $this->assertTrue($normalXPath->query('//input[@id="is_featured"]')->item(0)->hasAttribute('checked'));

        $payload = [
            'name' => $course->name,
            'slug' => $course->slug,
            'category_id' => $category->id,
            'price' => $course->price,
            'duration' => $course->duration,
            'instructor' => $course->instructor,
            'capacity' => $course->capacity,
            'description' => $course->description,
            'course_outline' => $course->course_outline,
            'status' => 'active',
            'is_featured' => '0',
        ];

        $invalidEdit = $this->actingAs($this->admin)
            ->followingRedirects()
            ->from(route('admin.courses.edit', $course))
            ->put(route('admin.courses.update', $course), array_merge($payload, [
                'name' => '',
                'status' => 'inactive',
                'photo_path' => 'site/img/unsaved-course-path.png',
            ]))
            ->assertOk()
            ->assertSee('value="site/img/unsaved-course-path.png"', false);

        $invalidXPath = $this->xpath($invalidEdit->getContent());
        $this->assertFalse($invalidXPath->query('//input[@id="is_featured"]')->item(0)->hasAttribute('checked'));
        $this->assertTrue($invalidXPath->query('//option[@value="inactive"]')->item(0)->hasAttribute('selected'));
        $this->assertSame('active', $course->fresh()->status);

        $this->actingAs($this->admin)
            ->put(route('admin.courses.update', $course), $payload)
            ->assertRedirect(route('admin.courses.index'));

        $course->refresh();
        $this->assertSame('active', $course->status);
        $this->assertSame(0, (int) $course->is_featured);
    }

    public function test_blog_category_is_admin_editable_persisted_and_rendered_publicly(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.blog.store'), [
                'title' => 'Category Workflow Article',
                'slug' => 'category-workflow-article',
                'author' => 'Academy Team',
                'category' => 'Study Guides',
                'content' => '<p>Formatted article body.</p>',
                'status' => 'published',
            ])
            ->assertRedirect(route('admin.blog.index'));

        $post = BlogPost::where('slug', 'category-workflow-article')->firstOrFail();
        $this->assertSame('Study Guides', $post->category);

        $this->actingAs($this->admin)
            ->get(route('admin.blog.edit', $post))
            ->assertOk()
            ->assertSee('value="Study Guides"', false);

        $this->get(route('blog-detail', $post->slug))
            ->assertOk()
            ->assertSee('Study Guides');
    }

    public function test_dead_or_unconsumed_cms_controls_are_not_presented_to_staff(): void
    {
        $this->actingAs($this->staff)
            ->get(route('admin.notices.create'))
            ->assertOk()
            ->assertDontSee('name="meta_title"', false)
            ->assertDontSee('name="content"', false);

        $this->actingAs($this->staff)
            ->get(route('admin.faq.create'))
            ->assertOk()
            ->assertDontSee('name="category"', false);

        $this->actingAs($this->staff)
            ->get(route('admin.teachers.create'))
            ->assertOk()
            ->assertDontSee('name="twitter_url"', false)
            ->assertDontSee('name="instagram_url"', false);

        $this->actingAs($this->staff)
            ->get(route('admin.categories.create'))
            ->assertOk()
            ->assertDontSee('name="icon"', false);

        $this->actingAs($this->staff)
            ->get(route('admin.testimonials.create'))
            ->assertOk()
            ->assertDontSee('name="meta_title"', false);
    }

    public function test_staff_cannot_permanently_delete_newsletter_subscribers(): void
    {
        $subscriber = NewsLetter::create(['email' => 'staff-delete-guard@example.test']);

        $this->actingAs($this->staff)
            ->get(route('admin.submissions.newsletter-display'))
            ->assertOk()
            ->assertDontSee('Remove this subscriber permanently?')
            ->assertDontSee('Bulk Delete');

        $this->actingAs($this->staff)
            ->delete(route('admin.submissions.newsletter.destroy', $subscriber))
            ->assertForbidden();

        $this->actingAs($this->staff)
            ->post(route('admin.submissions.bulk-delete'), [
                'ids' => [$subscriber->id],
                'type' => 'newsletter',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas(NewsLetter::class, ['id' => $subscriber->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.submissions.newsletter.destroy', $subscriber))
            ->assertRedirect();

        $this->assertDatabaseMissing(NewsLetter::class, ['id' => $subscriber->id]);
    }

    public function test_hard_delete_controls_use_permanent_delete_language(): void
    {
        FAQ::factory()->create();
        Course::factory()->create();
        BlogPost::factory()->draft()->create();
        CourseCategory::factory()->create();
        Notice::factory()->create();
        Teacher::factory()->create();
        Testimonial::factory()->create();
        ServicePillar::factory()->create();

        $this->actingAs($this->admin)->get(route('admin.faq.index'))
            ->assertOk()
            ->assertSee('Permanently delete this FAQ? This cannot be undone.');
        $this->actingAs($this->admin)->get(route('admin.courses.index'))
            ->assertOk()
            ->assertSee('Permanently delete this course? This cannot be undone.');
        $this->actingAs($this->admin)->get(route('admin.blog.index'))
            ->assertOk()
            ->assertSee('Permanently delete this article? This cannot be undone.');
        $this->actingAs($this->admin)->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Permanently delete this category? This cannot be undone.');
        $this->actingAs($this->admin)->get(route('admin.notices.index'))
            ->assertOk()
            ->assertSee('Permanently delete this notice? This cannot be undone.');
        $this->actingAs($this->admin)->get(route('admin.teachers.index'))
            ->assertOk()
            ->assertSee('Permanently delete this faculty member? This cannot be undone.');
        $this->actingAs($this->admin)->get(route('admin.testimonials.index'))
            ->assertOk()
            ->assertSee('Permanently delete this testimonial? This cannot be undone.');
        $this->actingAs($this->admin)->get(route('admin.service-pillars.index'))
            ->assertOk()
            ->assertSee('Permanently delete this service pillar? This cannot be undone.');
    }

    public function test_faq_rich_content_is_included_in_asset_reference_checks(): void
    {
        FAQ::factory()->create([
            'answer' => '<p><img src="/site/img/faq-only-reference.png" alt="Reference"></p>',
        ]);

        $probe = new class
        {
            use InteractsWithAssets;

            public function isReferenced(string $path): bool
            {
                return $this->isAssetInUse($path);
            }
        };

        $this->assertTrue($probe->isReferenced('site/img/faq-only-reference.png'));
    }

    public function test_notice_activation_is_atomic_and_future_notices_do_not_displace_current_notice(): void
    {
        $current = Notice::create([
            'title' => 'Current Notice',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER fail_notice_replacement
            BEFORE INSERT ON notices
            WHEN NEW.title = 'Failing Replacement'
            BEGIN
                SELECT RAISE(ABORT, 'Simulated notice persistence failure.');
            END;
        SQL);

        $failure = null;
        $this->withoutExceptionHandling();

        try {
            $this->actingAs($this->admin)->post(route('admin.notices.store'), [
                'title' => 'Failing Replacement',
                'status' => 'active',
                'display_type' => 'popup',
            ]);
        } catch (Throwable $exception) {
            $failure = $exception;
        } finally {
            $this->withExceptionHandling();
            DB::unprepared('DROP TRIGGER fail_notice_replacement');
        }

        $this->assertNotNull($failure);
        $this->assertStringContainsString('Simulated notice persistence failure.', $failure->getMessage());
        $this->assertSame('active', $current->fresh()->status);

        $candidate = Notice::create([
            'title' => 'Inactive Candidate',
            'status' => 'inactive',
            'display_type' => 'popup',
        ]);
        DB::unprepared('CREATE TRIGGER fail_notice_update BEFORE UPDATE ON notices WHEN OLD.id = '.$candidate->id." AND NEW.title = 'Failing Update' BEGIN SELECT RAISE(ABORT, 'Simulated notice update failure.'); END;");

        $updateFailure = null;
        $this->withoutExceptionHandling();
        try {
            $this->actingAs($this->admin)->put(route('admin.notices.update', $candidate), [
                'title' => 'Failing Update',
                'status' => 'active',
                'display_type' => 'popup',
            ]);
        } catch (Throwable $exception) {
            $updateFailure = $exception;
        } finally {
            $this->withExceptionHandling();
            DB::unprepared('DROP TRIGGER fail_notice_update');
        }

        $this->assertNotNull($updateFailure);
        $this->assertSame('active', $current->fresh()->status);
        $this->assertSame('inactive', $candidate->fresh()->status);

        $this->actingAs($this->admin)
            ->post(route('admin.notices.store'), [
                'title' => 'Future Replacement',
                'status' => 'active',
                'display_type' => 'standard',
                'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->assertSame('active', $current->fresh()->status);

        $this->actingAs($this->admin)
            ->post(route('admin.notices.store'), [
                'title' => 'Successful Current Replacement',
                'status' => 'active',
                'display_type' => 'standard',
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->assertSame('inactive', $current->fresh()->status);
        $this->assertDatabaseHas(Notice::class, [
            'title' => 'Successful Current Replacement',
            'status' => 'active',
        ]);
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);

        return new DOMXPath($document);
    }
}
