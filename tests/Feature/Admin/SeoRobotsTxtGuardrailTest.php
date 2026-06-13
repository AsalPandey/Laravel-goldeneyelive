<?php

namespace Tests\Feature\Admin;

use App\Http\Requests\Admin\SEORequest;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoRobotsTxtGuardrailTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    public function test_safe_robots_txt_saves_without_confirmation(): void
    {
        $robotsTxt = "User-agent: *\nDisallow: /admin\nDisallow: /login\n\nSitemap: http://localhost/sitemap.xml";

        $this->actingAs($this->admin)
            ->post(route('admin.seo.update'), [
                'meta_title' => 'GoldenEye Academy SEO',
                'meta_description' => 'Study abroad, language, and computer training in Pokhara.',
                'robots_txt' => $robotsTxt,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'robots_txt',
            'value' => $robotsTxt,
        ]);
        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'meta_title',
            'value' => 'Golden Eye Academy SEO',
        ]);
        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'meta_description',
            'value' => 'Study abroad, language, and computer training in Pokhara.',
        ]);
    }

    public function test_dangerous_robots_txt_fails_without_confirmation(): void
    {
        $robotsTxt = "User-agent: *\nDisallow: /";

        $this->actingAs($this->admin)
            ->from(route('admin.seo.index'))
            ->post(route('admin.seo.update'), [
                'robots_txt' => $robotsTxt,
            ])
            ->assertRedirect(route('admin.seo.index'))
            ->assertSessionHasErrors([
                'robots_txt' => SEORequest::ROBOTS_FULL_SITE_BLOCK_WARNING,
            ]);

        $this->assertDatabaseMissing(SiteSetting::class, [
            'key' => 'robots_txt',
            'value' => $robotsTxt,
        ]);
    }

    public function test_dangerous_robots_txt_saves_with_confirmation(): void
    {
        $robotsTxt = "User-agent: *\nDisallow: /";

        $this->actingAs($this->admin)
            ->post(route('admin.seo.update'), [
                'robots_txt' => $robotsTxt,
                'robots_txt_deindex_confirm' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'robots_txt',
            'value' => $robotsTxt,
        ]);
    }

    public function test_seo_page_renders_robots_txt_warning_and_help_text(): void
    {
        SiteSetting::create([
            'key' => 'robots_txt',
            'value' => "User-agent: *\nDisallow: /",
            'type' => 'text',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.seo.index'))
            ->assertOk()
            ->assertSee(SEORequest::ROBOTS_FULL_SITE_BLOCK_WARNING, false)
            ->assertSee('For production, avoid', false)
            ->assertSee('I understand this robots.txt may block the entire website from Google and other search engines.', false);
    }

    public function test_robots_txt_detection_catches_full_site_blockers(): void
    {
        $this->assertTrue(SEORequest::robotsTxtBlocksFullSite("User-agent: *\nDisallow: /"));
        $this->assertTrue(SEORequest::robotsTxtBlocksFullSite("User-agent: Googlebot\nDisallow: /*"));
        $this->assertTrue(SEORequest::robotsTxtBlocksFullSite('X-Robots-Tag: noindex'));
        $this->assertTrue(SEORequest::robotsTxtBlocksFullSite('Noindex: /'));
        $this->assertFalse(SEORequest::robotsTxtBlocksFullSite("User-agent: *\nDisallow: /admin\nDisallow: /login"));
        $this->assertFalse(SEORequest::robotsTxtBlocksFullSite(''));
    }

    public function test_public_homepage_contract_remains_unchanged_after_seo_update(): void
    {
        SiteSetting::insert([
            ['key' => 'popup_status', 'value' => 'active', 'type' => 'text'],
            ['key' => 'hero_cta_1_text', 'value' => 'Ask for Course Help', 'type' => 'text'],
            ['key' => 'hero_cta_2_text', 'value' => 'View Course Details', 'type' => 'text'],
            ['key' => 'popup_button_text', 'value' => 'Ask for Course Help', 'type' => 'text'],
            ['key' => 'whatsapp_cta_text', 'value' => 'Message on WhatsApp', 'type' => 'text'],
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.seo.update'), [
                'meta_title' => 'GoldenEye Academy SEO',
                'robots_txt' => "User-agent: *\nDisallow: /admin\nDisallow: /login",
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $response = $this->get(route('home'))->assertOk();
        $html = $response->getContent();

        $this->assertSame(1, substr_count($html, 'site-desktop-nav'));
        $this->assertSame(1, substr_count($html, 'site-mobile-nav'));
        $this->assertStringContainsString('siteNoticePopup', $html);
        $this->assertStringContainsString('Ask for Course Help', $html);
        $this->assertStringNotContainsString('Ask for Course Guidance', $html);
        $this->assertStringNotContainsString('Message us on WhatsApp', $html);
    }
}
