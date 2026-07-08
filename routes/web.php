<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes (belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest.redirect')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (sudah login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth.custom')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard: satu route, tapi tampilan beda tergantung role (lihat DashboardController).
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Khusus Admin: CRUD kategori, barang, dan laporan
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        // CRUD kategori barang
        Route::resource('kategori', KategoriController::class)->except(['show']);

        // CRUD barang + upload gambar ke storage
        Route::resource('barang', BarangController::class);

        // Laporan chart tren penjualan + export PDF (dengan chart sebagai gambar)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');

            // Mode default: chart PNG dibuat di storage lalu dihapus lagi setelah PDF jadi (cleanup).
            Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');

            // Mode arsip: chart PNG dibuat di storage dan TIDAK dihapus (tidak ada cleanup),
            // supaya bisa disimpan/diakses lagi nanti. Perlu dibersihkan manual/berkala.
            Route::get('/export-pdf-archive', [ReportController::class, 'exportPdfArchive'])->name('export-pdf-archive');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Khusus Kasir: input transaksi penjualan
    |----------------------------------------------------------------------
    */
    Route::middleware('role:kasir')->group(function () {
        Route::resource('transaksi', TransaksiController::class);
    });
});
