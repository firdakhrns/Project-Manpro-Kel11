<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SharedLoginSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        
        DB::table('shared_logins')->insert([
            [
                'id' => 1, 
                'username' => 'admin_utama',
                'password' => Hash::make('admin123'),
                'region' => 'Banjarmasin',
                'role' => 'Admin',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 2, 
                'username' => 'ADMIN_ERIKA',
                'password' => Hash::make('manprojaya'),
                'region' => 'Banjarmasin',
                'role' => 'Admin',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 3, 
                'username' => 'manajer_penjualan',
                'password' => Hash::make('manajer123'),
                'region' => 'Banjarmasin',
                'role' => 'Manajer',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 4, 
                'username' => 'SUPERADMIN_ERIKA',
                'password' => Hash::make('manprojaya'),
                'region' => 'Banjarmasin',
                'role' => 'Manajer',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}