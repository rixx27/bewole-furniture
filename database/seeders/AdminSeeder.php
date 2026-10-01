<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        $newEmail = 'adminbewole@gmail.com';
        $oldEmail = 'mozaiq03@gmail.com';

        // Hapus atau perbarui akun admin lama ke akun baru
        $oldAdmin = User::where('email', $oldEmail)->first();
        $newAdmin = User::where('email', $newEmail)->first();

        if ($oldAdmin && !$newAdmin) {
            // Jika akun lama ada dan email baru belum terdaftar, ganti email & password akun lama
            $oldAdmin->update([
                'name' => 'Administrator',
                'email' => $newEmail,
                'password' => Hash::make('bwlbwlbwl'),
                'email_verified_at' => $oldAdmin->email_verified_at ?? now(),
            ]);
            $admin = $oldAdmin;
        } elseif ($oldAdmin && $newAdmin) {
            // Jika kedua akun ada, alihkan referensi dari akun lama ke akun baru lalu hapus akun lama
            DB::table('orders')->where('user_id', $oldAdmin->id)->update(['user_id' => $newAdmin->id]);
            DB::table('order_status_histories')->where('changed_by', $oldAdmin->id)->update(['changed_by' => $newAdmin->id]);
            $oldAdmin->delete();
            $admin = $newAdmin;
            $admin->update([
                'name' => 'Administrator',
                'password' => Hash::make('bwlbwlbwl'),
                'email_verified_at' => $newAdmin->email_verified_at ?? now(),
            ]);
        } else {
            // Buat atau perbarui akun administrator baru
            $admin = User::updateOrCreate(
                ['email' => $newEmail],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make('bwlbwlbwl'),
                    'email_verified_at' => now(),
                ]
            );
        }

        // Assign the admin role if not already assigned
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        $this->command->info('Administrator account seeded successfully.');
        $this->command->info("Email: {$newEmail}");
    }
}
