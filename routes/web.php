<?php

use App\Http\Controllers\Admin\AlatController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DataPemeriksaanController;
use App\Http\Controllers\Admin\InventarisAlatController;
use App\Http\Controllers\Admin\JenisPemeriksaanController;
use App\Http\Controllers\Admin\LabkesmasController;
use App\Http\Controllers\Admin\StandarAlatController;
use App\Http\Controllers\Admin\Wilayah\KabupatenKotaController;
use App\Http\Controllers\Admin\Wilayah\NegaraController;
use App\Http\Controllers\Admin\Wilayah\ProvinsiController;
use App\Http\Controllers\Admin\Wilayah\RegionalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\StandarLabkesmasController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik (Inertia)
|--------------------------------------------------------------------------
*/
// Landing InPULS KEMENKES — pintu masuk pertama.
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Dua dashboard tujuan dari landing.
Route::get('/pemeriksaan', [DashboardController::class, 'index'])->name('pemeriksaan');
Route::get('/standar-labkesmas', [StandarLabkesmasController::class, 'index'])->name('standar-labkesmas');
Route::get('/list-labkesmas', [StandarLabkesmasController::class, 'list'])->name('list-labkesmas');
Route::get('/list-labkesmas/{labkesmas}', [StandarLabkesmasController::class, 'profile'])->name('list-labkesmas.profile');

/*
|--------------------------------------------------------------------------
| Endpoint JSON publik (dikonsumsi React via axios) — envelope ApiResponse
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard-data')->group(function () {
    Route::get('/summary', [DashboardController::class, 'summary'])->name('dashboard-data.summary');
    Route::get('/trend', [DashboardController::class, 'trend'])->name('dashboard-data.trend');
    Route::get('/trend-by-jenis', [DashboardController::class, 'trendByJenis'])->name('dashboard-data.trend-by-jenis');
    Route::get('/jenis-pemeriksaan', [DashboardController::class, 'jenisPemeriksaan'])->name('dashboard-data.jenis-pemeriksaan');
    Route::get('/trend-grouped', [DashboardController::class, 'trendGrouped'])->name('dashboard-data.trend-grouped');
    Route::get('/trend-multi-labkesmas', [DashboardController::class, 'trendMultiLabkesmas'])->name('dashboard-data.trend-multi-labkesmas');
    Route::get('/lab-pemeriksaan/{labkesmas}', [DashboardController::class, 'labPemeriksaan'])->name('dashboard-data.lab-pemeriksaan');
    Route::get('/wilayah/regional', [WilayahController::class, 'regional'])->name('dashboard-data.wilayah.regional');
    Route::get('/wilayah/provinsi', [WilayahController::class, 'provinsi'])->name('dashboard-data.wilayah.provinsi');
    Route::get('/wilayah/kabupaten-kota', [WilayahController::class, 'kabupatenKota'])->name('dashboard-data.wilayah.kabupaten-kota');
});

/*
|--------------------------------------------------------------------------
| Endpoint JSON publik — pemenuhan standar alat (envelope ApiResponse)
|--------------------------------------------------------------------------
*/
Route::prefix('standar-data')->name('standar-data.')->group(function () {
    Route::get('/lab/{labkesmas}', [StandarLabkesmasController::class, 'fulfillment'])->name('lab');
    Route::get('/agregat', [StandarLabkesmasController::class, 'aggregate'])->name('agregat');
    Route::get('/perbandingan', [StandarLabkesmasController::class, 'comparison'])->name('perbandingan');
    Route::get('/grouped', [StandarLabkesmasController::class, 'grouped'])->name('grouped');
    Route::get('/multi', [StandarLabkesmasController::class, 'multi'])->name('multi');
});

