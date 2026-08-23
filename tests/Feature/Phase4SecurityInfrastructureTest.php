<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Notice;
use App\Models\SiteSetting;
use App\Rules\ApprovedMapEmbedUrl;
use App\Support\PublicSiteCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class Phase4SecurityInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dynamic_pages_receive_security_headers_and_non_immutable_cache_policy(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Cache-Control', 'no-cache, private');

        $csp = (string) $response->headers->get('Content-Security-Policy-Report-Only');
        $this->assertNotSame('', $csp);
        $this->assertStringNotContainsString('unsafe-eval', $csp);
    }

    public function test_static_cache_policy_separates_fingerprinted_and_mutable_assets(): void
    {
        $htaccess = (string) file_get_contents(public_path('.htaccess'));

        $this->assertStringContainsString('max-age=31536000, immutable', $htaccess);
        $this->assertStringContainsString('max-age=3600, must-revalidate', $htaccess);
        $this->assertStringContainsString('^/build/assets/', $htaccess);
        $this->assertStringContainsString('manifest.json', $htaccess);
        $this->assertStringContainsString('\\.(html?|php)$', $htaccess);
    }

    public function test_hsts_is_emitted_only_for_secure_production_requests_and_admin_is_not_cacheable(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->get('http://localhost/contact')->assertHeaderMissing('Strict-Transport-Security');
        $this->get('https://localhost/contact')
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $this->get('https://localhost/admin')
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_map_embed_rule_accepts_only_https_google_map_embeds(): void
    {
        $rule = ['nullable', new ApprovedMapEmbedUrl];

        $this->assertFalse(Validator::make(['map' => 'https://www.google.com/maps/embed?pb=approved'], ['map' => $rule])->fails());

        foreach (['javascript:alert(1)', 'data:text/html,unsafe', 'https://example.com/maps/embed', 'http://www.google.com/maps/embed', 'https://www.google.com/search?q=map'] as $invalid) {
            $this->assertTrue(Validator::make(['map' => $invalid], ['map' => $rule])->fails(), $invalid);
        }
    }

    public function test_untrusted_legacy_map_value_is_not_rendered_as_iframe_source(): void
    {
        SiteSetting::create(['key' => 'google_maps_embed', 'value' => 'javascript:alert(1)', 'type' => 'text']);
        Cache::flush();

        $this->get(route('contact'))
            ->assertOk()
            ->assertDontSee('src="javascript:alert(1)"', false)
            ->assertSee('https://www.google.com/maps/embed?', false);
    }

    public function test_notice_cache_expires_at_the_next_scheduled_transition(): void
    {
        Carbon::setTestNow('2026-08-24 10:00:00');
        Notice::create([
            'title' => 'Scheduled Notice',
            'status' => 'active',
            'display_type' => 'popup',
            'starts_at' => now()->addSeconds(30),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->assertLessThanOrEqual(31, PublicSiteCache::secondsUntilNoticeTransition());
        $this->get(route('home'))->assertDontSee('Scheduled Notice');

        Carbon::setTestNow('2026-08-24 10:00:32');
        $this->get(route('home'))->assertSee('Scheduled Notice');
    }

    public function test_notice_expiry_does_not_remain_stale_for_an_hour(): void
    {
        Carbon::setTestNow('2026-08-24 11:00:00');
        Notice::create([
            'title' => 'Short Notice',
            'status' => 'active',
            'display_type' => 'popup',
            'expires_at' => now()->addSeconds(30),
        ]);

        $this->get(route('home'))->assertSee('Short Notice');
        Carbon::setTestNow('2026-08-24 11:00:32');
        $this->get(route('home'))->assertDontSee('Short Notice');
    }

    public function test_homepage_recovers_from_historical_serialized_model_cache(): void
    {
        Course::factory()->create(['status' => 'active']);
        Cache::put('homepage_data', ['courses' => Course::all()], 3600);

        $this->get(route('home'))->assertOk();

        $cached = Cache::get('homepage_data');
        $this->assertIsArray($cached);
        $this->assertIsArray($cached['courses']);
        $this->assertNotInstanceOf(Course::class, $cached['courses'][0]);
    }

    public function test_readiness_command_passes_in_a_configured_safe_rehearsal(): void
    {
        config([
            'app.url' => 'https://goldeneye.example',
            'app.debug' => false,
            'services.recaptcha.site_key' => 'present',
            'services.recaptcha.secret_key' => 'present',
            'security.csp_report_only' => false,
            'queue.default' => 'sync',
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.goldeneye.example',
            'mail.from.address' => 'no-reply@goldeneye.example',
        ]);

        $exitCode = Artisan::call('app:production-readiness', ['--skip-dependency-audits' => true]);
        $output = Artisan::output();

        $this->assertSame(0, $exitCode, $output);
        $this->assertStringContainsString('CAPTCHA protection', $output);
        $this->assertStringContainsString('Course-FAQ schema', $output);
        $this->assertStringContainsString(storage_path('logs'), $output);
        $this->assertStringContainsString(public_path('site/img'), $output);
    }

    public function test_production_readiness_rejects_a_non_mysql_database_connection(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        config([
            'app.url' => 'https://goldeneye.example',
            'app.debug' => false,
            'services.recaptcha.site_key' => 'present',
            'services.recaptcha.secret_key' => 'present',
            'security.csp_report_only' => false,
            'queue.default' => 'sync',
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.goldeneye.example',
            'mail.from.address' => 'no-reply@goldeneye.example',
            'database.default' => 'sqlite',
        ]);

        $exitCode = Artisan::call('app:production-readiness', ['--skip-dependency-audits' => true]);
        $output = Artisan::output();

        $this->assertSame(1, $exitCode, $output);
        $this->assertStringContainsString('production requires MySQL/MariaDB', $output);
    }

    public function test_production_readiness_fails_when_captcha_is_missing(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        config([
            'app.url' => 'https://goldeneye.example',
            'services.recaptcha.site_key' => null,
            'services.recaptcha.secret_key' => null,
        ]);

        $exitCode = Artisan::call('app:production-readiness', ['--skip-dependency-audits' => true]);
        $output = Artisan::output();

        $this->assertSame(1, $exitCode);
        $this->assertStringContainsString('FAIL', $output);
        $this->assertStringContainsString('CAPTCHA protection', $output);
    }
}
