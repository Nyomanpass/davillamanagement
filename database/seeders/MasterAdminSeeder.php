<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterAdminSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('users')->insert([
            'username' => 'masteradmin', // <-- Username Master Admin
            'name' => 'Admin Utama',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'), // <-- Ganti 'password' dengan password yang kuat
            'role' => 'master', // <-- ROLE HARUS 'master'
            'villa_id' => null, // Master Admin tidak boleh memiliki villa_id
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
