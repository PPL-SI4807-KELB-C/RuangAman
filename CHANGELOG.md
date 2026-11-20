# Changelog - RuangAman

Semua perubahan tercatat dalam format standar changelog.

---

## [PBI-01, PBI-02, PBI-03] - 2026-11-18

### Ditambahkan (Added)
- **Model & Relasi**:
  - `app/Models/HalamanInformasi.php`: Model tabel `halaman_informasi` (PK `halaman_id`) dengan konstanta status `draft`/`published`, scope `published()`, helper `isPublished()`, `statusLabel()`, dan `statusBadgeVariant()`.
  - `app/Models/GlosariumKbg.php`: Model tabel `glosarium_kbg` (PK `glosarium_id`) dengan scope `published()` dan helper `inisialHuruf()` untuk pengelompokan A–Z pada daftar publik.
  - `app/Models/Pengumuman.php`: Model tabel `pengumuman` (PK `pengumuman_id`) dengan relasi `pembuat()`, cast tanggal, scope `tayang()` (status aktif dan berada dalam periode), serta helper `sedangTayang()`, `statusTayangLabel()`, `statusLabel()`, dan `statusBadgeVariant()`.
- **Otorisasi & Keamanan**:
  - `app/Policies/HalamanInformasiPolicy.php`, `app/Policies/GlosariumKbgPolicy.php`, `app/Policies/PengumumanPolicy.php`: kemampuan `viewAny`, `view`, `create`, `update`, `delete`. Kemampuan `view` mengizinkan tamu membuka konten yang sudah dipublikasikan/tayang; pada `Pengumuman` kebijakan inilah yang menegakkan aturan "hanya tayang dalam periode" termasuk di halaman detail.
- **Form Request & Validasi (Bahasa Indonesia)**:
  - `app/Http/Requests/HalamanInformasiRequest.php` beserta `StoreHalamanInformasiRequest` dan `UpdateHalamanInformasiRequest`.
  - `app/Http/Requests/GlosariumKbgRequest.php` beserta `StoreGlosariumKbgRequest` dan `UpdateGlosariumKbgRequest`: keunikan `istilah` dengan pengabaian baris yang sedang diedit (`glosariumIdSaatIni()`), `kategori` boleh kosong, `definisi` minimal 10 karakter.
  - `app/Http/Requests/PengumumanRequest.php` beserta `StorePengumumanRequest` dan `UpdatePengumumanRequest`: `akhir_tayang` wajib `after_or_equal:mulai_tayang`.
- **Service Layer**:
  - `app/Services/HalamanInformasiService.php`: pembangkitan slug dari judul dengan akhiran `-2`, `-3` bila sudah terpakai, regenerasi slug hanya ketika judul berubah (agar tautan publik yang sudah dibagikan tidak berpindah), dan penghapusan permanen. Modul glosarium dan pengumuman tanpa service karena tidak ada invarian yang menjaga (mengikuti pola `StatusKasusController`).
- **Controller**:
  - `app/Http/Controllers/Admin/HalamanInformasiController.php`, `Admin/GlosariumKbgController.php`, `Admin/PengumumanController.php`: CRUD admin dengan pencarian, filter status, dan `paginate(10)->withQueryString()`. Pengumuman mencatat `dibuat_oleh` dari pengguna yang sedang login dan tidak pernah mengubahnya saat update.
  - `app/Http/Controllers/HalamanInformasiPublikController.php` (akses lewat slug), `GlosariumController.php` (daftar publik A–Z dengan pencarian), `PengumumanPublikController.php` (daftar hanya pengumuman yang sedang tayang).
- **Database Seeder**:
  - `database/seeders/HalamanInformasiSeeder.php`: empat halaman terbit (Tentang RuangAman, Visi dan Misi, Panduan Menggunakan Layanan, Kebijakan Privasi) plus satu draft.
  - `database/seeders/GlosariumKbgSeeder.php`: 13 istilah KBG yang sudah dipublikasikan lintas kategori (Umum, Hukum, Digital, Psikologi, Pendampingan, Layanan) plus satu draft.
  - `database/seeders/PengumumanSeeder.php`: empat pengumuman yang mewakili seluruh kondisi periode tayang (sedang tayang, belum mulai, sudah berakhir, diarsipkan) dengan `dibuat_oleh` merujuk akun Admin Kasus.
- **Antarmuka Pengguna (Blade Views)**:
  - `resources/views/admin/halaman-informasi/*`, `resources/views/admin/glosarium/*`, `resources/views/admin/pengumuman/*`: daftar, formulir tambah/ubah (dipisah lewat `_form.blade.php`), dan detail admin dengan badge status, tautan publik, serta modal konfirmasi hapus.
  - `resources/views/halaman/show.blade.php`, `resources/views/glosarium/index.blade.php`, `resources/views/pengumuman/index.blade.php`, `resources/views/pengumuman/show.blade.php`: tampilan publik. Konten panjang dirender dengan `{{ }}` + `whitespace-pre-line`, bukan `{!! !!}`, agar bebas XSS.
