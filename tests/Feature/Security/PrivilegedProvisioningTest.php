<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PrivilegedProvisioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_reference_seeding_creates_roles_without_users(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(RoleSeeder::class);

        $this->assertDatabaseCount('roles', 4);
        $this->assertDatabaseCount(User::class, 0);
    }

    public function test_privileged_provisioning_uses_a_one_time_setup_link_without_exposing_account_details(): void
    {
        Notification::fake();
        $this->seed(RoleSeeder::class);

        $email = 'authorized-owner@example.test';
        $exitCode = Artisan::call('account:provision-privileged', [
            'email' => $email,
            '--name' => 'Authorized Owner',
            '--role' => 'Admin',
        ]);

        $user = User::where('email', $email)->firstOrFail();

        $this->assertSame(0, $exitCode);
        $this->assertTrue($user->hasRole('Admin'));
        $this->assertNull($user->email_verified_at);
        $this->assertStringNotContainsString($email, Artisan::output());
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_existing_account_is_not_changed_by_privileged_provisioning(): void
    {
        Notification::fake();
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create([
            'password' => Hash::make('existing-test-secret'),
        ]);
        $passwordHash = $user->password;
        $verifiedAt = $user->email_verified_at;

        $exitCode = Artisan::call('account:provision-privileged', [
            'email' => $user->email,
            '--name' => 'Existing Account',
            '--role' => 'Admin',
        ]);

        $user->refresh();

        $this->assertSame(1, $exitCode);
        $this->assertSame($passwordHash, $user->password);
        $this->assertTrue($verifiedAt->equalTo($user->email_verified_at));
        $this->assertFalse($user->hasRole('Admin'));
        $this->assertStringNotContainsString($user->email, Artisan::output());
        Notification::assertNothingSent();
    }

    public function test_production_provisioning_requires_explicit_confirmation(): void
    {
        $this->seed(RoleSeeder::class);
        $originalEnvironment = $this->app->environment();
        $this->app['env'] = 'production';

        try {
            $this->artisan('account:provision-privileged', [
                'email' => 'production-owner@example.test',
                '--name' => 'Production Owner',
                '--role' => 'Staff',
            ])
                ->expectsConfirmation('Create this privileged account in production?', 'no')
                ->expectsOutput('Provisioning cancelled. No account data was changed.')
                ->assertExitCode(1);
        } finally {
            $this->app['env'] = $originalEnvironment;
        }

        $this->assertDatabaseCount(User::class, 0);
    }
}
