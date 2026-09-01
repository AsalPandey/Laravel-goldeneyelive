<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\FAQ;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\LiveSiteSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Phase3b4LegacyCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_baseline_omits_dead_settings_and_raw_schema_values(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $retiredSettingKeys = [
            'about_section_tagline',
            'about_section_title',
            'about_text',
            'about_title',
            'breadcrumb_bg',
            'cta_bg',
            'founder_image',
            'founder_message',
            'founder_name',
            'founder_position',
            'founder_section_tagline',
            'founder_section_title',
            'hero_cta_text',
            'recaptcha_secret_key',
            'recaptcha_site_key',
            'robots_txt',
            'schema_markup',
            'site_footer_logo',
            'teachers_subtitle',
            'teachers_title',
            'testimonials_title',
            'twitter_url',
        ];

        $this->assertSame([], SiteSetting::query()->whereIn('key', $retiredSettingKeys)->pluck('key')->all());
        $this->assertSame('https://www.tiktok.com/@goldeneye.academy', SiteSetting::getValue('tiktok_url'));
        $this->assertSame('Ask for Course Help', SiteSetting::getValue('hero_cta_1_text'));
        $this->assertSame(13, Course::query()->where('rating_star', '0')->where('rating_count', '0')->count());

        $this->assertTrue(Schema::hasColumns('courses', ['rating_star', 'rating_count', 'schema_markup']));
        $this->assertSame(0, Course::query()->whereNotNull('schema_markup')->count());
        $this->assertSame(0, BlogPost::query()->whereNotNull('schema_markup')->count());
        $this->assertSame(0, FAQ::query()->whereNotNull('schema_markup')->count());
        $this->assertSame(0, ServicePillar::query()->whereNotNull('schema_markup')->count());
    }

    public function test_branding_cms_does_not_expose_or_persist_retired_controls(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $branding = $this->actingAs($admin)->get(route('admin.branding.index'));
        $branding->assertOk()
            ->assertSee('name="tiktok_url"', false)
            ->assertSee('name="hero_cta_1_text"', false)
            ->assertDontSee('Founder\'s Vision Section')
            ->assertDontSee('name="founder_image"', false)
            ->assertDontSee('name="founder_message"', false)
            ->assertDontSee('name="hero_cta_text"', false)
            ->assertDontSee('name="testimonials_title"', false)
            ->assertDontSee('name="twitter_url"', false);

        $retiredPayload = [
            'about_section_title' => 'Hidden About Heading',
            'founder_name' => 'Hidden Founder',
            'hero_cta_text' => 'Hidden Hero CTA',
            'site_footer_logo_path' => 'site/img/logo.png',
            'testimonials_title' => 'Hidden Testimonial Heading',
            'twitter_url' => 'https://x.com/example',
        ];

        $this->actingAs($admin)
            ->post(route('admin.branding.update'), $retiredPayload)
            ->assertRedirect();

        $this->assertSame([], SiteSetting::query()->whereIn('key', array_keys($retiredPayload))->pluck('key')->all());
    }

    public function test_compatibility_rows_are_not_deleted_by_repeat_seeding(): void
    {
        foreach ([
            'founder_image' => 'site/img/message-chairperson.jpg',
            'recaptcha_secret_key' => 'environment-owned-secret',
            'robots_txt' => 'legacy database policy',
            'schema_markup' => '{"legacy":true}',
        ] as $key => $value) {
            SiteSetting::query()->create(['key' => $key, 'value' => $value, 'type' => 'text']);
        }

        $this->seed(LiveSiteSeeder::class);

        $this->assertSame('site/img/message-chairperson.jpg', SiteSetting::getValue('founder_image'));
        $this->assertSame('environment-owned-secret', SiteSetting::getValue('recaptcha_secret_key'));
        $this->assertSame('legacy database policy', SiteSetting::getValue('robots_txt'));
        $this->assertSame('{"legacy":true}', SiteSetting::getValue('schema_markup'));
    }
}
