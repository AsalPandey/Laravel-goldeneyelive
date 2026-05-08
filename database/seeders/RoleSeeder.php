<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);

        // Create Default Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@goldeneye.edu.np'],
            [
                'name' => 'GoldenEye Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(), // Bypass verified middleware
            ]
        );

        $admin->assignRole($adminRole);

        // Optionally create a staff member for demo
        $staff = User::firstOrCreate(
            ['email' => 'staff@goldeneye.edu.np'],
            [
                'name' => 'GoldenEye Staff',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $staff->assignRole($staffRole);
    }
}
