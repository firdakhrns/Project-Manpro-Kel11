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
public function viewStok(Request $request)
{
    $userRegion = Auth::guard('shared')->user()->region;
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $dseId = $request->input('dse_id');

    $isFiltered = $startDate || $endDate || $dseId;

    if ($startDate && $endDate) {
        try {
            $startCarbon = Carbon::parse($startDate);
            $endCarbon = Carbon::parse($endDate);

            if ($startCarbon->greaterThan($endCarbon)) {
                $isFiltered = false; // Batalkan query
                return redirect()->back()->withErrors([
                    'date_range' => 'Tanggal "Dari" tidak boleh melebihi Tanggal "Sampai". Harap periksa filter Anda.'
                ])->withInput();
            }
        } catch (\Exception $e) {
            $isFiltered = false; // Batalkan query
            return redirect()->back()->withErrors(['date_format' => 'Format tanggal tidak valid.'])->withInput();
        }
    }

    $stokData = collect(); 
    $pivotData = [];
    $productHeaders = \App\Models\Product::orderBy('product_name')
                        ->pluck('product_name')
                        ->toArray();
    
    $dseList = User::where('role', 'DSE')->where('region', $userRegion)->get(); 

    if ($isFiltered) {
        // 1. QUERY DATA
        $query = StockLog::with(['user', 'outlet', 'items.product'])
                        ->whereHas('user', function($query) use ($userRegion) {
                            $query->where('region', $userRegion);
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

        foreach ($dseList as $dse) {
            $pivotData[$dse->id_dse] = $initialProductCounts;
        }
        
        foreach ($stokData as $log) {
            $dseId = $log->username_id;
            
            // Inisialisasi jika DSE belum ada
            if (!isset($pivotData[$dseId])) {
                $pivotData[$dseId] = $initialProductCounts;
            }
            
            foreach ($log->items as $item) {
                $productName = $item->product->product_name ?? 'Produk Tidak Diketahui'; 
        
                if (isset($pivotData[$dseId][$productName])) {
                    $pivotData[$dseId][$productName] += $item->quantity;
                }
            }
        }
    }

    
    return view('cse.view_stok', compact('stokData', 'pivotData', 'productHeaders', 'dseList', 'isFiltered'));
}

/**
 * View retur semua DSE di region CSE dengan filter
 */
public function viewRetur(Request $request)
{
    $userRegion = Auth::guard('shared')->user()->region;
    $startDate = $request->input('start_date'); 
    $endDate = $request->input('end_date'); 
    $dseId = $request->input('dse_id'); // Pastikan Anda juga menggunakan filter DSE

    $isFiltered = $startDate || $endDate || $dseId;
    
    if ($startDate && $endDate) {
        try {
            $startCarbon = Carbon::parse($startDate);
            $endCarbon = Carbon::parse($endDate);

            if ($startCarbon->greaterThan($endCarbon)) {
                $isFiltered = false; // Batalkan query jika validasi gagal
                return redirect()->back()->withErrors([
                    'date_range' => 'Tanggal "Dari" tidak boleh melebihi Tanggal "Sampai". Harap periksa filter Anda.'
                ])->withInput();
            }
        } catch (\Exception $e) {
            $isFiltered = false; // Batalkan query jika format salah
            return redirect()->back()->withErrors(['date_format' => 'Format tanggal tidak valid.'])->withInput();
        }
    }
    // END VALIDASI TANGGAL

    // Inisialisasi variabel hasil (default kosong)
    $productHeaders = \App\Models\Product::orderBy('product_name')
                        ->pluck('product_name')
                        ->toArray();
    
    $returData = collect();
    $pivotData = [];

    $dseList = User::where('role', 'DSE')
                    ->where('region', $userRegion)
                    ->get();
    
    if ($isFiltered) {
        $query = ReturnLog::with(['user', 'outlet', 'items.product'])
                         ->whereHas('user', function($query) use ($userRegion) {
                             $query->where('region', $userRegion);
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

        foreach ($dseList as $dse) {
            $pivotData[$dse->id_dse] = $initialProductCounts;
        }
        
        foreach ($returData as $log) {
            $dseId = $log->username_id;
            
            if (!isset($pivotData[$dseId])) {
                $pivotData[$dseId] = $initialProductCounts; 
            }
            
            foreach ($log->items as $item) {
                $productName = $item->product->product_name ?? null;
                
                if ($productName && isset($pivotData[$dseId][$productName])) {
                    $pivotData[$dseId][$productName] += $item->quantity;
                }
            }
        }
    }
    
    return view('cse.view_retur', compact('returData', 'pivotData', 'productHeaders', 'dseList', 'isFiltered'));
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
    $userRegion = Auth::guard('shared')->user()->region;
    
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    
    $isFiltered = $startDate && $endDate;

    $queryStartDate = $startDate ? $startDate : Carbon::today()->subDays(30)->toDateString();
    $queryEndDate = $endDate ? $endDate : Carbon::today()->toDateString();

    if ($startDate && $endDate) {
        try {
            $startCarbon = Carbon::parse($startDate);
            $endCarbon = Carbon::parse($endDate);

            if ($startCarbon->greaterThan($endCarbon)) {
                return redirect()->back()->withErrors([
                    'date_range' => 'Tanggal "Dari" tidak boleh melebihi Tanggal "Sampai". Harap periksa filter Anda.'
                ])->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['date_format' => 'Format tanggal tidak valid.'])->withInput();
        }
    }
    
    $performanceData = collect(); 

    if ($isFiltered) {
        
        // 1. HITUNG TOTAL STOK MASUK (via Subquery)
        $stockTotals = DB::table('stock_logs as sl')
            ->join('stock_log_items as sli', 'sl.id', '=', 'sli.stock_log_id')
            ->select('sl.username_id as dse_id', DB::raw('SUM(sli.quantity) as total_stok_masuk'))
            ->whereDate('sl.date', '>=', $queryStartDate)
            ->whereDate('sl.date', '<=', $queryEndDate)
            ->groupBy('sl.username_id');

        // 2. HITUNG TOTAL RETUR (via Subquery)
        $returnTotals = DB::table('return_logs as rl')
            ->join('return_log_items as rli', 'rl.id', '=', 'rli.return_log_id')
            ->select('rl.username_id as dse_id', DB::raw('SUM(rli.quantity) as total_retur'))
            ->whereDate('rl.date', '>=', $queryStartDate)
            ->whereDate('rl.date', '<=', $queryEndDate)
            ->groupBy('rl.username_id');
            
        // 3. GABUNGKAN DENGAN USER LIST (Outer Join)
        // Kita gunakan user list regional sebagai dasar agar semua DSE muncul (termasuk yang 0 stok/retur)
        $performanceData = User::where('role', 'DSE')
            ->where('region', $userRegion)
            ->select('users.id_dse') // Hanya pilih ID DSE
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
            ->get();
    }

    // 4. Hitung Rasio Retur dan Finalisasi Data
    $finalData = $performanceData->map(function ($item) {
        $stokMasuk = (int) $item->total_stok_masuk;
        $totalRetur = (int) $item->total_retur;
        $stokKeluar = $stokMasuk - $totalRetur; 
        
        $returnRate = $stokMasuk > 0 
                      ? ($totalRetur / $stokMasuk) * 100
                      : 0; // Hindari pembagian dengan nol

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
        'startDate' => $queryStartDate, 
        'endDate' => $queryEndDate, 
        'userRegion' => $userRegion,
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
        $userRegion = Auth::guard('shared')->user()->region;
        
        $dseList = User::where('role', 'DSE')
                      ->where('region', $userRegion)
                      ->get();
        
        return view('cse.input_kritik_saran', compact('dseList'));
    }

    /**
     * Menyimpan kritik & saran (POST method)
     */
    public function storeKritikSaran(Request $request)
    {
        $request->validate([
            'dse_target' => 'required|string',
            'jenis_feedback' => 'required|string|in:Kritik,Saran',
            'feedback_text' => 'required|string|min:10|max:1000',
        ]);

        // Simpan ke database
        Feedbacks::create([
            'cse_id' => Auth::guard('shared')->user()->username,
            'dse_target' => $request->dse_target,
            'type' => strtolower($request->jenis_feedback),
            'message' => $request->feedback_text,
            'is_urgent' => false,
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
    $userRegion = Auth::guard('shared')->user()->region;
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    if ($startDate && $endDate) {
        try {
            $startCarbon = Carbon::parse($startDate);
            $endCarbon = Carbon::parse($endDate);

            if ($startCarbon->greaterThan($endCarbon)) {
                return redirect()->back()->withErrors([
                    'date_range' => 'Tanggal "Dari" tidak boleh melebihi Tanggal "Sampai". Harap periksa filter Anda.'
                ])->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['date_format' => 'Format tanggal tidak valid.'])->withInput();
        }
    }
    
    $query = Feedbacks::where('cse_id', '!=', null) 
                 ->whereHas('dseTarget', function($q) use ($userRegion) {
                     $q->where('region', $userRegion);
                 })
                 ->orderBy('created_at', 'desc');

    if ($startDate) {
        $query->whereDate('created_at', '>=', $startDate);
    }
    if ($endDate) {
        $query->whereDate('created_at', '<=', $endDate);
    }

    $feedbackData = $query->get();

    return view('cse.hasil_kritik_saran', compact('feedbackData'));
}

    /**
     * Export PDF Kritik Saran
     */
    public function exportKritikSaranPDF(Request $request)
    {
        $userRegion = Auth::guard('shared')->user()->region;
    
    $query = Feedbacks::where('cse_id', '!=', null) 
                     ->whereHas('dseTarget', function($q) use ($userRegion) {
                         $q->where('region', $userRegion);
                     })
                     ->orderBy('created_at', 'desc');

    if ($request->has('start_date') && $request->start_date) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }
    if ($request->has('end_date') && $request->end_date) {
        $query->whereDate('created_at', '<=', $request->end_date);
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