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
        Route::get('pendaftar/download-all-zip', [PendaftarController::class, 'downloadAllZip'])->name('pendaftar.download-all-zip');
        Route::get('pendaftar/{pendaftar}/download-all', [PendaftarController::class, 'downloadAllFiles'])->name('pendaftar.download-all');
        Route::post('pendaftar/{pendaftar}/resend-email', [PendaftarController::class, 'resendEmail'])->name('pendaftar.resend-email');
        Route::post('pendaftar/{pendaftar}/status', [PendaftarController::class, 'updateStatus'])->name('pendaftar.update-status');
        Route::post('pendaftar/{pendaftar}/foto', [PendaftarController::class, 'updateFoto'])->name('pendaftar.update-foto');
        Route::post('pendaftar/{pendaftar}/ktp', [PendaftarController::class, 'updateKtp'])->name('pendaftar.update-ktp');
        Route::post('pendaftar/{pendaftar}/provinsi', [PendaftarController::class, 'updateProvinsi'])->name('pendaftar.update-provinsi');
        Route::resource('pendaftar', PendaftarController::class);
    }
);
