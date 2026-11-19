<?php

use App\Http\Controllers\Admin\KegiatanEdukasiController as AdminKegiatanEdukasiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KegiatanEdukasiController;
use App\Http\Controllers\LaporanKasusController;
use App\Http\Controllers\PenangananKasusController;
use App\Http\Controllers\PendaftaranKegiatanController;
use App\Http\Controllers\ProfilPelaporController;
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

/*
|--------------------------------------------------------------------------
| Pelacakan Publik Laporan Kasus (Tanpa Login - FR-14)
|--------------------------------------------------------------------------
*/
Route::controller(LaporanKasusController::class)->group(function () {
    Route::get('/lacak', 'trackForm')->name('lacak.form');
    Route::get('/lacak/hasil', 'trackResult')->name('lacak.hasil');
});

/*
|--------------------------------------------------------------------------
| PBI-16 & PBI-17: Modul Pelaporan Kasus, Lampiran, dan Profil Pelapor
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'pelapor'])->group(function () {
    // PBI-17: Manajemen Mode Anonim & Profil Pelapor
    Route::controller(ProfilPelaporController::class)->prefix('profil-pelapor')->name('profil-pelapor.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
    });

    // PBI-16: Manajemen Laporan Kasus (CRUD lengkap, mempertahankan nama rute eksisting)
    Route::controller(LaporanKasusController::class)->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/sukses', 'success')->name('success');
        Route::get('/{laporan}', 'show')->name('show');
        Route::get('/{laporan}/edit', 'edit')->name('edit');
        Route::put('/{laporan}', 'update')->name('update');
        Route::delete('/{laporan}', 'destroy')->name('destroy');

        // Lampiran Bukti Privat
        Route::get('/lampiran/{lampiran}/download', 'downloadLampiran')->name('lampiran.download');
        Route::delete('/lampiran/{lampiran}', 'destroyLampiran')->name('lampiran.destroy');
    });

    // PBI-05: Pendaftaran Kegiatan oleh Pelapor (Eksisting)
    Route::post('/edukasi/kegiatan/{kegiatan}/daftar', [PendaftaranKegiatanController::class, 'store'])
        ->name('kegiatan.daftar');

    Route::get('/pendaftaran-saya', [PendaftaranKegiatanController::class, 'index'])
        ->name('pendaftaran-kegiatan.index');

    Route::patch('/pendaftaran-saya/{pendaftaran}/batalkan', [PendaftaranKegiatanController::class, 'cancel'])
        ->name('pendaftaran-kegiatan.cancel');
});

/*
|--------------------------------------------------------------------------
| PBI-04 & PBI-05: Modul Admin Kasus
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin.kasus'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('status-kasus', StatusKasusController::class)
        ->parameters(['status-kasus' => 'statusKasus'])
        ->except(['show']);

    Route::resource('penanganan-kasus', PenangananKasusController::class)
        ->parameters(['penanganan-kasus' => 'penangananKasus']);

    Route::resource('kegiatan-edukasi', AdminKegiatanEdukasiController::class)
        ->parameters(['kegiatan-edukasi' => 'kegiatan']);

    Route::patch('kegiatan-edukasi/{kegiatan}/nonaktifkan', [AdminKegiatanEdukasiController::class, 'nonaktifkan'])
        ->name('kegiatan-edukasi.nonaktifkan');

    Route::patch('kegiatan-edukasi/{kegiatan}/pendaftaran/{pendaftaran}/verifikasi', [AdminKegiatanEdukasiController::class, 'verifikasiKehadiran'])
        ->name('kegiatan-edukasi.verifikasi-kehadiran');
});

/*
|--------------------------------------------------------------------------
| PBI-05: Kegiatan/Webinar Edukasi (Publik)
|--------------------------------------------------------------------------
*/
Route::controller(KegiatanEdukasiController::class)->prefix('edukasi/kegiatan')->name('kegiatan.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{kegiatan}', 'show')->name('show');
});