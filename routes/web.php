<?php

use App\Http\Controllers\Admin\ArtikelController as AdminArtikelController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\KategoriArtikelController as AdminKategoriArtikelController;
use App\Http\Controllers\Admin\GlosariumKbgController as AdminGlosariumKbgController;
use App\Http\Controllers\Admin\HalamanInformasiController as AdminHalamanInformasiController;
use App\Http\Controllers\Admin\KegiatanEdukasiController as AdminKegiatanEdukasiController;
use App\Http\Controllers\Admin\KonselorController as AdminKonselorController;
use App\Http\Controllers\Admin\PengumumanController as AdminPengumumanController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GlosariumController;
use App\Http\Controllers\HalamanInformasiPublikController;
use App\Http\Controllers\InstansiBantuanController;
use App\Http\Controllers\KontakBantuanController;
use App\Http\Controllers\LayananBantuanController;
use App\Http\Controllers\KegiatanEdukasiController;
use App\Http\Controllers\KonselorProfilController;
use App\Http\Controllers\LaporanKasusController;
use App\Http\Controllers\LogPenangananController;
use App\Http\Controllers\PenangananKasusController;
use App\Http\Controllers\PengumumanPublikController;
use App\Http\Controllers\PendaftaranKegiatanController;
use App\Http\Controllers\ProfilPelaporController;
use App\Http\Controllers\StatusKasusController;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $pengumumanTerbaru = Pengumuman::query()
        ->tayang()
        ->orderByDesc('mulai_tayang')
        ->limit(3)
        ->get();

    return view('welcome', ['pengumumanTerbaru' => $pengumumanTerbaru]);
})->name('welcome');

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');
});

// Pelaporan Kasus: khusus Pelapor yang sudah login (Tabel 3.1.2 & alur Gambar 2.3.2).
// Mode anonim menyembunyikan identitas dari konselor, bukan berarti tanpa akun.
Route::middleware(['auth', 'pelapor'])->controller(LaporanKasusController::class)->group(function () {
    Route::get('/laporan/create', 'create')->name('laporan.create');
    Route::post('/laporan', 'store')->name('laporan.store');
    Route::get('/laporan/sukses', 'success')->name('laporan.success');
});

// Draf dari mini-form beranda: dikirim lewat POST dan disimpan di session server,
// supaya kronologi tidak pernah masuk ke URL, riwayat peramban, atau log server.
Route::post('/laporan/draf', [LaporanKasusController::class, 'simpanDraf'])
    ->middleware('throttle:lacak')
    ->name('laporan.draf');

// Pelacakan status tetap publik (FR-14: memantau lewat kode acak tanpa login).
// Kode dikirim lewat POST dan disimpan di session agar tidak tercatat di URL/riwayat peramban.
Route::controller(LaporanKasusController::class)->group(function () {
    Route::get('/lacak', 'trackForm')->name('lacak.form');
    Route::get('/lacak/hasil', 'trackResult')->name('lacak.hasil');

    Route::middleware('throttle:lacak')->group(function () {
        Route::post('/lacak', 'cariLaporan')->name('lacak.cari');
        Route::post('/lacak/pesan', 'kirimPesan')->name('lacak.pesan');
        Route::post('/lacak/tautkan', 'tautkanAkun')->name('lacak.tautkan');
    });
});

