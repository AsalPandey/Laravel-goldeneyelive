<?php

namespace Tests\Feature\Admin;

use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        // Mocking settings
        SiteSetting::create(['key' => 'site_email', 'value' => 'old@example.com', 'type' => 'text']);
        SiteSetting::create(['key' => 'hero_title', 'value' => 'Old Title', 'type' => 'text']);
    }

    /** @test */
    public function test_branding_index_calculates_completeness_correctly()
    {
        // We have 2 settings filled out of 17 expected keys in BrandingController
        // 2 / 17 = 11.76% -> 12%

        $response = $this->actingAs($this->admin)
            ->get(route('admin.branding.index'));

        $response->assertStatus(200);
        $response->assertViewHas('brandCompleteness', 12);
    }

    /** @test */
    public function test_branding_update_validates_email_and_urls()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'site_email' => 'invalid-email',
                'facebook_url' => 'not-a-url',
            ]);

        $response->assertSessionHasErrors(['site_email', 'facebook_url']);
    }

    /** @test */
    public function test_branding_update_performs_bulk_upsert()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'site_email' => 'new@example.com',
                'hero_title' => 'New Awesome Title',
                'whatsapp_number' => '9876543210',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('site_settings', [
            'key' => 'site_email',
            'value' => 'new@example.com',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'hero_title',
            'value' => 'New Awesome Title',
        ]);

        $this->assertDatabaseHas('site_settings', [
            'key' => 'whatsapp_number',
            'value' => '9876543210',
        ]);
    }

    /** @test */
    public function test_branding_analytics_id_regex_validation()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'google_analytics_id' => 'UA-12345-1', // Invalid for our G-XXXX regex
            ]);

        $response->assertSessionHasErrors(['google_analytics_id']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'google_analytics_id' => 'G-ABC123XYZ', // Valid
            ]);

        $response->assertSessionDoesntHaveErrors(['google_analytics_id']);
    }
}
