<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Models\User;
use App\Support\GoldenEyeContentBaseline;
use Database\Seeders\SiteSettingSeeder;
use Database\Seeders\TeacherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Phase3bCriticalCmsAuthorityTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, string> */
    private array $uploadedPublicFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Admin']);
    }

    protected function tearDown(): void
    {
        foreach ($this->uploadedPublicFiles as $path) {
            File::delete(public_path($path));
        }

        parent::tearDown();
    }

    public function test_course_create_accepts_upload_and_media_path_with_upload_precedence(): void
    {
        $admin = $this->adminUser();
        $category = CourseCategory::factory()->create(['status' => 'active']);

        $this->actingAs($admin)->post(route('admin.courses.store'), $this->coursePayload($category, [
            'name' => 'Uploaded Course Image',
            'slug' => 'uploaded-course-image',
            'photo' => $this->fakeImage('uploaded-course.png'),
            'photo_path' => 'site/img/cat-1.jpg',
        ]))->assertRedirect(route('admin.courses.index'));

        $uploadedCourse = Course::query()->where('slug', 'uploaded-course-image')->firstOrFail();
        $this->assertStringStartsWith('site/img/courses/', $uploadedCourse->photo);
        $this->assertNotSame('site/img/cat-1.jpg', $uploadedCourse->photo);
        $this->assertFileExists(public_path($uploadedCourse->photo));
        $this->uploadedPublicFiles[] = $uploadedCourse->photo;
        $this->get(route('courses-detail', $uploadedCourse->slug))
            ->assertOk()
            ->assertSee($uploadedCourse->photo, false);

        $this->actingAs($admin)->post(route('admin.courses.store'), $this->coursePayload($category, [
            'name' => 'Vault Course Image',
            'slug' => 'vault-course-image',
            'photo_path' => 'site/img/cat-1.jpg',
        ]))->assertRedirect(route('admin.courses.index'));

        $vaultCourse = Course::query()->where('slug', 'vault-course-image')->firstOrFail();
        $this->assertSame('site/img/cat-1.jpg', $vaultCourse->photo);
        $this->get(route('courses-detail', $vaultCourse->slug))
            ->assertOk()
            ->assertSee('site/img/cat-1.jpg', false);
    }

    public function test_course_update_accepts_upload_and_media_path_and_preserves_existing_image(): void
    {
        $admin = $this->adminUser();
        $category = CourseCategory::factory()->create(['status' => 'active']);
        $course = Course::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'photo' => 'site/img/cat-1.jpg',
        ]);

        $this->actingAs($admin)->put(route('admin.courses.update', $course), $this->coursePayload($category, [
            'name' => $course->name,
            'slug' => $course->slug,
            'photo' => $this->fakeImage('replacement.png'),
            'photo_path' => 'site/img/cat-2.jpg',
        ]))->assertRedirect(route('admin.courses.index'));

        $course->refresh();
        $uploadedPath = $course->photo;
        $this->assertStringStartsWith('site/img/courses/', $uploadedPath);
        $this->assertNotSame('site/img/cat-2.jpg', $uploadedPath);
        $this->assertFileExists(public_path($uploadedPath));
        $this->uploadedPublicFiles[] = $uploadedPath;

        $this->actingAs($admin)->put(route('admin.courses.update', $course), $this->coursePayload($category, [
            'name' => $course->name,
            'slug' => $course->slug,
            'photo_path' => 'site/img/cat-2.jpg',
        ]))->assertRedirect(route('admin.courses.index'));

        $this->assertSame('site/img/cat-2.jpg', $course->refresh()->photo);
        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('site/img/cat-2.jpg', false)
            ->assertDontSee($uploadedPath, false);

        $this->actingAs($admin)->put(route('admin.courses.update', $course), $this->coursePayload($category, [
            'name' => $course->name,
            'slug' => $course->slug,
        ]))->assertRedirect(route('admin.courses.index'));

        $this->assertSame('site/img/cat-2.jpg', $course->refresh()->photo);
    }

    public function test_visible_hero_fields_are_the_only_primary_public_hero_authority(): void
    {
        $admin = $this->adminUser();
        SiteSetting::query()->insert([
            ['key' => 'hero_title', 'value' => 'Original visible title', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'value' => 'Original visible body', 'type' => 'text'],
            ['key' => 'hero_hook_headline', 'value' => 'Hidden legacy title', 'type' => 'text'],
            ['key' => 'hero_hook_body', 'value' => 'Hidden legacy body', 'type' => 'text'],
        ]);

        $this->actingAs($admin)->post(route('admin.branding.update'), [
            'hero_title' => 'CMS authoritative hero title',
            'hero_subtitle' => 'CMS authoritative hero supporting body',
        ])->assertRedirect();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('CMS authoritative hero title')
            ->assertSee('CMS authoritative hero supporting body')
            ->assertDontSee('Hidden legacy title')
            ->assertDontSee('Hidden legacy body');
    }

    public function test_fresh_site_setting_seed_uses_only_the_accepted_visible_hero_keys(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $this->assertSame(GoldenEyeContentBaseline::settingValue('hero_title'), SiteSetting::getValue('hero_title'));
        $this->assertSame(GoldenEyeContentBaseline::settingValue('hero_subtitle'), SiteSetting::getValue('hero_subtitle'));
        $this->assertFalse(SiteSetting::query()->whereIn('key', ['hero_hook_headline', 'hero_hook_body'])->exists());

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(GoldenEyeContentBaseline::settingValue('hero_title'))
            ->assertSee(GoldenEyeContentBaseline::settingValue('hero_subtitle'));
    }

    public function test_teacher_seed_keeps_academy_socials_central_without_false_person_profiles(): void
    {
        $this->seed(SiteSettingSeeder::class);
        $this->seed(TeacherSeeder::class);

        $this->assertSame(0, Teacher::query()->whereNotNull('facebook_url')->count());
        $this->assertSame(0, Teacher::query()->whereNotNull('linkedin_url')->count());

        $response = $this->get(route('about'))->assertOk();
        $content = $response->getContent();

        $response
            ->assertSee('https://www.facebook.com/goldeneyeacademy', false)
            ->assertSee('https://www.linkedin.com/company/golden-eye-academy/', false)
            ->assertDontSee('Shankar Pokharel on Facebook')
            ->assertDontSee('Shankar Pokharel on LinkedIn');

        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $schemaBlocks);
        $personSchema = collect($schemaBlocks[1])->first(fn (string $schema): bool => str_contains($schema, '"@type": "Person"'));

        $this->assertNotNull($personSchema);
        $this->assertStringNotContainsString('https://www.facebook.com/goldeneyeacademy', $personSchema);
        $this->assertStringNotContainsString('https://www.linkedin.com/company/golden-eye-academy/', $personSchema);
    }

    public function test_teacher_seed_preserves_an_individually_managed_social_url(): void
    {
        Teacher::factory()->create([
            'name' => 'Shankar Pokharel',
            'facebook_url' => 'https://www.facebook.com/shankar.verified',
            'linkedin_url' => 'https://www.linkedin.com/in/shankar-verified/',
        ]);

        $this->seed(TeacherSeeder::class);

        $teacher = Teacher::query()->where('name', 'Shankar Pokharel')->firstOrFail();
        $this->assertSame('https://www.facebook.com/shankar.verified', $teacher->facebook_url);
        $this->assertSame('https://www.linkedin.com/in/shankar-verified/', $teacher->linkedin_url);
    }

    public function test_linked_teacher_controls_instructor_on_create_and_update_and_public_output(): void
    {
        $admin = $this->adminUser();
        $category = CourseCategory::factory()->create(['status' => 'active']);
        $firstTeacher = Teacher::factory()->create(['name' => 'Authoritative First Teacher']);
        $secondTeacher = Teacher::factory()->create(['name' => 'Authoritative Second Teacher']);

        $this->actingAs($admin)->post(route('admin.courses.store'), $this->coursePayload($category, [
            'name' => 'Teacher Authority Course',
            'slug' => 'teacher-authority-course',
            'teacher_id' => $firstTeacher->id,
            'instructor' => 'Conflicting submitted instructor',
            'photo_path' => 'site/img/cat-1.jpg',
        ]))->assertRedirect(route('admin.courses.index'));

        $course = Course::query()->where('slug', 'teacher-authority-course')->firstOrFail();
        $this->assertSame($firstTeacher->name, $course->instructor);

        $this->actingAs($admin)->put(route('admin.courses.update', $course), $this->coursePayload($category, [
            'name' => $course->name,
            'slug' => $course->slug,
            'teacher_id' => $secondTeacher->id,
            'instructor' => 'Another conflicting instructor',
        ]))->assertRedirect(route('admin.courses.index'));

        $course->refresh();
        $this->assertSame($secondTeacher->id, $course->teacher_id);
        $this->assertSame($secondTeacher->name, $course->instructor);
        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee($secondTeacher->name)
            ->assertDontSee('Authoritative First Teacher')
            ->assertDontSee('Another conflicting instructor');
    }

    public function test_manual_instructor_remains_supported_without_a_linked_teacher(): void
    {
        $admin = $this->adminUser();
        $category = CourseCategory::factory()->create(['status' => 'active']);

        $this->actingAs($admin)->post(route('admin.courses.store'), $this->coursePayload($category, [
            'name' => 'Manual Instructor Course',
            'slug' => 'manual-instructor-course',
            'teacher_id' => null,
            'instructor' => 'Visiting Teaching Team',
            'photo_path' => 'site/img/cat-1.jpg',
        ]))->assertRedirect(route('admin.courses.index'));

        $course = Course::query()->where('slug', 'manual-instructor-course')->firstOrFail();
        $this->assertNull($course->teacher_id);
        $this->assertSame('Visiting Teaching Team', $course->instructor);
        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Visiting Teaching Team');
    }

    private function adminUser(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        return $admin;
    }

    private function fakeImage(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true),
        );
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function coursePayload(CourseCategory $category, array $overrides = []): array
    {
        return array_merge([
            'name' => 'CMS Course',
            'slug' => 'cms-course',
            'category_id' => $category->id,
            'price' => 'Rs. 10,000',
            'duration' => '8 Weeks',
            'instructor' => 'Manual Teaching Team',
            'teacher_id' => null,
            'capacity' => '20 Seats',
            'description' => '<p>Course description.</p>',
            'course_outline' => '<ul><li>Course outcome</li></ul>',
            'status' => 'active',
            'display_order' => 10,
        ], $overrides);
    }
}
