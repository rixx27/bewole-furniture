<?php

namespace Database\Seeders;

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
        // Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        $this->command->info('Roles seeded successfully.');
    }
}