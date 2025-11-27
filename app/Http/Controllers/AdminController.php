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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
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
        // 1. Ambil Filter dan Tetapkan Default
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $dseIdFilter = $request->input('dse_id'); // Mengambil DSE ID dari dropdown
        $tipe = $request->input('tipe', 'stok'); // stok, retur, all

        try {
            $tanggalCari = Carbon::parse($tanggal);
        } catch (\Exception $e) {
            $tanggalCari = Carbon::today();
        }

        // 2. Ambil List Master Data untuk Filter Dropdown
        $dseList = User::where('role', 'DSE')->orderBy('id_dse')->get(['id_dse', 'name', 'region']);
        $regions = User::where('role', 'DSE')->distinct()->pluck('region');
        
        // 3. Ambil semua produk unik sebagai Header Kolom (Product Codes)
        $productHeaders = Product::pluck('product_code')->toArray();
        $allDseKeys = User::where('role', 'DSE')->pluck('id_dse'); 
        
        // 4. Struktur Data Agregat Global (Pivot Matrix)
        $pivotData = [];
        
        foreach ($allDseKeys as $id) {
            if ($dseIdFilter && $dseIdFilter != $id) continue;
            
            $pivotData[$id] = [
                'stok' => array_fill_keys($productHeaders, 0),
                'retur' => array_fill_keys($productHeaders, 0),
            ];
        }

        // --- 5. Logika Query dan Agregasi ---

        // A. Query Stok (stok & all)
        if ($tipe == 'stok' || $tipe == 'all') {
            $stokQuery = $this->buildPivotQuery('stock_log_items', 'stock_logs', $tanggalCari, $dseIdFilter);
            $this->aggregatePivotData($stokQuery->get(), $pivotData, 'stok');
        }

        // B. Query Retur (retur & all)
        if ($tipe == 'retur' || $tipe == 'all') {
            $returQuery = $this->buildPivotQuery('return_log_items', 'return_logs', $tanggalCari, $dseIdFilter);
            $this->aggregatePivotData($returQuery->get(), $pivotData, 'retur');
        }
        
        // 6. Kirim data ke View
        return view('admin.riwayat_pencatatan', [
            'pivotData' => $pivotData,
            'productHeaders' => $productHeaders,
            'tanggalFilter' => $tanggal,
            'tipe' => $tipe,
            'dseList' => $dseList,
            'regions' => $regions,
            'totalDSE' => User::where('role', 'DSE')->count(), 
            'totalOutlets' => Outlet::count(),
        ]);
    }
    
    /**
     * Helper: Membuat query pivot yang efisien untuk Stok/Retur
     */
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

    /**
     * Helper: Mengisi data ke struktur pivot.
     */
    private function aggregatePivotData($logData, &$pivotData, string $logType) 
    {
        foreach ($logData as $log) {
            // Gunakan dse_id dari log (yang dikembalikan oleh SELECT)
            $dseId = $log->username_id; 
            $productCode = $log->product_code;

            if (isset($pivotData[$dseId])) {
                $pivotData[$dseId][$logType][$productCode] = $log->total_qty;
            }
        }
    }

    public function createStok()
{
    $dseUsers = User::where('role', 'DSE')->get();  // Huruf besar 'DSE'
    $outlets = Outlet::all();
    return view('admin.view_stok', compact('dseUsers', 'outlets')); // Langsung ke view_stok.blade.php
}

