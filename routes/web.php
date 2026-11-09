<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstansiBantuanController;
use App\Http\Controllers\LaporanKasusController;
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

Route::middleware('auth')->controller(InstansiBantuanController::class)->group(function () {
    Route::get('/instansi', 'index')->name('instansi.index');
    Route::get('/instansi/create', 'create')->name('instansi.create');
    Route::post('/instansi', 'store')->name('instansi.store');
});

Route::controller(LaporanKasusController::class)->group(function () {
    Route::get('/lacak', 'trackForm')->name('lacak.form');
    Route::get('/lacak/hasil', 'trackResult')->name('lacak.hasil');
});
