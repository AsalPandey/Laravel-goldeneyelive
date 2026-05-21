<?php

namespace Tests\Feature\Admin;

use App\Models\Contact;
use App\Models\JoinNowQuery;
use App\Models\NewsLetter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BulkActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Staff']);
        Role::firstOrCreate(['name' => 'Student']);
    }

    public function test_admin_can_bulk_delete_contacts()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $contacts = Contact::factory()->count(3)->create();
        $ids = $contacts->pluck('id')->toArray();

        $response = $this->actingAs($admin)
            ->post(route('admin.submissions.bulk-delete'), [
                'type' => 'contact',
                'ids' => $ids,
            ]);

        $response->assertStatus(302);
        $this->assertEquals(0, Contact::count());
    }

    public function test_admin_can_bulk_delete_enrollments()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $enrollments = JoinNowQuery::factory()->count(3)->create();
        $ids = $enrollments->pluck('id')->toArray();

        $response = $this->actingAs($admin)
            ->post(route('admin.submissions.bulk-delete'), [
                'type' => 'join_now',
                'ids' => $ids,
            ]);

        $response->assertStatus(302);
        $this->assertEquals(0, JoinNowQuery::count());
    }

    public function test_admin_can_mark_enrollment_as_enrolled()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $enrollment = JoinNowQuery::factory()->create(['status' => 'new']);

        $response = $this->actingAs($admin)
            ->patch(route('admin.submissions.join_now.status.update', $enrollment->id), [
                'status' => 'enrolled',
                'admin_notes' => 'Student completed admission.',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas(JoinNowQuery::class, [
            'id' => $enrollment->id,
            'status' => 'enrolled',
            'admin_notes' => 'Student completed admission.',
        ]);

        $this->assertNotNull($enrollment->refresh()->followed_up_at);
    }

    public function test_admin_can_bulk_delete_subscribers()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $subscribers = NewsLetter::factory()->count(3)->create();
        $ids = $subscribers->pluck('id')->toArray();

        $response = $this->actingAs($admin)
            ->post(route('admin.submissions.bulk-delete'), [
                'type' => 'newsletter',
                'ids' => $ids,
            ]);

        $response->assertStatus(302);
        $this->assertEquals(0, NewsLetter::count());
    }

    public function test_admin_submission_listing_views_render_clean_empty_tables()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin)
            ->get(route('admin.submissions.contact-display'))
            ->assertOk()
            ->assertSee('colspan="6"', false)
            ->assertDontSee('â€¢', false);

        $this->actingAs($admin)
            ->get(route('admin.submissions.join_now-display'))
            ->assertOk()
            ->assertSee('colspan="6"', false)
            ->assertDontSee('â€¢', false);

        $this->actingAs($admin)
            ->get(route('admin.submissions.newsletter-display'))
            ->assertOk()
            ->assertSee('<tr>', false)
            ->assertSee('colspan="4"', false);
    }

    public function test_admin_enrollment_list_shows_course_help_submission_source_and_searches_it()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        JoinNowQuery::factory()->create([
            'firstName' => 'Asha',
            'lastName' => 'Sharma',
            'email' => 'asha@example.com',
            'phone' => '9812345678',
            'course' => 'Need help choosing a program',
            'queries' => 'I need help choosing IELTS or PTE.',
            'lead_source' => 'join_now_page',
            'cta_id' => 'join-now-form',
            'help_topic' => 'IELTS / PTE',
            'selected_course' => 'ielts-masterclass',
            'source_page' => 'home',
            'source_section' => 'hero',
            'audience_type' => 'study_abroad_applicant',
            'inquiry_intent' => 'course_guidance',
            'lead_score' => 15,
            'lead_status' => 'Hot',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.submissions.join_now-display', ['search' => 'Hot']))
            ->assertOk()
            ->assertSee('Asha Sharma')
            ->assertSee('9812345678')
            ->assertSee('Need help choosing a program')
            ->assertSee('Source: join_now_page')
            ->assertSee('CTA: join-now-form')
            ->assertSee('Help: IELTS / PTE')
            ->assertSee('Hot: 15')
            ->assertSee('Selected: ielts-masterclass');
    }

    public function test_unauthorized_user_cannot_bulk_delete()
    {
        $user = User::factory()->create();
        $user->assignRole('Student');

        $contacts = Contact::factory()->count(3)->create();
        $ids = $contacts->pluck('id')->toArray();

        $response = $this->actingAs($user)
            ->post(route('admin.submissions.bulk-delete'), [
                'type' => 'contact',
                'ids' => $ids,
            ]);

        $response->assertStatus(403);
        $this->assertEquals(3, Contact::count());
    }
}
