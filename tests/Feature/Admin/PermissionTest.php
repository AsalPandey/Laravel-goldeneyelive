<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected $staff;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Staff']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('Staff');
    }

    public function test_admin_can_access_branding_center()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.branding.index'));

        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_branding_center()
    {
        $response = $this->actingAs($this->staff)
            ->get(route('admin.branding.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_seo_center()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.seo.index'));

        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_seo_center()
    {
        $response = $this->actingAs($this->staff)
            ->get(route('admin.seo.index'));

        $response->assertStatus(403);
    }

    public function test_staff_can_access_content_modules()
    {
        $response = $this->actingAs($this->staff)
            ->get(route('admin.courses.index'));

        $response->assertStatus(200);

        $response = $this->actingAs($this->staff)
            ->get(route('admin.blog.index'));

        $response->assertStatus(200);
    }
}
