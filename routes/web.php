<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', LandingController::class)->middleware('cacheResponse')->name('landing');
Route::get('/daftar-berita', [LandingController::class, 'berita'])->name('berita.public');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', HomeController::class)->name('dashboard');
    Route::get('/home', fn() => redirect()->route('dashboard'))->name('home');
    Route::post('/clear-cache', [HomeController::class, 'clearCache'])->name('clear-cache');

    Route::resource('berita', \App\Http\Controllers\BeritaController::class)->parameters([
        'berita' => 'berita'
    ]);

    Route::post('/kategori-aspek/seed', [\App\Http\Controllers\KategoriAspekController::class, 'seedDefault'])->name('kategori-aspek.seed');

    Route::resource('kategori-aspek', \App\Http\Controllers\KategoriAspekController::class)->parameters([
        'kategori-aspek' => 'kategori_aspek'
    ]);

    Route::post('/pendaftar/generate-history', [\Modules\Pendaftar\Controllers\PendaftarController::class, 'generateHistory'])->name('pendaftar.generate-history')->middleware('can:*');
    Route::post('/pendaftar/riwayat/{riwayat}/keterangan', [\Modules\Pendaftar\Controllers\PendaftarController::class, 'updateRiwayatKeterangan'])->name('pendaftar.riwayat.update-keterangan');

    Route::prefix('storage-manager')->name('storage-manager.')->group(function () {
        Route::get('/', [\App\Http\Controllers\StorageManagerController::class, 'index'])->name('index');
        Route::get('/file', [\App\Http\Controllers\StorageManagerController::class, 'serveFile'])->name('file');
        Route::get('/download-zip', [\App\Http\Controllers\StorageManagerController::class, 'downloadZip'])->name('download-zip');
        Route::get('/bulk-download', [\App\Http\Controllers\StorageManagerController::class, 'bulkDownload'])->name('bulk-download');
        Route::post('/folder', [\App\Http\Controllers\StorageManagerController::class, 'createFolder'])->name('create-folder');
        Route::post('/upload', [\App\Http\Controllers\StorageManagerController::class, 'upload'])->name('upload');
        Route::post('/rename', [\App\Http\Controllers\StorageManagerController::class, 'rename'])->name('rename');
        Route::post('/move', [\App\Http\Controllers\StorageManagerController::class, 'move'])->name('move');
        Route::post('/extract-zip', [\App\Http\Controllers\StorageManagerController::class, 'extractZip'])->name('extract-zip');
        Route::delete('/item', [\App\Http\Controllers\StorageManagerController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [\App\Http\Controllers\StorageManagerController::class, 'bulkDelete'])->name('bulk-delete');
    });
});

Route::get('/nominasi', [\App\Http\Controllers\NominasiController::class, 'create'])->name('nominasi');
Route::post('/nominasi', [\App\Http\Controllers\NominasiController::class, 'store'])->middleware('throttle:150,1')->name('nominasi.store');
Route::get('/nominasi/{regId}/cetak-bukti', [\App\Http\Controllers\NominasiController::class, 'cetakBukti'])->name('nominasi.cetak-bukti');
Route::get('/track', function () {
    return redirect('/');
});
Route::post('/track', [LandingController::class, 'track'])->middleware('throttle:200,1')->name('track');

Route::get('/admin', [LoginController::class, 'show'])->name('admin.login');

include __DIR__ . '/auth.php';
include __DIR__ . '/my.php';
