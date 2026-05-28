<?php

namespace Tests\Feature;

use App\Http\Requests\Site\ContactRequest;
use App\Http\Requests\Site\JoinNowRequest;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SiteSettingCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_value_returns_scalar_setting_value_and_caches_scalar(): void
    {
        SiteSetting::create([
            'key' => 'recaptcha_secret_key',
            'value' => 'test-secret',
            'type' => 'text',
        ]);

        $this->assertSame('test-secret', SiteSetting::getValue('recaptcha_secret_key'));
        $this->assertSame('test-secret', Cache::get('setting_recaptcha_secret_key'));
        $this->assertNotInstanceOf(SiteSetting::class, Cache::get('setting_recaptcha_secret_key'));
    }

    public function test_get_value_replaces_cached_model_object_with_scalar_value(): void
    {
        $setting = SiteSetting::create([
            'key' => 'recaptcha_secret_key',
            'value' => 'test-secret',
            'type' => 'text',
        ]);

        Cache::put('setting_recaptcha_secret_key', $setting, 3600);

        $this->assertSame('test-secret', SiteSetting::getValue('recaptcha_secret_key'));
        $this->assertSame('test-secret', Cache::get('setting_recaptcha_secret_key'));
        $this->assertNotInstanceOf(SiteSetting::class, Cache::get('setting_recaptcha_secret_key'));
    }

    public function test_get_value_returns_default_for_missing_setting(): void
    {
        $this->assertSame('fallback-value', SiteSetting::getValue('missing_setting', 'fallback-value'));
    }

    public function test_get_value_returns_null_safely_for_existing_null_setting(): void
    {
        SiteSetting::create([
            'key' => 'recaptcha_secret_key',
            'value' => null,
            'type' => 'text',
        ]);

        $this->assertNull(SiteSetting::getValue('recaptcha_secret_key', 'fallback-value'));
    }

    public function test_contact_request_rules_do_not_crash_when_recaptcha_secret_key_is_missing(): void
    {
        $rules = (new ContactRequest)->rules();

        $this->assertArrayNotHasKey('g-recaptcha-response', $rules);
    }

    public function test_join_now_request_rules_do_not_crash_when_recaptcha_secret_key_is_missing(): void
    {
        $rules = (new JoinNowRequest)->rules();

        $this->assertArrayNotHasKey('g-recaptcha-response', $rules);
    }
}
