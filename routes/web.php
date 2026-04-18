<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use UniSharp\LaravelFilemanager\Lfm;

// Route for Drive Proxy & Home
Route::get('/proxy-drive-image/{id}', [\App\Http\Controllers\DriveProxyController::class, 'show']);
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes(['verify' => false]);

// Laravel Filemanager (Khusus Admin)
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['role:admin', 'auth']], function () {
    Lfm::routes();
});

/*
|--------------------------------------------------------------------------
| AREA ADMIN KHUSUS (Pengaturan Sistem & User)
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin', 'middleware' => ['role:admin', 'auth']], function () {
    Route::name('admin.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('/users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
        
        Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        
        Route::get('/profiles', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profiles.index');
        Route::put('/profiles', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profiles.update');
    });
});

/*
|--------------------------------------------------------------------------
| AREA SHARED & OPERASIONAL (Bisa diakses Admin & Kasir)
| Keterangan: Kasir biasanya memiliki role 'user'
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'dashboard', 'middleware' => ['auth']], function () {
    
    // Dashboard & Profil Kasir (Gunakan controller yang ada)
    Route::get('/', [App\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/profiles', [App\Http\Controllers\Dashboard\ProfileController::class, 'index'])->name('dashboard.profiles.index');
    Route::put('/profiles', [App\Http\Controllers\Dashboard\ProfileController::class, 'update'])->name('dashboard.profiles.update');

    // 1. TRANSAKSI
    // Kasir HANYA bisa jualan (out). Admin bisa dua-duanya (in & out).
    Route::get('/transaction/out', [App\Http\Controllers\Dashboard\TransactionController::class, 'createOut'])->name('dashboard.transaction.createOut');
    Route::post('/transaction/out', [App\Http\Controllers\Dashboard\TransactionController::class, 'storeOut'])->name('dashboard.transaction.storeOut');
    
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/transaction/in', [App\Http\Controllers\Dashboard\TransactionController::class, 'createIn'])->name('dashboard.transaction.createIn');
        Route::post('/transaction/in', [App\Http\Controllers\Dashboard\TransactionController::class, 'storeIn'])->name('dashboard.transaction.storeIn');
    });

    // 2. MASTER DATA (KATEGORI & PRODUK)
    // HANYA Admin yang bisa modifikasi (create, store, edit, update, destroy)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('categories', App\Http\Controllers\Dashboard\CategoryController::class)->except(['index', 'show'])->names('dashboard.categories');
        Route::resource('products', App\Http\Controllers\Dashboard\ProductController::class)->except(['index', 'show'])->names('dashboard.products');
    });
    // Semua role bisa melihat (index, show)
    Route::resource('categories', App\Http\Controllers\Dashboard\CategoryController::class)->only(['index', 'show'])->names('dashboard.categories');
    Route::resource('products', App\Http\Controllers\Dashboard\ProductController::class)->only(['index', 'show'])->names('dashboard.products');
    

    // 3. PRODUCT BATCHES (Stock Opname / Pemusnahan)
    // HANYA Admin yang boleh melakukan penyesuaian stok
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('batches', App\Http\Controllers\Dashboard\ProductBatchController::class)->names('dashboard.batches');
    });

    // 4. LAPORAN (REPORTS)
    Route::prefix('reports')->name('dashboard.reports.')->group(function () {
        
        // Laporan Stok (Bisa dilihat Kasir)
        Route::get('/stock', [App\Http\Controllers\Dashboard\ReportController::class, 'stockReport'])->name('stock');
        Route::get('/stock/export', [App\Http\Controllers\Dashboard\ReportController::class, 'exportStock'])->name('exportStock');
        Route::get('/stock/pdf', [App\Http\Controllers\Dashboard\ReportController::class, 'exportStockPdf'])->name('exportStockPdf');
        
        // Laporan Transaksi (Khusus Admin)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/incoming', [App\Http\Controllers\Dashboard\ReportController::class, 'incomingReport'])->name('incoming');
            Route::get('/incoming/export', [App\Http\Controllers\Dashboard\ReportController::class, 'exportIncoming'])->name('exportIncoming');
            Route::get('/incoming/pdf', [App\Http\Controllers\Dashboard\ReportController::class, 'exportIncomingPdf'])->name('exportIncomingPdf');
            
            Route::get('/outgoing', [App\Http\Controllers\Dashboard\ReportController::class, 'outgoingReport'])->name('outgoing');
            Route::get('/outgoing/export', [App\Http\Controllers\Dashboard\ReportController::class, 'exportOutgoing'])->name('exportOutgoing');
            Route::get('/outgoing/pdf', [App\Http\Controllers\Dashboard\ReportController::class, 'exportOutgoingPdf'])->name('exportOutgoingPdf');
        });
    });
});