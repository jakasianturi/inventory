<?php

use App\Models\Seleksi;
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
    });
});