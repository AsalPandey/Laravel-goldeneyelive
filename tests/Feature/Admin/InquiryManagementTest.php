<?php

namespace Tests\Feature\Admin;

use App\Models\Contact;
use App\Models\JoinNowQuery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InquiryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Staff']);
        Role::firstOrCreate(['name' => 'Student']);
    }

    public function test_processed_and_null_timestamp_inquiry_lists_render_without_errors(): void
    {
        $admin = $this->userWithRole('Admin');

        Contact::factory()->create([
            'status' => 'contacted',
            'replied_at' => now()->subHour(),
        ]);
        Contact::factory()->create([
            'status' => 'new',
            'replied_at' => null,
        ]);
        JoinNowQuery::factory()->create([
            'status' => 'resolved',
            'followed_up_at' => now()->subDay(),
        ]);
        JoinNowQuery::factory()->create([
            'status' => 'new',
            'followed_up_at' => null,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.submissions.contact-display'))
            ->assertOk()
            ->assertSee('First follow-up')
            ->assertSee('New');

        $this->actingAs($admin)
            ->get(route('admin.submissions.join_now-display'))
            ->assertOk()
            ->assertSee('First follow-up')
            ->assertSee('Resolved / Closed');
    }

    public function test_status_changes_are_consistent_and_only_contacted_records_get_follow_up_timestamps(): void
    {
        Carbon::setTestNow('2026-07-29 10:00:00');
        $staff = $this->userWithRole('Staff');
        $contact = Contact::factory()->create(['status' => 'new']);
        $courseInquiry = JoinNowQuery::factory()->create(['status' => 'new']);

        $this->actingAs($staff)
            ->patch(route('admin.submissions.contact.status.update', $contact->id), [
                'status' => 'contacted',
                'admin_notes' => 'Called the parent.',
            ])
            ->assertRedirect();

        $this->actingAs($staff)
            ->patch(route('admin.submissions.join_now.status.update', $courseInquiry->id), [
                'status' => 'resolved',
                'admin_notes' => 'Question resolved by message.',
            ])
            ->assertRedirect();

        $this->assertSame('contacted', $contact->refresh()->status);
        $this->assertTrue($contact->replied_at->equalTo(now()));
        $this->assertSame('resolved', $courseInquiry->refresh()->status);
        $this->assertNull($courseInquiry->followed_up_at);

        Carbon::setTestNow();
    }

    public function test_admin_can_archive_filter_and_restore_a_contact_inquiry(): void
    {
        $admin = $this->userWithRole('Admin');
        $contact = Contact::factory()->create([
            'name' => 'Archived Contact Person',
            'message' => 'Keep this original contact message.',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.submissions.contact.destroy', $contact->id))
            ->assertRedirect();

        $this->assertSoftDeleted($contact);

        $this->actingAs($admin)
            ->get(route('admin.submissions.contact-display'))
            ->assertOk()
            ->assertDontSee('Archived Contact Person');

        $this->actingAs($admin)
            ->get(route('admin.submissions.contact-display', ['view' => 'archived']))
            ->assertOk()
            ->assertSee('Archived Contact Person')
            ->assertSee('Keep this original contact message.')
            ->assertSee('Restore inquiry');

        $this->actingAs($admin)
            ->patch(route('admin.submissions.contact.restore', $contact->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted($contact);
    }

    public function test_staff_can_archive_and_restore_a_course_inquiry(): void
    {
        $staff = $this->userWithRole('Staff');
        $courseInquiry = JoinNowQuery::factory()->create([
            'queries' => 'Keep this original course message.',
        ]);

        $this->actingAs($staff)
            ->delete(route('admin.submissions.join_now.destroy', $courseInquiry->id))
            ->assertRedirect();

        $this->assertSoftDeleted($courseInquiry);

        $this->actingAs($staff)
            ->get(route('admin.submissions.join_now-display', ['view' => 'archived']))
            ->assertOk()
            ->assertSee('Keep this original course message.')
            ->assertSee('Restore course inquiry');

        $this->actingAs($staff)
            ->patch(route('admin.submissions.join_now.restore', $courseInquiry->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted($courseInquiry);
    }

    public function test_student_cannot_archive_or_restore_inquiries(): void
    {
        $student = $this->userWithRole('Student');
        $activeContact = Contact::factory()->create();
        $archivedContact = Contact::factory()->create();
        $archivedContact->delete();

        $this->actingAs($student)
            ->delete(route('admin.submissions.contact.destroy', $activeContact->id))
            ->assertForbidden();

        $this->actingAs($student)
            ->patch(route('admin.submissions.contact.restore', $archivedContact->id))
            ->assertForbidden();

        $this->assertNotSoftDeleted($activeContact);
        $this->assertSoftDeleted($archivedContact);
    }

    public function test_bulk_actions_archive_inquiries_without_permanently_deleting_them(): void
    {
        $admin = $this->userWithRole('Admin');
        $contacts = Contact::factory()->count(2)->create();
        $courseInquiries = JoinNowQuery::factory()->count(2)->create();

        $this->actingAs($admin)
            ->post(route('admin.submissions.bulk-delete'), [
                'type' => 'contact',
                'ids' => $contacts->modelKeys(),
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.submissions.bulk-delete'), [
                'type' => 'join_now',
                'ids' => $courseInquiries->modelKeys(),
            ])
            ->assertRedirect();

        $this->assertSame(2, Contact::onlyTrashed()->count());
        $this->assertSame(2, JoinNowQuery::onlyTrashed()->count());
        $this->assertSame(2, Contact::withTrashed()->count());
        $this->assertSame(2, JoinNowQuery::withTrashed()->count());
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
