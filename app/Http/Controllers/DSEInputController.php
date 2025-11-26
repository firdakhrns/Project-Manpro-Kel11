<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Outlet;
use App\Models\User; 
use App\Models\StockLog;
use App\Models\StockLogItem;
use App\Models\ReturnLog;
use App\Models\ReturnLogItem;
use Carbon\Carbon;

class DSEInputController extends Controller
{
    // Mapping Form Input Key ke Product Code
    private $productMapping = [
        // Kartu Perdana
        '3_gb' => 'KP_3GB',
        '6_gb' => 'KP_6GB', 
        '9_gb' => 'KP_9GB',
        '20_gb' => 'KP_20GB',
        
        // Voucher  
        '1_gb_2_h' => 'FI15_1D',
        '15_gb_7_h' => 'FI15_7D',
        '3_gb_3_h' => 'FI3_3D',
        '3_gb_28_h' => 'FI3_3D',
        '5_gb_5_h' => 'FI5_5D',
        '5_gb_2_h' => 'FI5_2D',
        '7_gb_7_h' => 'FI7_7D',
        '5_gb_3_h' => 'FI5_3D',
    ];
    
    private function getProductsMap()
    {
        return Product::whereIn('product_code', array_values($this->productMapping))
                        ->pluck('id', 'product_code');
    }

    public function showInputStok()
    {
        if (!Auth::guard('web')->check()) { 
            return redirect()->route('login'); 
        }
        $outlets = Outlet::all(); 
        return view('dse.input_stok', compact('outlets')); 
    }

