<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== USERS =====
        DB::table('users')->insert([
            ['name'=>'Admin Utama','email'=>'admin@example.com','password'=>Hash::make('password'),'role'=>'Admin','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Manajer Penjualan','email'=>'manajer@example.com','password'=>Hash::make('password'),'role'=>'Manajer','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'DSE Lapangan 1','email'=>'dse1@example.com','password'=>Hash::make('password'),'role'=>'DSE','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'DSE Lapangan 2','email'=>'dse2@example.com','password'=>Hash::make('password'),'role'=>'DSE','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'DSE Lapangan 3','email'=>'dse3@example.com','password'=>Hash::make('password'),'role'=>'DSE','created_at'=>now(),'updated_at'=>now()],
        ]);

        // Ambil user ID yang pasti
        $users = DB::table('users')->pluck('id', 'email');

        // ===== OUTLETS =====
        DB::table('outlets')->insert([
            ['name'=>'Outlet Banjarbaru','address'=>'Jl. A. Yani Km. 35, Banjarbaru','owner_name'=>'Rudi Santoso','status'=>'Aktif','latitude'=>'-3.4420','longitude'=>'114.8320','created_by'=>$users['admin@example.com'],'updated_at'=>now()],
            ['name'=>'Outlet Banjarmasin Tengah','address'=>'Jl. Pangeran Antasari No.45, Banjarmasin','owner_name'=>'Siti Rahma','status'=>'Aktif','latitude'=>'-3.3276','longitude'=>'114.5888','created_by'=>$users['manajer@example.com'],'updated_at'=>now()],
            ['name'=>'Outlet Banjarmasin Selatan','address'=>'Jl. A. Yani Km.2, Banjarmasin','owner_name'=>'Andi Wijaya','status'=>'Aktif','latitude'=>'-3.3350','longitude'=>'114.5885','created_by'=>$users['dse1@example.com'],'updated_at'=>now()],
            ['name'=>'Outlet Banjarmasin Utara','address'=>'Jl. Ahmad Yani Km. 5, Banjarmasin','owner_name'=>'Nina Marlina','status'=>'Aktif','latitude'=>'-3.3020','longitude'=>'114.5920','created_by'=>$users['dse2@example.com'],'updated_at'=>now()],
            ['name'=>'Outlet Banjarmasin Timur','address'=>'Jl. Gatot Subroto No.10, Banjarmasin','owner_name'=>'Hendra Putra','status'=>'Aktif','latitude'=>'-3.3220','longitude'=>'114.6100','created_by'=>$users['dse3@example.com'],'updated_at'=>now()],
        ]);

        $outlets = DB::table('outlets')->pluck('id', 'name');

        // ===== PRODUCTS =====
        DB::table('products')->insert([
            ['product_name'=>'IM3 Freedom 5GB','product_code'=>'IM35','price'=>35000,'created_at'=>now(),'updated_at'=>now()],
            ['product_name'=>'IM3 Freedom 10GB','product_code'=>'IM310','price'=>50000,'created_at'=>now(),'updated_at'=>now()],
            ['product_name'=>'IM3 Freedom 15GB','product_code'=>'IM315','price'=>75000,'created_at'=>now(),'updated_at'=>now()],
            ['product_name'=>'IM3 Unlimited 5GB','product_code'=>'IMU5','price'=>45000,'created_at'=>now(),'updated_at'=>now()],
            ['product_name'=>'IM3 Unlimited 10GB','product_code'=>'IMU10','price'=>80000,'created_at'=>now(),'updated_at'=>now()],
        ]);

        $products = DB::table('products')->pluck('id', 'product_code');

        // ===== STOCK LOGS =====
        DB::table('stock_logs')->insert([
            ['user_id'=>$users['dse1@example.com'],'outlet_id'=>$outlets['Outlet Banjarbaru'],'date'=>now()->subDays(5),'notes'=>'Stok awal minggu','created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse2@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Tengah'],'date'=>now()->subDays(4),'notes'=>'Penambahan stok','created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse3@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Selatan'],'date'=>now()->subDays(3),'notes'=>'Re-stock','created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse1@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Utara'],'date'=>now()->subDays(2),'notes'=>'Stok tambahan','created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse2@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Timur'],'date'=>now()->subDay(),'notes'=>'Update stok','created_at'=>now(),'updated_at'=>now()],
        ]);

        $stockLogs = DB::table('stock_logs')->pluck('id');

        // ===== STOCK LOG ITEMS =====
        DB::table('stock_log_items')->insert([
            ['stock_log_id'=>$stockLogs[0],'product_id'=>$products['IM35'],'quantity'=>20,'created_at'=>now(),'updated_at'=>now()],
            ['stock_log_id'=>$stockLogs[1],'product_id'=>$products['IM310'],'quantity'=>15,'created_at'=>now(),'updated_at'=>now()],
            ['stock_log_id'=>$stockLogs[2],'product_id'=>$products['IM315'],'quantity'=>30,'created_at'=>now(),'updated_at'=>now()],
            ['stock_log_id'=>$stockLogs[3],'product_id'=>$products['IMU5'],'quantity'=>25,'created_at'=>now(),'updated_at'=>now()],
            ['stock_log_id'=>$stockLogs[4],'product_id'=>$products['IMU10'],'quantity'=>10,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // ===== RETURN LOGS =====
        DB::table('return_logs')->insert([
            ['user_id'=>$users['dse1@example.com'],'outlet_id'=>$outlets['Outlet Banjarbaru'],'date'=>now()->subDays(1),'notes'=>'Retur kartu rusak','created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse2@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Tengah'],'date'=>now()->subDays(1),'notes'=>'Retur saldo error','created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse3@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Selatan'],'date'=>now()->subDays(2),'notes'=>'Retur pelanggan','created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse1@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Utara'],'date'=>now()->subDays(3),'notes'=>'Retur kartu hilang','created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse2@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Timur'],'date'=>now()->subDays(2),'notes'=>'Retur saldo tidak valid','created_at'=>now(),'updated_at'=>now()],
        ]);

        $returnLogs = DB::table('return_logs')->pluck('id');

        // ===== RETURN LOG ITEMS =====
        DB::table('return_log_items')->insert([
            ['return_log_id'=>$returnLogs[0],'product_id'=>$products['IM35'],'quantity'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['return_log_id'=>$returnLogs[1],'product_id'=>$products['IM310'],'quantity'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['return_log_id'=>$returnLogs[2],'product_id'=>$products['IM315'],'quantity'=>3,'created_at'=>now(),'updated_at'=>now()],
            ['return_log_id'=>$returnLogs[3],'product_id'=>$products['IMU5'],'quantity'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['return_log_id'=>$returnLogs[4],'product_id'=>$products['IMU10'],'quantity'=>2,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // ===== SALES LOGS =====
        DB::table('sales_logs')->insert([
            ['user_id'=>$users['dse1@example.com'],'outlet_id'=>$outlets['Outlet Banjarbaru'],'date'=>now(),'total_sales'=>1200000,'created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse2@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Tengah'],'date'=>now(),'total_sales'=>950000,'created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse3@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Selatan'],'date'=>now(),'total_sales'=>875000,'created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse1@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Utara'],'date'=>now(),'total_sales'=>1100000,'created_at'=>now(),'updated_at'=>now()],
            ['user_id'=>$users['dse2@example.com'],'outlet_id'=>$outlets['Outlet Banjarmasin Timur'],'date'=>now(),'total_sales'=>980000,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
