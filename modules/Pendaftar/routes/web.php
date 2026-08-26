<?php

use Illuminate\Support\Facades\Route;
use Modules\Pendaftar\Controllers\PendaftarController;

Route::group(
    [
        'prefix' => config('modules.pendaftar.routes.prefix'),
        'as' => 'modules::',
        'middleware' => config('modules.pendaftar.routes.middleware'),
    ],
    function () {
        Route::get('pendaftar/file', [PendaftarController::class, 'serveFile'])->name('pendaftar.file');
        Route::get('pendaftar/export', [PendaftarController::class, 'exportExcel'])->name('pendaftar.export');
        Route::get('pendaftar/template-keterangan', [PendaftarController::class, 'downloadTemplateKeterangan'])->name('pendaftar.template-keterangan');
        Route::post('pendaftar/import-keterangan', [PendaftarController::class, 'importKeterangan'])->name('pendaftar.import-keterangan');
        Route::get('pendaftar/download-all-zip', [PendaftarController::class, 'downloadAllZip'])->name('pendaftar.download-all-zip');
        Route::get('pendaftar/{pendaftar}/download-all', [PendaftarController::class, 'downloadAllFiles'])->name('pendaftar.download-all');
        Route::post('pendaftar/{pendaftar}/resend-email', [PendaftarController::class, 'resendEmail'])->name('pendaftar.resend-email');
        Route::post('pendaftar/{pendaftar}/status', [PendaftarController::class, 'updateStatus'])->name('pendaftar.update-status');
        Route::post('pendaftar/{pendaftar}/foto', [PendaftarController::class, 'updateFoto'])->name('pendaftar.update-foto');
        Route::post('pendaftar/{pendaftar}/ktp', [PendaftarController::class, 'updateKtp'])->name('pendaftar.update-ktp');
        Route::post('pendaftar/{pendaftar}/provinsi', [PendaftarController::class, 'updateProvinsi'])->name('pendaftar.update-provinsi');
        
        // Kontribusi & Penghargaan (Admin Management)
        Route::post('pendaftar/{pendaftar}/kontribusi', [PendaftarController::class, 'storeKontribusi'])->name('pendaftar.store-kontribusi');
        Route::put('pendaftar/{pendaftar}/kontribusi/{kontribusi}', [PendaftarController::class, 'updateKontribusi'])->name('pendaftar.update-kontribusi');
        Route::delete('pendaftar/{pendaftar}/kontribusi/{kontribusi}', [PendaftarController::class, 'destroyKontribusi'])->name('pendaftar.destroy-kontribusi');
        
        Route::post('pendaftar/{pendaftar}/penghargaan', [PendaftarController::class, 'storePenghargaan'])->name('pendaftar.store-penghargaan');
        Route::put('pendaftar/{pendaftar}/penghargaan/{penghargaan}', [PendaftarController::class, 'updatePenghargaan'])->name('pendaftar.update-penghargaan');
        Route::delete('pendaftar/{pendaftar}/penghargaan/{penghargaan}', [PendaftarController::class, 'destroyPenghargaan'])->name('pendaftar.destroy-penghargaan');

        // Penilaian Kertas Kerja
        Route::get('pendaftar/{pendaftar}/kertas-kerja', [PendaftarController::class, 'kertasKerja'])->name('pendaftar.kertas-kerja');
        Route::post('pendaftar/{pendaftar}/kertas-kerja', [PendaftarController::class, 'storeKertasKerja'])->name('pendaftar.store-kertas-kerja');
        Route::get('pendaftar/{pendaftar}/kertas-kerja/export-excel', [PendaftarController::class, 'exportKertasKerjaExcel'])->name('pendaftar.export-kertas-kerja-excel');
        
        Route::get('pendaftars/kertas-kerja/export-batch/init', [PendaftarController::class, 'exportBatchInit'])->name('pendaftar.export-batch-init');
        Route::post('pendaftars/kertas-kerja/export-batch/process', [PendaftarController::class, 'exportBatchProcess'])->name('pendaftar.export-batch-process');
        Route::get('pendaftars/kertas-kerja/export-batch/download', [PendaftarController::class, 'exportBatchDownload'])->name('pendaftar.export-batch-download');

        Route::resource('pendaftar', PendaftarController::class);
    }
);
