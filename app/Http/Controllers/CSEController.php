<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockLog;
use App\Models\ReturnLog;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Feedbacks;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CSEController extends Controller
{

    // Di CSEController.php
private function getManagedRegions($userRegion, $userRole)
{
    if ($userRole === 'Manager' && $userRegion === 'Banjarmasin') {
        return [
            'Banjarmasin Timur',
            'Banjarmasin Utara', 
            'Banjarmasin Selatan',
            'Banjarmasin Tengah',
            'Banjarmasin Barat'
        ];
    }
    
    if (str_contains($userRegion, 'Banjarmasin')) {
        $subRegions = ['Timur', 'Utara', 'Selatan', 'Tengah', 'Barat'];
        foreach ($subRegions as $subRegion) {
            if (str_contains($userRegion, $subRegion)) {
                return [$userRegion];
            }
        }
        
        return [
            'Banjarmasin Timur',
            'Banjarmasin Utara', 
            'Banjarmasin Selatan',
            'Banjarmasin Tengah',
            'Banjarmasin Barat'
        ];
    }
    
    // Default
    return [$userRegion];
}

    public function viewStok(Request $request)
{
    $user = Auth::guard('shared')->user();
    $userRegion = $user->region;
    $regionsToSearch = $this->getManagedRegions($userRegion, $user->role);
    
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $dseId = $request->input('dse_id');

    $isFiltered = $startDate || $endDate || $dseId;

    // Set default jika tidak ada filter
    if (!$isFiltered) {
        $startDate = Carbon::today()->subDays(7)->toDateString();
        $endDate = Carbon::today()->toDateString();
        $isFiltered = true;
    }

    if ($startDate && $endDate) {
        try {
            $startCarbon = Carbon::parse($startDate);
            $endCarbon = Carbon::parse($endDate);

            if ($startCarbon->greaterThan($endCarbon)) {
                return redirect()->back()->withErrors([
                    'date_range' => 'Tanggal "Dari" tidak boleh melebihi Tanggal "Sampai".'
                ])->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['date_format' => 'Format tanggal tidak valid.'])->withInput();
        }
    }

    $stokData = collect(); 
    $pivotData = [];
    $productHeaders = \App\Models\Product::orderBy('product_name')
                        ->pluck('product_name')
                        ->toArray();
    
    // PERBAIKAN: Query DSE berdasarkan filter
    $dseQuery = User::where('role', 'DSE')
                   ->whereIn('region', $regionsToSearch);
    
    if ($dseId) {
        // Jika filter DSE dipilih, hanya ambil DSE tersebut
        $dseQuery->where('id_dse', $dseId);
    }
    
    $dseList = $dseQuery->select('id_dse', 'name')
                       ->get(); 

    // QUERY DATA dengan kondisi yang benar
    $query = StockLog::with(['user', 'outlet', 'items.product'])
                    ->whereHas('user', function($query) use ($regionsToSearch) {
                        $query->whereIn('region', $regionsToSearch);
                    });

    if ($startDate) {
        $query->whereDate('date', '>=', $startDate);
    }
    if ($endDate) {
        $query->whereDate('date', '<=', $endDate);
    }
    if ($dseId) {
        $query->where('username_id', $dseId);
    }

    $stokData = $query->orderBy('date', 'desc')->get();

    $initialProductCounts = array_fill_keys($productHeaders, 0);

    // PERBAIKAN: Inisialisasi pivot data hanya untuk DSE yang akan ditampilkan
    foreach ($dseList as $dse) {
        $pivotData[$dse->id_dse] = [
            'dse_name' => $dse->name ?? 'Unknown',
            'counts' => $initialProductCounts
        ];
    }

    // Proses data stok
    foreach ($stokData as $log) {
        $dseIdLog = $log->username_id;
        
        // Inisialisasi jika DSE belum ada (untuk jaga-jaga)
        if (!isset($pivotData[$dseIdLog])) {
            $pivotData[$dseIdLog] = [
                'dse_name' => $log->user->name ?? 'Unknown',
                'counts' => $initialProductCounts
            ];
        }
        
        foreach ($log->items as $item) {
            $productName = $item->product->product_name ?? 'Produk Tidak Diketahui';
    
            if (isset($pivotData[$dseIdLog]['counts'][$productName])) {
                $pivotData[$dseIdLog]['counts'][$productName] += $item->quantity;
            }
        }
    }

    return view('cse.view_stok', compact(
        'stokData', 
        'pivotData', 
        'productHeaders', 
        'dseList', 
        'isFiltered',
        'startDate',
        'endDate',
        'dseId',
        'userRegion',
        'regionsToSearch'
    ));
}

    public function viewRetur(Request $request)
{
    $user = Auth::guard('shared')->user();
    $userRegion = $user->region;
    $regionsToSearch = $this->getManagedRegions($userRegion, $user->role);
    
    $startDate = $request->input('start_date'); 
    $endDate = $request->input('end_date'); 
    $dseId = $request->input('dse_id');

    $isFiltered = $startDate || $endDate || $dseId;
    
    // Set default jika tidak ada filter
    if (!$isFiltered) {
        $startDate = Carbon::today()->subDays(7)->toDateString();
        $endDate = Carbon::today()->toDateString();
        $isFiltered = true;
    }
    
    if ($startDate && $endDate) {
        try {
            $startCarbon = Carbon::parse($startDate);
            $endCarbon = Carbon::parse($endDate);

            if ($startCarbon->greaterThan($endCarbon)) {
                return redirect()->back()->withErrors([
                    'date_range' => 'Tanggal "Dari" tidak boleh melebihi Tanggal "Sampai".'
                ])->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['date_format' => 'Format tanggal tidak valid.'])->withInput();
        }
    }

    $productHeaders = \App\Models\Product::orderBy('product_name')
                        ->pluck('product_name')
                        ->toArray();
    
    $returData = collect();
    $pivotData = [];

    // PERBAIKAN: Query DSE berdasarkan filter
    $dseQuery = User::where('role', 'DSE')
                   ->whereIn('region', $regionsToSearch);
    
    if ($dseId) {
        // Jika filter DSE dipilih, hanya ambil DSE tersebut
        $dseQuery->where('id_dse', $dseId);
    }
    
    $dseList = $dseQuery->select('id_dse', 'name')
                       ->get();
    
    $query = ReturnLog::with(['user', 'outlet', 'items.product'])
                     ->whereHas('user', function($query) use ($regionsToSearch) {
                         $query->whereIn('region', $regionsToSearch);
                     });
    
    if ($startDate) {
        $query->whereDate('date', '>=', $startDate);
    }
    if ($endDate) {
        $query->whereDate('date', '<=', $endDate);
    }
    if ($dseId) {
        $query->where('username_id', $dseId);
    }

    $returData = $query->orderBy('date', 'desc')->get();

    $initialProductCounts = array_fill_keys($productHeaders, 0);

    // PERBAIKAN: Inisialisasi pivot data hanya untuk DSE yang akan ditampilkan
    foreach ($dseList as $dse) {
        $pivotData[$dse->id_dse] = [
            'dse_name' => $dse->name ?? 'Unknown',
            'counts' => $initialProductCounts
        ];
    }
    
    foreach ($returData as $log) {
        $dseIdLog = $log->username_id;
        
        if (!isset($pivotData[$dseIdLog])) {
            $pivotData[$dseIdLog] = [
                'dse_name' => $log->user->name ?? 'Unknown',
                'counts' => $initialProductCounts
            ];
        }
        
        foreach ($log->items as $item) {
            $productName = $item->product->product_name ?? null;
            
            if ($productName && isset($pivotData[$dseIdLog]['counts'][$productName])) {
                $pivotData[$dseIdLog]['counts'][$productName] += $item->quantity;
            }
        }
    }
    
    return view('cse.view_retur', compact(
        'returData', 
        'pivotData', 
        'productHeaders', 
        'dseList', 
        'isFiltered',
        'startDate',
        'endDate',
        'dseId',
        'userRegion',
        'regionsToSearch'
    ));
}

    /**
     * View outlet di region CSE
     */
    public function viewOutlet()
    {
        $outlets = Outlet::with(['stockLogs', 'salesLogs'])
                        ->orderBy('name')
                        ->get();
        
        return view('cse.view_outlet', compact('outlets'));
    }

    /**
     * View performa DSE di region CSE
     */
    public function viewPerforma(Request $request)
    {
        $user = Auth::guard('shared')->user();
        $userRegion = $user->region; // TAMBAHKAN INI
        $regionsToSearch = $this->getManagedRegions($userRegion, $user->role);
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $isFiltered = $startDate && $endDate;

        // Set default jika tidak ada filter
        if (!$isFiltered) {
            $startDate = Carbon::today()->subDays(30)->toDateString();
            $endDate = Carbon::today()->toDateString();
            $isFiltered = true;
        }

        if ($startDate && $endDate) {
            try {
                $startCarbon = Carbon::parse($startDate);
                $endCarbon = Carbon::parse($endDate);

                if ($startCarbon->greaterThan($endCarbon)) {
                    return redirect()->back()->withErrors([
                        'date_range' => 'Tanggal "Dari" tidak boleh melebihi Tanggal "Sampai".'
                    ])->withInput();
                }
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['date_format' => 'Format tanggal tidak valid.'])->withInput();
            }
        }
        
        // 1. HITUNG TOTAL STOK MASUK - PERBAIKAN: gunakan $regionsToSearch
        $stockTotals = DB::table('stock_logs as sl')
            ->join('stock_log_items as sli', 'sl.id', '=', 'sli.stock_log_id')
            ->join('users as u', 'sl.username_id', '=', 'u.id_dse')
            ->select('sl.username_id as dse_id', DB::raw('SUM(sli.quantity) as total_stok_masuk'))
            ->whereDate('sl.date', '>=', $startDate)
            ->whereDate('sl.date', '<=', $endDate)
            ->whereIn('u.region', $regionsToSearch) // PERBAIKAN: whereIn bukan where
            ->where('u.role', 'DSE')
            ->groupBy('sl.username_id');

        // 2. HITUNG TOTAL RETUR - PERBAIKAN: gunakan $regionsToSearch
        $returnTotals = DB::table('return_logs as rl')
            ->join('return_log_items as rli', 'rl.id', '=', 'rli.return_log_id')
            ->join('users as u', 'rl.username_id', '=', 'u.id_dse')
            ->select('rl.username_id as dse_id', DB::raw('SUM(rli.quantity) as total_retur'))
            ->whereDate('rl.date', '>=', $startDate)
            ->whereDate('rl.date', '<=', $endDate)
            ->whereIn('u.region', $regionsToSearch) // PERBAIKAN: whereIn bukan where
            ->where('u.role', 'DSE')
            ->groupBy('rl.username_id');
            
        // 3. GABUNGKAN DATA - PERBAIKAN: gunakan $regionsToSearch
        $performanceData = User::where('role', 'DSE')
            ->whereIn('region', $regionsToSearch) // PERBAIKAN: whereIn bukan where
            ->select('users.id_dse')
            ->leftJoinSub($stockTotals, 'stock_t', function ($join) {
                $join->on('users.id_dse', '=', 'stock_t.dse_id');
            })
            ->leftJoinSub($returnTotals, 'return_t', function ($join) {
                $join->on('users.id_dse', '=', 'return_t.dse_id');
            })
            ->select(
                'users.id_dse as dse_id',
                DB::raw('COALESCE(stock_t.total_stok_masuk, 0) as total_stok_masuk'),
                DB::raw('COALESCE(return_t.total_retur, 0) as total_retur')
            )
            ->orderBy('users.id_dse')
            ->get();

        Log::info('Performance query executed:', [
            'user_region' => $userRegion,
            'regions_to_search' => $regionsToSearch,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'dse_count' => $performanceData->count()
        ]);

        // 4. Hitung Rasio Retur
        $finalData = $performanceData->map(function ($item) {
            $stokMasuk = (int) $item->total_stok_masuk;
            $totalRetur = (int) $item->total_retur;
            $stokKeluar = $stokMasuk - $totalRetur; 
            
            $returnRate = $stokMasuk > 0 
                          ? ($totalRetur / $stokMasuk) * 100
                          : 0;

            return [
                'dse_id' => $item->dse_id,
                'total_stok_masuk' => $stokMasuk,
                'total_retur' => $totalRetur,
                'stok_keluar_netto' => $stokKeluar,
                'return_rate' => round($returnRate, 2)
            ];
        })->sortBy('return_rate')->values()->all();

        return view('cse.view_performa', [
            'performaData' => $finalData,
            'startDate' => $startDate, 
            'endDate' => $endDate, 
            'userRegion' => $userRegion,
            'regionsToSearch' => $regionsToSearch, // TAMBAHKAN INI
            'isFiltered' => $isFiltered, 
        ]);
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
        
        return view('cse.edit_outlet', compact('outlet', 'regions'));
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

            return redirect()->route('cse.view_outlet')->with('success', 'Data outlet berhasil diupdate!');

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

            return redirect()->route('cse.view_outlet')->with('success', 'Outlet berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus outlet: ' . $e->getMessage());
        }
    }

    public function showOutletDetail($id)
    {
        $outlet = Outlet::findOrFail($id); 
    
        return view('cse.view_outlet_detail', compact('outlet'));
    }

    /**
     * Halaman kritik & saran untuk DSE
     */
    
    public function kritikSaran()
    {
        return view('cse.kritik_saran'); // Dashboard pilihan
    }

    /**
     * Form Input Kritik Saran
     */
    public function showInputKritikSaran()
    {
        $user = Auth::guard('shared')->user();
        $userRegion = $user->region;
        $regionsToSearch = $this->getManagedRegions($userRegion, $user->role);
        
        // Pastikan mengambil DSE dengan kolom yang benar
        $dseList = User::where('role', 'DSE')
                      ->whereIn('region', $regionsToSearch) // PERBAIKAN: whereIn
                      ->select('id_dse', 'name', 'region') // Tambah region
                      ->orderBy('region')
                      ->orderBy('id_dse')
                      ->get();
        
        Log::info('DSE List for Kritik Saran:', [
            'cse_region' => $userRegion,
            'regions_searched' => $regionsToSearch,
            'dse_count' => $dseList->count(),
            'dses' => $dseList->pluck('id_dse')->toArray()
        ]);
        
        return view('cse.input_kritik_saran', compact('dseList'));
    }

