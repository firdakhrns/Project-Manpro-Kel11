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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

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
            
            return redirect()->route('dse.riwayat_pencatatan', [
                'tanggal' => Carbon::now()->toDateString(),
                'tipe' => 'stok'
            ])->with('success', 'Data stok berhasil disimpan!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data stok: ' . $e->getMessage())->withInput();
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
            return back()->with('error', 'Gagal menyimpan data retur: ' . $e->getMessage())->withInput();
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
    Log::info('=== DSE STORE OUTLET START ===');
    Log::info('Form Data:', $request->all());
    
    // Fix untuk user info
    $user = Auth::user();
    if ($user) {
        Log::info('User Auth:', [
            'id' => $user->id,
            'id_dse' => $user->id_dse,
            'name' => $user->name,
            'email' => $user->email,
            'region' => $user->region
        ]);
    } else {
        Log::info('User Auth: No user authenticated');
    }

    // Validasi
    $request->validate([
        'nama_outlet' => 'required|string|max:255',
        'alamat_outlet' => 'required|string',
        'nama_pemilik' => 'required|string|max:255',
        'no_telepon_pemilik' => 'required|string',
        'tanggal_bergabung' => 'required|date',
        'tampak_depan_outlet_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'foto_etalase_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    Log::info('Validation passed');

    DB::beginTransaction();
    try {
        // Handle file upload untuk foto depan outlet
        $frontPhotoPath = null;
        if ($request->hasFile('tampak_depan_outlet_file')) {
            $frontPhoto = $request->file('tampak_depan_outlet_file');
            $frontPhotoName = 'front_' . time() . '_' . uniqid() . '.' . $frontPhoto->getClientOriginalExtension();
            $frontPhotoPath = $frontPhoto->storeAs('outlet_photos', $frontPhotoName, 'public');
            Log::info('Front photo stored: ' . $frontPhotoPath);
        }

        // Handle file upload untuk foto etalase
        $displayPhotoPath = null;
        if ($request->hasFile('foto_etalase_file')) {
            $displayPhoto = $request->file('foto_etalase_file');
            $displayPhotoName = 'display_' . time() . '_' . uniqid() . '.' . $displayPhoto->getClientOriginalExtension();
            $displayPhotoPath = $displayPhoto->storeAs('outlet_photos', $displayPhotoName, 'public');
            Log::info('Display photo stored: ' . $displayPhotoPath);
        }

        // Data untuk disimpan
        $outletData = [
            'name' => $request->nama_outlet,
            'address' => $request->alamat_outlet,
            'owner_name' => $request->nama_pemilik,
            'phone' => $request->no_telepon_pemilik,
            'emergency_phone' => $request->no_telepon_darurat,
            'join_date' => $request->tanggal_bergabung,
            'front_photo' => $frontPhotoPath,
            'display_photo' => $displayPhotoPath,
            'status' => 'Aktif',
            'region' => Auth::user()->region,
        ];

        Log::info('Outlet data to save:', $outletData);

        $outlet = Outlet::create($outletData);
        
        Log::info('Outlet created with ID: ' . $outlet->id);

        DB::commit();
        
        Log::info('=== DSE STORE OUTLET SUCCESS ===');
        return redirect()->route('dse.dashboard')->with('success', 'Data outlet berhasil disimpan!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error storeOutlet: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Gagal menyimpan data outlet: ' . $e->getMessage())->withInput();
    }
}

    public function riwayatPencatatan(Request $request)
    {
        if (!Auth::guard('web')->check()) { 
            return redirect()->route('login'); 
        }

        $user = Auth::user();
        $dseId = $user->id_dse;
        
        $tanggalFilter = $request->input('tanggal', Carbon::today()->toDateString());
        $tipe = $request->input('tipe', 'stok');

        try {
            $tanggalCari = Carbon::parse($tanggalFilter);
        } catch (\Exception $e) {
            $tanggalCari = Carbon::today();
        }
        
        $dataToDisplay = [];
        $judulRiwayat = '';

        if ($tipe === 'stok') {
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
                            $quantity
                        ];
                    } else {
                        $detailData[$key][2] += $quantity;
                    }
                }
            }

            $dataToDisplay = array_values($detailData);
            $judulRiwayat = 'Riwayat Stok';
            
        } else if ($tipe === 'retur') {
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
                            $quantity
                        ];
                    } else {
                        $detailData[$key][2] += $quantity;
                    }
                }
            }

            $dataToDisplay = array_values($detailData);
            $judulRiwayat = 'Riwayat Retur';
            
        } else {
            $stockLogs = StockLog::where('username_id', $dseId)
                ->whereDate('date', $tanggalCari->toDateString())
                ->with(['items.product'])
                ->get();

            $returnLogs = ReturnLog::where('username_id', $dseId)
                ->whereDate('date', $tanggalCari->toDateString())
                ->with(['items.product'])
                ->get();

            $detailData = [];
            
            foreach ($stockLogs as $log) {
                foreach ($log->items as $item) {
                    if (!$item->product) continue;

                    $productName = $item->product->product_name;
                    $quantity = $item->quantity;

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
                            $quantity,
                            0,
                            $quantity
                        ];
                    } else {
                        $detailData[$key][2] += $quantity;
                        $detailData[$key][4] = $detailData[$key][2] - $detailData[$key][3];
                    }
                }
            }

            foreach ($returnLogs as $log) {
                foreach ($log->items as $item) {
                    if (!$item->product) continue;

                    $productName = $item->product->product_name;
                    $quantity = $item->quantity;

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
                            0,
                            $quantity,
                            -$quantity
                        ];
                    } else {
                        $detailData[$key][3] += $quantity;
                        $detailData[$key][4] = $detailData[$key][2] - $detailData[$key][3];
                    }
                }
            }

            $dataToDisplay = array_values($detailData);
            $judulRiwayat = 'Riwayat Semua';
        }

        return view('dse.riwayat_pencatatan', compact('dataToDisplay', 'dseId', 'tanggalFilter', 'tipe', 'judulRiwayat'));
    }
}