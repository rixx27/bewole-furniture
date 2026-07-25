<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions before seeding
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create or retrieve roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Assign admin role to the existing admin user (is_admin = true)
        $adminUser = User::where('is_admin', true)->first();
        if ($adminUser && !$adminUser->hasRole('admin')) {
            $adminUser->assignRole($adminRole);
            $this->command->info("Admin role assigned to user: {$adminUser->email}");
        }

        $this->command->info("Roles seeded: admin, user");
    }
}
