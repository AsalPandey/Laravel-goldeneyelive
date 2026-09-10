<?php

namespace Tests\Feature\Admin;

use App\Mail\StaffWelcomeMail;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('Staff', 'web');
        $this->admin = User::factory()->create(['email' => config('goldeneye.permanent_admin_emails')[0]]);
        $this->admin->assignRole('Admin');
        $this->staff = User::factory()->create(['email' => 'staff@'.config('goldeneye.organization_email_domain')]);
        $this->staff->assignRole('Staff');
    }

    public function test_only_admin_can_access_staff_management_and_create_staff(): void
    {
        $this->get(route('admin.staff.index'))->assertRedirect(route('login'));
        $this->actingAs($this->staff)->get(route('admin.staff.index'))->assertForbidden();
        $this->get(route('admin.staff.create'))->assertForbidden();
        $this->post(route('admin.staff.store'), ['name' => 'New Staff', 'email' => 'new@goldeneye.edu.np'])->assertForbidden();
        $this->actingAs($this->admin)->get(route('admin.staff.index'))->assertOk()->assertSee($this->staff->email);
        $this->get(route('admin.staff.create'))->assertOk()->assertDontSee('name="password"', false);
        $this->post(route('admin.staff.store'), ['name' => 'New Staff', 'email' => 'NEW@GOLDENEYE.EDU.NP'])
            ->assertRedirect(route('admin.staff.index'))
            ->assertSessionHas('success', 'Staff account created and onboarding instructions were emailed successfully.');
        $created = User::where('email', 'new@goldeneye.edu.np')->firstOrFail();
        $this->assertTrue($created->hasRole('Staff'));
        $this->assertFalse($created->hasRole('Admin'));
        $this->assertFalse(Hash::check('password', $created->password));
        $this->assertNull($created->email_verified_at);
    }

    public function test_admin_creates_staff_and_automatically_sends_onboarding_email(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->admin)->post(route('admin.staff.store'), [
            'name' => 'Bikram Thapa',
            'email' => 'bikram.thapa@goldeneye.edu.np',
        ]);

        $response->assertRedirect(route('admin.staff.index'))
            ->assertSessionHas('success', 'Staff account created and onboarding instructions were emailed successfully.');

        $created = User::where('email', 'bikram.thapa@goldeneye.edu.np')->firstOrFail();
        $this->assertTrue($created->hasRole('Staff'));
        $this->assertFalse($created->hasRole('Admin'));

        Mail::assertSent(StaffWelcomeMail::class, 1);
        Mail::assertSent(StaffWelcomeMail::class, function (StaffWelcomeMail $mail) use ($created): bool {
            return $mail->hasTo($created->email)
                && $mail->envelope()->from->address === config('goldeneye.security_email')
                && $mail->envelope()->from->name === config('goldeneye.security_email_name')
                && $mail->envelope()->subject === 'Your Golden Eye Academy CMS Account Is Ready';
        });

        $mailable = new StaffWelcomeMail($created);
        $mailable->assertSeeInHtml($created->name);
        $mailable->assertSeeInHtml($created->email);
        $mailable->assertSeeInHtml(route('login'));
        $mailable->assertSeeInHtml(route('password.request'));
        $mailable->assertSeeInHtml('For security, no temporary password has been sent by email.');
        $mailable->assertDontSeeInHtml($created->password);
    }

    public function test_both_permanent_admins_can_onboard_staff_and_send_email(): void
    {
        Mail::fake();

        $secondAdmin = User::factory()->create(['email' => config('goldeneye.permanent_admin_emails')[1]]);
        $secondAdmin->assignRole('Admin');

        $this->actingAs($this->admin)->post(route('admin.staff.store'), [
            'name' => 'Staff One',
            'email' => 'staffone@goldeneye.edu.np',
        ])->assertRedirect(route('admin.staff.index'))->assertSessionHas('success');

        $this->actingAs($secondAdmin)->post(route('admin.staff.store'), [
            'name' => 'Staff Two',
            'email' => 'stafftwo@goldeneye.edu.np',
        ])->assertRedirect(route('admin.staff.index'))->assertSessionHas('success');

        Mail::assertSent(StaffWelcomeMail::class, 2);
    }

    public function test_non_admin_cannot_trigger_staff_creation_or_onboarding_email(): void
    {
        Mail::fake();

        $this->post(route('admin.staff.store'), [
            'name' => 'Unauthorized Guest',
            'email' => 'guest@goldeneye.edu.np',
        ])->assertRedirect(route('login'));

        $this->actingAs($this->staff)->post(route('admin.staff.store'), [
            'name' => 'Unauthorized Staff',
            'email' => 'anotherstaff@goldeneye.edu.np',
        ])->assertForbidden();

        Mail::assertNothingSent();
    }

    public function test_invalid_or_duplicate_email_does_not_send_onboarding_email(): void
    {
        Mail::fake();

        $this->actingAs($this->admin);

        $this->post(route('admin.staff.store'), [
            'name' => 'Invalid Domain',
            'email' => 'user@gmail.com',
        ])->assertSessionHasErrors('email');

        $this->post(route('admin.staff.store'), [
            'name' => 'Duplicate Email',
            'email' => $this->staff->email,
        ])->assertSessionHasErrors('email');

        $this->post(route('admin.staff.store'), [
            'name' => 'Reserved Admin',
            'email' => config('goldeneye.permanent_admin_emails')[1],
        ])->assertSessionHasErrors('email');

        Mail::assertNothingSent();
    }

    public function test_staff_account_remains_created_and_warning_shown_when_mail_fails(): void
    {
        $this->actingAs($this->admin);

        Mail::shouldReceive('to')
            ->once()
            ->with('mailfailure@goldeneye.edu.np')
            ->andReturnSelf();

        Mail::shouldReceive('send')
            ->once()
            ->andThrow(new RuntimeException('SMTP transport connection refused'));

        $response = $this->post(route('admin.staff.store'), [
            'name' => 'Mail Failure Staff',
            'email' => 'mailfailure@goldeneye.edu.np',
        ]);

        $response->assertRedirect(route('admin.staff.index'))
            ->assertSessionHas('warning', 'Staff account created, but the welcome email could not be sent. The staff member can still use Forgot Password to set their password.');

        $created = User::where('email', 'mailfailure@goldeneye.edu.np')->firstOrFail();
        $this->assertTrue($created->hasRole('Staff'));
        $this->assertFalse($created->hasRole('Admin'));
        $this->assertModelExists($created);
    }

    public function test_invalid_duplicate_and_reserved_emails_and_role_injection_are_rejected(): void
    {
        $this->actingAs($this->admin);
        foreach (['person@gmail.com', 'person@goldeneye.edu.np.fake.com', 'goldeneye.edu.np@gmail.com', 'person@subdomain.goldeneye.edu.np', 'STAFF@GOLDENEYE.EDU.NP', ...config('goldeneye.permanent_admin_emails')] as $email) {
            $this->post(route('admin.staff.store'), ['name' => 'Invalid', 'email' => $email])->assertSessionHasErrors('email');
        }
        $this->post(route('admin.staff.store'), ['name' => 'Invalid', 'email' => 'new@goldeneye.edu.np', 'role' => 'Admin'])->assertSessionHasErrors('role');
        $this->assertDatabaseCount('users', 2);
    }

    public function test_only_admin_can_delete_ordinary_staff_and_cannot_delete_admins(): void
    {
        $this->actingAs($this->staff)->delete(route('admin.staff.destroy', $this->staff))->assertForbidden();
        $this->delete(route('admin.staff.destroy', $this->admin))->assertForbidden();
        $this->actingAs($this->admin)->delete(route('admin.staff.destroy', $this->admin))->assertForbidden();
        $otherAdmin = User::factory()->create();
        $otherAdmin->assignRole(['Admin', 'Staff']);
        $this->delete(route('admin.staff.destroy', $otherAdmin))->assertForbidden();
        $ordinaryUser = User::factory()->create();
        $this->delete(route('admin.staff.destroy', $ordinaryUser))->assertForbidden();
        $this->delete(route('admin.staff.destroy', $this->staff))->assertRedirect(route('admin.staff.index'));
        $this->assertModelMissing($this->staff);
        $this->assertDatabaseMissing('model_has_roles', ['model_id' => $this->staff->id]);
    }

    public function test_permanent_addresses_are_normalized_and_protected_even_with_staff_role(): void
    {
        foreach (config('goldeneye.permanent_admin_emails') as $email) {
            $user = User::factory()->create(['email' => strtoupper($email)]);
            $user->assignRole('Staff');
            $this->assertTrue($user->isPermanentAdmin());
            $this->actingAs($this->admin)->delete(route('admin.staff.destroy', $user))->assertForbidden();
            $this->patch('/admin/staff/'.$user->id, ['role' => 'Staff'])->assertStatus(405);
            $this->assertModelExists($user);
        }
    }

    public function test_staff_and_permanent_admin_cannot_self_delete_or_escape_email_protection(): void
    {
        foreach ([$this->admin, $this->staff] as $user) {
            Livewire::actingAs($user)->test('pages::settings.delete-user-modal')
                ->set('password', 'password')->call('deleteUser')->assertForbidden();
            $this->assertModelExists($user);
        }
        Livewire::actingAs($this->admin)->test('pages::settings.profile')
            ->set('email', 'changed@goldeneye.edu.np')->call('updateProfileInformation')->assertForbidden();
        $this->assertTrue($this->admin->fresh()->isPermanentAdmin());
        Livewire::actingAs($this->staff)->test('pages::settings.profile')
            ->set('email', config('goldeneye.permanent_admin_emails')[1])->call('updateProfileInformation')->assertForbidden();
    }

    public function test_deletion_is_blocked_without_losing_roles_when_a_foreign_key_restricts_it(): void
    {
        Schema::create('staff_linked_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
        });
        DB::table('staff_linked_records')->insert(['user_id' => $this->staff->id]);
        $this->actingAs($this->admin)->delete(route('admin.staff.destroy', $this->staff))
            ->assertSessionHasErrors('staff');
        $this->assertModelExists($this->staff);
        $this->assertTrue($this->staff->fresh()->hasRole('Staff'));
        $this->assertDatabaseCount('staff_linked_records', 1);
    }
}
