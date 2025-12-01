<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DSEInputController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CSEController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return view('auth.login');
});

// --- PUBLIC ROUTES ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// --- PROTECTED ROUTES ---
// DSE Routes
Route::middleware(['auth:web'])->group(function () {
    
    Route::prefix('dse')->group(function () {
        
        Route::get('/dashboard', function () {
            return view('dse.dashboard');
        })->name('dse.dashboard'); 
        
        // DSE Input Routes
        Route::get('/input-stok', [DSEInputController::class, 'showInputStok'])->name('dse.input_stok');
        Route::post('/input-stok', [DSEInputController::class, 'storeStok'])->name('dse.input_stok.store');
        
        Route::get('/input-retur', [DSEInputController::class, 'showInputRetur'])->name('dse.input_retur');
        Route::post('/input-retur', [DSEInputController::class, 'storeRetur'])->name('dse.input_retur.store');
        
        Route::get('/input-outlet', [DSEInputController::class, 'showInputOutlet'])->name('dse.input_outlet');
        Route::post('/input-outlet', [DSEInputController::class, 'storeOutlet'])->name('dse.input_outlet.store');
        
        Route::get('/riwayat-pencatatan', [DSEInputController::class, 'riwayatPencatatan'])->name('dse.riwayat_pencatatan');
    });
});

// Admin & CSE Routes (Shared Login)
Route::middleware(['auth:shared'])->group(function () {
    // Dashboard berdasarkan role
    Route::get('/shared-dashboard', function () {
        $user = Auth::guard('shared')->user();
        if ($user->role === 'Admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'CSE') {
            return redirect()->route('cse.dashboard');
        }
        abort(403, 'Unauthorized');
    })->name('shared.dashboard'); 
    
    // Route khusus Admin
    Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/view-stok', [AdminController::class, 'createStok'])->name('admin.view_stok');
    Route::post('/view-stok', [AdminController::class, 'storeStok'])->name('admin.view_stok.store');
    
    Route::get('/view-retur', [AdminController::class, 'createRetur'])->name('admin.view_retur');
    Route::post('/view-retur', [AdminController::class, 'storeRetur'])->name('admin.view_retur.store');

    Route::get('/riwayat-pencatatan', [AdminController::class, 'riwayatPencatatan'])->name('admin.riwayat_pencatatan');
    Route::get('/view-outlet', [AdminController::class, 'viewOutlet'])->name('admin.view_outlet');
    Route::get('/outlet/create', [AdminController::class, 'createOutlet'])->name('admin.outlet.create');
    Route::post('/outlet/store', [AdminController::class, 'storeOutlet'])->name('admin.outlet.store');
    Route::get('/outlet/{id}/edit', [AdminController::class, 'editOutlet'])->name('admin.outlet.edit');
    Route::put('/outlet/{id}/update', [AdminController::class, 'updateOutlet'])->name('admin.outlet.update');
    Route::delete('/outlet/{id}/delete', [AdminController::class, 'deleteOutlet'])->name('admin.outlet.delete');
    Route::get('/export-stok', [AdminController::class, 'exportStok'])->name('admin.export.stok');
    Route::get('/export-retur', [AdminController::class, 'exportRetur'])->name('admin.export.retur');
    Route::get('/outlet-pdf', [AdminController::class, 'exportOutletPdf'])->name('admin.export.outlet_pdf');
    Route::get('/outlet/{id}/detail', [AdminController::class, 'showOutletDetail'])->name('admin.outlet.detail');
    Route::get('/outlet/detail/{id}/pdf', [AdminController::class, 'exportOutletDetailPdf'])->name('admin.export.outlet_detail_pdf');
});
    
    // Route khusus CSE
    Route::prefix('cse')->group(function () {
    Route::get('/dashboard', function () {
        return view('cse.dashboard');
    })->name('cse.dashboard');
    
    Route::get('/view-stok', [CSEController::class, 'viewStok'])->name('cse.view_stok');
    Route::get('/view-retur', [CSEController::class, 'viewRetur'])->name('cse.view_retur');
    Route::get('/view-outlet', [CSEController::class, 'viewOutlet'])->name('cse.view_outlet');
    Route::get('/view-performa', [CSEController::class, 'viewPerforma'])->name('cse.view_performa');
    
    // === KRITIK SARAN ROUTES - HANYA INI YANG ADA ===
    Route::get('/kritik-saran', [CSEController::class, 'kritikSaran'])->name('cse.kritik_saran');
    Route::get('/kritik-saran/input', [CSEController::class, 'showInputKritikSaran'])->name('cse.kritik_saran.input');
    Route::post('/kritik-saran', [CSEController::class, 'storeKritikSaran'])->name('cse.kritik_saran.store');
    Route::get('/kritik-saran/hasil', [CSEController::class, 'showHasilKritikSaran'])->name('cse.kritik_saran.hasil');
    Route::get('/kritik-saran/export-pdf', [CSEController::class, 'exportKritikSaranPDF'])->name('cse.kritik_saran.export.pdf');
    
    // EXPORT OUTLET
    Route::get('/export-outlet', [CSEController::class, 'exportOutletPdf'])->name('cse.export.outlet_pdf');
    Route::get('/outlet/{id}/detail', [CSEController::class, 'showOutletDetail'])->name('cse.outlet.detail');   
    Route::get('/outlet/{id}/edit', [CSEController::class, 'editOutlet'])->name('cse.outlet.edit');
    Route::put('/outlet/{id}/update', [CSEController::class, 'updateOutlet'])->name('cse.outlet.update');
    Route::delete('/outlet/{id}/delete', [CSEController::class, 'deleteOutlet'])->name('cse.outlet.delete');
    Route::get('/outlet/detail/{id}/pdf', [CSEController::class, 'exportOutletDetailPdf'])->name('cse.export.outlet_detail_pdf');
});
});

// Logout universal
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');