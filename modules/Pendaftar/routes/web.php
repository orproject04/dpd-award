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
        Route::get('pendaftar/{pendaftar}/download-all', [PendaftarController::class, 'downloadAllFiles'])->name('pendaftar.download-all');
        Route::post('pendaftar/{pendaftar}/status', [PendaftarController::class, 'updateStatus'])->name('pendaftar.update-status');
        Route::resource('pendaftar', PendaftarController::class);
    }
);
