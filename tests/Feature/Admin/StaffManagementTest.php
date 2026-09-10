<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
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
            ->assertRedirect(route('admin.staff.index'))->assertSessionHas('success');
        $created = User::where('email', 'new@goldeneye.edu.np')->firstOrFail();
        $this->assertTrue($created->hasRole('Staff'));
        $this->assertFalse($created->hasRole('Admin'));
        $this->assertFalse(Hash::check('password', $created->password));
        $this->assertNull($created->email_verified_at);
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