/*
|--------------------------------------------------------------------------
| Panel admin (Inertia) — dilindungi middleware `auth` (Fortify di slice berikutnya)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // Landing panel admin (tujuan redirect setelah login — lihat config/fortify.php 'home').
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Master Wilayah — CRUD terpisah per jenjang (index/store/update/destroy).
    Route::prefix('wilayah')->name('wilayah.')->group(function () {
        Route::get('/negara', [NegaraController::class, 'index'])->name('negara.index');
        Route::post('/negara', [NegaraController::class, 'store'])->name('negara.store');
        Route::put('/negara/{negara}', [NegaraController::class, 'update'])->name('negara.update');
        Route::delete('/negara/{negara}', [NegaraController::class, 'destroy'])->name('negara.destroy');

        Route::get('/regional', [RegionalController::class, 'index'])->name('regional.index');
        Route::post('/regional', [RegionalController::class, 'store'])->name('regional.store');
        Route::put('/regional/{regional}', [RegionalController::class, 'update'])->name('regional.update');
        Route::delete('/regional/{regional}', [RegionalController::class, 'destroy'])->name('regional.destroy');

        Route::get('/provinsi', [ProvinsiController::class, 'index'])->name('provinsi.index');
        Route::post('/provinsi', [ProvinsiController::class, 'store'])->name('provinsi.store');
        Route::put('/provinsi/{provinsi}', [ProvinsiController::class, 'update'])->name('provinsi.update');
        Route::delete('/provinsi/{provinsi}', [ProvinsiController::class, 'destroy'])->name('provinsi.destroy');

        Route::get('/kabupaten-kota', [KabupatenKotaController::class, 'index'])->name('kabupaten-kota.index');
        Route::post('/kabupaten-kota', [KabupatenKotaController::class, 'store'])->name('kabupaten-kota.store');
        Route::put('/kabupaten-kota/{kabupatenKota}', [KabupatenKotaController::class, 'update'])->name('kabupaten-kota.update');
        Route::delete('/kabupaten-kota/{kabupatenKota}', [KabupatenKotaController::class, 'destroy'])->name('kabupaten-kota.destroy');
    });

    // Master Jenis Pemeriksaan (tes)
    Route::get('/jenis-pemeriksaan', [JenisPemeriksaanController::class, 'index'])->name('jenis-pemeriksaan.index');
    Route::post('/jenis-pemeriksaan', [JenisPemeriksaanController::class, 'store'])->name('jenis-pemeriksaan.store');
    Route::put('/jenis-pemeriksaan/{jenisPemeriksaan}', [JenisPemeriksaanController::class, 'update'])->name('jenis-pemeriksaan.update');
    Route::delete('/jenis-pemeriksaan/{jenisPemeriksaan}', [JenisPemeriksaanController::class, 'destroy'])->name('jenis-pemeriksaan.destroy');

    // Pendaftaran Labkesmas
    Route::get('/labkesmas', [LabkesmasController::class, 'index'])->name('labkesmas.index');
    Route::post('/labkesmas', [LabkesmasController::class, 'store'])->name('labkesmas.store');
    Route::put('/labkesmas/{labkesmas}', [LabkesmasController::class, 'update'])->name('labkesmas.update');
    Route::delete('/labkesmas/{labkesmas}', [LabkesmasController::class, 'destroy'])->name('labkesmas.destroy');

    // Input data pemeriksaan bulanan (upsert)
    Route::get('/data-pemeriksaan', [DataPemeriksaanController::class, 'index'])->name('data-pemeriksaan.index');
    Route::post('/data-pemeriksaan', [DataPemeriksaanController::class, 'store'])->name('data-pemeriksaan.store');
    Route::delete('/data-pemeriksaan/{dataPemeriksaan}', [DataPemeriksaanController::class, 'destroy'])->name('data-pemeriksaan.destroy');

    // Master Alat & Standar Peralatan (katalog + standar per tier)
    Route::get('/alat', [AlatController::class, 'index'])->name('alat.index');
    Route::post('/alat', [AlatController::class, 'store'])->name('alat.store');
    Route::put('/alat/{alat}', [AlatController::class, 'update'])->name('alat.update');
    Route::delete('/alat/{alat}', [AlatController::class, 'destroy'])->name('alat.destroy');

    Route::post('/standar-alat', [StandarAlatController::class, 'store'])->name('standar-alat.store');
    Route::post('/alat/{alat}/standar', [StandarAlatController::class, 'sync'])->name('standar-alat.sync');
    Route::delete('/standar-alat/{standarAlat}', [StandarAlatController::class, 'destroy'])->name('standar-alat.destroy');

    // Input inventaris alat per Labkesmas (bulk upsert)
    Route::get('/inventaris-alat', [InventarisAlatController::class, 'index'])->name('inventaris-alat.index');
    Route::post('/inventaris-alat', [InventarisAlatController::class, 'store'])->name('inventaris-alat.store');
});