public function storeKritikSaran(Request $request)
{
    $request->validate([
        'dse_target' => [
            'required',
            'string',
            function ($attribute, $value, $fail) {
                // Validasi bahwa DSE target ada di database
                $dseExists = User::where('id_dse', $value)
                               ->where('role', 'DSE')
                               ->exists();
                if (!$dseExists) {
                    $fail('DSE target tidak valid.');
                }
            }
        ],
        'jenis_feedback' => 'required|string|in:Kritik,Saran',
        'feedback_text' => 'required|string|min:10|max:1000',
    ]);

    // Simpan ke database
    Feedbacks::create([
        'cse_id' => Auth::guard('shared')->user()->username ?? Auth::guard('shared')->user()->id_dse,
        'dse_target' => $request->dse_target,
        'type' => strtolower($request->jenis_feedback),
        'message' => $request->feedback_text,
        'is_urgent' => false,
        'created_at' => now(),
    ]);

    Log::info('Feedback dari CSE', [
        'cse' => Auth::guard('shared')->user()->username,
        'dse_target' => $request->dse_target,
        'type' => $request->jenis_feedback,
        'message' => $request->feedback_text
    ]);

    return redirect()->route('cse.kritik_saran')
                     ->with('success', 'Kritik dan saran berhasil dikirim!');
}

    public function showHasilKritikSaran(Request $request)
    {
        $user = Auth::guard('shared')->user();
        $userRegion = $user->region;
        $regionsToSearch = $this->getManagedRegions($userRegion, $user->role);
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $isFiltered = $startDate || $endDate;
        
        // Set default jika tidak ada filter
        if (!$isFiltered) {
            $startDate = Carbon::today()->subDays(30)->toDateString();
            $endDate = Carbon::today()->toDateString();
            $isFiltered = true;
        }

        if ($startDate && $endDate) {
            try {
                $startCarbon = Carbon::parse($startDate);
                $endCarbon = Carbon::parse($endDate);

                if ($startCarbon->greaterThan($endCarbon)) {
                    return redirect()->back()->withErrors([
                        'date_range' => 'Tanggal "Dari" tidak boleh melebihi Tanggal "Sampai".'
                    ])->withInput();
                }
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['date_format' => 'Format tanggal tidak valid.'])->withInput();
            }
        }
        
        $query = Feedbacks::whereNotNull('cse_id')
                         ->whereHas('dseTarget', function($q) use ($regionsToSearch) {
                             $q->whereIn('region', $regionsToSearch); // PERBAIKAN: whereIn
                         })
                         ->with(['dseTarget:id_dse,name'])
                         ->orderBy('created_at', 'desc');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $feedbackData = $query->get();

        Log::info('Feedback query executed:', [
            'user_region' => $userRegion,
            'regions_to_search' => $regionsToSearch,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'records_found' => $feedbackData->count()
        ]);

        return view('cse.hasil_kritik_saran', compact(
            'feedbackData',
            'startDate',
            'endDate',
            'isFiltered',
            'userRegion',
            'regionsToSearch' // TAMBAHKAN INI
        ));
    }

    /**
     * Export PDF Kritik Saran
     */
    public function exportKritikSaranPDF(Request $request)
{
    $user = Auth::guard('shared')->user();
    $userRegion = $user->region;
    $regionsToSearch = $this->getManagedRegions($userRegion, $user->role);
    
    // Gunakan parameter dari query string
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');
    
    $query = Feedbacks::whereNotNull('cse_id')
                     ->whereHas('dseTarget', function($q) use ($regionsToSearch) {
                         $q->whereIn('region', $regionsToSearch);
                     })
                     ->orderBy('created_at', 'desc');

    if ($startDate) {
        $query->whereDate('created_at', '>=', $startDate);
    }
    if ($endDate) {
        $query->whereDate('created_at', '<=', $endDate);
    }
    
    $kritikSaran = $query->get();
    
    if ($kritikSaran->isEmpty()) {
        return redirect()->back()->with('error', 'Tidak ada data untuk diekspor berdasarkan filter yang dipilih.')->withInput();
    }
    
    $pdf = Pdf::loadView('cse.export.kritik_saran_pdf', compact('kritikSaran'));
    
    $filename = 'kritik-saran-' . $userRegion . '-' . date('Y-m-d') . '.pdf';
    return $pdf->download($filename);
}

    // Di CSEController
    public function exportOutletPdf(Request $request)
    {
        $outlets = Outlet::where('status', 'Aktif') 
                        ->orderBy('region')
                        ->orderBy('name')
                        ->get(); 
        
        $region = Auth::guard('shared')->user()->region ?? 'Global';
        $title = "Daftar Outlet Aktif Regional {$region}";

        $pdf = Pdf::loadView('cse.export.outlet_pdf', compact('outlets', 'title')); 
        return $pdf->download('Daftar_Outlet_Aktif_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    /**
 * Export PDF Detail Outlet Spesifik (hanya 1 outlet)
 */
    public function exportOutletDetailPdf($id)
    {
        $outlet = Outlet::findOrFail($id); 

        $pdf = Pdf::loadView('cse.export.outlet_detail_pdf', compact('outlet')); 

        $filename = 'Detail_Outlet_' . str_replace(' ', '_', $outlet->name) . '_' . date('Ymd') . '.pdf';
        return $pdf->download($filename);
    }
}