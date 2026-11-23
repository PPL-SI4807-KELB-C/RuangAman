# PBI-08 Manajemen Layanan dan Kontak Bantuan - Implementasi Summary

## Status Implementasi: ✅ SELESAI (CRUD Backend Lengkap + Integrasi Instansi + Siap Merge develop)

**Implementasi tanggal:** 23 November 2026  
**Developer:** Muhammad Haqi Aqilla  
**Branch:** `feature/PBI-08-manajemen-layanan-dan-kontak-bantuan`

---

## 📋 Yang Telah Dikerjakan

### 1. Model Database
✅ **File:** `app/Models/LayananBantuan.php`
- Primary key: `layanan_id` (BIGINT UNSIGNED)
- Route key name: `layanan_id`
- Table: `layanan_bantuan`
- Fillable attributes: `instansi_id`, `nama_layanan`, `deskripsi`
- Relationship: `BelongsTo` ke `InstansiBantuan`

✅ **File:** `app/Models/KontakBantuan.php`
- Primary key: `kontak_id` (BIGINT UNSIGNED)
- Route key name: `kontak_id`
- Table: `kontak_bantuan`
- Fillable attributes: `instansi_id`, `jenis_kontak`, `nilai_kontak`, `keterangan`
- Casts: `jenis_kontak` as string (enum: `telepon`, `whatsapp`, `email`, `hotline`)
- Relationship: `BelongsTo` ke `InstansiBantuan`
- Accessor: `label_jenis_kontak` untuk label human-readable

✅ **File:** `app/Models/InstansiBantuan.php`
- Diperbarui dengan relasi `layananBantuan()` dan `kontakBantuan()` (keduanya `HasMany`).

---

### 2. Controller
✅ **File:** `app/Http/Controllers/LayananBantuanController.php`
- `index(InstansiBantuan $instansi)`: Menampilkan daftar seluruh layanan milik instansi terkait.
- `create(InstansiBantuan $instansi)`: Form tambah layanan baru.
- `store(Request $request, InstansiBantuan $instansi)`: Validasi & simpan layanan baru ke database.
- `show(InstansiBantuan $instansi, LayananBantuan $layanan)`: Tampilkan detail layanan dengan proteksi scope instansi (404 jika cross-instansi).
- `edit(InstansiBantuan $instansi, LayananBantuan $layanan)`: Form ubah layanan.
- `update(Request $request, InstansiBantuan $instansi, LayananBantuan $layanan)`: Validasi & simpan pembaruan layanan.
- `destroy(InstansiBantuan $instansi, LayananBantuan $layanan)`: Hapus layanan secara permanen.

✅ **File:** `app/Http/Controllers/KontakBantuanController.php`
- `index(InstansiBantuan $instansi)`: Menampilkan daftar seluruh kontak milik instansi terkait.
- `create(InstansiBantuan $instansi)`: Form tambah kontak baru.
- `store(Request $request, InstansiBantuan $instansi)`: Validasi & simpan kontak baru ke database (`jenis_kontak` divalidasi `in:telepon,whatsapp,email,hotline`).
- `show(InstansiBantuan $instansi, KontakBantuan $kontak)`: Tampilkan detail kontak dengan proteksi scope instansi (404 jika cross-instansi).
- `edit(InstansiBantuan $instansi, KontakBantuan $kontak)`: Form ubah kontak.
- `update(Request $request, InstansiBantuan $instansi, KontakBantuan $kontak)`: Validasi & simpan pembaruan kontak.
- `destroy(InstansiBantuan $instansi, KontakBantuan $kontak)`: Hapus kontak secara permanen.

✅ **File:** `app/Http/Controllers/InstansiBantuanController.php`
- Diperbarui agar eager-load `layananBantuan` dan `kontakBantuan` pada method `show()` dan `publicDirectory()`.

---

### 3. Routes
✅ **File:** `routes/web.php`
- Mendukung kedua skema route name untuk fleksibilitas dan kompatibilitas penuh:
  - `/instansi/{instansi}/layanan` & `/instansi/{instansi}/kontak` (`instansi.layanan.*` & `instansi.kontak.*`)
  - `/admin/instansi/{instansi}/layanan` & `/admin/instansi/{instansi}/kontak` (`admin.instansi.layanan.*` & `admin.instansi.kontak.*`)
- Keduanya diproteksi dengan middleware `['auth', 'admin.kasus']`.

---

### 4. Views & Integrasi UI
- Placeholder views dibuat fungsional dan bersih, siap diperbarui setelah UI Figma dikirimkan:
  - `resources/views/layanan/index.blade.php`
  - `resources/views/layanan/create.blade.php`
  - `resources/views/layanan/edit.blade.php`
  - `resources/views/layanan/show.blade.php`
  - `resources/views/kontak/index.blade.php`
  - `resources/views/kontak/create.blade.php`
  - `resources/views/kontak/edit.blade.php`
  - `resources/views/kontak/show.blade.php`
- Halaman detail instansi (`resources/views/instansi/show.blade.php`) diintegrasikan dengan bagian Layanan Bantuan dan Kontak Bantuan lengkap dengan aksi tambah/kelola/ubah/hapus konfirmasi modal.
- Direktori publik (`resources/views/instansi/public.blade.php`) menampilkan badge layanan dan informasi kontak untuk setiap instansi aktif.

---

### 5. Seeder Database
✅ **File:** `database/seeders/LayananKontakBantuanSeeder.php`
- Mengisi data realistis layanan dan kontak untuk 4 instansi bawaan (Pusat Bantuan Harmoni, Yayasan Ruang Pulih, Lembaga Advokasi Nusantara, Rumah Perlindungan Sejati).
- Terdaftar di `database/seeders/DatabaseSeeder.php`.

---

### 6. Feature Tests
✅ **File:** `tests/Feature/Pbi08LayananKontakBantuanTest.php`
- 19 test cases komprehensif:
  - CRUD Layanan Bantuan (Index, Create, Store, Validasi, Show, Edit, Update, Destroy)
  - Scope isolation Layanan Bantuan (404 jika diakses dari instansi berbeda)
  - CRUD Kontak Bantuan (Index, Create, Store, Validasi enum jenis kontak, Show, Edit, Update, Destroy)
  - Scope isolation Kontak Bantuan (404 jika diakses dari instansi berbeda)
  - Akses tamu ditolak (redirect login)
  - Akses non-admin (Pelapor dan Konselor) ditolak (403 Forbidden)
