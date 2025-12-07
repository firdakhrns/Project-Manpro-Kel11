<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        
        DB::table('users')->insert([
            [
                'id' => 6, 
                'name' => 'Erika',
                'id_dse' => 'DSE_ERIKA',
                'password' => Hash::make('manprojaya'),
                'role' => 'DSE',
                'region' => 'Banjarmasin Utara',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}