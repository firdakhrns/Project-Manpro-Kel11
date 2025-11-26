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
            ['name'=>'DSE Lapangan 1','id_dse'=>'CSOB-BJM1','password'=>Hash::make('dsel1'),'role'=>'DSE','created_at'=>$now,'updated_at'=>$now,'region'=>'Banjarmasin'],
            ['name'=>'DSE Lapangan 2','id_dse'=>'CSOB-BJM2','password'=>Hash::make('dsel2'),'role'=>'DSE','created_at'=>$now,'updated_at'=>$now,'region'=>'Banjarmasin'],
            ['name'=>'DSE Lapangan 3','id_dse'=>'CSOB-BJM3','password'=>Hash::make('dsel3'),'role'=>'DSE','created_at'=>$now,'updated_at'=>$now,'region'=>'Banjarmasin'],
            ['name'=>'DSE Lapangan 4','id_dse'=>'CSOB-BJM4','password'=>Hash::make('dsel4'),'role'=>'DSE','created_at'=>$now,'updated_at'=>$now,'region'=>'Banjarmasin'],
            ['name'=>'DSE Lapangan 5','id_dse'=>'CSOB-BJM5','password'=>Hash::make('dsel5'),'role'=>'DSE','created_at'=>$now,'updated_at'=>$now,'region'=>'Banjarmasin'],
        ]);
    }
}