<?php

namespace Tests\Feature\Admin;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NoticeManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Staff']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('Staff');
    }

    public function test_index_reports_effective_publication_states_and_accessible_controls(): void
    {
        Notice::factory()->create(['title' => 'Live Notice', 'status' => 'active', 'display_type' => 'popup', 'image' => null]);
        Notice::factory()->create(['title' => 'Scheduled Notice', 'status' => 'active', 'display_type' => 'bar', 'starts_at' => now()->addDay()]);
        Notice::factory()->create(['title' => 'Expired Notice', 'status' => 'active', 'display_type' => 'popup', 'expires_at' => now()->subMinute()]);
        Notice::factory()->create(['title' => 'Inactive Notice', 'status' => 'inactive']);

        $response = $this->actingAs($this->admin)->get(route('admin.notices.index'));

        $response->assertOk()
            ->assertSee('Live Notice')
            ->assertSee('scheduled')
            ->assertSee('expired')
            ->assertSee('inactive')
            ->assertSee('for="notice-search"', false)
            ->assertSee('aria-label="Edit Live Notice"', false)
            ->assertSee('aria-label="Permanently delete Live Notice"', false)
            ->assertSee('site/img/carousel-1.png', false)
            ->assertSee('md:hidden', false);
    }

    public function test_search_filters_notice_titles_and_subtitles(): void
    {
        Notice::factory()->create(['title' => 'Enrollment Week', 'subtitle' => 'Admissions are open']);
        Notice::factory()->create(['title' => 'Holiday', 'subtitle' => 'Office closed']);

        $this->actingAs($this->admin)
            ->get(route('admin.notices.index', ['search' => 'Admissions']))
            ->assertOk()
            ->assertSee('Enrollment Week')
            ->assertDontSee('Holiday');
    }

    public function test_server_validation_matches_the_editor_character_limits(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.notices.store'), [
            'title' => str_repeat('T', 61),
            'subtitle' => str_repeat('S', 161),
            'badge' => str_repeat('B', 21),
            'status' => 'inactive',
            'display_type' => 'popup',
        ]);

        $response->assertSessionHasErrors(['title', 'subtitle', 'badge']);
        $this->assertDatabaseCount('notices', 0);
    }

    public function test_staff_can_create_only_an_inactive_notice_and_cannot_manage_existing_notices(): void
    {
        $this->actingAs($this->staff)
            ->post(route('admin.notices.store'), [
                'title' => 'Staff Draft',
                'status' => 'active',
                'display_type' => 'popup',
            ])
            ->assertRedirect(route('admin.notices.index'))
            ->assertSessionHasNoErrors();

        $notice = Notice::query()->where('title', 'Staff Draft')->firstOrFail();
        $this->assertSame('inactive', $notice->status);

        $this->actingAs($this->staff)->patch(route('admin.notices.toggle', $notice))->assertForbidden();
        $this->actingAs($this->staff)->delete(route('admin.notices.destroy', $notice))->assertForbidden();
    }

    public function test_activating_a_current_notice_deactivates_the_competing_surface_only(): void
    {
        $oldPopup = Notice::factory()->create(['status' => 'active', 'display_type' => 'popup']);
        $bar = Notice::factory()->create(['status' => 'active', 'display_type' => 'bar']);
        $newPopup = Notice::factory()->create(['status' => 'inactive', 'display_type' => 'popup']);

        $this->actingAs($this->admin)
            ->patch(route('admin.notices.toggle', $newPopup))
            ->assertRedirect();

        $this->assertSame('inactive', $oldPopup->fresh()->status);
        $this->assertSame('active', $bar->fresh()->status);
        $this->assertSame('active', $newPopup->fresh()->status);
    }
}
