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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;


class DSEInputController extends Controller
{
    private $productMapping = [
    '3_gb' => 'KP_3GB',
    '6_gb' => 'KP_6GB', 
    '9_gb' => 'KP_9GB',
    '20_gb' => 'KP_20GB',

    '1.5gb1h' => 'FI15_1D',  
    '5gb5h' => 'FI5_5D',    
    '3.5gb5h' => 'FI35_5D',  
    '7gb7h' => 'FI7_7D',     
    '5gb3h' => 'FI5_3D',     
    '3gb3h' => 'FI3_3D',     
    '5gb2h' => 'FI5_2D',    
    '3gb1h' => 'FI3_1D',     
    '15gb7h' => 'FI15_7D',  
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
        $user = Auth::user();
        $outlets = Outlet::where('region', $user->region)->where('status', 'Aktif')->get(); 
        return view('dse.input_stok', compact('outlets')); 
    }

    public function storeStok(Request $request)
{
    Log::info('=== STORE STOK START ===');
    Log::info('Full Request:', $request->all());
    
    $messages = [
        'stok.kp.*.max' => 'Jumlah stok Kartu Perdana tidak boleh melebihi :max.',
        'stok.v.*.max' => 'Jumlah stok Voucher tidak boleh melebihi :max.',
        'stok.kp.*.min' => 'Jumlah stok harus minimal :min.',
        'stok.v.*.min' => 'Jumlah stok harus minimal :min.',
    ];

    $user = Auth::user();
    
    $request->validate([
        'outlet_id' => [
            'required',
            'exists:outlets,id',
            function ($attribute, $value, $fail) use ($user) {
                $outlet = Outlet::find($value);
                if (!$outlet || $outlet->region !== $user->region) {
                    $fail('Outlet tidak valid atau tidak berada di region Anda.');
                }
            }
        ],
        'stok' => 'nullable|array',
        'stok.kp.*' => 'nullable|integer|min:0|max:500',
        'stok.v.*' => 'nullable|integer|min:0|max:500',
    ], $messages);

    Log::info('KP Inputs:', $request->input('stok.kp', []));
    Log::info('V Inputs:', $request->input('stok.v', []));
    
    $kpInputs = $request->input('stok.kp', []);
    $vInputs = $request->input('stok.v', []);
    
    Log::info('KP Inputs Array:', $kpInputs);
    Log::info('V Inputs Array:', $vInputs);
    
    $allStokInputs = [];
    
    foreach ($kpInputs as $key => $value) {
        $allStokInputs[$key] = $value;
    }
    
    foreach ($vInputs as $key => $value) {
        $allStokInputs[$key] = $value;
    }
    
    Log::info('All Stok Inputs Combined:', $allStokInputs);
    
    $productsMap = $this->getProductsMap();
    Log::info('Products Map:', $productsMap->toArray());

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
    
        Log::info("Processing {$inputKey}: quantity={$quantity}, productCode={$productCode}");

        if ($quantity > 0 && $productCode && $productsMap->has($productCode)) {
            $logItemsToInsert[] = [
                'stock_log_id' => $stockLog->id, 
                'product_id' => $productsMap[$productCode],
                'quantity' => $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $savedItems++;
            Log::info("✓ Added: {$productCode} (ID: {$productsMap[$productCode]}) = {$quantity}");
        } elseif ($quantity > 0) {
            Log::warning("✗ Skipped: {$inputKey} -> productCode not found in mapping");
        }
    }

Log::info("Total stock items to insert: " . count($logItemsToInsert));

if (!empty($logItemsToInsert)) {
    StockLogItem::insert($logItemsToInsert); 
    Log::info("Stock items inserted successfully");
} else {
    Log::warning("No stock items to insert - all quantities might be 0");
}
        
        DB::commit();
        
        Log::info('=== STORE STOK SUCCESS ===');
        
        return redirect()->route('dse.riwayat_pencatatan', [
            'tanggal' => Carbon::now()->toDateString(),
            'tipe' => 'stok'
        ])->with('success', 'Data stok berhasil disimpan!');
        
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error saving stok: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}
    
    public function showInputRetur()
    {
        if (!Auth::guard('web')->check()) { 
            return redirect()->route('login'); 
        }
        $user = Auth::user();
        $outlets = Outlet::where('region', $user->region)->where('status', 'Aktif')->get();
        return view('dse.input_retur', compact('outlets'));
    }

