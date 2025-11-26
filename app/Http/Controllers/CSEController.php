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

class CSEController extends Controller
{
    /**
     * View stok semua DSE di region CSE
     */
    /**
 * View stok semua DSE di region CSE
 */
/**
 * View stok semua DSE di region CSE dengan filter
 */
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
                $pivotData[$dseId] = array_fill_keys($productHeaders, 0);
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
        $userRegion = Auth::guard('shared')->user()->region;
        
        $outlets = Outlet::where('region', $userRegion)
                        ->with(['stockLogs', 'salesLogs'])
                        ->orderBy('name')
                        ->get();
        
        return view('cse.view_outlet', compact('outlets'));
    }

    /**
     * View performa DSE di region CSE
     */
    public function viewPerforma(Request $request)
    {
        $userRegion = Auth::guard('shared')->user()->region; // ✅ PERBAIKI: Auth::guard('shared')
        
        // 1. Ambil Filter
        $startDate = $request->input('start_date', Carbon::today()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        
        // 2. Query Data Agregat (Total Stok Masuk vs. Total Retur)
        $performanceData = DB::table('stock_logs')
            ->select(
                'stock_logs.username_id as dse_id',
                DB::raw('SUM(stock_log_items.quantity) as total_stok_masuk'),
                DB::raw('COALESCE(SUM(return_log_items.quantity), 0) as total_retur')
            )
            ->join('stock_log_items', 'stock_logs.id', '=', 'stock_log_items.stock_log_id')
            
            // LEFT JOIN dengan Retur untuk mendapatkan 0 jika tidak ada retur
            ->leftJoin('return_logs', function ($join) use ($startDate, $endDate) {
                $join->on('stock_logs.username_id', '=', 'return_logs.username_id')
                     ->whereDate('return_logs.date', '>=', $startDate)
                     ->whereDate('return_logs.date', '<=', $endDate);
            })
            ->leftJoin('return_log_items', 'return_logs.id', '=', 'return_log_items.return_log_id')
            
            // Filter Regional dan Periode
            ->join('users', 'stock_logs.username_id', '=', 'users.id_dse')
            ->where('users.region', $userRegion)
            ->whereDate('stock_logs.date', '>=', $startDate)
            ->whereDate('stock_logs.date', '<=', $endDate)
            
            ->groupBy('stock_logs.username_id')
            ->orderBy('total_retur', 'asc') // Urutkan DSE dengan retur terendah di atas
            ->get();

        // 3. Hitung Rasio Retur dan Finalisasi Data
        $finalData = $performanceData->map(function ($item) {
            $stokKeluar = $item->total_stok_masuk - $item->total_retur; // Total produk yang berhasil didistribusikan (dijual/diberikan)
            
            // Rasio Retur = (Total Retur / Total Stok Masuk) * 100
            $returnRate = $item->total_stok_masuk > 0 
                          ? ($item->total_retur / $item->total_stok_masuk) * 100
                          : 0;

            return [
                'dse_id' => $item->dse_id,
                'total_stok_masuk' => (int) $item->total_stok_masuk,
                'total_retur' => (int) $item->total_retur,
                'stok_keluar_netto' => $stokKeluar,
                'return_rate' => round($returnRate, 2) // Persentase Retur
            ];
        })->sortBy('return_rate')->values()->all(); // Urutkan lagi by rate

        return view('cse.view_performa', [
            'performaData' => $finalData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'userRegion' => $userRegion,
        ]);
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
}