- **Pengujian**:
  - `tests/Feature/Pbi01ManajemenHalamanInformasiTest.php` (12 uji), `tests/Feature/Pbi02ManajemenGlosariumKbgTest.php` (8 uji), `tests/Feature/Pbi03ManajemenPengumumanTest.php` (9 uji): RBAC (tamu, Pelapor, Konselor), CRUD penuh, validasi, pembangkitan slug otomatis dan stabil, penyaringan daftar publik berdasarkan status dan periode tayang, serta `assertDatabaseCount('notifikasi', 0)` sebagai bukti tidak ada notifikasi otomatis.

### Diubah (Changed)
- `routes/web.php`: tiga resource admin (`halaman-informasi`, `glosarium`, `pengumuman`) di dalam grup `['auth', 'admin.kasus']` ditambah rute publik `/halaman/{slug}`, `/glosarium`, dan `/pengumuman`.
- `app/Providers/AppServiceProvider.php`: pendaftaran tiga policy serta Gate `kelola-halaman-informasi`, `kelola-glosarium`, dan `kelola-pengumuman`. Nama permission sudah ada di `DatabaseSeeder::PERMISSIONS` dan sudah diberikan kepada Admin Kasus, sehingga tidak ada perubahan data izin.
- `database/seeders/DatabaseSeeder.php`: memanggil ketiga seeder konten demo setelah `KegiatanEdukasiSeeder`.
- `resources/views/layouts/app.blade.php`: tautan publik "Glosarium" dan "Pengumuman", dropdown "Tata Kelola Konten" untuk tiga modul admin pada navbar desktop, serta entri yang sama di drawer mobile.

### Diperbaiki (Fixed)
- `resources/views/layouts/app.blade.php`: memasang kembali komponen `<x-confirm-modal />` yang hilang saat penyelesaian konflik merge pada commit `a6e71ab`. Selama komponen itu tidak ada, seluruh tombol hapus/nonaktifkan di aplikasi (10 view yang melakukan `$dispatch('open-confirm-modal', ...)`) tidak merespons.
- `app/Http/Controllers/Admin/HalamanInformasiController.php`: nama variabel model pada `show`, `edit`, `update`, dan `destroy` disesuaikan dengan parameter rute `halamanInformasi`. Sebelumnya route binding tidak pernah terpenuhi sehingga controller menerima instance kosong: update dan hapus tampak berhasil (redirect + flash success) tetapi tidak mengubah apa pun di database.
- `app/Http/Requests/GlosariumKbgRequest.php`: aturan keunikan `istilah` kini benar-benar mengabaikan baris yang sedang diperbarui, sehingga menyimpan ulang istilah tanpa mengubah namanya tidak lagi ditolak.

### Catatan Verifikasi
- `php artisan test`: 90 lulus, 1 gagal. Gagalnya adalah `Pbi13ManajemenDataKonselorTest::admin_kasus_can_update_konselor_without_changing_owner_account` yang sudah merah sebelum perubahan ini (form request PBI-13 menolak `user_id` non-Konselor, sedangkan pengujian mengirim akun Pelapor).
- Migrasi dan seluruh seeder diverifikasi pada basis data sqlite sementara (bukan TiDB tim): `migrate --force` lalu `db:seed --force` dua kali menghasilkan 5 halaman, 14 istilah, 4 pengumuman (1 di antaranya dalam periode tayang) dan 0 baris `notifikasi`, sehingga seeder terbukti idempoten dan tidak memicu notifikasi.
- Alur utama diverifikasi lewat browser terhadap server sementara bersistem sqlite: login Admin Kasus, daftar dan formulir ketiga modul (status 200), pembuatan halaman via form menghasilkan slug `kebijakan-rekaman-sesi` dan halaman publiknya tampil, modal konfirmasi hapus berfungsi sampai flash "berhasil dihapus permanen", dan perubahan istilah glosarium tanpa ganti nama lolos validasi.
- `public/hot` (penanda Vite HMR, di-gitignore) dihapus karena menunjuk ke dev server port 5173 yang tidak berjalan sehingga `artisan serve` memuat aset dari alamat mati; `npm run build` kembali normal dan aset terpasang dari `public/build`.

---

## [PBI-13] - 2026-11-11

### Ditambahkan (Added)
- **Model & Relasi**:
  - `app/Models/Konselor.php`: Model Eloquent tabel `konselor` (PK `konselor_id`) dengan konstanta status `aktif`/`cuti`/`nonaktif`, opsi label status, scope `aktif()`, relasi `user()`, serta helper `isAktif()`, `bisaMenerimaJadwal()`, `statusLabel()`, dan `statusBadgeVariant()`.
