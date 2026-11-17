<?php

use App\Http\Controllers\Admin\KegiatanEdukasiController as AdminKegiatanEdukasiController;
use App\Http\Controllers\Admin\KonselorController as AdminKonselorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KegiatanEdukasiController;
use App\Http\Controllers\KonselorProfilController;
use App\Http\Controllers\LaporanKasusController;
use App\Http\Controllers\PenangananKasusController;
use App\Http\Controllers\PendaftaranKegiatanController;
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

    /*
    |--------------------------------------------------------------------------
    | PBI-05: Manajemen Kegiatan/Webinar Edukasi (Admin Kasus)
    |--------------------------------------------------------------------------
    */
    Route::resource('kegiatan-edukasi', AdminKegiatanEdukasiController::class)
        ->parameters(['kegiatan-edukasi' => 'kegiatan']);

    Route::patch('kegiatan-edukasi/{kegiatan}/nonaktifkan', [AdminKegiatanEdukasiController::class, 'nonaktifkan'])
        ->name('kegiatan-edukasi.nonaktifkan');

    Route::patch('kegiatan-edukasi/{kegiatan}/pendaftaran/{pendaftaran}/verifikasi', [AdminKegiatanEdukasiController::class, 'verifikasiKehadiran'])
        ->name('kegiatan-edukasi.verifikasi-kehadiran');

    /*
    |--------------------------------------------------------------------------
    | PBI-13: Manajemen Data Konselor/Psikolog (Admin Kasus)
    |--------------------------------------------------------------------------
    */
    Route::resource('konselor', AdminKonselorController::class)
        ->parameters(['konselor' => 'konselor']);
});

/*
|--------------------------------------------------------------------------
| PBI-13: Profil Konselor/Psikolog (Konselor)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'konselor'])->prefix('konselor')->name('konselor.')->group(function () {
    Route::get('/profil', [KonselorProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [KonselorProfilController::class, 'update'])->name('profil.update');
});

/*
|--------------------------------------------------------------------------
| PBI-05: Kegiatan/Webinar Edukasi (Publik & Pelapor)
|--------------------------------------------------------------------------
*/
Route::controller(KegiatanEdukasiController::class)->prefix('edukasi/kegiatan')->name('kegiatan.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{kegiatan}', 'show')->name('show');
});

Route::middleware(['auth', 'pelapor'])->group(function () {
    Route::post('/edukasi/kegiatan/{kegiatan}/daftar', [PendaftaranKegiatanController::class, 'store'])
        ->name('kegiatan.daftar');

    Route::get('/pendaftaran-saya', [PendaftaranKegiatanController::class, 'index'])
        ->name('pendaftaran-kegiatan.index');

    Route::patch('/pendaftaran-saya/{pendaftaran}/batalkan', [PendaftaranKegiatanController::class, 'cancel'])
        ->name('pendaftaran-kegiatan.cancel');
});
