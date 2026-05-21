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
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckpointSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkpoint_seeders_populate_cms_content_without_missing_assets(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(5, CourseCategory::count());
        $this->assertGreaterThanOrEqual(12, Course::count());
        $this->assertGreaterThanOrEqual(7, Teacher::count());
        $this->assertGreaterThanOrEqual(7, Testimonial::count());
        $this->assertGreaterThanOrEqual(8, BlogPost::where('status', 'published')->count());
        $this->assertGreaterThanOrEqual(20, FAQ::where('status', 'active')->count());
        $this->assertGreaterThanOrEqual(7, ServicePillar::where('status', 'active')->count());
        $this->assertGreaterThanOrEqual(2, Notice::count());

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'whatsapp_cta_text',
            'value' => 'Message on WhatsApp',
        ]);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'stat_3_val',
            'value' => 'Regular',
        ]);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'google_business_profile_url',
            'value' => '',
        ]);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'external_review_proof_note',
            'value' => 'Ask the academy team for current Google review proof or verified review screenshots before enrollment.',
        ]);

        $this->assertDatabaseHas(Course::class, [
            'slug' => 'free-course-roadmap-help',
            'price' => 'Free',
        ]);

        $this->assertSeededAssetsExist();
    }

    public function test_seeded_public_pages_render_dynamic_cms_content(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Study Abroad, Language &amp; Computer Courses in Pokhara', false)
            ->assertSee('Trusted by students in Pokhara since 2008')
            ->assertSee('Srijana Chowk, Pokhara, Nepal')
            ->assertSee('Specific progress/result:')
            ->assertSee('Courses taught:')
            ->assertSee('External social proof')
            ->assertSee('I am a Parent')
            ->assertSee('Courses students ask about most')
            ->assertSee('Browse by learning goal')
            ->assertSee('Real students. Practical progress.')
            ->assertSee('Still confused about which course fits you?')
            ->assertSee('Message on WhatsApp')
            ->assertSee('Ask for Course Help');

        $this->get(route('courses-all'))
            ->assertOk()
            ->assertSee('IELTS Preparation for Band 7 Goal')
            ->assertSee('Office and Admin Skills Package')
            ->assertSee('Ask for Course Help');

        $this->get(route('courses'))
            ->assertRedirect(route('courses-all'));

        $this->get(route('blog'))
            ->assertOk()
            ->assertSee('Which Course Should I Choose After SEE or Plus Two?');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Founder and Academic Director');
    }

    public function test_admin_cms_can_update_seeded_category_and_sync_course_fields(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@goldeneye.edu.np')->firstOrFail();
        $category = CourseCategory::where('slug', 'study-abroad-test-prep')->firstOrFail();

        $course = Course::where('slug', 'ielts-masterclass')->firstOrFail();
        $this->assertSame($category->id, $course->category_id);

        $response = $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Study Abroad and Test Prep',
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
            'name' => 'Study Abroad and Test Prep',
        ]);

        $this->assertDatabaseHas(Course::class, [
            'id' => $course->id,
            'category' => 'Study Abroad and Test Prep',
            'category_slug' => 'study-abroad-test-prep',
        ]);
    }

    public function test_admin_branding_accepts_seeded_dynamic_image_paths(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@goldeneye.edu.np')->firstOrFail();

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