public function createRetur()
{
    $dseUsers = User::where('role', 'DSE')->get();  // Huruf besar 'DSE'
    $outlets = Outlet::all();
    return view('admin.view_retur', compact('dseUsers', 'outlets')); // Langsung ke view_retur.blade.php
}

    public function storeStok(Request $request)
    {
        $request->validate([
            'username_id' => 'required|exists:users,id_dse',
            'outlet_id' => 'required|exists:outlets,id',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $stockLog = StockLog::create([
                'username_id' => $request->username_id,
                'outlet_id' => $request->outlet_id,
                'date' => $request->date,
                'notes' => $request->notes ?? 'Stok oleh Admin',
            ]);

            $productMapping = [
                '3gb' => 'KP_3GB',
                '6gb' => 'KP_6GB', 
                '9gb' => 'KP_9GB',
                '20gb' => 'KP_20GB',
                '1gb_2h' => 'FI15_1D',
                '15gb_7h' => 'FI15_7D',
                '3gb_3h' => 'FI3_3D',
                '3gb_28h' => 'FI3_28D',
                '5gb_5h' => 'FI5_5D',
                '5gb_2h' => 'FI5_2D',
                '7gb_7h' => 'FI7_7D',
                '5gb_3h' => 'FI5_3D',
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
            return redirect()->route('admin.view_stok')->with('success', 'Data stok berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data stok: ' . $e->getMessage())->withInput();
        }
    }

    public function storeRetur(Request $request)
    {
        // PERBAIKAN: Field name harus 'username_id' bukan 'dse_id'
        $request->validate([
            'username_id' => 'required|exists:users,id_dse', // PERBAIKAN: username_id
            'outlet_id' => 'required|exists:outlets,id',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $returnLog = ReturnLog::create([
                'username_id' => $request->username_id, // PERBAIKAN: username_id
                'outlet_id' => $request->outlet_id,
                'date' => $request->date,
                'notes' => $request->notes ?? 'Retur oleh Admin',
                'status' => 'pending',
            ]);

            $productMapping = [
                '3gb' => 'KP_3GB',
                '6gb' => 'KP_6GB',
                '9gb' => 'KP_9GB', 
                '20gb' => 'KP_20GB',
                '1gb_2h' => 'FI15_1D',
                '15gb_7h' => 'FI15_7D',
                '3gb_3h' => 'FI3_3D',
                '3gb_28h' => 'FI3_28D',
                '5gb_5h' => 'FI5_5D',
                '5gb_2h' => 'FI5_2D',
                '7gb_7h' => 'FI7_7D',
                '5gb_3h' => 'FI5_3D',
            ];

            $productsMap = Product::whereIn('product_code', array_values($productMapping))
                                ->pluck('id', 'product_code');

            $allReturInputs = array_merge($request->input('retur.kp', []), $request->input('retur.v', []));
            $savedItems = 0;

            foreach ($allReturInputs as $inputKey => $quantity) {
                $productCode = $productMapping[$inputKey] ?? null;
                $quantity = (int) $quantity;

                if ($quantity > 0 && $productCode && $productsMap->has($productCode)) {
                    ReturnLogItem::create([
                        'return_log_id' => $returnLog->id,
                        'product_id' => $productsMap[$productCode],
                        'quantity' => $quantity,
                    ]);
                    $savedItems++;
                }
            }

            DB::commit();
            return redirect()->route('admin.view_retur')->with('success', 'Data retur berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data retur: ' . $e->getMessage())->withInput();
        }
    }

    public function editStok($id)
    {
        $stok = StockLog::with('items.product')->findOrFail($id);
        $dseList = User::where('role', 'DSE')->get();
        $outlets = Outlet::all();
        $products = Product::all();
        
        return view('admin.edit_stok', compact('stok', 'dseList', 'outlets', 'products'));
    }

    public function updateStok(Request $request, $id)
    {
        $request->validate([
            'username_id' => 'required|exists:users,id_dse',
            'outlet_id' => 'required|exists:outlets,id',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $stok = StockLog::findOrFail($id);
            
            $stok->update([
                'username_id' => $request->username_id,
                'outlet_id' => $request->outlet_id,
                'date' => $request->date,
                'notes' => $request->notes,
            ]);

            if ($request->has('items')) {
                foreach ($request->items as $itemId => $quantity) {
                    $stokItem = StockLogItem::where('stock_log_id', $stok->id)
                                           ->where('id', $itemId)
                                           ->first();
                    if ($stokItem && $quantity > 0) {
                        $stokItem->update(['quantity' => $quantity]);
                    } elseif ($stokItem && $quantity == 0) {
                        $stokItem->delete();
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.view_stok')->with('success', 'Data stok berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate data stok: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteStok($id)
    {
        DB::beginTransaction();
        try {
            $stok = StockLog::findOrFail($id);
            $stok->items()->delete();
            $stok->delete();

            DB::commit();
            return redirect()->route('admin.view_stok')->with('success', 'Data stok berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data stok: ' . $e->getMessage());
        }
    }

    public function updateReturStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
            'admin_notes' => 'nullable|string'
        ]);

        try {
            $retur = ReturnLog::findOrFail($id);
            $retur->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes
            ]);

            return back()->with('success', 'Status retur berhasil diupdate!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate status retur: ' . $e->getMessage());
        }
    }

    public function createOutlet()
    {
        $regions = [
            'Banjarmasin Utara',
            'Banjarmasin Selatan', 
            'Banjarmasin Barat',
            'Banjarmasin Tengah',
            'Banjarmasin Timur'
        ];
        
        return view('admin.create_outlet', compact('regions'));
    }

    public function storeOutlet(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:outlets,name',
            'address' => 'required|string',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string',
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
            'Banjarmasin Utara',
            'Banjarmasin Selatan',
            'Banjarmasin Barat',
            'Banjarmasin Tengah',
            'Banjarmasin Timur'
        ];
        
        return view('admin.edit_outlet', compact('outlet', 'regions'));
    }

    public function updateOutlet(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:outlets,name,' . $id,
            'address' => 'required|string',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string',
            'status' => 'required|in:Aktif,Non-Aktif',
            'region' => 'required|string',
        ]);

        try {
            $outlet = Outlet::findOrFail($id);
            $outlet->update($request->all());

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

    public function exportStok(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $startDate = $request->start_date ?? now()->subMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        
        $stokData = StockLog::with(['user', 'outlet', 'items.product'])
                          ->whereBetween('date', [$startDate, $endDate])
                          ->get();

        return view('admin.export.stok_excel', compact('stokData', 'startDate', 'endDate'));
    }

    public function exportRetur(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $startDate = $request->start_date ?? now()->subMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        
        $returData = ReturnLog::with(['user', 'outlet', 'items.product'])
                            ->whereBetween('date', [$startDate, $endDate])
                            ->get();

        return view('admin.export.retur_excel', compact('returData', 'startDate', 'endDate'));
    }

    public function exportOutletPdf(Request $request)
    {
        $outlets = Outlet::orderBy('name')->get(); 
        $region = Auth::guard('shared')->user()->region ?? 'Global';
        $title = "Daftar Outlet Aktif Regional {$region}";

        $pdf = Pdf::loadView('admin.export.outlet_pdf', compact('outlets', 'title')); 

        return $pdf->download('Daftar_Outlet_Aktif_' . Carbon::now()->format('Ymd_His') . '.pdf');
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
}