- **Otorisasi & Keamanan**:
  - `app/Policies/KonselorPolicy.php`: Kebijakan otorisasi `viewAny`, `view`, `create`, `update`, dan `delete` berbasis role/permission `kelola-konselor`, serta `updateProfil` yang hanya mengizinkan konselor mengubah profilnya sendiri.
  - `app/Http/Middleware/EnsureKonselor.php`: Middleware untuk membatasi rute profil konselor hanya untuk pengguna ber-role *Konselor*.
- **Form Request & Validasi (Bahasa Indonesia)**:
  - `app/Http/Requests/KonselorRequest.php`: Aturan dasar validasi biodata konselor (nama, spesialisasi, bio, status) beserta verifikasi role akun calon konselor dan larangan profil ganda.
  - `app/Http/Requests/StoreKonselorRequest.php` dan `app/Http/Requests/UpdateKonselorRequest.php`: Validasi penambahan (wajib memilih akun ber-role Konselor yang belum memiliki profil) dan pembaruan (akun pengguna terkunci).
  - `app/Http/Requests/UpdateProfilKonselorRequest.php`: Validasi pembaruan profil mandiri oleh konselor (tanpa status ketersediaan dan akun pengguna).
- **Service Layer**:
  - `app/Services/KonselorService.php`: Pembuatan profil dalam transaksi, pembaruan biodata/status, pembaruan profil mandiri, penghitungan slot jadwal, proteksi hapus permanen bila sudah memiliki `jadwal_konseling`, serta daftar akun Konselor yang belum memiliki profil.
- **Controller**:
  - `app/Http/Controllers/Admin/KonselorController.php`: CRUD data konselor, pencarian nama/spesialisasi, filter status, pagination, dan penghapusan terkendali.
  - `app/Http/Controllers/KonselorProfilController.php`: Halaman profil mandiri konselor (lihat & perbarui biodata sendiri).
- **Database Seeder**:
  - `database/seeders/KonselorSeeder.php`: Seeder idempoten dua profil konselor demo tambahan (aktif dan cuti) untuk kebutuhan demo/jadwal konseling.
- **Antarmuka Pengguna (Blade Views)**:
  - `resources/views/admin/konselor/index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`, `_form.blade.php`: Halaman manajemen data konselor/psikolog lengkap dengan badge status, jumlah slot jadwal, dan modal konfirmasi hapus.
  - `resources/views/konselor/profil.blade.php`: Halaman profil mandiri konselor.
- **Pengujian**:
  - `tests/Feature/Pbi13ManajemenDataKonselorTest.php`: 16 pengujian akses RBAC, CRUD, validasi role/duplikasi, proteksi hapus, pembatasan status mandiri, dan kebijakan privasi email.

### Diubah (Changed)
- `app/Models/User.php`: Menambahkan relasi `konselor()` (hasOne) dan helper `isKonselor()`.
- `app/Providers/AppServiceProvider.php`: Mendaftarkan `KonselorPolicy`, Gate `konselor`, dan Gate `kelola-konselor`.
- `bootstrap/app.php`: Mendaftarkan middleware alias `'konselor' => EnsureKonselor::class`.
- `routes/web.php`: Menambahkan resource `admin/konselor` pada grup Admin Kasus dan rute profil konselor (`/konselor/profil`).
- `resources/views/layouts/app.blade.php`: Menambahkan navigasi "Data Konselor" (Admin Kasus) dan "Profil Saya" (Konselor) pada navbar desktop maupun drawer mobile.
- `database/seeders/DatabaseSeeder.php`: Memanggil `KonselorSeeder::class` setelah pembuatan akun demo.

### Catatan Skema
- Tidak ada migrasi baru. Struktur tabel `konselor` telah tersedia pada `2026_10_29_000012_create_konselor_table.php` dan sesuai kamus data (Bagian 3, tabel 12).

---

## [PBI-04] - 2026-11-08

### Ditambahkan (Added)
- **Model & Relasi**:
  - `app/Models/PenangananKasus.php`: Model Eloquent untuk entitas penanganan kasus dengan relasi ke `LaporanKasus` (belongsTo), `User` sebagai petugas (belongsTo), dan `LogPenanganan` (hasMany).
  - `app/Models/LogPenanganan.php`: Stub model Eloquent untuk tabel `log_penanganan` sebagai titik integrasi dengan PBI-06.
- **Otorisasi & Keamanan**:
  - `app/Http/Middleware/EnsureAdminKasus.php`: Middleware untuk memastikan hanya pengguna berautentikasi dengan peran *Admin Kasus* yang dapat mengakses rute manajemen status dan penugasan kasus.
  - `app/Policies/StatusKasusPolicy.php`: Kebijakan otorisasi untuk operasi view, create, update, dan delete master status kasus.
  - `app/Policies/PenangananKasusPolicy.php`: Kebijakan otorisasi untuk operasi view, create, update, dan delete penugasan kasus.
