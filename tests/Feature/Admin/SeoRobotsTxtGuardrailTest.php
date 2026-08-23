<?php

namespace Tests\Feature\Admin;

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

    public function test_crafted_robots_input_is_ignored_while_supported_metadata_saves(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.seo.update'), [
                'meta_title' => 'GoldenEye Academy SEO',
                'meta_description' => 'Study abroad, language, and computer training in Pokhara.',
                'robots_txt' => "User-agent: *\nDisallow: /",
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing(SiteSetting::class, [
            'key' => 'robots_txt',
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

    public function test_seo_page_exposes_a_read_only_laravel_managed_policy(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.seo.index'))
            ->assertOk()
            ->assertSee('Laravel-managed crawler policy')
            ->assertSee('User-agent: *')
            ->assertSee('Disallow: /admin')
            ->assertDontSee('name="robots_txt"', false)
            ->assertDontSee('robots_txt_deindex_confirm', false);
    }

    public function test_public_robots_policy_ignores_legacy_database_content(): void
    {
        config()->set('app.url', 'https://robots-policy.example');
        SiteSetting::create([
            'key' => 'robots_txt',
            'value' => "User-agent: *\nDisallow: /\nSitemap: https://stale.example/sitemap.xml",
            'type' => 'text',
        ]);

        $content = $this->get('/robots.txt')->assertOk()->getContent();

        $this->assertStringContainsString("User-agent: *\nAllow: /", $content);
        $this->assertStringContainsString('Disallow: /admin', $content);
        $this->assertStringContainsString('Sitemap: https://robots-policy.example/sitemap.xml', $content);
        $this->assertStringNotContainsString('Disallow: /\n', $content);
        $this->assertStringNotContainsString('stale.example', $content);
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
