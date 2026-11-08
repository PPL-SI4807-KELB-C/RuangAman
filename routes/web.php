<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanKasusController;
use App\Http\Controllers\PenangananKasusController;
use App\Http\Controllers\StatusKasusController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware('auth')->controller(LaporanKasusController::class)->group(function () {
    Route::get('/laporan/create', 'create')->name('laporan.create');
    Route::post('/laporan', 'store')->name('laporan.store');
    Route::get('/laporan/sukses', 'success')->name('laporan.success');
});

Route::controller(LaporanKasusController::class)->group(function () {
    Route::get('/lacak', 'trackForm')->name('lacak.form');
    Route::get('/lacak/hasil', 'trackResult')->name('lacak.hasil');
});

/*
|--------------------------------------------------------------------------
| PBI-04: Modul Manajemen Status dan Penugasan Kasus (Admin Kasus)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin.kasus'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('status-kasus', StatusKasusController::class)
        ->parameters(['status-kasus' => 'statusKasus'])
        ->except(['show']);

    Route::resource('penanganan-kasus', PenangananKasusController::class)
        ->parameters(['penanganan-kasus' => 'penangananKasus']);
});
