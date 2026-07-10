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
        Route::resource('pendaftar', PendaftarController::class);
    }
);
