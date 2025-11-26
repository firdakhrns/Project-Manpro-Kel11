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

        // View Routes
        Route::get('/view-stok', [AdminController::class, 'viewStok'])->name('admin.view_stok');
        Route::get('/view-retur', [AdminController::class, 'viewRetur'])->name('admin.view_retur');
        Route::get('/view-outlet', [AdminController::class, 'viewOutlet'])->name('admin.view_outlet');
        Route::get('/riwayat-pencatatan', [AdminController::class, 'riwayatPencatatan'])->name('admin.riwayat_pencatatan');

        // CRUD Stok Routes
        Route::get('/stok/create', [AdminController::class, 'createStok'])->name('admin.stok.create');
        Route::post('/stok/store', [AdminController::class, 'storeStok'])->name('admin.stok.store');
        Route::get('/stok/{id}/edit', [AdminController::class, 'editStok'])->name('admin.stok.edit');
        Route::put('/stok/{id}/update', [AdminController::class, 'updateStok'])->name('admin.stok.update');
        Route::delete('/stok/{id}/delete', [AdminController::class, 'deleteStok'])->name('admin.stok.delete');

        // CRUD Outlet Routes
        Route::get('/outlet/create', [AdminController::class, 'createOutlet'])->name('admin.outlet.create');
        Route::post('/outlet/store', [AdminController::class, 'storeOutlet'])->name('admin.outlet.store');
        Route::get('/outlet/{id}/edit', [AdminController::class, 'editOutlet'])->name('admin.outlet.edit');
        Route::put('/outlet/{id}/update', [AdminController::class, 'updateOutlet'])->name('admin.outlet.update');
        Route::delete('/outlet/{id}/delete', [AdminController::class, 'deleteOutlet'])->name('admin.outlet.delete');

        Route::get('/retur/create', [AdminController::class, 'createRetur'])->name('admin.retur.create');
    Route::post('/retur/store', [AdminController::class, 'storeRetur'])->name('admin.retur.store');
    Route::get('/retur/{id}/edit', [AdminController::class, 'editRetur'])->name('admin.retur.edit');
    Route::put('/retur/{id}/update', [AdminController::class, 'updateRetur'])->name('admin.retur.update');
    Route::delete('/retur/{id}/delete', [AdminController::class, 'deleteRetur'])->name('admin.retur.delete');

        // Retur Status Routes
        Route::put('/retur/{id}/status', [AdminController::class, 'updateReturStatus'])->name('admin.retur.status');

        // Export Routes
        Route::get('/export-stok', [AdminController::class, 'exportStok'])->name('admin.export.stok');
        Route::get('/export-retur', [AdminController::class, 'exportRetur'])->name('admin.export.retur');
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
        Route::get('/kritik-saran', [CSEController::class, 'kritikSaran'])->name('cse.kritik_saran');
    Route::post('/kritik-saran', [CSEController::class, 'storeKritikSaran'])->name('cse.kritik_saran.store');
    });
});

// Logout universal
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');