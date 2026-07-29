<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds to create or update the default administrator account.
     *
     * This seeder is safe to run multiple times — it will:
     * - Create the admin user if not exists, or update it if already exists.
     * - Ensure the "admin" role exists (without duplicates).
     * - Assign the "admin" role to the user.
     */
    public function run(): void
    {
        // Reset cached permissions to ensure fresh state
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure the "admin" role exists (default guard: web)
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // Create or update the administrator account
        $admin = User::updateOrCreate(
            ['email' => 'mozaiq03@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('mozaiq@03'),
                'email_verified_at' => now(),
            ]
        );

        // Assign the admin role if not already assigned
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        $this->command->info('Administrator account seeded successfully.');
        $this->command->info('Email: mozaiq03@gmail.com');
    }
}
