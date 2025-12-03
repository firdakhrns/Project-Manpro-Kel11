<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockLog;
use App\Models\ReturnLog;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Product;
use App\Models\StockLogItem;
use App\Models\ReturnLogItem;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    private $productMapping = [
            '3gb' => 'KP_3GB',
            '6gb' => 'KP_6GB', 
            '9gb' => 'KP_9GB',
            '20gb' => 'KP_20GB',

            '15gb_1h' => 'FI15_1D', 
            '35gb_5h' => 'FI35_5D', 
            '5gb_3h' => 'FI5_3D',     
            '5gb_2h' => 'FI5_2D',    
            '5gb_5h' => 'FI5_5D', 
            '7gb_7h' => 'FI7_7D', 
            '3gb_3h' => 'FI3_3D', 
            '3gb_1h' => 'FI3_1D', 
            '15gb_7h' => 'FI15_7D',
        ];
    
    private function getProductsMap()
    {
        return Product::whereIn('product_code', array_values($this->productMapping))
                        ->pluck('id', 'product_code');
    }
    public function viewStok()
    {
        $stokData = StockLog::with(['user', 'outlet', 'items.product'])
                          ->orderBy('date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        return view('admin.view_stok', compact('stokData'));
    }

    public function viewRetur()
    {
        $returData = ReturnLog::with(['user', 'outlet', 'items.product'])
                            ->orderBy('date', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();
        
        return view('admin.view_retur', compact('returData'));
    }

    public function viewOutlet()
    {
        $outlets = Outlet::with(['stockLogs', 'salesLogs'])
                        ->orderBy('name')
                        ->get();
        
        return view('admin.view_outlet', compact('outlets'));
    }

    public function riwayatPencatatan(Request $request)
{
    $isFiltered = $request->filled('tanggal'); 
    
    $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
    $dseIdFilter = $request->input('dse_id');
    $tipe = $request->input('tipe', 'stok');

    $validationError = null;

    try {
        $tanggalCari = Carbon::parse($tanggal);

        if ($tanggalCari->greaterThan(Carbon::today())) {
            $tanggalCari = Carbon::today();
            if ($isFiltered) { 
                $validationError = 'Tanggal tidak boleh melebihi Tanggal Hari Ini.';
            }
            $tanggal = Carbon::today()->toDateString();
        }
    } catch (\Exception $e) {
        $tanggalCari = Carbon::today();
        if ($isFiltered) {
            $validationError = 'Format tanggal tidak valid. Menggunakan Tanggal Hari Ini.';
        }
        $tanggal = Carbon::today()->toDateString();
    }

    $dseList = User::where('role', 'DSE')->orderBy('id_dse')->get(['id_dse', 'name', 'region']);
    $regions = User::where('role', 'DSE')->distinct()->pluck('region');
    
    $productHeaders = Product::pluck('product_code')->toArray();
    $allDseKeys = User::where('role', 'DSE')->pluck('id_dse'); 
    
    $pivotData = [];
    
    foreach ($allDseKeys as $id) {
        if ($dseIdFilter && $dseIdFilter != $id) continue;
        
        $pivotData[$id] = [
            'stok' => array_fill_keys($productHeaders, 0),
            'retur' => array_fill_keys($productHeaders, 0),
        ];
    }

    // A. Query Stok (stok & all)
    if ($tipe == 'stok' || $tipe == 'all') {
        $stokQuery = $this->buildPivotQuery('stock_log_items', 'stock_logs', $tanggalCari, $dseIdFilter);
        $this->aggregatePivotData($stokQuery->get(), $pivotData, 'stok');
    }

    if ($tipe == 'retur' || $tipe == 'all') {
        $returQuery = $this->buildPivotQuery('return_log_items', 'return_logs', $tanggalCari, $dseIdFilter);
        $this->aggregatePivotData($returQuery->get(), $pivotData, 'retur');
    }
    
    return view('admin.riwayat_pencatatan', [
        'pivotData' => $pivotData,
        'productHeaders' => $productHeaders,
        'tanggalFilter' => $tanggal, 
        'tipe' => $tipe,
        'dseList' => $dseList,
        'regions' => $regions,
        'totalDSE' => User::where('role', 'DSE')->count(), 
        'totalOutlets' => Outlet::count(),
        'validationError' => $validationError, 
    ]);
}

    private function buildPivotQuery(string $itemTable, string $logTable, Carbon $date, ?string $dseId)
    {
        $logIdColumn = $logTable == 'stock_logs' ? 'stock_log_id' : 'return_log_id';

        $query = DB::table($itemTable)
            ->join($logTable, "{$itemTable}.{$logIdColumn}", '=', "{$logTable}.id")
            ->join('products', "{$itemTable}.product_id", '=', 'products.id')
            ->select(
                "{$logTable}.username_id",
                'products.product_code',
                DB::raw('SUM('.$itemTable.'.quantity) as total_qty')
            )
            ->whereDate("{$logTable}.date", $date->toDateString());

        if ($dseId) {
            $query->where("{$logTable}.username_id", $dseId);
        }
        
        $query->groupBy("{$logTable}.username_id", 'products.product_code');
        
        return $query;
    }

    private function aggregatePivotData($logData, &$pivotData, string $logType) 
    {
        foreach ($logData as $log) {
            $dseId = $log->username_id; 
            $productCode = $log->product_code;

            if (isset($pivotData[$dseId])) {
                $pivotData[$dseId][$logType][$productCode] = $log->total_qty;
            }
        }
    }

    public function createStok()
    {
        $dseUsers = User::where('role', 'DSE')->get();
        $outlets = Outlet::all();
        return view('admin.view_stok', compact('dseUsers', 'outlets')); 
    }

    public function createRetur()
    {
        $dseUsers = User::where('role', 'DSE')->get();
        $outlets = Outlet::all();
        return view('admin.view_retur', compact('dseUsers', 'outlets'));
    }

    public function storeStok(Request $request)
    {
        $messages = [
        'stok.kp.*.max' => 'Jumlah stok Kartu Perdana tidak boleh melebihi :max.',
        'stok.v.*.max' => 'Jumlah stok Voucher tidak boleh melebihi :max.',
        'stok.kp.*.min' => 'Jumlah stok harus minimal :min.',
        'stok.v.*.min' => 'Jumlah stok harus minimal :min.',
    ];
    
    $request->validate([
        'outlet_id' => 'required|exists:outlets,id',
        'stok' => 'nullable|array',
        'stok.kp.*' => 'nullable|integer|min:0|max:500',
        'stok.v.*' => 'nullable|integer|min:0|max:500',
    ], $messages);

        DB::beginTransaction();
        try {
            $stockLog = StockLog::create([
                'username_id' => $request->username_id,
                'outlet_id' => $request->outlet_id,
                'date' => Carbon::now()->toDateString(),
                'notes' => $request->notes ?? 'Stok oleh Admin',
            ]);

        $productMapping = [
            '3gb' => 'KP_3GB',
            '6gb' => 'KP_6GB', 
            '9gb' => 'KP_9GB',
            '20gb' => 'KP_20GB',

            '15gb_1h' => 'FI15_1D', 
            '35gb_5h' => 'FI35_5D', 
            '5gb_3h' => 'FI5_3D',     
            '5gb_2h' => 'FI5_2D',    
            '5gb_5h' => 'FI5_5D', 
            '7gb_7h' => 'FI7_7D', 
            '3gb_3h' => 'FI3_3D', 
            '3gb_1h' => 'FI3_1D', 
            '15gb_7h' => 'FI15_7D',
        ];

            $productsMap = Product::whereIn('product_code', array_values($productMapping))
                                ->pluck('id', 'product_code');

            $allStokInputs = array_merge($request->input('stok.kp', []), $request->input('stok.v', []));
            $savedItems = 0;

            foreach ($allStokInputs as $inputKey => $quantity) {
                $productCode = $productMapping[$inputKey] ?? null;
                $quantity = (int) $quantity;

                if ($quantity > 0 && $productCode && $productsMap->has($productCode)) {
                    StockLogItem::create([
                        'stock_log_id' => $stockLog->id,
                        'product_id' => $productsMap[$productCode],
                        'quantity' => $quantity,
                    ]);
                    $savedItems++;
                }
            }

            DB::commit();
            return redirect()->route('admin.riwayat_pencatatan')->with('success', 'Data stok berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data stok: ' . $e->getMessage())->withInput();
        }
    }

    public function storeRetur(Request $request)
    {
        $messages = [
        'retur.kp.*.max' => 'Jumlah retur Kartu Perdana tidak boleh melebihi :max.',
        'retur.v.*.max' => 'Jumlah retur Voucher tidak boleh melebihi :max.',
        'retur.kp.*.min' => 'Jumlah retur harus minimal :min.',
        'retur.v.*.min' => 'Jumlah retur harus minimal :min.',
    ];
    
    $validator = Validator::make($request->all(), [
        'outlet_id' => 'required|exists:outlets,id',
        'retur' => 'nullable|array',
        'retur.kp.*' => 'nullable|integer|min:0|max:500', 
        'retur.v.*' => 'nullable|integer|min:0|max:500',
    ], $messages);
    
    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }
    
    $dseIdTarget = $request->username_id;
    $outletId = $request->outlet_id;
    $allReturInputs = array_merge($request->input('retur.kp', []), $request->input('retur.v', []));
    $productsMap = $this->getProductsMap();
    $errors = []; 
    
    foreach ($allReturInputs as $inputKey => $returQuantity) {
        $returQuantity = (int) $returQuantity;
        if ($returQuantity <= 0) continue;
    
        $productCode = $this->productMapping[$inputKey] ?? null;
        $productId = $productsMap[$productCode] ?? null;
    
        if ($productId) {
        $lastStockItem = StockLogItem::where('product_id', $productId)
            ->whereHas('stockLog', function($q) use ($dseIdTarget, $outletId) {
                $q->where('username_id', $dseIdTarget)->where('outlet_id', $outletId); 
            })
            ->orderBy('created_at', 'desc')
            ->first();
            
        $stokTersedia = $lastStockItem ? $lastStockItem->quantity : 0;
        
        if ($returQuantity > $stokTersedia) {
            $errors[$inputKey] = "Gagal: Jumlah Retur untuk produk $productCode ($returQuantity) melebihi Stok Terakhir yang tercatat ($stokTersedia). Harap periksa riwayat stok atau input stok terlebih dahulu.";
        }
        }
    }
    
    if (!empty($errors)) {

        return back()->withErrors($errors)->withInput();
    }

        DB::beginTransaction();
        try {
            $returnLog = ReturnLog::create([
                'username_id' => $dseIdTarget,
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
            return redirect()->route('admin.riwayat_pencatatan', [
            'tanggal' => Carbon::now()->toDateString(),
            'tipe' => 'retur'
            ])->with('success', 'Data retur berhasil disimpan!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data retur: ' . $e->getMessage())->withInput();
        }
    }

    public function storeOutlet(Request $request)
    {
        $request->validate([
        'name' => [
            'required', 'string', 'max:255', 
            'unique:outlets,name', 
            'regex:/^[\pL\pN\s\-\.]+$/u', 
        ],
            'address' => 'required|string|max:500', 
            'owner_name' => [
                'required', 'string', 'max:255',
                'regex:/^[\pL\s]+$/u', 
            ],
            'phone' => [
            'required', 
            'string', 
            'max:12', 
            'regex:/^[0-9]+$/', 
        ], 
            'region' => 'required|string',
        ]);

        try {
            Outlet::create([
                'name' => $request->name,
                'address' => $request->address,
                'owner_name' => $request->owner_name,
                'status' => 'Aktif',
                'region' => $request->region,
                'phone' => $request->phone,
                'join_date' => now(),
            ]);

            return redirect()->route('admin.view_outlet')->with('success', 'Outlet berhasil ditambahkan!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan outlet: ' . $e->getMessage())->withInput();
        }
    }

    public function editOutlet($id)
    {
        $outlet = Outlet::findOrFail($id);
        $regions = [
            'Banjarmasin Utara', 'Banjarmasin Selatan', 'Banjarmasin Barat',
            'Banjarmasin Tengah', 'Banjarmasin Timur'
        ];
        
        return view('admin.edit_outlet', compact('outlet', 'regions'));
    }

    public function updateOutlet(Request $request, $id)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s]+$/u', 
            ],
            'phone' => [
                'required', 
                'string', 
                'max:12', 
                'regex:/^[0-9]+$/', 
            ], 
            'status' => 'required|in:Aktif,Non-Aktif',
            'region' => 'required|string',
            
            'front_photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:5120', 
            'display_photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:5120',
        ];

        $request->validate($rules);
        
        try {
            $outlet = Outlet::findOrFail($id);
            $dataToUpdate = $request->except(['_token', '_method', 'front_photo', 'display_photo']);

            if ($request->hasFile('front_photo')) {
                if ($outlet->front_photo) {
                    Storage::disk('public')->delete($outlet->front_photo);
                }
                $dataToUpdate['front_photo'] = $request->file('front_photo')->store('outlet_photos', 'public');
            } else {
                unset($dataToUpdate['front_photo']);
            }

            if ($request->hasFile('display_photo')) {
                if ($outlet->display_photo) {
                    Storage::disk('public')->delete($outlet->display_photo);
                }
                $dataToUpdate['display_photo'] = $request->file('display_photo')->store('outlet_photos', 'public');
            } else {
                unset($dataToUpdate['display_photo']);
            }

            $outlet->update($dataToUpdate);

            return redirect()->route('admin.view_outlet')->with('success', 'Data outlet berhasil diupdate!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate outlet: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteOutlet($id)
    {
        DB::beginTransaction();
        try {
            $outlet = Outlet::findOrFail($id);
            
            $hasStockLogs = $outlet->stockLogs()->exists();
            $hasReturnLogs = $outlet->returnLogs()->exists();
            
            if ($hasStockLogs || $hasReturnLogs) {
                return back()->with('error', 'Tidak dapat menghapus outlet karena memiliki data stok atau retur terkait.');
            }
            
            $outlet->delete();
            DB::commit();

            return redirect()->route('admin.view_outlet')->with('success', 'Outlet berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus outlet: ' . $e->getMessage());
        }
    }

    public function showOutletDetail($id)
    {
        $outlet = Outlet::findOrFail($id); 
    
        return view('admin.view_outlet_detail', compact('outlet'));
    }


    public function exportOutletPdf(Request $request)
    {
        $outlets = Outlet::where('status', 'Aktif') 
                        ->orderBy('region')
                        ->orderBy('name')
                        ->get(); 
        
        $region = Auth::guard('shared')->user()->region ?? 'Global';
        $title = "Daftar Outlet Aktif Regional {$region}";

        $pdf = Pdf::loadView('admin.export.outlet_pdf', compact('outlets', 'title')); 
        return $pdf->download('Daftar_Outlet_Aktif_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    public function exportOutletDetailPdf($id)
    {
        $outlet = Outlet::findOrFail($id); 

        $pdf = Pdf::loadView('admin.export.outlet_detail_pdf', compact('outlet')); 

        $filename = 'Detail_Outlet_' . str_replace(' ', '_', $outlet->name) . '_' . date('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    public function dashboardStats()
    {
        $totalDSE = User::where('role', 'DSE')->count();
        $totalOutlets = Outlet::count();
        $todayStok = StockLog::whereDate('date', today())->count();
        $todayRetur = ReturnLog::whereDate('date', today())->count();
        $pendingRetur = ReturnLog::where('status', 'pending')->count();

        return compact('totalDSE', 'totalOutlets', 'todayStok', 'todayRetur', 'pendingRetur');
    }

    public function getOutletsByDSE(Request $request)
{
    $dseId = $request->input('dse_id');
    
    // 1. Ambil data DSE untuk mendapatkan region-nya
    $dseUser = User::where('id_dse', $dseId)->first(['region']);

    if (!$dseUser) {
        return response()->json([]); // Kembalikan array kosong jika DSE tidak ditemukan
    }

    $regionTarget = $dseUser->region;

    // 2. Ambil Outlet yang region-nya sama
    $outlets = Outlet::where('region', $regionTarget)
                     ->orderBy('name')
                     ->get(['id', 'name', 'region']);

    return response()->json($outlets);
}
}