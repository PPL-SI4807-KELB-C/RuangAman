# PBI-08 Manajemen Layanan dan Kontak Bantuan - Implementasi Summary

## Status Implementasi: ✅ SELESAI (CRUD Backend Lengkap + UI Figma Exact 100% + Siap Merge develop)

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
- Casts: `jenis_kontak` as string (enum: `telepon`, `whatsapp`, `email`, `hotline`, `website`)
- Relationship: `BelongsTo` ke `InstansiBantuan`
- Accessor: `label_jenis_kontak` untuk label human-readable (`Telepon`, `WhatsApp`, `Email`, `Hotline`, `Situs web`)

✅ **File:** `app/Models/InstansiBantuan.php`
- Diperbarui dengan relasi `layananBantuan()` dan `kontakBantuan()` (keduanya `HasMany`).

---

### 2. Controller
✅ **File:** `app/Http/Controllers/LayananBantuanController.php`
- `index(InstansiBantuan $instansi)`: Menampilkan antarmuka tunggal terpadu Layanan & Kontak untuk instansi terpilih beserta dropdown instansi switcher.
- `create(InstansiBantuan $instansi)`: Form tambah layanan baru (layout 2 kolom Figma dengan sidebar panduan).
- `store(Request $request, InstansiBantuan $instansi)`: Validasi (`nama_layanan` & `deskripsi` required) & simpan layanan baru ke database.
- `show(InstansiBantuan $instansi, LayananBantuan $layanan)`: Detail layanan dengan proteksi scope instansi (404 jika cross-instansi).
- `edit(InstansiBantuan $instansi, LayananBantuan $layanan)`: Form ubah layanan sesuai Figma.
- `update(Request $request, InstansiBantuan $instansi, LayananBantuan $layanan)`: Validasi & simpan pembaruan layanan.
- `destroy(InstansiBantuan $instansi, LayananBantuan $layanan)`: Hapus layanan secara permanen.

✅ **File:** `app/Http/Controllers/KontakBantuanController.php`
- `index(InstansiBantuan $instansi)`: Render halaman terpadu Layanan & Kontak.
- `create(InstansiBantuan $instansi)`: Form tambah kontak baru sesuai Figma.
- `store(Request $request, InstansiBantuan $instansi)`: Validasi & simpan kontak baru (`jenis_kontak` divalidasi `in:telepon,whatsapp,email,hotline,website`).
- `show(InstansiBantuan $instansi, KontakBantuan $kontak)`: Detail kontak dengan proteksi scope instansi (404 jika cross-instansi).
- `edit(InstansiBantuan $instansi, KontakBantuan $kontak)`: Form ubah kontak sesuai Figma.
- `update(Request $request, InstansiBantuan $instansi, KontakBantuan $kontak)`: Validasi & simpan pembaruan kontak.
- `destroy(InstansiBantuan $instansi, KontakBantuan $kontak)`: Hapus kontak secara permanen.

---

### 3. Rute (Routes)
✅ **File:** `routes/web.php`
- Mendukung kedua skema route name untuk fleksibilitas dan kompatibilitas penuh:
  - `/instansi/{instansi}/layanan` & `/instansi/{instansi}/kontak` (`instansi.layanan.*` & `instansi.kontak.*`)
  - `/admin/instansi/{instansi}/layanan` & `/admin/instansi/{instansi}/kontak` (`admin.instansi.layanan.*` & `admin.instansi.kontak.*`)
- Keduanya diproteksi dengan middleware `['auth', 'admin.kasus']`.

---

### 4. Views & Antarmuka Sesuai Desain Figma (100% Exact)
✅ **File:** `resources/views/layanan/index.blade.php`
- Halaman terpadu **Layanan & kontak bantuan** sesuai rancangan Figma:
  - Breadcrumb: `Ruang admin / Instansi bantuan / Layanan & kontak`
  - Instansi Selector Banner: Dropdown switcher instansi interaktif dengan info alamat dan status instansi (`#166534`).
  - Section 1: Tabel Layanan bantuan dengan kolom `NAMA LAYANAN`, `DESKRIPSI`, dan tombol aksi `Ubah` & `Hapus`.
  - Section 2: Tabel Kontak bantuan dengan kolom `JENIS KONTAK`, `NILAI KONTAK`, `KETERANGAN`, dan tombol aksi `Ubah` & `Hapus`.
  - Modal Konfirmasi Hapus Alpine.js Figma Exact:
    - Overlay `#1C1917` opacity 30%
    - Dialog `#FAF9F7` rounded 12px
    - Target detail & nama instansi
    - Kotak peringatan `#EDECE9` dengan teks peringatan tindakan tidak dapat dibatalkan
    - Tombol `Batal` (`#EDECE9`) dan `Ya, hapus layanan` / `Ya, hapus kontak` (`#B42318`).

✅ **File:** `resources/views/layanan/create.blade.php` & `resources/views/layanan/edit.blade.php`
- Layout 2 kolom: Form kiri (704px) + Sidebar panduan kanan (320px).
- Kotak informasi instansi (read-only, `#EDECE9`).
- Input `Nama layanan *` dan textarea `Deskripsi *` dengan helper text.
- Tombol aksi `Simpan layanan` / `Simpan perubahan` (`#16A34A`) dan `Batal` (`#EDECE9`).

✅ **File:** `resources/views/kontak/create.blade.php` & `resources/views/kontak/edit.blade.php`
- Layout 2 kolom Figma: Form kiri (704px) + Sidebar informasi kontak kanan (320px).
- Kotak informasi instansi (read-only, `#EDECE9`).
- Pilihan `Jenis kontak *` (Telepon, WhatsApp, Email, Hotline, Situs web).
- Input `Nilai kontak *` dan textarea `Keterangan (opsional)` dengan helper text.
- Tombol aksi `Simpan kontak` / `Simpan perubahan` (`#16A34A`) dan `Batal` (`#EDECE9`).

---

### 5. Seeder Database
✅ **File:** `database/seeders/LayananKontakBantuanSeeder.php`
- Mengisi data realistis sesuai sampel Figma (Pendampingan psikologis, Pendampingan hukum, Rujukan layanan lanjutan, Email, Telepon, Situs web).
- Terdaftar di `DatabaseSeeder.php`.

---

### 6. Feature Tests
✅ **File:** `tests/Feature/Pbi08LayananKontakBantuanTest.php`
- 20 test cases komprehensif menguji seluruh alur CRUD, validasi, otorisasi, dan scope isolation.
