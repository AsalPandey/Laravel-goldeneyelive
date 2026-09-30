<?php

namespace Tests\Feature\Admin;

use App\Models\Notice;
use App\Models\User;
use DOMDocument;
use DOMXPath;
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
            ->assertSee('Status / Public display')
            ->assertSee('aria-label="Activate Inactive Notice"', false)
            ->assertSee('aria-label="Deactivate Live Notice"', false)
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

    public function test_create_form_offers_only_the_two_public_placements(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.notices.create'))
            ->assertOk()
            ->assertSee('Popup-style notice')
            ->assertSee('Top announcement bar')
            ->assertDontSee('value="standard"', false);
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

    public function test_admin_can_activate_and_deactivate_a_popup_from_the_index(): void
    {
        $notice = Notice::factory()->create([
            'title' => 'Toggleable Popup',
            'status' => 'inactive',
            'display_type' => 'popup',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.notices.index'))
            ->assertSee('aria-label="Activate Toggleable Popup"', false);

        $this->patch(route('admin.notices.toggle', $notice))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('active', $notice->fresh()->status);
        $this->get(route('home'))->assertSee('Toggleable Popup');
        $this->get(route('admin.notices.index'))
            ->assertSee('aria-label="Deactivate Toggleable Popup"', false)
            ->assertSee('Public: live');

        $this->patch(route('admin.notices.toggle', $notice))->assertRedirect();

        $this->assertSame('inactive', $notice->fresh()->status);
        $this->get(route('home'))->assertDontSee('Toggleable Popup');
        $this->get(route('admin.notices.index'))
            ->assertSee('aria-label="Activate Toggleable Popup"', false)
            ->assertSee('Public: hidden');
    }

    public function test_activating_expired_notice_changes_status_but_does_not_ignore_its_schedule(): void
    {
        $notice = Notice::factory()->create([
            'title' => 'Expired Announcement',
            'status' => 'inactive',
            'display_type' => 'bar',
            'expires_at' => now()->subDay(),
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.notices.toggle', $notice))
            ->assertRedirect();

        $this->assertSame('active', $notice->fresh()->status);
        $this->get(route('admin.notices.index'))
            ->assertSee('aria-label="Deactivate Expired Announcement"', false)
            ->assertSee('Public: expired');
        $this->get(route('home'))->assertDontSee('Expired Announcement');
    }

    public function test_overridden_active_notice_is_queued_in_the_cms(): void
    {
        Notice::factory()->create([
            'title' => 'Fallback Popup',
            'status' => 'active',
            'display_type' => 'popup',
            'starts_at' => null,
        ]);
        Notice::factory()->create([
            'title' => 'Scheduled Priority Popup',
            'status' => 'active',
            'display_type' => 'popup',
            'starts_at' => now()->subMinute(),
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.notices.index'));

        $response->assertOk();

        $document = new DOMDocument;
        $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $fallbackRow = $xpath->query('//tr[.//div[contains(text(), "Fallback Popup")]]')->item(0);
        $priorityRow = $xpath->query('//tr[.//div[contains(text(), "Scheduled Priority Popup")]]')->item(0);

        $this->assertNotNull($fallbackRow);
        $this->assertNotNull($priorityRow);
        $this->assertSame('Public: queued', trim($xpath->query('.//td[3]//span[2]', $fallbackRow)->item(0)?->textContent ?? ''));
        $this->assertSame('Public: live', trim($xpath->query('.//td[3]//span[2]', $priorityRow)->item(0)?->textContent ?? ''));

        $publicPage = $this->get(route('home'));
        $publicPage->assertSee('Scheduled Priority Popup')->assertDontSee('Fallback Popup');
    }

    public function test_edit_form_preserves_legacy_standard_display_type(): void
    {
        $notice = Notice::factory()->create([
            'title' => 'Legacy Standard',
            'status' => 'inactive',
            'display_type' => 'standard',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.notices.index'))
            ->assertOk()
            ->assertSee('Popup')
            ->assertDontSee('>standard<', false);

        $this->actingAs($this->admin)
            ->get(route('admin.notices.edit', $notice))
            ->assertOk()
            ->assertSee('value="standard" selected', false)
            ->assertSee('Popup (legacy record)');

        $this->put(route('admin.notices.update', $notice), [
            'title' => 'Legacy Standard Updated',
            'status' => 'inactive',
            'display_type' => 'standard',
        ])->assertRedirect(route('admin.notices.index'));

        $this->assertSame('standard', $notice->fresh()->display_type);
    }

    public function test_bar_index_uses_an_icon_instead_of_a_photo_preview(): void
    {
        Notice::factory()->create([
            'title' => 'Bar Without Public Image',
            'status' => 'active',
            'display_type' => 'bar',
            'image' => 'site/img/notices/private-preview.jpg',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.notices.index'))
            ->assertOk()
            ->assertSee('Announcement bar')
            ->assertSee('fa-bullhorn', false)
            ->assertDontSee('private-preview.jpg');
    }

    public function test_updating_a_bar_gives_its_dismissal_a_new_version(): void
    {
        $notice = Notice::factory()->create([
            'title' => 'First Bar Message',
            'status' => 'active',
            'display_type' => 'bar',
        ]);

        $before = $this->get(route('home'))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/data-notice-version="[a-f0-9]{40}"/', $before);
        preg_match('/data-notice-version="([a-f0-9]{40})"/', $before, $initialVersion);

        $this->actingAs($this->admin)->put(route('admin.notices.update', $notice), [
            'title' => 'Updated Bar Message',
            'status' => 'active',
            'display_type' => 'bar',
        ])->assertRedirect(route('admin.notices.index'));

        $after = $this->get(route('home'))->assertOk()->getContent();
        preg_match('/data-notice-version="([a-f0-9]{40})"/', $after, $updatedVersion);

        $this->assertNotSame($initialVersion[1], $updatedVersion[1]);
        $this->assertStringContainsString('dismissedVersion === notice.dataset.noticeVersion', $after);
        $this->assertStringContainsString('12 * 60 * 60 * 1000', $after);
    }
}
