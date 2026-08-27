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
use App\Support\ApprovedCourseFaqDeploymentData;
use Database\Seeders\LiveSiteSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CheckpointSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkpoint_seeders_populate_cms_content_without_missing_assets(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $this->assertSame(5, CourseCategory::count());
        $this->assertSame(13, Course::count());
        $this->assertSame(7, Teacher::count());
        $this->assertSame(7, Testimonial::count());
        $this->assertSame(8, BlogPost::where('status', 'published')->count());
        $this->assertSame(24, FAQ::where('status', 'active')->count());
        $this->assertSame(68, DB::table('course_faq')->count());
        $this->assertSame(7, ServicePillar::where('status', 'active')->count());
        $this->assertSame(3, Notice::count());

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'whatsapp_cta_text',
            'value' => 'Message on WhatsApp',
        ]);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'stat_3_val',
            'value' => '',
        ]);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'google_business_profile_url',
            'value' => '',
        ]);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'external_review_proof_note',
            'value' => '',
        ]);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'course_confirmation_note',
            'value' => 'Ask the academy team for the current batch timing, seats and faculty information.',
        ]);

        $this->assertDatabaseHas(Course::class, [
            'slug' => 'free-course-roadmap-help',
            'price' => 'Free',
        ]);

        $this->assertSeededAssetsExist();
    }

    public function test_checkpoint_seeders_match_the_approved_release_data_contract(): void
    {
        $this->seed(LiveSiteSeeder::class);

        foreach (ApprovedCourseFaqDeploymentData::courseMetadata() as $slug => $metadata) {
            $course = Course::query()->where('slug', $slug)->firstOrFail();

            $this->assertSame($metadata['name'], $course->name);
            $this->assertSame($metadata['price'], $course->price);
            $this->assertSame($metadata['duration'], $course->duration);
            $this->assertSame($metadata['instructor'], $course->instructor);
        }

        foreach (ApprovedCourseFaqDeploymentData::matrix() as $slug => $questions) {
            $assignedQuestions = Course::query()
                ->where('slug', $slug)
                ->firstOrFail()
                ->faqs()
                ->pluck('question')
                ->all();

            $this->assertEqualsCanonicalizing($questions, $assignedQuestions);
        }

        $this->assertSame(5, Course::query()->whereNotNull('teacher_id')->count());
        $this->assertSame(4, Testimonial::query()->whereNotNull('course_id')->count());
        $this->assertSame(0, DB::table('blog_course')->count());
        $this->assertDatabaseMissing(BlogPost::class, ['slug' => 'phase-5b-browser-relationship-guide']);
        $this->assertDatabaseMissing(Teacher::class, ['name' => 'Phase 5B Browser Teacher']);
        $this->assertDatabaseMissing(Testimonial::class, ['student_name' => 'PHASE 5B TEST ONLY']);

        $this->artisan('course-faq:apply-deployment-data')
            ->expectsOutputToContain('NO CHANGES REQUIRED')
            ->assertSuccessful();
    }

    public function test_seeded_public_pages_render_dynamic_cms_content(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Build skills you can use with confidence.')
            ->assertSee('Established in Pokhara since 2008')
            ->assertSee('Srijana Chowk, Pokhara, Nepal')
            ->assertSee('Courses taught:')
            ->assertDontSee('External social proof')
            ->assertSee('I am a Parent')
            ->assertSee('Explore courses students ask about most')
            ->assertSee('Find classes by subject and skill.')
            ->assertSee('Ready to find a suitable course?')
            ->assertSee('Message on WhatsApp')
            ->assertSee('Ask for Course Help');

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'home_testimonials_title',
            'value' => 'Hear from learners who studied at Golden Eye Academy',
        ]);

        $this->get(route('courses-all'))
            ->assertOk()
            ->assertSee('IELTS Masterclass for Band 7+')
            ->assertSee('Corporate Office and Admin Package')
            ->assertSee('Ask for Course Help');

        $this->get(route('courses'))
            ->assertRedirect(route('courses-all'));

        $this->get(route('blog'))
            ->assertOk()
            ->assertSee('How to Choose a Course After SEE or Plus Two');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Founder and Academic Director');
    }

    public function test_admin_cms_can_update_seeded_category_and_sync_course_fields(): void
    {
        $this->seed(LiveSiteSeeder::class);
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $category = CourseCategory::where('slug', 'study-abroad-test-prep')->firstOrFail();

        $course = Course::where('slug', 'ielts-masterclass')->firstOrFail();
        $this->assertSame($category->id, $course->category_id);

        $response = $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'IELTS, PTE and Language Preparation',
            'slug' => 'study-abroad-test-prep',
            'status' => 'active',
            'order_priority' => 10,
            'image_path' => $category->image,
            'description' => $category->description,
            'meta_title' => $category->meta_title,
            'meta_description' => $category->meta_description,
            'meta_keywords' => $category->meta_keywords,
            'aeo_summary' => $category->aeo_summary,
            'schema_markup' => $category->schema_markup,
        ]);

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas(CourseCategory::class, [
            'id' => $category->id,
            'name' => 'IELTS, PTE and Language Preparation',
        ]);

        $this->assertDatabaseHas(Course::class, [
            'id' => $course->id,
            'category' => 'IELTS, PTE and Language Preparation',
            'category_slug' => 'study-abroad-test-prep',
        ]);
    }

    public function test_admin_branding_accepts_seeded_dynamic_image_paths(): void
    {
        $this->seed(LiveSiteSeeder::class);
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->post(route('admin.branding.update'), [
            'hero_title' => 'Choose Your Next Step With Clarity',
            'hero_image_path' => 'site/img/carousel-1.png',
            'about_image_path' => 'site/img/about.jpg',
            'founder_image_path' => 'site/img/message-chairperson.jpg',
            'popup_image_path' => 'site/img/premium.png',
            'site_logo_path' => 'site/img/logo.png',
            'site_favicon_path' => 'site/img/logo.png',
            'google_business_profile_url' => 'https://www.google.com/maps/place/GoldenEye+Academy',
            'external_review_proof_note' => 'Verified review screenshots are available from the academy team.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'hero_title',
            'value' => 'Choose Your Next Step With Clarity',
        ]);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'popup_image',
            'value' => 'site/img/premium.png',
        ]);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'google_business_profile_url',
            'value' => 'https://www.google.com/maps/place/GoldenEye+Academy',
        ]);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'external_review_proof_note',
            'value' => 'Verified review screenshots are available from the academy team.',
        ]);
    }

    private function assertSeededAssetsExist(): void
    {
        $paths = collect()
            ->merge(Course::pluck('photo'))
            ->merge(CourseCategory::pluck('image'))
            ->merge(Teacher::pluck('photo'))
            ->merge(Testimonial::pluck('photo'))
            ->merge(BlogPost::pluck('image'))
            ->merge(Notice::pluck('image'))
            ->merge(SiteSetting::where('type', 'image')->pluck('value'))
            ->filter()
            ->unique();

        foreach ($paths as $path) {
            $this->assertFileExists(public_path($path), "Seeded asset path [{$path}] does not exist.");
        }
    }
}
