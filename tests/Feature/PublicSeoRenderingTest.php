<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSeoRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_head_renders_cms_managed_verification_and_speakable_seo(): void
    {
        SiteSetting::insert([
            ['key' => 'google_search_console_id', 'value' => 'google-token', 'type' => 'text'],
            ['key' => 'bing_webmaster_id', 'value' => 'bing-token', 'type' => 'text'],
            ['key' => 'speakable_selectors', 'value' => '.course-description, #main-description', 'type' => 'text'],
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('google-site-verification', false)
            ->assertSee('google-token', false)
            ->assertSee('msvalidate.01', false)
            ->assertSee('bing-token', false)
            ->assertSee('SpeakableSpecification', false)
            ->assertSee('.course-description', false);
    }
}
