<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\CanonicalUrl;
use App\Support\SocialImage;
use Database\Seeders\LiveSiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SocialPreviewRefinementTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_seed_contains_tailored_page_course_and_blog_previews(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $this->assertSame('', SiteSetting::getValue('homepage_social_image'));
        $this->assertSame(
            'Golden Eye Academy | Computer, Language & IELTS/PTE in Pokhara',
            SiteSetting::getValue('home_meta_title'),
        );
        $this->assertSame(
            'Computer, Language and IELTS/PTE Courses in Pokhara',
            SiteSetting::getValue('courses_title'),
        );

        $coursePreviews = [
            'ielts-masterclass' => 'IELTS Preparation in Pokhara | Golden Eye Academy',
            'pte-elite-training' => 'PTE Academic Preparation in Pokhara | Golden Eye Academy',
            'jlpt-n5-elite' => 'Beginner Japanese and JLPT N5 in Pokhara | Golden Eye Academy',
            'jlpt-n4' => 'JLPT N4 Japanese Course in Pokhara | Golden Eye Academy',
            'professional-korean-eps' => 'EPS-TOPIK Korean Preparation in Pokhara | Golden Eye Academy',
            'basic-korean-course' => 'Basic Korean Course in Pokhara | Golden Eye Academy',
            'global-english-pro' => 'Practical English for Study & Work | Golden Eye Academy',
            'basic-english-course' => 'Basic English Course in Pokhara | Golden Eye Academy',
            'professional-web-development' => 'Web Development with Laravel in Pokhara | Golden Eye Academy',
            'advanced-computer-diploma' => 'Advanced Computer Diploma in Pokhara | Golden Eye Academy',
            'corporate-office-package' => 'Office Skills Course in Pokhara | Golden Eye Academy',
            'chinese-language-course' => 'Beginner Chinese Course in Pokhara | Golden Eye Academy',
            'free-course-roadmap-help' => 'Free Course Guidance in Pokhara | Golden Eye Academy',
        ];

        foreach ($coursePreviews as $slug => $title) {
            $course = Course::query()->where('slug', $slug)->firstOrFail();
            $this->assertSame($title, $course->meta_title);
            $this->assertLessThanOrEqual(70, mb_strlen($course->meta_title));
            $this->assertLessThanOrEqual(160, mb_strlen($course->meta_description));
            $this->get(route('courses-detail', $course->slug))
                ->assertOk()
                ->assertSee('<title>'.e($title).'</title>', false)
                ->assertSee('property="og:title" content="'.e($title).'"', false)
                ->assertSee('name="twitter:title" content="'.e($title).'"', false);
        }

        $this->assertSame(8, BlogPost::query()->count());
        foreach (BlogPost::query()->get() as $post) {
            $this->assertNotEmpty($post->meta_description);
            $this->assertLessThanOrEqual(160, mb_strlen($post->meta_description));
            $this->assertStringEndsWith('.', $post->meta_description);
        }
    }

    public function test_homepage_social_image_is_cms_controlled_and_preserves_hero_behavior(): void
    {
        $this->seed(LiveSiteSeeder::class);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertSee('Social Share Image')
            ->assertSee('name="homepage_social_image_path"', false)
            ->assertSee('A wide classroom or computer-class image works best.');

        $this->assertSocialImage($this->get(route('home')), 'site/img/carousel-1.png');

        $this->actingAs($admin)
            ->post(route('admin.branding.update'), [
                'homepage_social_image_path' => 'site/img/premium.png',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('site/img/premium.png', SiteSetting::getValue('homepage_social_image'));
        $homepage = $this->get(route('home'))->assertOk();
        $this->assertSocialImage($homepage, 'site/img/premium.png');
        $homepage->assertSee('site/img/carousel-1.png', false);

        $this->actingAs($admin)
            ->post(route('admin.branding.update'), ['hero_title' => 'Preserve the social image'])
            ->assertRedirect();
        $this->assertSame('site/img/premium.png', SiteSetting::getValue('homepage_social_image'));

        $this->actingAs($admin)
            ->from(route('admin.branding.index'))
            ->post(route('admin.branding.update'), [
                'homepage_social_image_path' => 'site/img/missing-social-image.png',
            ])
            ->assertRedirect(route('admin.branding.index'))
            ->assertSessionHasErrors('homepage_social_image_path');
        $this->assertSame('site/img/premium.png', SiteSetting::getValue('homepage_social_image'));

        $this->actingAs($admin)
            ->post(route('admin.branding.update'), ['remove_homepage_social_image' => '1'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
        $this->assertSame('', SiteSetting::getValue('homepage_social_image'));
        $this->assertSocialImage($this->get(route('home')), 'site/img/carousel-1.png');
    }

    public function test_page_specific_and_global_social_image_fallbacks_are_deterministic(): void
    {
        $this->seed(LiveSiteSeeder::class);
        SiteSetting::query()->where('key', 'homepage_social_image')->update(['value' => 'site/img/premium.png']);
        SiteSetting::query()->where('key', 'audience_students_image')->update(['value' => 'site/img/cat-1.jpg']);
        cache()->flush();

        $this->assertSocialImage($this->get(route('for-students')), 'site/img/cat-1.jpg');
        $this->assertSocialImage($this->get(route('about')), 'site/img/about.jpg');
        $this->assertSocialImage($this->get(route('faq')), 'site/img/premium.png');

        SiteSetting::query()->where('key', 'audience_students_image')->update(['value' => '']);
        cache()->flush();
        $this->assertSocialImage($this->get(route('for-students')), 'site/img/premium.png');

        SiteSetting::query()->where('key', 'homepage_social_image')->update(['value' => '']);
        cache()->flush();
        $this->assertSocialImage($this->get(route('faq')), 'site/img/carousel-1.png');

        SiteSetting::query()->where('key', 'hero_image')->update(['value' => '']);
        cache()->flush();
        $this->assertSocialImage($this->get(route('faq')), 'site/img/logo.png');
    }

    public function test_course_and_blog_images_keep_page_specific_social_authority(): void
    {
        $this->seed(LiveSiteSeeder::class);
        SiteSetting::query()->where('key', 'homepage_social_image')->update(['value' => 'site/img/premium.png']);
        cache()->flush();

        $course = Course::query()->where('slug', 'professional-web-development')->firstOrFail();
        $this->assertSocialImage($this->get(route('courses-detail', $course->slug)), $course->photo);

        $post = BlogPost::query()->where('slug', 'how-web-development-builds-a-career-portfolio')->firstOrFail();
        $this->assertSocialImage($this->get(route('blog-detail', $post->slug)), $post->image);
    }

    public function test_representative_pages_render_one_consistent_metadata_set(): void
    {
        $this->seed(LiveSiteSeeder::class);

        foreach ([
            route('home'),
            route('courses-all'),
            route('catalogue'),
            route('for-students'),
            route('for-parents'),
            route('study-abroad-guidance'),
            route('job-computer-skills'),
            route('about'),
            route('contact'),
            route('faq'),
            route('blog'),
        ] as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertSame(1, substr_count($html, '<title>'));
            $this->assertSame(1, substr_count($html, 'name="description"'));
            $this->assertSame(1, substr_count($html, 'property="og:title"'));
            $this->assertSame(1, substr_count($html, 'property="og:description"'));
            $this->assertSame(1, substr_count($html, 'property="og:image"'));
            $this->assertSame(1, substr_count($html, 'name="twitter:title"'));
            $this->assertSame(1, substr_count($html, 'name="twitter:description"'));
            $this->assertSame(1, substr_count($html, 'name="twitter:image"'));
            $this->assertSame(1, substr_count($html, 'rel="canonical"'));
        }
    }

    private function admin(): User
    {
        Role::firstOrCreate(['name' => 'Admin']);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        return $admin;
    }

    private function assertSocialImage(TestResponse $response, string $path): void
    {
        $url = url($path);
        if (str_contains($response->getContent(), 'property="og:url" content="'.CanonicalUrl::baseUrl().'"')) {
            $url = SocialImage::resolve($path)['url'];
        }

        $response
            ->assertOk()
            ->assertSee('property="og:image" content="'.$url.'"', false)
            ->assertSee('name="twitter:image" content="'.$url.'"', false);
    }
}
