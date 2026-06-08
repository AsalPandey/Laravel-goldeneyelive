<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Staff']);
        Role::firstOrCreate(['name' => 'Student']);
    }

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('login'));
    }

    public function test_verified_students_can_visit_their_dashboard_flow(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Student');

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Student');

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_unverified_admin_cannot_access_verified_admin_dashboard(): void
    {
        $user = User::factory()->unverified()->create();
        $user->assignRole('Admin');

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertRedirect(route('verification.notice'));
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_staff_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Staff');

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk();
    }
}
