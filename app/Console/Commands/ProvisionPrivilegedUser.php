<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Rules\OrganizationEmail;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

#[Signature('account:provision-privileged
    {email : Email address owned by the authorized account holder}
    {--name= : Full name of the account holder}
    {--role= : Privileged role to assign (Admin or Staff)}')]
#[Description('Create a privileged account with a random unusable password for email OTP password setup.')]
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
            'email' => ['required', 'email', new OrganizationEmail],
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

        if ((new User(['email' => $input['email']]))->isPermanentAdmin() && $input['role'] !== 'Admin') {
            $this->error('Permanent Admin emails cannot be provisioned as Staff.');

            return self::INVALID;
        }

        if (User::whereRaw('LOWER(email) = ?', [$input['email']])->exists()) {
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

        DB::transaction(function () use ($input, $role): User {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Str::password(64),
            ]);

            $user->assignRole($role);

            return $user;
        });

        $this->info('The privileged account was created. Use Forgot Password to set the password with an email OTP.');

        return self::SUCCESS;
    }
}
