<?php

use Illuminate\Support\Facades\Auth;
use UniSharp\LaravelFilemanager\Lfm;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route for Drive Proxy
Route::get('/proxy-drive-image/{id}', [\App\Http\Controllers\DriveProxyController::class, 'show']);

/**
 * Route for public
 */
// Home
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('/html', function () {
//     $hasil_seleksi = Seleksi::where('id', 1)->latest()->first();
//     return view('dashboard.hasil_seleksi.export',  compact('hasil_seleksi'));
// });

/**
 * Route for Auth
 * Registration not aviable for this website
 */
Auth::routes(['verify' => false]);

// Laravel Filemanager
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['role:admin', 'auth']], function () {
    Lfm::routes();
});

/**
 * Route for Admin
 */
Route::group(['prefix' => 'admin', 'middleware' => ['role:admin', 'auth']], function () {
    Route::name('admin.')->group(function () {
        // Dashboard
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        // Siswa
        Route::resource('/users', App\Http\Controllers\Admin\UserController::class)
            ->except(['show']);
        // Pengaturan Website
        Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        // Profil Admin
        Route::get('/profiles', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profiles.index');
        Route::put('/profiles', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profiles.update');
    });
});

/**
 * Route for User
 */
Route::group(['prefix' => 'dashboard', 'middleware' => ['role:user', 'auth']], function () {
    Route::name('dashboard.')->group(function () {
        // Dashboard
        Route::get('/', [App\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('index');
        // Profile User
        Route::get('/profiles', [App\Http\Controllers\Dashboard\ProfileController::class, 'index'])->name('profiles.index');
        Route::put('/profiles', [App\Http\Controllers\Dashboard\ProfileController::class, 'update'])->name('profiles.update');
        // Transaction
        Route::post('/transaction/in', [App\Http\Controllers\Dashboard\TransactionController::class, 'storeIn'])->name('transaction.storeIn');
        Route::post('/transaction/out', [App\Http\Controllers\Dashboard\TransactionController::class, 'storeOut'])->name('transaction.storeOut');
        // Product
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [App\Http\Controllers\Dashboard\ProductController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Dashboard\ProductController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Dashboard\ProductController::class, 'store'])->name('store');
            Route::get('/{product}', [App\Http\Controllers\Dashboard\ProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [App\Http\Controllers\Dashboard\ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [App\Http\Controllers\Dashboard\ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [App\Http\Controllers\Dashboard\ProductController::class, 'destroy'])->name('destroy');
        });
        // Product Batch
        Route::prefix('batches')->name('batches.')->group(function () {
            Route::get('/', [App\Http\Controllers\Dashboard\ProductBatchController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Dashboard\ProductBatchController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Dashboard\ProductBatchController::class, 'store'])->name('store');
            Route::get('/{batch}', [App\Http\Controllers\Dashboard\ProductBatchController::class, 'show'])->name('show');
            Route::get('/{batch}/edit', [App\Http\Controllers\Dashboard\ProductBatchController::class, 'edit'])->name('edit');
            Route::put('/{batch}', [App\Http\Controllers\Dashboard\ProductBatchController::class, 'update'])->name('update');
            Route::delete('/{batch}', [App\Http\Controllers\Dashboard\ProductBatchController::class, 'destroy'])->name('destroy');
        });
        // Category
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [App\Http\Controllers\Dashboard\CategoryController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Dashboard\CategoryController::class, 'create'])->name('categories.create');
            Route::post('/', [App\Http\Controllers\Dashboard\CategoryController::class, 'store'])->name('categories.store');
            Route::get('/{category}', [App\Http\Controllers\Dashboard\CategoryController::class, 'show'])->name('categories.show');
            Route::get('/{category}/edit', [App\Http\Controllers\Dashboard\CategoryController::class, 'edit'])->name('categories.edit');
            Route::put('/{category}', [App\Http\Controllers\Dashboard\CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/{category}', [App\Http\Controllers\Dashboard\CategoryController::class, 'destroy'])->name('categories.destroy');
        });
        // Report
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/stock', [App\Http\Controllers\Dashboard\ReportController::class, 'stockReport'])->name('stock');
            Route::get('/incoming', [App\Http\Controllers\Dashboard\ReportController::class, 'incomingReport'])->name('incoming');
            Route::get('/outgoing', [App\Http\Controllers\Dashboard\ReportController::class, 'outgoingReport'])->name('outgoing');
        });
    });
});