- **Form Request & Validasi (Bahasa Indonesia)**:
  - `app/Http/Requests/StoreStatusKasusRequest.php`: Validasi penambahan status kasus baru (nama_status unik, max 50 karakter).
  - `app/Http/Requests/UpdateStatusKasusRequest.php`: Validasi pembaruan status kasus dengan pengecualian ID unik status saat ini.
  - `app/Http/Requests/StorePenangananKasusRequest.php`: Validasi pembuatan penugasan kasus (laporan_id unik dan valid, petugas_id valid, tanggal_mulai required, tanggal_selesai >= tanggal_mulai).
  - `app/Http/Requests/UpdatePenangananKasusRequest.php`: Validasi pembaruan penugasan kasus dengan pengecualian unik untuk laporan saat ini.
- **Controller**:
  - `app/Http/Controllers/StatusKasusController.php`: CRUD lengkap master status kasus, pencarian nama/deskripsi, pagination, dan proteksi hapus status yang sedang dipakai laporan.
  - `app/Http/Controllers/PenangananKasusController.php`: CRUD lengkap penugasan kasus, filter laporan & petugas, detail penanganan terintegrasi, dan pembatalan penugasan.
- **Database Seeder**:
  - `database/seeders/StatusKasusSeeder.php`: Seeder master status kasus mandiri berisi 4 status baku (*Pending*, *Proses*, *Selesai*, *Ditolak*).
- **Antarmuka Pengguna (Blade Views & Komponen)**:
  - `resources/views/components/confirm-modal.blade.php`: Komponen UI Modal konfirmasi kustom dengan backdrop blur, animasi halus, dan dukungan keyboard (`Escape`/klik luar) menggantikan popup bawaan browser (`confirm()`).
  - `resources/views/status-kasus/index.blade.php`: Halaman daftar status kasus, pencarian, dan badge status.
  - `resources/views/status-kasus/create.blade.php`: Formulir pembuatan status kasus baru.
  - `resources/views/status-kasus/edit.blade.php`: Formulir pembaruan status kasus.
  - `resources/views/status-kasus/_form.blade.php`: Partial form input status kasus.
  - `resources/views/penanganan-kasus/index.blade.php`: Halaman daftar penugasan kasus, pencarian laporan/petugas, filter dropdown petugas, dan status penanganan.
  - `resources/views/penanganan-kasus/create.blade.php`: Formulir penugasan kasus (hanya menampilkan laporan yang belum memiliki penanganan).
  - `resources/views/penanganan-kasus/edit.blade.php`: Formulir pembaruan data penugasan kasus.
  - `resources/views/penanganan-kasus/show.blade.php`: Halaman detail penugasan kasus lengkap dengan data laporan terkait dan timeline riwayat log penanganan (PBI-06).
  - `resources/views/penanganan-kasus/_form.blade.php`: Partial form input penugasan kasus.

### Diubah (Changed)
- `database/migrations/2026_10_29_000007_create_status_kasus_table.php`: Menyesuaikan tipe kolom `deskripsi` menjadi `TEXT NULL` agar persis sesuai spesifikasi ERD RuangAman.
- `app/Models/User.php`: Menambahkan relasi `penangananKasus()` (hasMany), serta metode otorisasi `isAdminKasus()`, `hasRole()`, dan `hasPermission()`.
- `app/Models/StatusKasus.php`: Menambahkan helper method `isUsedByLaporan()`.
- `app/Models/LaporanKasus.php`: Menambahkan relasi `penangananKasus()` (hasOne).
- `app/Providers/AppServiceProvider.php`: Mendaftarkan policy `StatusKasusPolicy`, `PenangananKasusPolicy`, Gate `admin-kasus`, `kelola-status-kasus`, `kelola-penugasan-kasus`, dan Tailwind paginator.
- `bootstrap/app.php`: Mendaftarkan middleware alias `'admin.kasus' => EnsureAdminKasus::class`.
- `database/seeders/DatabaseSeeder.php`: Memanggil `StatusKasusSeeder::class` pada tahapan `seedStatusKasus()`.
- `routes/web.php`: Mendaftarkan rute grup ber-prefix `/admin` dengan middleware `['auth', 'admin.kasus']` untuk resource `status-kasus` dan `penanganan-kasus`.
- `resources/views/layouts/app.blade.php`: Menambahkan navigasi khusus Admin Kasus untuk "Status Kasus" dan "Penugasan Kasus" pada navbar desktop dan drawer mobile menu.
- `skills-lock.json`: Mengonsolidasikan metadata skill project.

### Dihapus / Dirapikan (Cleaned / Removed)