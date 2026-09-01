<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Notice;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use App\Rules\PublicMediaPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Phase3b2CmsMediaIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, string> */
    private array $uploadedPublicFiles = [];

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    protected function tearDown(): void
    {
        foreach ($this->uploadedPublicFiles as $path) {
            File::delete(public_path($path));
        }

        parent::tearDown();
    }

    public function test_public_media_path_rule_accepts_only_existing_vault_images_and_allows_blank_optional_values(): void
    {
        $this->assertFalse($this->mediaPathValidator('site/img/cat-1.jpg')->fails());
        $this->assertFalse($this->mediaPathValidator('/site/img/cat-1.jpg')->fails());
        $this->assertFalse($this->mediaPathValidator('')->fails());
        $this->assertTrue($this->mediaPathValidator('site/img/does-not-exist.png')->fails());
        $this->assertTrue($this->mediaPathValidator('../site/img/cat-1.jpg')->fails());
        $this->assertTrue($this->mediaPathValidator('C:\\xampp\\htdocs\\site\\img\\cat-1.jpg')->fails());
        $this->assertTrue($this->mediaPathValidator('https://example.com/image.jpg')->fails());
    }

    public function test_course_rejects_invalid_media_paths_without_changing_its_authoritative_photo(): void
    {
        $category = CourseCategory::factory()->create();
        $course = Course::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'photo' => 'site/img/cat-1.jpg',
        ]);

        foreach (['site/img/does-not-exist.png', '../site/img/cat-2.jpg'] as $invalidPath) {
            $this->actingAs($this->admin)
                ->from(route('admin.courses.edit', $course))
                ->put(route('admin.courses.update', $course), $this->coursePayload($course, $category, [
                    'photo_path' => $invalidPath,
                ]))
                ->assertRedirect(route('admin.courses.edit', $course))
                ->assertSessionHasErrors('photo_path');

            $this->assertSame('site/img/cat-1.jpg', $course->fresh()->photo);
        }
    }

    public function test_optional_editorial_images_can_be_removed_with_safe_public_fallbacks(): void
    {
        $blog = BlogPost::factory()->create(['image' => 'site/img/premium.png']);
        $this->actingAs($this->admin)
            ->put(route('admin.blog.update', $blog), $this->blogPayload($blog, [
                'image_path' => $blog->image,
                'remove_image' => '1',
            ]))
            ->assertRedirect(route('admin.blog.index'));
        $this->assertNull($blog->fresh()->image);
        $this->assertFileExists(public_path('site/img/premium.png'));
        $this->get(route('blog-detail', $blog->slug))
            ->assertOk()
            ->assertSee('site/img/carousel-1.png', false);

        $category = CourseCategory::factory()->create(['image' => 'site/img/cat-2.jpg']);
        $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $category), $this->categoryPayload($category, [
                'image_path' => $category->image,
                'remove_image' => '1',
            ]))
            ->assertRedirect(route('admin.categories.index'));
        $this->assertNull($category->fresh()->image);
        $this->assertFileExists(public_path('site/img/cat-2.jpg'));
        $this->get(route('catalogue'))
            ->assertOk()
            ->assertSee('site/img/cat-1.jpg', false);

        $teacher = Teacher::factory()->create(['photo' => 'site/img/team_4.jpg']);
        $this->actingAs($this->admin)
            ->put(route('admin.teachers.update', $teacher), $this->teacherPayload($teacher, [
                'photo_path' => $teacher->photo,
                'remove_photo' => '1',
            ]))
            ->assertRedirect(route('admin.teachers.index'));
        $this->assertNull($teacher->fresh()->photo);
        $this->assertFileExists(public_path('site/img/team_4.jpg'));
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('site/img/team-1.jpg', false);

        $testimonial = Testimonial::factory()->create(['photo' => 'site/img/testimonial-2.jpg']);
        $this->actingAs($this->admin)
            ->put(route('admin.testimonials.update', $testimonial), $this->reviewPayload($testimonial, [
                'photo_path' => $testimonial->photo,
                'remove_photo' => '1',
            ]))
            ->assertRedirect(route('admin.testimonials.index'));
        $this->assertNull($testimonial->fresh()->photo);
        $this->assertFileExists(public_path('site/img/testimonial-2.jpg'));
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-testimonial-avatar=', false)
            ->assertDontSee('site/img/testimonial-2.jpg', false);

        $notice = Notice::factory()->create([
            'title' => 'Popup removal proof',
            'image' => 'site/img/premium.png',
            'display_type' => 'popup',
            'status' => 'active',
        ]);
        $this->actingAs($this->admin)
            ->put(route('admin.notices.update', $notice), $this->noticePayload($notice, [
                'image_path' => $notice->image,
                'remove_image' => '1',
            ]))
            ->assertRedirect(route('admin.notices.index'));
        $this->assertNull($notice->fresh()->image);
        $this->assertFileExists(public_path('site/img/premium.png'));
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Popup removal proof')
            ->assertSee('site/img/carousel-1.png', false);
    }

    public function test_unrelated_editorial_edits_preserve_existing_images(): void
    {
        $blog = BlogPost::factory()->create(['image' => 'site/img/premium.png']);
        $category = CourseCategory::factory()->create(['image' => 'site/img/cat-2.jpg']);
        $teacher = Teacher::factory()->create(['photo' => 'site/img/team-2.jpg']);
        $testimonial = Testimonial::factory()->create(['photo' => 'site/img/testimonial-2.jpg']);
        $notice = Notice::factory()->create(['image' => 'site/img/premium.png', 'status' => 'inactive']);

        $this->actingAs($this->admin)->put(route('admin.blog.update', $blog), $this->blogPayload($blog, ['title' => 'Edited blog title']))->assertRedirect();
        $this->actingAs($this->admin)->put(route('admin.categories.update', $category), $this->categoryPayload($category, ['name' => 'Edited Category']))->assertRedirect();
        $this->actingAs($this->admin)->put(route('admin.teachers.update', $teacher), $this->teacherPayload($teacher, ['designation' => 'Edited Designation']))->assertRedirect();
        $this->actingAs($this->admin)->put(route('admin.testimonials.update', $testimonial), $this->reviewPayload($testimonial, ['content' => 'Edited testimonial']))->assertRedirect();
        $this->actingAs($this->admin)->put(route('admin.notices.update', $notice), $this->noticePayload($notice, ['title' => 'Edited notice']))->assertRedirect();

        $this->assertSame('site/img/premium.png', $blog->fresh()->image);
        $this->assertSame('site/img/cat-2.jpg', $category->fresh()->image);
        $this->assertSame('site/img/team-2.jpg', $teacher->fresh()->photo);
        $this->assertSame('site/img/testimonial-2.jpg', $testimonial->fresh()->photo);
        $this->assertSame('site/img/premium.png', $notice->fresh()->image);
    }

    public function test_blog_replacement_precedence_and_shared_file_deletion_are_safe(): void
    {
        $blog = BlogPost::factory()->create(['image' => 'site/img/carousel-2.jpg']);

        $this->actingAs($this->admin)
            ->put(route('admin.blog.update', $blog), $this->blogPayload($blog, [
                'image_path' => 'site/img/cat-1.jpg',
            ]))
            ->assertRedirect(route('admin.blog.index'));
        $this->assertSame('site/img/cat-1.jpg', $blog->fresh()->image);

        $this->actingAs($this->admin)
            ->put(route('admin.blog.update', $blog), $this->blogPayload($blog, [
                'image' => $this->fakeImage('blog-replacement.png'),
                'image_path' => 'site/img/cat-2.jpg',
            ]))
            ->assertRedirect(route('admin.blog.index'));

        $uploadedPath = $blog->fresh()->image;
        $this->assertStringStartsWith('site/img/blog/', $uploadedPath);
        $this->assertNotSame('site/img/cat-2.jpg', $uploadedPath);
        $this->assertFileExists(public_path($uploadedPath));
        $this->uploadedPublicFiles[] = $uploadedPath;
        $this->get(route('blog-detail', $blog->slug))
            ->assertOk()
            ->assertSee($uploadedPath, false);

        $sharedBlog = BlogPost::factory()->create(['image' => $uploadedPath]);
        $this->actingAs($this->admin)
            ->put(route('admin.blog.update', $blog), $this->blogPayload($blog, ['remove_image' => '1']))
            ->assertRedirect(route('admin.blog.index'));
        $this->assertFileExists(public_path($uploadedPath));

        $this->actingAs($this->admin)
            ->put(route('admin.blog.update', $sharedBlog), $this->blogPayload($sharedBlog, ['remove_image' => '1']))
            ->assertRedirect(route('admin.blog.index'));
        $this->assertFileDoesNotExist(public_path($uploadedPath));
    }

    public function test_branding_images_support_validation_replacement_removal_and_critical_preservation(): void
    {
        $removableImages = [
            'about_image' => 'site/img/about.jpg',
            'founder_image' => 'site/img/message-chairperson.jpg',
            'popup_image' => 'site/img/premium.png',
            'external_review_screenshot' => 'site/img/testimonial-1.jpg',
            'audience_students_image' => 'site/img/cat-1.jpg',
            'audience_parents_image' => 'site/img/cat-2.jpg',
            'audience_study_abroad_image' => 'site/img/cat-3.jpg',
            'audience_job_computer_skills_image' => 'site/img/cat-4.jpg',
        ];

        foreach ([
            ...$removableImages,
            'site_logo' => 'site/img/logo.png',
            'site_favicon' => 'site/img/logo.png',
            'hero_image' => 'site/img/carousel-1.png',
            'popup_status' => 'active',
        ] as $key => $value) {
            SiteSetting::query()->create(['key' => $key, 'value' => $value, 'type' => str_ends_with($key, '_status') ? 'text' : 'image']);
        }

        $this->actingAs($this->admin)
            ->from(route('admin.branding.index'))
            ->post(route('admin.branding.update'), ['about_image_path' => 'site/img/missing-about.png'])
            ->assertRedirect(route('admin.branding.index'))
            ->assertSessionHasErrors('about_image_path');
        $this->assertSame('site/img/about.jpg', SiteSetting::getValue('about_image'));

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'about_image' => $this->fakeImage('about-upload.png'),
                'about_image_path' => 'site/img/cat-2.jpg',
                'remove_about_image' => '1',
            ])
            ->assertRedirect();

        $uploadedAbout = (string) SiteSetting::getValue('about_image');
        $this->assertStringStartsWith('site/img/', $uploadedAbout);
        $this->assertNotSame('site/img/cat-2.jpg', $uploadedAbout);
        $this->uploadedPublicFiles[] = $uploadedAbout;

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), ['footer_about_text' => 'Unrelated branding edit'])
            ->assertRedirect();
        $this->assertSame($uploadedAbout, SiteSetting::getValue('about_image'));

        $removalPayload = ['remove_about_image' => '1'];
        foreach (array_keys($removableImages) as $imageKey) {
            $removalPayload['remove_'.$imageKey] = '1';
        }
        $removalPayload['remove_site_logo'] = '1';
        $removalPayload['remove_site_favicon'] = '1';
        $removalPayload['remove_hero_image'] = '1';

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), $removalPayload)
            ->assertRedirect();

        foreach (array_keys($removableImages) as $imageKey) {
            $this->assertSame('', SiteSetting::getValue($imageKey));
        }
        $this->assertSame('site/img/logo.png', SiteSetting::getValue('site_logo'));
        $this->assertSame('site/img/logo.png', SiteSetting::getValue('site_favicon'));
        $this->assertSame('site/img/carousel-1.png', SiteSetting::getValue('hero_image'));
        $this->assertFileDoesNotExist(public_path($uploadedAbout));
        $this->assertFileExists(public_path('site/img/about.jpg'));

        $this->get(route('about'))->assertOk()->assertSee('site/img/about.jpg', false);
        $this->get(route('for-students'))->assertOk()->assertSee('site/img/carousel-1.png', false);
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="siteNoticePopup"', false)
            ->assertSee('site/img/carousel-1.png', false)
            ->assertDontSee('Verified review proof for Golden Eye Academy');
    }

    public function test_media_removal_controls_match_supported_surfaces_and_notice_bar_is_clear(): void
    {
        foreach (['about_image', 'founder_image', 'popup_image', 'external_review_screenshot'] as $imageKey) {
            SiteSetting::query()->create(['key' => $imageKey, 'value' => 'site/img/premium.png', 'type' => 'image']);
        }

        $blog = BlogPost::factory()->create();
        $category = CourseCategory::factory()->create();
        $teacher = Teacher::factory()->create();
        $testimonial = Testimonial::factory()->create(['photo' => 'site/img/testimonial-1.jpg']);
        $notice = Notice::factory()->create(['display_type' => 'popup', 'status' => 'inactive']);

        $this->actingAs($this->admin)->get(route('admin.blog.edit', $blog))->assertOk()->assertSee('name="remove_image"', false);
        $this->actingAs($this->admin)->get(route('admin.categories.edit', $category))->assertOk()->assertSee('name="remove_image"', false);
        $this->actingAs($this->admin)->get(route('admin.teachers.edit', $teacher))->assertOk()->assertSee('name="remove_photo"', false);
        $this->actingAs($this->admin)->get(route('admin.testimonials.edit', $testimonial))->assertOk()->assertSee('name="remove_photo"', false);
        $this->actingAs($this->admin)->get(route('admin.notices.edit', $notice))->assertOk()->assertSee('name="remove_image"', false)->assertSee('top announcement bar intentionally does not display an image');

        $branding = $this->actingAs($this->admin)->get(route('admin.branding.index'))->assertOk();
        foreach (['about_image', 'founder_image', 'popup_image', 'external_review_screenshot', 'audience_students_image', 'audience_parents_image', 'audience_study_abroad_image', 'audience_job_computer_skills_image'] as $imageKey) {
            $branding->assertSee('name="remove_'.$imageKey.'"', false);
        }
        $branding
            ->assertDontSee('name="remove_site_logo"', false)
            ->assertDontSee('name="remove_site_favicon"', false)
            ->assertDontSee('name="remove_hero_image"', false);

        $barNotice = Notice::factory()->create([
            'title' => 'Bar without misleading image',
            'display_type' => 'bar',
            'image' => 'site/img/premium.png',
            'status' => 'active',
        ]);
        $html = $this->get(route('home'))->assertOk()->assertSee($barNotice->title)->getContent();
        $this->assertStringContainsString('id="siteNoticeStrip"', $html);
        preg_match('/<section id="siteNoticeStrip".*?<\/section>/s', $html, $noticeStrip);
        $this->assertArrayHasKey(0, $noticeStrip);
        $this->assertStringNotContainsString('site/img/premium.png', $noticeStrip[0]);
    }

    private function mediaPathValidator(?string $path): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make(
            ['path' => $path],
            ['path' => ['nullable', new PublicMediaPath]],
        );
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
        ], $overrides);
    }

    /** @param  array<string, mixed>  $overrides */
    private function blogPayload(BlogPost $blog, array $overrides = []): array
    {
        return array_merge([
            'title' => $blog->title,
            'slug' => $blog->slug,
            'author' => $blog->author,
            'category' => $blog->category,
            'content' => $blog->content,
            'status' => $blog->status,
        ], $overrides);
    }

    /** @param  array<string, mixed>  $overrides */
    private function categoryPayload(CourseCategory $category, array $overrides = []): array
    {
        return array_merge([
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'status' => $category->status,
            'order_priority' => $category->order_priority,
        ], $overrides);
    }

    /** @param  array<string, mixed>  $overrides */
    private function teacherPayload(Teacher $teacher, array $overrides = []): array
    {
        return array_merge([
            'name' => $teacher->name,
            'designation' => $teacher->designation,
            'bio' => $teacher->bio,
            'facebook_url' => $teacher->facebook_url,
            'linkedin_url' => $teacher->linkedin_url,
            'status' => $teacher->status,
            'is_featured' => (int) $teacher->is_featured,
        ], $overrides);
    }

    /** @param  array<string, mixed>  $overrides */
    private function reviewPayload(Testimonial $testimonial, array $overrides = []): array
    {
        return array_merge([
            'student_name' => $testimonial->student_name,
            'course_name' => $testimonial->course_name,
            'course_id' => $testimonial->course_id,
            'content' => $testimonial->content,
            'rating' => $testimonial->rating,
            'status' => $testimonial->status,
            'is_featured' => (int) $testimonial->is_featured,
        ], $overrides);
    }

    /** @param  array<string, mixed>  $overrides */
    private function noticePayload(Notice $notice, array $overrides = []): array
    {
        return array_merge([
            'title' => $notice->title,
            'subtitle' => $notice->subtitle,
            'badge' => $notice->badge,
            'link' => $notice->link,
            'button_text' => $notice->button_text,
            'status' => $notice->status,
            'display_type' => $notice->display_type ?? 'popup',
            'is_urgent' => (int) $notice->is_urgent,
        ], $overrides);
    }

    private function fakeImage(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true),
        );
    }
}