    public function storeRetur(Request $request)
{
    Log::info('=== STORE RETUR START ===');
    Log::info('Full Request:', $request->all());
    
    $user = Auth::user();
    
    $messages = [
        'retur.kp.*.max' => 'Jumlah retur Kartu Perdana tidak boleh melebihi :max.',
        'retur.v.*.max' => 'Jumlah retur Voucher tidak boleh melebihi :max.',
        'retur.kp.*.min' => 'Jumlah retur harus minimal :min.',
        'retur.v.*.min' => 'Jumlah retur harus minimal :min.',
    ];
    
    $customErrors = [];
    
    $validator = Validator::make($request->all(), [
        'outlet_id' => [
            'required',
            'exists:outlets,id',
            function ($attribute, $value, $fail) use ($user) {
                $outlet = Outlet::find($value);
                if (!$outlet || $outlet->region !== $user->region) {
                    $fail('Outlet tidak valid atau tidak berada di region Anda.');
                }
            }
        ],
        'retur' => 'nullable|array',
        'retur.kp.*' => 'nullable|integer|min:0|max:500', 
        'retur.v.*' => 'nullable|integer|min:0|max:500',
    ], $messages);

    if ($validator->fails()) {
        Log::error('Validation failed:', $validator->errors()->toArray());
        return back()->withErrors($validator)->withInput();
    }
    
    $outletId = $request->outlet_id;
    $today = Carbon::today()->toDateString();
    
    $todayStockLogs = StockLog::where('outlet_id', $outletId)
        ->whereDate('date', $today)
        ->with('items.product')
        ->get();
    
    $todayReturnLogs = ReturnLog::where('outlet_id', $outletId)
        ->whereDate('date', $today)
        ->with('items.product')
        ->get();
    
    $availableStock = [];
    
    foreach ($todayStockLogs as $stockLog) {
        foreach ($stockLog->items as $item) {
            if ($item->product) {
                $productId = $item->product_id;
                $productCode = $item->product->product_code;
                
                if (!isset($availableStock[$productCode])) {
                    $availableStock[$productCode] = 0;
                }
                $availableStock[$productCode] += $item->quantity;
            }
        }
    }
    
    foreach ($todayReturnLogs as $returnLog) {
        foreach ($returnLog->items as $item) {
            if ($item->product) {
                $productCode = $item->product->product_code;
                
                if (isset($availableStock[$productCode])) {
                    $availableStock[$productCode] -= $item->quantity;
                } else {
                    $availableStock[$productCode] = -$item->quantity;
                }
            }
        }
    }
    
    Log::info('Available stock before new retur:', $availableStock);
    
    $kpInputs = $request->input('retur.kp', []);
    $vInputs = $request->input('retur.v', []);
    
    Log::info('KP Retur Inputs:', $kpInputs);
    Log::info('V Retur Inputs:', $vInputs);
    
    $allReturInputs = [];
    
    foreach ($kpInputs as $key => $value) {
        $allReturInputs[$key] = $value;
    }
    
    foreach ($vInputs as $key => $value) {
        $allReturInputs[$key] = $value;
    }
    
    Log::info('All Retur Inputs Combined:', $allReturInputs);
    
    $productsMap = $this->getProductsMap();
    Log::info('Products Map:', $productsMap->toArray());
    
    foreach ($allReturInputs as $inputKey => $quantity) {
        $quantity = (int) $quantity;
        
        if ($quantity > 0) {
            $productCode = $this->productMapping[$inputKey] ?? null;
            
            if ($productCode && $productsMap->has($productCode)) {
                $available = $availableStock[$productCode] ?? 0;
                
                if ($quantity > $available) {
                    $product = Product::where('product_code', $productCode)->first();
                    $productName = $product ? $product->product_name : $productCode;
                    
                    $customErrors[] = "Retur {$productName} ({$quantity}) melebihi stok tersedia ({$available})";
                    
                    Log::warning("Retur validation failed: {$productCode} - Requested: {$quantity}, Available: {$available}");
                }
            }
        }
    }
    
    if (!empty($customErrors)) {
        Log::error('Business validation failed:', $customErrors);
        return back()->with('custom_errors', $customErrors)->withInput();
    }
    
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
            
            Log::info("Processing {$inputKey}: quantity={$quantity}, productCode={$productCode}");

            if ($quantity > 0 && $productCode && $productsMap->has($productCode)) {
                $logItemsToInsert[] = [
                    'return_log_id' => $returnLog->id,
                    'product_id' => $productsMap[$productCode],
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $savedItems++;
                Log::info("✓ Added: {$productCode} (ID: {$productsMap[$productCode]}) = {$quantity}");
            } elseif ($quantity > 0) {
                Log::warning("✗ Skipped: {$inputKey} -> productCode not found in mapping");
            }
        }

        Log::info("Total retur items to insert: " . count($logItemsToInsert));

        if (!empty($logItemsToInsert)) {
            ReturnLogItem::insert($logItemsToInsert);
            Log::info("Retur items inserted successfully");
        } else {
            Log::warning("No retur items to insert - all quantities might be 0");
        }
        
        DB::commit();
        
        Log::info('=== STORE RETUR SUCCESS ===');
        
        return redirect()->route('dse.riwayat_pencatatan', [
            'tanggal' => Carbon::now()->toDateString(),
            'tipe' => 'retur'
        ])->with('success', 'Data retur berhasil disimpan!');
        
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error saving retur: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
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
    Log::info('=== DSE STORE OUTLET START ===');
    Log::info('Form Data:', $request->all());
    
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
        'nama_outlet' => ['required', 'string', 'max:255', 'unique:outlets,name', 'regex:/^[\pL\pN\s\-\.]+$/u'],
        
        'alamat_outlet' => [
            'required',
            'string',
            'max:500',
            'regex:/^[\pL\pN\s\-\/,\.]+$/u',
        ], 
        
        'nama_pemilik' => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'], 
        
        'no_telepon_pemilik' => [
            'required', 
            'string', 
            'max:12', 
            'regex:/^[0-9]+$/', 
        ],
        'no_telepon_darurat' =>  [
            'nullable', 
            'string', 
            'max:12', 
            'regex:/^[0-9]+$/', 
        ], 
        
        'tanggal_bergabung' => 'required|date',

        'tampak_depan_outlet_file' => [
            'required', 
            'image',
            'mimes:jpeg,png,jpg,gif',
            'max:5120',
        ], 
        'foto_etalase_file' => [
            'required',
            'image',
            'mimes:jpeg,png,jpg,gif',
            'max:5120',
        ],
    ]);

    Log::info('Validation passed');

    DB::beginTransaction();
    try {
        $frontPhotoPath = null;
        if ($request->hasFile('tampak_depan_outlet_file')) {
            $frontPhoto = $request->file('tampak_depan_outlet_file');
            $frontPhotoName = 'front_' . time() . '_' . uniqid() . '.' . $frontPhoto->getClientOriginalExtension();
            $frontPhotoPath = $frontPhoto->storeAs('outlet_photos', $frontPhotoName, 'public');
            Log::info('Front photo stored: ' . $frontPhotoPath);
        }

        $displayPhotoPath = null;
        if ($request->hasFile('foto_etalase_file')) {
            $displayPhoto = $request->file('foto_etalase_file');
            $displayPhotoName = 'display_' . time() . '_' . uniqid() . '.' . $displayPhoto->getClientOriginalExtension();
            $displayPhotoPath = $displayPhoto->storeAs('outlet_photos', $displayPhotoName, 'public');
            Log::info('Display photo stored: ' . $displayPhotoPath);
        }

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

    } catch (Exception $e) {
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
                ->with(['items.product', 'outlet'])
                ->get();

            $detailData = [];
            
            foreach ($stockLogs as $log) {
                $outletName = $log->outlet ? $log->outlet->name : 'N/A';
                
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
                    
                    $key = $outletName . '_' . $productName;
                    
                    if (!isset($detailData[$key])) {
                        $detailData[$key] = [
                            $outletName,
                            $kategori,
                            $jenis,
                            $quantity
                        ];
                    } else {
                        $detailData[$key][3] += $quantity;
                    }
                }
            }

            $dataToDisplay = array_values($detailData);
            $judulRiwayat = 'Riwayat Stok';
            
        } else if ($tipe === 'retur') {
            $returnLogs = ReturnLog::where('username_id', $dseId)
                ->whereDate('date', $tanggalCari->toDateString())
                ->with(['items.product', 'outlet'])
                ->get();

            $detailData = [];
            
            foreach ($returnLogs as $log) {
                $outletName = $log->outlet ? $log->outlet->name : 'N/A';
                
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
                    
                    $key = $outletName . '_' . $productName;
                    
                    if (!isset($detailData[$key])) {
                        $detailData[$key] = [
                            $outletName,
                            $kategori,
                            $jenis,
                            $quantity
                        ];
                    } else {
                        $detailData[$key][3] += $quantity;
                    }
                }
            }

            $dataToDisplay = array_values($detailData);
            $judulRiwayat = 'Riwayat Retur';
            
        } else {
            $stockLogs = StockLog::where('username_id', $dseId)
                ->whereDate('date', $tanggalCari->toDateString())
                ->with(['items.product', 'outlet'])
                ->get();

            $returnLogs = ReturnLog::where('username_id', $dseId)
                ->whereDate('date', $tanggalCari->toDateString())
                ->with(['items.product', 'outlet'])
                ->get();

            $detailData = [];
            
            foreach ($stockLogs as $log) {
                $outletName = $log->outlet ? $log->outlet->name : 'N/A';
                
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
                    
                    $key = $outletName . '_' . $productName;
                    
                    if (!isset($detailData[$key])) {
                        $detailData[$key] = [
                            $outletName,
                            $kategori,
                            $jenis,
                            $quantity,
                            0,
                            $quantity
                        ];
                    } else {
                        $detailData[$key][3] += $quantity;
                        $detailData[$key][5] = $detailData[$key][3] - $detailData[$key][4];
                    }
                }
            }

            foreach ($returnLogs as $log) {
                $outletName = $log->outlet ? $log->outlet->name : 'N/A';
                
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
                    
                    $key = $outletName . '_' . $productName;
                    
                    if (!isset($detailData[$key])) {
                        $detailData[$key] = [
                            $outletName,
                            $kategori,
                            $jenis,
                            0,
                            $quantity,
                            -$quantity
                        ];
                    } else {
                        $detailData[$key][4] += $quantity;
                        $detailData[$key][5] = $detailData[$key][3] - $detailData[$key][4];
                    }
                }
            }

            $dataToDisplay = array_values($detailData);
            $judulRiwayat = 'Riwayat Semua';
        }
        
        return view('dse.riwayat_pencatatan', compact('dataToDisplay', 'dseId', 'tanggalFilter', 'tipe', 'judulRiwayat'));
    }
}