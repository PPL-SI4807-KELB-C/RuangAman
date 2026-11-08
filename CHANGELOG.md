# Changelog - RuangAman

Semua perubahan tercatat dalam format standar changelog.

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