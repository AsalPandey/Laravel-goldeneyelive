<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Throwable;

#[Signature('account:provision-privileged
    {email : Email address owned by the authorized account holder}
    {--name= : Full name of the account holder}
    {--role= : Privileged role to assign (Admin or Staff)}')]
#[Description('Create a privileged account with a random unusable password and send a one-time password setup link.')]
class ProvisionPrivilegedUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $input = [
            'email' => Str::lower(trim((string) $this->argument('email'))),
            'name' => trim((string) $this->option('name')),
            'role' => trim((string) $this->option('role')),
        ];

        $validator = Validator::make($input, [
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['Admin', 'Staff'])],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::INVALID;
        }

        if (app()->isProduction() && ! $this->confirm('Create this privileged account in production?')) {
            $this->warn('Provisioning cancelled. No account data was changed.');

            return self::FAILURE;
        }

        if (User::where('email', $input['email'])->exists()) {
            $this->warn('An account already exists. No account data was changed; use the password-reset workflow.');

            return self::FAILURE;
        }

        $role = Role::where('name', $input['role'])
            ->where('guard_name', 'web')
            ->first();

        if (! $role) {
            $this->error('The requested system role does not exist. Run the system-reference seeder first.');

            return self::FAILURE;
        }

        $user = DB::transaction(function () use ($input, $role): User {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Str::password(64),
            ]);

            $user->assignRole($role);

            return $user;
        });

        try {
            $status = Password::broker()->sendResetLink(['email' => $user->email]);
        } catch (Throwable) {
            $status = null;
        }

        if ($status !== Password::RESET_LINK_SENT) {
            Password::broker()->deleteToken($user);
            $user->delete();
            $this->error('The one-time setup link could not be sent, so the account was not retained.');

            return self::FAILURE;
        }

        $this->info('The privileged account was created and a one-time password setup link was sent.');

        return self::SUCCESS;
    }
}
