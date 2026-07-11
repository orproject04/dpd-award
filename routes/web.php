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

Route::get('/', LandingController::class)->name('landing');
Route::view('/nominasi', 'nominasi')->name('nominasi');
Route::post('/nominasi', [\App\Http\Controllers\NominasiController::class, 'store'])->middleware('throttle:3,1')->name('nominasi.store');
Route::post('/track', [LandingController::class, 'track'])->middleware('throttle:5,1')->name('track');

Route::middleware(['auth', 'verified'])->group(fn () => Route::get('/home', HomeController::class)->name('home'));

Route::get('/admin', [LoginController::class, 'show'])->name('admin.login');

include __DIR__.'/auth.php';
include __DIR__.'/my.php';
