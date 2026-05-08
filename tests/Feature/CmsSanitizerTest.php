<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsSanitizerTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_cms_html_removes_dangerous_attributes_and_urls(): void
    {
        cache()->flush();

        SiteSetting::create([
            'key' => 'about_content',
            'value' => '<p onclick="alert(1)">Safe <strong>copy</strong></p><a href="javascript:alert(1)">bad link</a><img src="javascript:alert(1)" onerror="alert(1)">',
            'type' => 'text',
        ]);

        $response = $this->get(route('about'));

        $response->assertOk();
        $response->assertSeeText('Safe copy');
        $response->assertDontSee('onclick="alert(1)"', false);
        $response->assertDontSee('javascript:alert(1)', false);
        $response->assertDontSee('onerror="alert(1)"', false);
    }

    public function test_invalid_cms_schema_markup_is_not_rendered(): void
    {
        cache()->flush();

        SiteSetting::create([
            'key' => 'schema_markup',
            'value' => '<script>alert(1)</script>',
            'type' => 'text',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