    public function storeStok(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'stok' => 'nullable|array',
            'stok.kp.*' => 'nullable|integer|min:0',
            'stok.v.*' => 'nullable|integer|min:0',
        ]);
        
        $allStokInputs = array_merge($request->input('stok.kp', []), $request->input('stok.v', []));
        $productsMap = $this->getProductsMap();

        DB::beginTransaction();
        try {
            $stockLog = StockLog::create([
                'username_id' => Auth::user()->id_dse,
                'outlet_id' => $request->outlet_id,
                'date' => now()->toDateString(),
                'notes' => 'Stok harian',
            ]);

            $logItemsToInsert = [];
            $savedItems = 0;

            foreach ($allStokInputs as $inputKey => $quantity) {
                $productCode = $this->productMapping[$inputKey] ?? null;
                $quantity = (int) $quantity;

                if ($quantity > 0 && $productCode && $productsMap->has($productCode)) {
                    $logItemsToInsert[] = [
                        'stock_log_id' => $stockLog->id,
                        'product_id' => $productsMap[$productCode],
                        'quantity' => $quantity,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $savedItems++;
                }
            }

            if (!empty($logItemsToInsert)) {
                StockLogItem::insert($logItemsToInsert);
            }
            
            DB::commit();
            
            // PERBAIKAN: Tambah parameter 'tipe'
            return redirect()->route('dse.riwayat_pencatatan', [
                'tanggal' => Carbon::now()->toDateString(),
                'tipe' => 'stok'
            ])->with('success', 'Data stok berhasil disimpan!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
    
    public function showInputRetur()
    {
        if (!Auth::guard('web')->check()) { 
            return redirect()->route('login'); 
        }
        $outlets = Outlet::all();
        return view('dse.input_retur', compact('outlets'));
    }

    public function storeRetur(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'retur' => 'nullable|array',
            'retur.kp.*' => 'nullable|integer|min:0',
            'retur.v.*' => 'nullable|integer|min:0',
        ]);
        
        $allReturInputs = array_merge($request->input('retur.kp', []), $request->input('retur.v', []));
        $productsMap = $this->getProductsMap();

        DB::beginTransaction();
        try {
            $returnLog = ReturnLog::create([
                'username_id' => Auth::user()->id_dse,
                'outlet_id' => $request->outlet_id,
                'date' => now()->toDateString(),
                'notes' => 'Retur harian',
            ]);

            $logItemsToInsert = [];
            $savedItems = 0;

            foreach ($allReturInputs as $inputKey => $quantity) {
                $productCode = $this->productMapping[$inputKey] ?? null;
                $quantity = (int) $quantity;

                if ($quantity > 0 && $productCode && $productsMap->has($productCode)) {
                    $logItemsToInsert[] = [
                        'return_log_id' => $returnLog->id,
                        'product_id' => $productsMap[$productCode],
                        'quantity' => $quantity,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $savedItems++;
                }
            }

            if (!empty($logItemsToInsert)) {
                ReturnLogItem::insert($logItemsToInsert);
            }
            
            DB::commit();
            
            return redirect()->route('dse.riwayat_pencatatan', [
                'tanggal' => Carbon::now()->toDateString(),
                'tipe' => 'retur'
            ])->with('success', 'Data retur berhasil disimpan!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan retur: ' . $e->getMessage());
        }
    }
    
    public function showInputOutlet()
    {
        if (!Auth::guard('web')->check()) { 
            return redirect()->route('login'); 
        }
        return view('dse.input_outlet'); 
    }

    public function storeOutlet(Request $request)
    {
        $request->validate([
            'nama_outlet' => 'required|string|max:255',
            'alamat_outlet' => 'required|string',
            'nama_pemilik' => 'required|string|max:255',
            'no_telepon_pemilik' => 'required|string',
            'tanggal_bergabung' => 'required|date',
        ]);

        Outlet::create([
            'name' => $request->nama_outlet,
            'address' => $request->alamat_outlet,
            'owner_name' => $request->nama_pemilik,
            'status' => 'Aktif',
            'region' => Auth::user()->region,
        ]);

        return back()->with('success', 'Data outlet berhasil disimpan!');
    }

    public function riwayatPencatatan(Request $request)
    {
        if (!Auth::guard('web')->check()) { 
            return redirect()->route('login'); 
        }

        $user = Auth::user();
        $dseId = $user->id_dse;
        
        $tanggalFilter = $request->input('tanggal', Carbon::today()->toDateString());
        $tipe = $request->input('tipe', 'stok'); // 'stok', 'retur', atau 'all'

        try {
            $tanggalCari = Carbon::parse($tanggalFilter);
        } catch (\Exception $e) {
            $tanggalCari = Carbon::today();
        }
        
        $dataToDisplay = [];
        $judulRiwayat = '';

        if ($tipe === 'stok') {
            // QUERY STOCK LOG
            $stockLogs = StockLog::where('username_id', $dseId)
                ->whereDate('date', $tanggalCari->toDateString())
                ->with(['items.product'])
                ->get();

            $detailData = [];
            
            foreach ($stockLogs as $log) {
                foreach ($log->items as $item) {
                    if (!$item->product) continue;

                    $productName = $item->product->product_name;
                    $quantity = $item->quantity;

                    // Kategorisasi produk
                    if (str_contains(strtolower($productName), 'kartu perdana')) {
                        $kategori = 'Kartu Perdana';
                        $jenis = trim(str_replace(['Kartu Perdana', 'Freedom Internet'], '', $productName));
                    } else {
                        $kategori = 'Voucher';
                        $jenis = $productName;
                    }
                    
                    $key = $productName;
                    
                    if (!isset($detailData[$key])) {
                        $detailData[$key] = [
                            $kategori, 
                            $jenis,
                            $quantity // Hanya tampilkan quantity untuk stok
                        ];
                    } else {
                        $detailData[$key][2] += $quantity;
                    }
                }
            }

            $dataToDisplay = array_values($detailData);
            $judulRiwayat = 'Riwayat Stok';
            
        } else if ($tipe === 'retur') {
            // QUERY RETURN LOG
            $returnLogs = ReturnLog::where('username_id', $dseId)
                ->whereDate('date', $tanggalCari->toDateString())
                ->with(['items.product'])
                ->get();

            $detailData = [];
            
            foreach ($returnLogs as $log) {
                foreach ($log->items as $item) {
                    if (!$item->product) continue;

                    $productName = $item->product->product_name;
                    $quantity = $item->quantity;

                    // Kategorisasi produk
                    if (str_contains(strtolower($productName), 'kartu perdana')) {
                        $kategori = 'Kartu Perdana';
                        $jenis = trim(str_replace(['Kartu Perdana', 'Freedom Internet'], '', $productName));
                    } else {
                        $kategori = 'Voucher';
                        $jenis = $productName;
                    }
                    
                    $key = $productName;
                    
                    if (!isset($detailData[$key])) {
                        $detailData[$key] = [
                            $kategori, 
                            $jenis,
                            $quantity // Hanya tampilkan quantity untuk retur
                        ];
                    } else {
                        $detailData[$key][2] += $quantity;
                    }
                }
            }

            $dataToDisplay = array_values($detailData);
            $judulRiwayat = 'Riwayat Retur';
            
        } else {
            // TAMPILKAN SEMUA (STOK + RETUR)
            $stockLogs = StockLog::where('username_id', $dseId)
                ->whereDate('date', $tanggalCari->toDateString())
                ->with(['items.product'])
                ->get();

            $returnLogs = ReturnLog::where('username_id', $dseId)
                ->whereDate('date', $tanggalCari->toDateString())
                ->with(['items.product'])
                ->get();

            $detailData = [];
            
            // Process STOCK items
            foreach ($stockLogs as $log) {
                foreach ($log->items as $item) {
                    if (!$item->product) continue;

                    $productName = $item->product->product_name;
                    $quantity = $item->quantity;

                    // Kategorisasi produk
                    if (str_contains(strtolower($productName), 'kartu perdana')) {
                        $kategori = 'Kartu Perdana';
                        $jenis = trim(str_replace(['Kartu Perdana', 'Freedom Internet'], '', $productName));
                    } else {
                        $kategori = 'Voucher';
                        $jenis = $productName;
                    }
                    
                    $key = $productName;
                    
                    if (!isset($detailData[$key])) {
                        $detailData[$key] = [
                            $kategori, 
                            $jenis,
                            $quantity, // Stok
                            0,         // Retur (default 0)
                            $quantity  // Total
                        ];
                    } else {
                        $detailData[$key][2] += $quantity; // Update stok
                        $detailData[$key][4] = $detailData[$key][2] - $detailData[$key][3]; // Update total
                    }
                }
            }

            // Process RETURN items
            foreach ($returnLogs as $log) {
                foreach ($log->items as $item) {
                    if (!$item->product) continue;

                    $productName = $item->product->product_name;
                    $quantity = $item->quantity;

                    // Kategorisasi produk
                    if (str_contains(strtolower($productName), 'kartu perdana')) {
                        $kategori = 'Kartu Perdana';
                        $jenis = trim(str_replace(['Kartu Perdana', 'Freedom Internet'], '', $productName));
                    } else {
                        $kategori = 'Voucher';
                        $jenis = $productName;
                    }
                    
                    $key = $productName;
                    
                    if (!isset($detailData[$key])) {
                        $detailData[$key] = [
                            $kategori, 
                            $jenis,
                            0,         // Stok (default 0)
                            $quantity, // Retur
                            -$quantity // Total (negatif karena retur)
                        ];
                    } else {
                        $detailData[$key][3] += $quantity; // Update retur
                        $detailData[$key][4] = $detailData[$key][2] - $detailData[$key][3]; // Update total
                    }
                }
            }

            $dataToDisplay = array_values($detailData);
            $judulRiwayat = 'Riwayat Semua';
        }

        return view('dse.riwayat_pencatatan', compact('dataToDisplay', 'dseId', 'tanggalFilter', 'tipe', 'judulRiwayat'));
    }
}