<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Roles
        $roles = ['admin', 'kasir', 'kitchen', 'bar', 'owner'];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // 2. Buat User Admin Utama
        $adminUser = User::create([
            'id' => Str::uuid(),
            'email' => 'admin@kafe.com',
            'password' => Hash::make('password123'),
        ]);

        $adminUser->assignRole('admin');

        // 3. Buat Profile untuk Admin Utama
        Profile::create([
            'user_id' => $adminUser->id,
            'name' => 'Alwan Fauzi Wahyu Ilham',
            'phone' => '081234567890',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
