<?php

namespace Tests\Feature\Admin;

use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsFormValidationPreservationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    public function test_branding_form_preserves_entered_data_on_validation_failure_without_saving(): void
    {
        SiteSetting::create(['key' => 'hero_title', 'value' => 'Original Hero Title', 'type' => 'text']);
        SiteSetting::create(['key' => 'courses_title', 'value' => 'Original Courses Title', 'type' => 'text']);

        $response = $this->actingAs($this->admin)
            ->from(route('admin.branding.index'))
            ->post(route('admin.branding.update'), [
                'facebook_url' => 'invalid-not-a-url',
                'hero_title' => 'Unsaved Draft Hero Title',
                'courses_title' => 'Unsaved Draft Courses Title',
                'site_name' => 'Draft Academy Name',
                'whatsapp_number' => '9841234567',
            ]);

        $response->assertRedirect(route('admin.branding.index'));
        $response->assertSessionHasErrors(['facebook_url']);

        // Check database was NOT updated
        $this->assertDatabaseHas('site_settings', [
            'key' => 'hero_title',
            'value' => 'Original Hero Title',
        ]);
        $this->assertDatabaseMissing('site_settings', [
            'key' => 'hero_title',
            'value' => 'Unsaved Draft Hero Title',
        ]);

        // Follow redirect and ensure entered old() values are displayed in the view
        $followUpResponse = $this->actingAs($this->admin)
            ->get(route('admin.branding.index'));

        $followUpResponse->assertOk();
        $followUpResponse->assertSee('Unsaved Draft Hero Title');
        $followUpResponse->assertSee('Unsaved Draft Courses Title');
        $followUpResponse->assertSee('Draft Academy Name');
    }

    public function test_seo_form_preserves_entered_data_on_validation_failure_without_saving(): void
    {
        SiteSetting::create(['key' => 'meta_title', 'value' => 'Original Meta Title', 'type' => 'text']);
        SiteSetting::create(['key' => 'meta_description', 'value' => 'Original Meta Description', 'type' => 'text']);

        $response = $this->actingAs($this->admin)
            ->from(route('admin.seo.index'))
            ->post(route('admin.seo.update'), [
                'google_analytics_id' => 'not-a-valid-ga-format',
                'meta_title' => 'Unsaved Draft Meta Title',
                'meta_description' => 'Unsaved Draft Meta Description',
                'aeo_summary' => 'Unsaved Draft AEO Summary',
            ]);

        $response->assertRedirect(route('admin.seo.index'));
        $response->assertSessionHasErrors(['google_analytics_id']);

        // Check database was NOT updated
        $this->assertDatabaseHas('site_settings', [
            'key' => 'meta_title',
            'value' => 'Original Meta Title',
        ]);
        $this->assertDatabaseMissing('site_settings', [
            'key' => 'meta_title',
            'value' => 'Unsaved Draft Meta Title',
        ]);

        // Follow redirect and ensure entered old() values are displayed in the view
        $followUpResponse = $this->actingAs($this->admin)
            ->get(route('admin.seo.index'));

        $followUpResponse->assertOk();
        $followUpResponse->assertSee('Unsaved Draft Meta Title');
        $followUpResponse->assertSee('Unsaved Draft Meta Description');
        $followUpResponse->assertSee('Unsaved Draft AEO Summary');
    }
}
