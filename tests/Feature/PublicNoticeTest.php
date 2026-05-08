<?php

namespace Tests\Feature;

use App\Models\Notice;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNoticeTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_notice_is_visible_on_homepage(): void
    {
        cache()->flush();
        $notice = Notice::create([
            'title' => 'Test Notice',
            'subtitle' => 'Test Subtitle',
            'link' => 'https://example.com/test-link',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        $response = $this->get('/');
        $response->assertSee('Test Notice');
        $response->assertSee('Test Subtitle');
        $response->assertSee('https://example.com/test-link');
        $response->assertSee('siteNoticePopup');
        $response->assertSee('notice-popup');
    }

    public function test_inactive_notice_is_not_visible(): void
    {
        cache()->flush();
        $notice = Notice::create([
            'title' => 'Hidden Notice',
            'content' => 'Hidden Content',
            'link' => 'https://example.com/hidden',
            'status' => 'inactive',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Hidden Notice');
    }

    public function test_urgent_notice_is_preferred_when_multiple_notices_match_same_surface(): void
    {
        cache()->flush();
        Notice::create([
            'title' => 'First Notice',
            'status' => 'active',
            'link' => 'link1',
            'display_type' => 'popup',
        ]);

        Notice::create([
            'title' => 'Urgent Notice',
            'status' => 'active',
            'link' => 'link2',
            'display_type' => 'popup',
            'is_urgent' => true,
        ]);

        $response = $this->get('/');

        $response->assertSee('Urgent Notice');
        $response->assertDontSee('First Notice');
    }

    public function test_active_bar_and_popup_notices_render_together(): void
    {
        cache()->flush();

        Notice::create([
            'title' => 'Top Bar Notice',
            'subtitle' => 'Visible above navbar',
            'status' => 'active',
            'display_type' => 'bar',
        ]);

        Notice::create([
            'title' => 'Popup Notice',
            'subtitle' => 'Visible as modal',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('siteNoticeStrip');
        $response->assertSee('siteNoticePopup');
        $response->assertSee('Top Bar Notice');
        $response->assertSee('Popup Notice');
    }

    public function test_active_bar_notice_does_not_suppress_marketing_popup_settings(): void
    {
        cache()->flush();

        SiteSetting::create([
            'key' => 'popup_status',
            'value' => 'active',
            'type' => 'text',
        ]);

        SiteSetting::create([
            'key' => 'popup_title',
            'value' => 'Marketing Popup',
            'type' => 'text',
        ]);

        Notice::create([
            'title' => 'Top Bar Notice',
            'status' => 'active',
            'display_type' => 'bar',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Top Bar Notice');
        $response->assertSee('Marketing Popup');
    }

    public function test_unscheduled_current_notice_is_preferred_over_future_and_expired_notices(): void
    {
        cache()->flush();

        Notice::create([
            'title' => 'Future Notice',
            'status' => 'active',
            'display_type' => 'popup',
            'starts_at' => now()->addDay(),
        ]);

        Notice::create([
            'title' => 'Expired Notice',
            'status' => 'active',
            'display_type' => 'popup',
            'expires_at' => now()->subDay(),
        ]);

        Notice::create([
            'title' => 'Current Notice',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Current Notice');
        $response->assertDontSee('Future Notice');
        $response->assertDontSee('Expired Notice');
    }

    public function test_homepage_tolerates_older_shared_site_cache_shape(): void
    {
        cache()->put('site_shared_data', [
            'settings' => [],
            'footerFaqs' => [],
            'activeNotice' => null,
            'activeCategories' => [],
        ], 3600);

        $this->get('/')
            ->assertOk();
    }
}
