<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use App\Support\StructuredData;
use Database\Seeders\LiveSiteSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficialTikTokSocialLinkTest extends TestCase
{
    use RefreshDatabase;

    private const OFFICIAL_TIKTOK_URL = 'https://www.tiktok.com/@goldeneye.academy';

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    public function test_branding_displays_the_current_tiktok_url(): void
    {
        SiteSetting::query()->create([
            'key' => 'tiktok_url',
            'value' => self::OFFICIAL_TIKTOK_URL,
            'type' => 'text',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertSee('name="tiktok_url"', false)
            ->assertSee('value="'.self::OFFICIAL_TIKTOK_URL.'"', false);
    }

    public function test_tiktok_url_can_be_created_edited_and_cleared(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'tiktok_url' => self::OFFICIAL_TIKTOK_URL,
            ])
            ->assertRedirect()
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('site_settings', [
            'key' => 'tiktok_url',
            'value' => self::OFFICIAL_TIKTOK_URL,
            'type' => 'text',
        ]);

        $editedUrl = 'https://www.tiktok.com/@goldeneye.academy.official';

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'tiktok_url' => $editedUrl,
            ])
            ->assertRedirect()
            ->assertSessionDoesntHaveErrors();

        $this->assertSame($editedUrl, SiteSetting::getValue('tiktok_url'));

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'tiktok_url' => '',
            ])
            ->assertRedirect()
            ->assertSessionDoesntHaveErrors();

        $this->assertSame('', SiteSetting::getValue('tiktok_url'));
        $this->assertSame(1, SiteSetting::query()->where('key', 'tiktok_url')->count());
    }

    public function test_invalid_tiktok_url_is_rejected_without_changing_the_setting(): void
    {
        SiteSetting::query()->create([
            'key' => 'tiktok_url',
            'value' => self::OFFICIAL_TIKTOK_URL,
            'type' => 'text',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'tiktok_url' => 'not-a-url',
            ])
            ->assertSessionHasErrors('tiktok_url');

        $this->assertSame(self::OFFICIAL_TIKTOK_URL, SiteSetting::getValue('tiktok_url'));
    }

    public function test_footer_renders_configured_tiktok_and_existing_social_links(): void
    {
        $socialLinks = [
            'facebook_url' => 'https://www.facebook.com/goldeneyeacademy',
            'instagram_url' => 'https://www.instagram.com/goldeneye.academy/',
            'linkedin_url' => 'https://www.linkedin.com/company/golden-eye-academy/',
            'youtube_url' => 'https://www.youtube.com/@goldeneyeacademy',
            'tiktok_url' => self::OFFICIAL_TIKTOK_URL,
        ];

        foreach ($socialLinks as $key => $value) {
            SiteSetting::query()->create(compact('key', 'value') + ['type' => 'text']);
        }

        cache()->flush();

        $response = $this->get(route('home'))->assertOk();

        foreach ($socialLinks as $value) {
            $response->assertSee('href="'.$value.'"', false);
        }

        $response
            ->assertSee('aria-label="Golden Eye Academy on TikTok"', false)
            ->assertSee('class="fab fa-tiktok"', false);
    }

    public function test_footer_does_not_render_a_blank_tiktok_link(): void
    {
        SiteSetting::query()->create([
            'key' => 'tiktok_url',
            'value' => '',
            'type' => 'text',
        ]);

        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Golden Eye Academy on TikTok', false)
            ->assertDontSee('fa-tiktok', false);
    }

    public function test_organization_same_as_includes_only_a_populated_tiktok_url(): void
    {
        $configuredSchema = StructuredData::siteGraph([
            'facebook_url' => 'https://www.facebook.com/goldeneyeacademy',
            'instagram_url' => 'https://www.instagram.com/goldeneye.academy/',
            'linkedin_url' => 'https://www.linkedin.com/company/golden-eye-academy/',
            'youtube_url' => 'https://www.youtube.com/@goldeneyeacademy',
            'tiktok_url' => self::OFFICIAL_TIKTOK_URL,
        ]);

        $this->assertSame([
            'https://www.facebook.com/goldeneyeacademy',
            'https://www.instagram.com/goldeneye.academy/',
            'https://www.linkedin.com/company/golden-eye-academy/',
            'https://www.youtube.com/@goldeneyeacademy',
            self::OFFICIAL_TIKTOK_URL,
        ], $configuredSchema['@graph'][0]['sameAs']);

        $blankSchema = StructuredData::siteGraph(['tiktok_url' => '']);

        $this->assertArrayNotHasKey('sameAs', $blankSchema['@graph'][0]);
    }

    public function test_live_site_seeder_sets_the_official_tiktok_url_idempotently(): void
    {
        $this->seed(LiveSiteSeeder::class);
        $this->seed(LiveSiteSeeder::class);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'tiktok_url',
            'value' => self::OFFICIAL_TIKTOK_URL,
            'type' => 'text',
        ]);
        $this->assertSame(1, SiteSetting::query()->where('key', 'tiktok_url')->count());
    }
}
