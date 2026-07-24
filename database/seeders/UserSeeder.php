<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar; // Tambahkan ini

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions (Wajib untuk Spatie)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat Roles
        $roles = ['super-admin', 'admin', 'viewer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 3. Super Admin
        // Best Practice: Ambil dari file config, bukan langsung dari env()
        $superAdmin = User::factory()->create([
            'name' => config('app.super_admin.name', 'Super Admin'),
            'email' => config('app.super_admin.email', 'admin@example.com'),
            'phone_number' => config('app.super_admin.phone', '6285150704897'),
            // Gunakan bcrypt() jika password dari env belum di-hash di UserFactory
            'password' => bcrypt(config('app.super_admin.password', 'password')),
        ]);
        $superAdmin->assignRole('super-admin');

        // 4. Admin Users (Menggunakan fitur factory sequence)
        User::factory(4)->sequence(fn ($sequence) => [
            'name' => 'Admin ' . ($sequence->index + 1),
            'email' => 'admin' . ($sequence->index + 1) . '@gudangku.com',
            'phone_number' => '62812345678' . str_pad($sequence->index + 1, 2, '0', STR_PAD_LEFT),
        ])->create()->each(fn ($user) => $user->assignRole('admin'));

        // 5. Viewer Users
        User::factory(4)->sequence(fn ($sequence) => [
            'name' => 'Viewer ' . ($sequence->index + 1),
            'email' => 'viewer' . ($sequence->index + 1) . '@gudangku.com',
            'phone_number' => '62822345678' . str_pad($sequence->index + 1, 2, '0', STR_PAD_LEFT),
        ])->create()->each(fn ($user) => $user->assignRole('viewer'));
    }
}
