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

class AdminController extends Controller
{
    /**
     * Pencatatan Stok Harian - Semua DSE (READ)
     */
    public function viewStok()
    {
        $stokData = StockLog::with(['user', 'outlet', 'items.product'])
                          ->orderBy('date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        return view('admin.view_stok', compact('stokData'));
    }

    /**
     * Pencatatan Retur Harian - Semua DSE (READ) 
     */
    public function viewRetur()
    {
        $returData = ReturnLog::with(['user', 'outlet', 'items.product'])
                            ->orderBy('date', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();
        
        return view('admin.view_retur', compact('returData'));
    }

    /**
     * Data Validasi Outlet - Semua DSE (READ)
     */
    public function viewOutlet()
    {
        $outlets = Outlet::with(['stockLogs', 'salesLogs'])
                        ->orderBy('name')
                        ->get();
        
        return view('admin.view_outlet', compact('outlets'));
    }

    /**
     * Riwayat Pencatatan - Semua DSE (READ) dengan Filter
     */
    public function riwayatPencatatan(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'nullable|date',
            'dse_id' => 'nullable|string',
            'region' => 'nullable|string',
            'tipe' => 'nullable|in:stok,retur,all'
        ]);

        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $dseId = $request->input('dse_id');
        $region = $request->input('region');
        $tipe = $request->input('tipe', 'stok');

        // Query untuk data stok
        $stockQuery = StockLog::with(['user', 'outlet', 'items.product'])
            ->when($tanggal, function($query) use ($tanggal) {
                return $query->whereDate('date', $tanggal);
            })
            ->when($dseId, function($query) use ($dseId) {
                return $query->where('username_id', $dseId);
            })
            ->when($region, function($query) use ($region) {
                return $query->whereHas('user', function($q) use ($region) {
                    $q->where('region', $region);
                });
            });

        // Query untuk data retur
        $returnQuery = ReturnLog::with(['user', 'outlet', 'items.product'])
            ->when($tanggal, function($query) use ($tanggal) {
                return $query->whereDate('date', $tanggal);
            })
            ->when($dseId, function($query) use ($dseId) {
                return $query->where('username_id', $dseId);
            })
            ->when($region, function($query) use ($region) {
                return $query->whereHas('user', function($q) use ($region) {
                    $q->where('region', $region);
                });
            });

        // Gabungkan data berdasarkan tipe
        $riwayatData = collect();
        
        if ($tipe === 'stok' || $tipe === 'all') {
            $stocks = $stockQuery->get()->map(function($stock) {
                return [
                    'id' => 'S-' . $stock->id,
                    'jenis' => 'stok',
                    'username_id' => $stock->username_id,
                    'dse_name' => $stock->user->name ?? 'Unknown',
                    'region' => $stock->user->region ?? 'Unknown',
                    'outlet_name' => $stock->outlet->name ?? 'Unknown',
                    'date' => $stock->date,
                    'created_at' => $stock->created_at,
                    'notes' => $stock->notes ?? 'Stok harian',
                    'total_items' => $stock->items->count(),
                    'total_quantity' => $stock->items->sum('quantity')
                ];
            });
            $riwayatData = $riwayatData->merge($stocks);
        }

        if ($tipe === 'retur' || $tipe === 'all') {
            $returns = $returnQuery->get()->map(function($return) {
                return [
                    'id' => 'R-' . $return->id,
                    'jenis' => 'retur',
                    'username_id' => $return->username_id,
                    'dse_name' => $return->user->name ?? 'Unknown',
                    'region' => $return->user->region ?? 'Unknown',
                    'outlet_name' => $return->outlet->name ?? 'Unknown',
                    'date' => $return->date,
                    'created_at' => $return->created_at,
                    'notes' => $return->notes ?? 'Retur harian',
                    'status' => $return->status ?? 'pending',
                    'total_items' => $return->items->count(),
                    'total_quantity' => $return->items->sum('quantity')
                ];
            });
            $riwayatData = $riwayatData->merge($returns);
        }

        // Urutkan berdasarkan created_at
        $riwayatData = $riwayatData->sortByDesc('created_at')->values();

        // Data untuk dropdown filter
        $dseList = User::where('role', 'DSE')->get(['id_dse', 'name']);
        $regions = User::where('role', 'DSE')->distinct()->pluck('region');

        // Stats untuk info header
        $totalDSE = User::where('role', 'DSE')->count();
        $totalStokEntries = $stockQuery->count();
        $totalReturEntries = $returnQuery->count();
        $totalOutlets = Outlet::count();

        return view('admin.riwayat_pencatatan', compact(
            'riwayatData',
            'dseList',
            'regions',
            'totalDSE',
            'totalStokEntries',
            'totalReturEntries',
            'totalOutlets'
        ));
    }

    // ==================== CRUD OPERATIONS ====================

    /**
     * CREATE - Tambah Stok Manual (Admin)
     */
    public function createStok()
{
    $dseUsers = User::where('role', 'DSE')->get(['id_dse', 'name']);
    $outlets = Outlet::all();
    
    return view('admin.create_stok', compact('dseUsers', 'outlets'));
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

            // Mapping untuk product codes
            $productMapping = [
                // Kartu Perdana
                '3gb' => 'KP_3GB',
                '6gb' => 'KP_6GB',
                '9gb' => 'KP_9GB',
                '20gb' => 'KP_20GB',
                
                // Voucher
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

            // Simpan items stok
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

    /**
     * CREATE - Tambah Retur Manual (Admin)
     */
    public function createRetur()
    {
        $dseUsers = User::where('role', 'DSE')->get(['id_dse', 'name']);
        $outlets = Outlet::all();
        
        return view('admin.create_retur', compact('dseUsers', 'outlets'));
    }

    public function storeRetur(Request $request)
    {
        $request->validate([
            'username_id' => 'required|exists:users,id_dse',
            'outlet_id' => 'required|exists:outlets,id',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $returnLog = ReturnLog::create([
                'username_id' => $request->username_id,
                'outlet_id' => $request->outlet_id,
                'date' => $request->date,
                'notes' => $request->notes ?? 'Retur oleh Admin',
                'status' => 'pending',
            ]);

            // Mapping untuk product codes (sama dengan stok)
            $productMapping = [
                // Kartu Perdana
                '3gb' => 'KP_3GB',
                '6gb' => 'KP_6GB',
                '9gb' => 'KP_9GB',
                '20gb' => 'KP_20GB',
                
                // Voucher
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

            // Simpan items retur
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

    /**
     * UPDATE - Edit Data Stok
     */
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

            // Update items
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

    /**
     * DELETE - Hapus Data Stok
     */
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

    /**
     * UPDATE - Approve/Reject Retur
     */
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

    /**
     * CREATE - Tambah Outlet Baru (Admin)
     */
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

    /**
     * UPDATE - Edit Outlet
     */
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

    /**
     * DELETE - Hapus Outlet
     */
    public function deleteOutlet($id)
    {
        DB::beginTransaction();
        try {
            $outlet = Outlet::findOrFail($id);
            
            // Cek apakah outlet memiliki data terkait
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

    /**
     * REPORT - Export Data
     */
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

    /**
     * DASHBOARD STATS
     */
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