/*
|--------------------------------------------------------------------------
| FR-02/FR-04/FR-05/FR-06: Dashboard sesuai role
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| PBI-16 & PBI-17: Manajemen Laporan Kasus, Lampiran, dan Profil Pelapor
|--------------------------------------------------------------------------
| Didaftarkan sesudah /laporan/create dan /laporan/sukses agar rute statis
| itu tidak tertangkap oleh /laporan/{laporan}.
*/
Route::middleware(['auth', 'pelapor'])->group(function () {
    // PBI-17: Manajemen Mode Anonim & Profil Pelapor
    Route::controller(ProfilPelaporController::class)->prefix('profil-pelapor')->name('profil-pelapor.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
    });

    // PBI-16: Manajemen Laporan Kasus (Detail, Edit, Batalkan, & Lampiran Privat)
    Route::controller(LaporanKasusController::class)->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{laporan}', 'show')->name('show');
        Route::get('/{laporan}/edit', 'edit')->name('edit');
        Route::put('/{laporan}', 'update')->name('update');
        Route::delete('/{laporan}', 'destroy')->name('destroy');

        // Lampiran Bukti Privat
        Route::get('/lampiran/{lampiran}/download', 'downloadLampiran')->name('lampiran.download');
        Route::delete('/lampiran/{lampiran}', 'destroyLampiran')->name('lampiran.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| PBI-07: Direktori Instansi Bantuan Publik (Dapat diakses tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/instansi-bantuan', [InstansiBantuanController::class, 'publicDirectory'])->name('instansi.publik');
Route::get('/instansi-bantuan/{instansi}', [InstansiBantuanController::class, 'publicShow'])->name('instansi.publik.show');

/*
|--------------------------------------------------------------------------
| PBI-07: Manajemen Instansi Bantuan (CRUD Lengkap, khusus Admin Kasus)
|--------------------------------------------------------------------------
| Tersedia via /instansi dan /admin/instansi untuk kompatibilitas penuh.
| Keduanya wajib lewat middleware admin.kasus agar Pelapor/Konselor tidak
| bisa menambah, mengubah, atau menghapus direktori instansi.
*/
Route::middleware(['auth', 'admin.kasus'])->controller(InstansiBantuanController::class)->group(function () {
    Route::get('/instansi', 'index')->name('instansi.index');
    Route::get('/instansi/create', 'create')->name('instansi.create');
    Route::post('/instansi', 'store')->name('instansi.store');
    Route::get('/instansi/{instansi}', 'show')->name('instansi.show');
    Route::get('/instansi/{instansi}/edit', 'edit')->name('instansi.edit');
    Route::put('/instansi/{instansi}', 'update')->name('instansi.update');
    Route::delete('/instansi/{instansi}', 'destroy')->name('instansi.destroy');
});

/*
|--------------------------------------------------------------------------
| PBI-08: Layanan & Kontak Bantuan (Root routes, nested under instansi)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin.kasus'])->prefix('instansi/{instansi}')->name('instansi.')->group(function () {
    Route::resource('layanan', LayananBantuanController::class)
        ->parameters(['layanan' => 'layanan']);

    Route::resource('kontak', KontakBantuanController::class)
        ->parameters(['kontak' => 'kontak']);
});

/*
|--------------------------------------------------------------------------
| PBI-04, PBI-05, PBI-07, PBI-10, PBI-11, PBI-13: Modul Admin Kasus
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin.kasus'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('status-kasus', StatusKasusController::class)
        ->parameters(['status-kasus' => 'statusKasus']);

    Route::resource('penanganan-kasus', PenangananKasusController::class)
        ->parameters(['penanganan-kasus' => 'penangananKasus']);

    Route::resource('kegiatan-edukasi', AdminKegiatanEdukasiController::class)
        ->parameters(['kegiatan-edukasi' => 'kegiatan']);

    Route::patch('kegiatan-edukasi/{kegiatan}/nonaktifkan', [AdminKegiatanEdukasiController::class, 'nonaktifkan'])
        ->name('kegiatan-edukasi.nonaktifkan');

    Route::patch('kegiatan-edukasi/{kegiatan}/pendaftaran/{pendaftaran}/kehadiran', [AdminKegiatanEdukasiController::class, 'verifikasiKehadiran'])
        ->name('kegiatan-edukasi.verifikasi-kehadiran');
    Route::patch('kegiatan-edukasi/{kegiatan}/pendaftaran/{pendaftaran}/verifikasi', [AdminKegiatanEdukasiController::class, 'verifikasiKehadiran']);

    Route::resource('konselor', AdminKonselorController::class)
        ->parameters(['konselor' => 'konselor']);

    Route::resource('artikel', AdminArtikelController::class)
        ->parameters(['artikel' => 'artikel']);

    // PBI-11: Manajemen Kategori Artikel (tanpa halaman detail terpisah).
    Route::resource('kategori-artikel', AdminKategoriArtikelController::class)
        ->parameters(['kategori-artikel' => 'kategori'])
        ->except(['show']);

    Route::resource('instansi', InstansiBantuanController::class)
        ->parameters(['instansi' => 'instansi']);

    /*
    |--------------------------------------------------------------------------
    | PBI-08: Layanan & Kontak Bantuan (nested under instansi)
    |--------------------------------------------------------------------------
    */
    Route::prefix('instansi/{instansi}')->name('instansi.')->group(function () {
        Route::resource('layanan', LayananBantuanController::class)
            ->parameters(['layanan' => 'layanan']);

        Route::resource('kontak', KontakBantuanController::class)
            ->parameters(['kontak' => 'kontak']);
    });

    /*
    |--------------------------------------------------------------------------
    | PBI-01: Manajemen Halaman Informasi Statis (Admin Kasus)
    |--------------------------------------------------------------------------
    */
    Route::resource('halaman-informasi', AdminHalamanInformasiController::class)
        ->parameters(['halaman-informasi' => 'halamanInformasi']);

    /*
    |--------------------------------------------------------------------------
    | PBI-02: Manajemen Glosarium Istilah KBG (Admin Kasus)
    |--------------------------------------------------------------------------
    */
    Route::resource('glosarium', AdminGlosariumKbgController::class)
        ->parameters(['glosarium' => 'glosarium'])
        ->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | PBI-03: Manajemen Pengumuman (Admin Kasus)
    |--------------------------------------------------------------------------
    */
    Route::resource('pengumuman', AdminPengumumanController::class)
        ->parameters(['pengumuman' => 'pengumuman']);

    /*
    |--------------------------------------------------------------------------
    | PBI-09: Manajemen FAQ / Pusat Bantuan (Admin Kasus)
    |--------------------------------------------------------------------------
    */
    Route::resource('faq', AdminFaqController::class)
        ->parameters(['faq' => 'faq']);
});

