<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class MasterAdminSeeder extends Seeder
{

    public function run(): void
    {
       User::create([
            'username' => 'masteradmin', // Username Master Admin
            'name' => 'Admin Utama',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'), // Ganti dengan password kuat
            'role' => 'master', // Role Master
            'villa_id' => null, // Master Admin tidak punya villa_id
            'access_code' => '112233',
            'permissions' => json_encode([
                'pendapatan' => ['create' => true, 'update' => true, 'delete' => true],
                'pengeluaran' => ['create' => true, 'update' => true, 'delete' => true],
                // tambahkan modul lain jika ada
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
