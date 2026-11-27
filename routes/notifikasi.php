<?php

use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PBI-18: Manajemen Notifikasi Sistem (Semua peran yang login)
|--------------------------------------------------------------------------
| Dimuat oleh App\Providers\NotifikasiServiceProvider agar routes/web.php
| milik bersama tidak perlu diubah.
*/
Route::middleware('auth')->controller(NotifikasiController::class)->prefix('notifikasi')->name('notifikasi.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::patch('/baca-semua', 'tandaiSemuaDibaca')->name('baca-semua');
    Route::delete('/hapus-dibaca', 'hapusSudahDibaca')->name('hapus-dibaca');
    Route::get('/{notifikasi}', 'show')->name('show');
    Route::patch('/{notifikasi}/baca', 'tandaiDibaca')->name('baca');
    Route::patch('/{notifikasi}/belum-dibaca', 'tandaiBelumDibaca')->name('belum-dibaca');
    Route::delete('/{notifikasi}', 'destroy')->name('destroy');
});