/*
|--------------------------------------------------------------------------
| PBI-13: Profil Mandiri Konselor
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'konselor'])->prefix('konselor')->name('konselor.')->group(function () {
    Route::get('/profil', [KonselorProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [KonselorProfilController::class, 'update'])->name('profil.update');
});

/*
|--------------------------------------------------------------------------
| PBI-05: Modul Kegiatan Edukasi Publik & Pendaftaran Mandiri
|--------------------------------------------------------------------------
*/
Route::controller(KegiatanEdukasiController::class)->group(function () {
    Route::get('/edukasi', 'index')->name('kegiatan.index');
    Route::get('/edukasi/{kegiatan}', 'show')->name('kegiatan.show');
});

/*
|--------------------------------------------------------------------------
| PBI-10: Artikel Edukasi Publik
|--------------------------------------------------------------------------
*/
Route::controller(ArtikelController::class)->prefix('artikel')->name('artikel.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{slug}', 'show')->name('show');
});

Route::middleware(['auth', 'pelapor'])->group(function () {
    Route::post('/edukasi/{kegiatan}/daftar', [PendaftaranKegiatanController::class, 'store'])
        ->name('kegiatan.daftar');

    Route::get('/pendaftaran-saya', [PendaftaranKegiatanController::class, 'index'])
        ->name('pendaftaran-kegiatan.index');

    Route::patch('/pendaftaran-saya/{pendaftaran}/batalkan', [PendaftaranKegiatanController::class, 'cancel'])
        ->name('pendaftaran-kegiatan.cancel');
});

/*
|--------------------------------------------------------------------------
| PBI-02: Glosarium Istilah KBG (Publik)
|--------------------------------------------------------------------------
*/
Route::get('/glosarium', [GlosariumController::class, 'index'])->name('glosarium.index');

/*
|--------------------------------------------------------------------------
| PBI-01: Halaman Informasi Statis (Publik, diakses lewat slug)
|--------------------------------------------------------------------------
*/
Route::get('/halaman/{slug}', [HalamanInformasiPublikController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-_]+')
    ->name('halaman.show');

/*
|--------------------------------------------------------------------------
| PBI-06: Log Penanganan Kasus (Admin Kasus & Konselor)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'petugas.kasus'])->group(function () {
    Route::resource('log-penanganan', LogPenangananController::class)
        ->parameters(['log-penanganan' => 'logPenanganan']);
});

/*
|--------------------------------------------------------------------------
| PBI-03: Pengumuman (Publik, hanya dalam periode tayang)
|--------------------------------------------------------------------------
*/
Route::controller(PengumumanPublikController::class)->prefix('pengumuman')->name('pengumuman.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{pengumuman}', 'show')->name('show');
});

/*
|--------------------------------------------------------------------------
| PBI-09: Pusat Bantuan / FAQ (Publik, tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/pusat-bantuan', [FaqController::class, 'index'])->name('faq.index');
