# PBI-09 Manajemen FAQ / Pusat Bantuan - Implementasi Summary

## Status Implementasi: ✅ SELESAI (CRUD Lengkap + Pusat Bantuan Publik + UI Figma Exact)

**Jira Key:** `PKBC-17`  
**Branch:** `feature/PBI-09-manajemen-faq/pusat-bantuan`  
**Developer:** Muhammad Haqi Aqilla  

---

## 📋 Yang Telah Dikerjakan

### 1. Model & Validasi
✅ **File:** `app/Models/Faq.php`
- Tabel `faq`, primary key `faq_id`, route key `faq_id`.
- Fillable: `pertanyaan`, `jawaban`, `status_publikasi`, `urutan`.
- Konstanta status: `STATUS_DRAFT` (`draft`), `STATUS_PUBLISHED` (`published`).
- Scope: `scopePublished()`, `scopeOrdered()`.

✅ **File:** `app/Http/Requests/FaqRequest.php`
- Otorisasi khusus `Admin Kasus`.
- Validasi: `pertanyaan` (required), `jawaban` (required), `urutan` (required, integer, min:1), `status_publikasi` (`in:draft,published`).

### 2. Controllers & Routes
✅ **Admin Controller:** `app/Http/Controllers/Admin/FaqController.php`
- CRUD lengkap: `index` (pencarian + filter status + statistik), `create`, `store`, `show`, `edit`, `update`, `destroy`.

✅ **Public Controller:** `app/Http/Controllers/FaqController.php`
- `index`: Menampilkan Pusat Bantuan publik tanpa login, hanya FAQ berstatus `published`, terurut dari `urutan` terkecil, dengan pencarian pertanyaan/jawaban.

✅ **Routes (`routes/web.php`):**
- Admin (`auth`, `admin.kasus`): `admin.faq.*` (`/admin/faq`)
- Publik: `faq.index` (`GET /pusat-bantuan`)

### 3. Views (UI Figma Exact — 6 Layar)
1. **Admin Index:** `resources/views/admin/faq/index.blade.php` — Breadcrumb, header `+ Tambah FAQ`, statistik, search bar, dropdown `Status: Semua/Dipublikasikan/Draf`, tombol `Lihat pusat bantuan`, tabel urutan `01..`, badge status, aksi `Detail / Ubah / Hapus`, serta catatan publikasi.
2. **Tambah FAQ:** `resources/views/admin/faq/create.blade.php` (+ `_form.blade.php`, `_sidebar.blade.php`).
3. **Detail FAQ:** `resources/views/admin/faq/show.blade.php` — Pertanyaan, Jawaban, ID FAQ (`FAQ-xxx`), sidebar `Publikasi & urutan`.
4. **Ubah FAQ:** `resources/views/admin/faq/edit.blade.php`.
5. **Modal Hapus FAQ:** `resources/views/admin/faq/_delete-modal.blade.php` — Dialog konfirmasi dengan saran ubah ke Draf dan tombol `#B42318` `Ya, hapus FAQ`.
6. **Pusat Bantuan Publik:** `resources/views/faq/index.blade.php` — Judul `Ada yang ingin Anda ketahui?`, pencarian `Cari jawaban`, daftar FAQ terpublikasi, serta sidebar `Butuh pilihan bantuan lain?` & `Keselamatan Anda utama`.

### 4. Seeder & Tests
- **Seeder:** `database/seeders/FaqSeeder.php` (terdaftar di `DatabaseSeeder.php`) dengan 6 item contoh sesuai desain Figma (5 `published`, 1 `draft`).
- **Feature Tests:** `tests/Feature/Pbi09ManajemenFaqTest.php` mencakup 9 test case untuk RBAC, CRUD, validasi, pencarian/filter, dan halaman publik.
