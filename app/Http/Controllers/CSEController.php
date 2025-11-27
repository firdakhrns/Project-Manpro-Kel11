<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockLog;
use App\Models\ReturnLog;
use App\Models\Outlet;
use App\Models\User;
use App\Models\SalesLog;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CSEController extends Controller
{

public function viewStok(Request $request)
{
    $userRegion = Auth::guard('shared')->user()->region;
    
    // Query dasar dengan relasi
    $query = StockLog::with(['user', 'outlet', 'items.product'])
                    ->whereHas('user', function($query) use ($userRegion) {
                        $query->where('region', $userRegion);
                    });

    // Filter tanggal
    if ($request->has('start_date') && $request->start_date) {
        $query->whereDate('date', '>=', $request->start_date);
    }
    if ($request->has('end_date') && $request->end_date) {
        $query->whereDate('date', '<=', $request->end_date);
    }

    // Filter DSE ID
    if ($request->has('dse_id') && $request->dse_id) {
        $query->where('username_id', $request->dse_id);
    }

    $stokData = $query->orderBy('date', 'desc')->get();

    // Daftar DSE untuk dropdown filter
    $dseList = User::where('role', 'DSE')
                  ->where('region', $userRegion)
                  ->get();

    // Format data untuk pivot table
    $pivotData = [];
    $productHeaders = [];

    foreach ($stokData as $log) {
        $dseId = $log->username_id;
        
        foreach ($log->items as $item) {
    $productName = $item->product->product_name;
    
    if (!in_array($productName, $productHeaders)) {
        $productHeaders[] = $productName;
    }
    
    if (!isset($pivotData[$dseId])) {
        $pivotData[$dseId] = [];
    }
    
    if (!isset($pivotData[$dseId][$productName])) {
        $pivotData[$dseId][$productName] = 0;
    }
    
    $pivotData[$dseId][$productName] += $item->quantity;
}
    }

    return view('cse.view_stok', compact('stokData', 'pivotData', 'productHeaders', 'dseList'));
}

/**
 * View retur semua DSE di region CSE dengan filter
 */
public function viewRetur(Request $request)
{
    $userRegion = Auth::guard('shared')->user()->region;
    
    // Query dasar dengan relasi
    $query = ReturnLog::with(['user', 'outlet', 'items.product'])
                     ->whereHas('user', function($query) use ($userRegion) {
                         $query->where('region', $userRegion);
                     });

    // Filter tanggal
    if ($request->has('start_date') && $request->start_date) {
        $query->whereDate('date', '>=', $request->start_date);
    }
    if ($request->has('end_date') && $request->end_date) {
        $query->whereDate('date', '<=', $request->end_date);
    }

    // Filter DSE ID
    if ($request->has('dse_id') && $request->dse_id) {
        $query->where('username_id', $request->dse_id);
    }

    $returData = $query->orderBy('date', 'desc')->get();

    // Daftar DSE untuk dropdown filter
    $dseList = User::where('role', 'DSE')
                  ->where('region', $userRegion)
                  ->get();

    // Format data untuk pivot table
    $pivotData = [];
    $productHeaders = [];

    foreach ($returData as $log) {
        $dseId = $log->username_id;
        
        foreach ($log->items as $item) {
            $productName = $item->product->product_name;
            
            if (!in_array($productName, $productHeaders)) {
                $productHeaders[] = $productName;
            }
            
            if (!isset($pivotData[$dseId])) {
                $pivotData[$dseId] = array_fill_keys($productHeaders, 0);
            }
            
            $pivotData[$dseId][$productName] += $item->quantity;
        }
    }

    return view('cse.view_retur', compact('returData', 'pivotData', 'productHeaders', 'dseList'));
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
        
        // Cek apakah filter tanggal sudah lengkap
        $isFiltered = $startDate && $endDate;

        // Tentukan nilai query date, default 30 hari terakhir jika filter belum lengkap
        $queryStartDate = $startDate ? $startDate : Carbon::today()->subDays(30)->toDateString();
        $queryEndDate = $endDate ? $endDate : Carbon::today()->toDateString();
        
        $performanceData = collect(); // Default data kosong

        if ($isFiltered) {
            // Lakukan query database hanya jika filter tanggal sudah lengkap
            $performanceData = DB::table('stock_logs as sl')
                ->select(
                    'sl.username_id as dse_id',
                    DB::raw('SUM(sli.quantity) as total_stok_masuk'),
                    DB::raw('COALESCE(SUM(rli.quantity), 0) as total_retur')
                )
                // Join Stock Log Items
                ->join('stock_log_items as sli', 'sl.id', '=', 'sli.stock_log_id')
                
                // LEFT JOIN Retur Log untuk mendapatkan 0 jika tidak ada retur
                ->leftJoin('return_logs as rl', function ($join) use ($queryStartDate, $queryEndDate) {
                    $join->on('sl.username_id', '=', 'rl.username_id')
                         // PERBAIKAN: Gunakan alias rl.date
                         ->whereDate('rl.date', '>=', $queryStartDate) 
                         ->whereDate('rl.date', '<=', $queryEndDate); 
                })
                // Join Retur Log Items
                ->leftJoin('return_log_items as rli', 'rl.id', '=', 'rli.return_log_id')
                
                // Join Users untuk Filter Regional
                ->join('users', 'sl.username_id', '=', 'users.id_dse')
                ->where('users.region', $userRegion)
                
                // Filter Periode STOK
                ->whereDate('sl.date', '>=', $queryStartDate)
                ->whereDate('sl.date', '<=', $queryEndDate)
                
                ->groupBy('sl.username_id')
                ->orderBy('total_retur', 'asc')
                ->get();
        }

        // 3. Hitung Rasio Retur dan Finalisasi Data
        $finalData = $performanceData->map(function ($item) {
            $stokKeluar = $item->total_stok_masuk - $item->total_retur; 
            
            $returnRate = $item->total_stok_masuk > 0 
                          ? ($item->total_retur / $item->total_stok_masuk) * 100
                          : 0;

            return [
                'dse_id' => $item->dse_id,
                'total_stok_masuk' => (int) $item->total_stok_masuk,
                'total_retur' => (int) $item->total_retur,
                'stok_keluar_netto' => $stokKeluar,
                'return_rate' => round($returnRate, 2)
            ];
        })->sortBy('return_rate')->values()->all();

        return view('cse.view_performa', [
            'performaData' => $finalData,
            'startDate' => $queryStartDate, 
            'endDate' => $queryEndDate, 
            'userRegion' => $userRegion,
            'isFiltered' => $isFiltered, // Mengontrol visibility konten
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

    /**
     * Halaman kritik & saran untuk DSE
     */
    public function kritikSaran()
    {
        $userRegion = Auth::guard('shared')->user()->region;
        
        $dseList = User::where('role', 'DSE')
                      ->where('region', $userRegion)
                      ->get();
        
        return view('cse.kritik_saran', compact('dseList'));
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
        Feedback::create([
            'cse_id' => Auth::guard('shared')->user()->username,
            'dse_target' => $request->dse_target,
            'type' => strtolower($request->jenis_feedback),
            'message' => $request->feedback_text,
            'is_urgent' => false,
        ]);

        // Juga simpan di log sebagai backup
        Log::info('Feedback dari CSE', [
            'cse' => Auth::guard('shared')->user()->username,
            'dse_target' => $request->dse_target,
            'type' => $request->jenis_feedback,
            'message' => $request->feedback_text
        ]);

        return redirect()->route('cse.kritik_saran')
                         ->with('success', 'Kritik dan saran berhasil dikirim!');
    }

    // Di CSEController
public function exportOutletPdf(Request $request)
    {
        $outlets = Outlet::orderBy('name')->get(); 
        $region = Auth::guard('shared')->user()->region ?? 'Global';
        $title = "Daftar Outlet Aktif Regional {$region}";

        $pdf = Pdf::loadView('admin.export.outlet_pdf', compact('outlets', 'title')); 

        return $pdf->download('Daftar_Outlet_Aktif_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }
}