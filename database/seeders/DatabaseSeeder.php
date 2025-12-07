<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon; 

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('sales_logs')->delete();
        DB::table('return_log_items')->delete();
        DB::table('return_logs')->delete();
        DB::table('stock_log_items')->delete();
        DB::table('stock_logs')->delete();
        DB::table('products')->delete();
        DB::table('outlets')->delete();
        DB::table('users')->delete();
        DB::table('shared_logins')->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. SHARED LOGINS
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
        
        DB::table('users')->insert([
            [
                'id' => 1, 
                'name' => 'DSE Lapangan 1',
                'id_dse' => 'CSOB-BJM1',
                'password' => Hash::make('dsel1'),
                'role' => 'DSE',
                'region' => 'Banjarmasin Utara',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 2, 
                'name' => 'DSE Lapangan 2',
                'id_dse' => 'CSOB-BJM2',
                'password' => Hash::make('dsel2'),
                'role' => 'DSE',
                'region' => 'Banjarmasin timur',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 3, 
                'name' => 'DSE Lapangan 3',
                'id_dse' => 'CSOB-BJM3',
                'password' => Hash::make('dsel3'),
                'role' => 'DSE',
                'region' => 'Banjarmasin Barat',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 4, 
                'name' => 'DSE Lapangan 4',
                'id_dse' => 'CSOB-BJM4',
                'password' => Hash::make('dsel4'),
                'role' => 'DSE',
                'region' => 'Banjarmasin tengah',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 5, 
                'name' => 'DSE Lapangan 5',
                'id_dse' => 'CSOB-BJM5',
                'password' => Hash::make('dsel5'),
                'role' => 'DSE',
                'region' => 'Banjarmasin Selatan',
                'created_at' => $now,
                'updated_at' => $now
            ],
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

        DB::table('outlets')->insert([
            [
                'id' => 1, 
                'name' => 'Ummi Cell',
                'address' => 'Jl. A. Yani Km. 35',
                'owner_name' => 'Rudi',
                'phone' => '081234567890',
                'emergency_phone' => '081298765432',
                'join_date' => '2024-01-15',
                'front_photo' => null,
                'display_photo' => null,
                'status' => 'Aktif',
                'region' => 'Banjarmasin Utara',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 2, 
                'name' => 'Zahra Cell',
                'address' => 'Jl. Antasari',
                'owner_name' => 'Siti',
                'phone' => '081234567891',
                'emergency_phone' => '081298765433',
                'join_date' => '2024-01-20',
                'front_photo' => null,
                'display_photo' => null,
                'status' => 'Aktif',
                'region' => 'Banjarmasin Tengah',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 3, 
                'name' => 'Detik Ponsel',
                'address' => 'Jl. A. Yani Km.2, Banjarmasin',
                'owner_name' => 'Andi Wijaya',
                'phone' => '081234567892',
                'emergency_phone' => '081298765434',
                'join_date' => '2024-02-01',
                'front_photo' => null,
                'display_photo' => null,
                'status' => 'Aktif',
                'region' => 'Banjarmasin Timur',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 4, 
                'name' => 'Oscar Cell',
                'address' => 'Jl. Ahmad Yani Km. 5, Banjarmasin',
                'owner_name' => 'Nina Marlina',
                'phone' => '081234567893',
                'emergency_phone' => '081298765435',
                'join_date' => '2024-02-10',
                'front_photo' => null,
                'display_photo' => null,
                'status' => 'Aktif',
                'region' => 'Banjarmasin Selatan',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 5, 
                'name' => 'AA Ponsel',
                'address' => 'Jl. Gatot Subroto No.10, Banjarmasin',
                'owner_name' => 'Hendra Putra',
                'phone' => '081234567894',
                'emergency_phone' => '081298765436',
                'join_date' => '2024-02-15',
                'front_photo' => null,
                'display_photo' => null,
                'status' => 'Aktif',
                'region' => 'Banjarmasin Timur',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // 4. PRODUCTS
        DB::table('products')->insert([
            [
                'id' => 1, 
                'product_name' => 'Freedom Internet 1.5GB 1 Hari',
                'product_code' => 'FI15_1D',
                'price' => 4300,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 2, 
                'product_name' => 'Freedom Internet 3.5GB 5 Hari',
                'product_code' => 'FI35_5D',
                'price' => 13500,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 3, 
                'product_name' => 'Freedom Internet 5GB (3 Hari)', 
                'product_code' => 'FI5_3D', 
                'price' => 12500, 
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 4, 
                'product_name' => 'Freedom Internet 5GB (2 Hari)', 
                'product_code' => 'FI5_2D', 
                'price' => 8000, 
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 5, 
                'product_name' => 'Freedom Internet 5GB (5 Hari)', 
                'product_code' => 'FI5_5D', 
                'price' => 17000, 
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 6, 
                'product_name' => 'Freedom Internet 7GB (7 Hari)', 
                'product_code' => 'FI7_7D', 
                'price' => 23000, 
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 7, 
                'product_name' => 'Freedom Internet 3GB (3 Hari)', 
                'product_code' => 'FI3_3D', 
                'price' => 11300, 
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 8, 
                'product_name' => 'Freedom Internet 3GB (1 Hari)', 
                'product_code' => 'FI3_1D', 
                'price' => 6600, 
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 9, 
                'product_name' => 'Freedom Internet 15GB (7 Hari)', 
                'product_code' => 'FI15_7D', 
                'price' => 27500, 
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 10, 
                'product_name' => 'Kartu Perdana 3 GB',
                'product_code' => 'KP_3GB',
                'price' => 10000,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 11, 
                'product_name' => 'Kartu Perdana 6 GB',
                'product_code' => 'KP_6GB',
                'price' => 15000,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 12, 
                'product_name' => 'Kartu Perdana 9 GB',
                'product_code' => 'KP_9GB',
                'price' => 20000,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 13, 
                'product_name' => 'Kartu Perdana 20 GB',
                'product_code' => 'KP_20GB',
                'price' => 30000,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // 5. STOCK LOGS - PERBAIKAN: username_id harus id_dse (string), bukan ID numerik
        DB::table('stock_logs')->insert([
            [
                'id' => 1, 
                'username_id' => 'CSOB-BJM1', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 1,
                'date' => $now->copy()->subDays(3),
                'notes' => 'Stok awal',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 2, 
                'username_id' => 'CSOB-BJM2', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 2,
                'date' => $now->copy()->subDays(4),
                'notes' => 'Penambahan stok',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 3, 
                'username_id' => 'CSOB-BJM3', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 3,
                'date' => $now->copy()->subDays(3),
                'notes' => 'Re-stock',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 4, 
                'username_id' => 'CSOB-BJM4', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 4,
                'date' => $now->copy()->subDays(2),
                'notes' => 'Stok tambahan',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 5, 
                'username_id' => 'CSOB-BJM5', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 5,
                'date' => $now->copy()->subDay(),
                'notes' => 'Update stok',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // 6. STOCK LOG ITEMS
        DB::table('stock_log_items')->insert([
            [
                'stock_log_id' => 1,
                'product_id' => 1,
                'quantity' => 10,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'stock_log_id' => 2,
                'product_id' => 2,
                'quantity' => 15,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'stock_log_id' => 3,
                'product_id' => 3,
                'quantity' => 30,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'stock_log_id' => 4,
                'product_id' => 4,
                'quantity' => 25,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'stock_log_id' => 5,
                'product_id' => 5,
                'quantity' => 10,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // 7. RETURN LOGS - PERBAIKAN: username_id harus id_dse (string), bukan ID numerik
        DB::table('return_logs')->insert([
            [
                'id' => 1, 
                'username_id' => 'CSOB-BJM1', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 1,
                'date' => $now->copy()->subDays(1),
                'notes' => 'Retur kartu rusak',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 2, 
                'username_id' => 'CSOB-BJM2', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 2,
                'date' => $now->copy()->subDays(1),
                'notes' => 'Retur saldo error',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 3, 
                'username_id' => 'CSOB-BJM3', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 3,
                'date' => $now->copy()->subDays(2),
                'notes' => 'Retur pelanggan',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 4, 
                'username_id' => 'CSOB-BJM4', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 4,
                'date' => $now->copy()->subDays(3),
                'notes' => 'Retur kartu hilang',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 5, 
                'username_id' => 'CSOB-BJM5', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 5,
                'date' => $now->copy()->subDays(2),
                'notes' => 'Retur saldo tidak valid',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // 8. RETURN LOG ITEMS
        DB::table('return_log_items')->insert([
            [
                'return_log_id' => 1,
                'product_id' => 1,
                'quantity' => 2,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'return_log_id' => 2,
                'product_id' => 2,
                'quantity' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'return_log_id' => 3,
                'product_id' => 3,
                'quantity' => 3,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'return_log_id' => 4,
                'product_id' => 4,
                'quantity' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'return_log_id' => 5,
                'product_id' => 5,
                'quantity' => 2,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // 9. SALES LOGS - PERBAIKAN: username_id harus id_dse (string), bukan ID numerik
        DB::table('sales_logs')->insert([
            [
                'username_id' => 'CSOB-BJM1', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 1,
                'date' => $now,
                'total_sales' => 120000,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'username_id' => 'CSOB-BJM2', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 2,
                'date' => $now,
                'total_sales' => 950000,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'username_id' => 'CSOB-BJM3', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 3,
                'date' => $now,
                'total_sales' => 875000,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'username_id' => 'CSOB-BJM4', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 4,
                'date' => $now,
                'total_sales' => 1100000,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'username_id' => 'CSOB-BJM5', // PAKAI id_dse, bukan ID numerik
                'outlet_id' => 5,
                'date' => $now,
                'total_sales' => 980000,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        DB::table('feedbacks')->insert([
            [
                'cse_id' => 'manajer_penjualan',
                'dse_target' => 'CSOB-BJM1',
                'type' => 'saran',
                'message' => 'Sebaiknya meningkatkan frekuensi kunjungan ke outlet Ummi Cell untuk memastikan stok selalu tersedia.',
                'is_urgent' => false,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5)
            ],
            [
                'cse_id' => 'manajer_penjualan',
                'dse_target' => 'CSOB-BJM2',
                'type' => 'kritik',
                'message' => 'Laporan stok minggu ini belum lengkap, beberapa outlet penting belum dilaporkan. Harap segera dilengkapi.',
                'is_urgent' => true,
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3)
            ],
            [
                'cse_id' => 'manajer_penjualan',
                'dse_target' => 'CSOB-BJM3',
                'type' => 'saran',
                'message' => 'Coba fokuskan penjualan produk Freedom Internet 15GB di outlet Detik Ponsel, karena respon pelanggan cukup baik.',
                'is_urgent' => false,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2)
            ],
            [
                'cse_id' => 'manajer_penjualan',
                'dse_target' => 'CSOB-BJM4',
                'type' => 'kritik',
                'message' => 'Tingkat retur di outlet Oscar Cell meningkat 15% dari bulan lalu. Perlu evaluasi proses penjualan dan pengecekan produk.',
                'is_urgent' => true,
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1)
            ],
            [
                'cse_id' => 'manajer_penjualan',
                'dse_target' => 'CSOB-BJM5',
                'type' => 'saran',
                'message' => 'Bagus sekali performa penjualan di AA Ponsel. Pertahankan dan coba terapkan strategi yang sama di outlet lainnya.',
                'is_urgent' => false,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    

        $this->command->info('Database seeded successfully!');
    }
}