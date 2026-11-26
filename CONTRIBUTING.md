# Panduan Kontribusi RuangAman 🤝

Selamat datang di repository proyek **RuangAman**! Dokumen ini memuat standar teknis, alur percabangan Git, konvensi commit, dan prosedur pengujian untuk memastikan kolaborasi enam anggota tim berjalan rapi, transparan, dan bebas dari konflik kode.

---

## 1. Aturan Percabangan Git (Branching Strategy)

Kami menerapkan modifikasi alur **Git Flow** yang disederhanakan:

```
  main ──────────────────────────────────────────────────────────► (Production / Demo Akhir)
           ▲
           │ Release PR (UAT Loloz)
  develop ─┴─────────────────────────────────────────────────────► (Staging / Integrasi Sprint)
           ▲              ▲              ▲
           │ PR PKBC-9    │ PR PKBC-24   │ PR PKBC-12
  feature/PKBC-xx ────────┴──────────────┴────────────────────────► (Branch Fitur Anggota)
```

### Cabang Utama:
1. **`main`**:
   - Cabang stabil untuk rilis resmi atau milestone demo akhir perkuliahan.
   - **Terkunci (*Protected*)**: Tidak boleh ada commit langsung (*direct push*). Perubahan hanya masuk melalui Pull Request dari `develop`.
2. **`develop`**:
   - Cabang integrasi aktif tempat seluruh fitur PBI yang telah selesai dan diuji digabungkan.
   - Semua branch fitur dicabangkan dari `develop` dan di-merge kembali ke `develop`.

> **Wajib issue key Jira.** Setiap branch, commit, dan judul PR harus memuat issue key Jira proyek **PKBC** (`PKBC-<nomor>`, huruf kapital, memakai tanda hubung). Nomor PBI (`PBI-XX`) boleh ditulis sebagai lingkup commit, tetapi tidak menggantikan issue key. Daftar key per PBI dan aturan lengkapnya ada di [Panduan Commit agar Terbaca di Jira](docs/panduan-commit-jira.md).

### Format Penamaan Branch:
- **Fitur Baru (PBI Individu)**:  
  `feature/PKBC-<nomor>-<nama-singkat>`  
  Contoh: `feature/PKBC-24-laporan-kasus`, `feature/PKBC-9-halaman-statis`, `feature/PKBC-21-data-konselor`
- **Fitur Tim (Fondasi/Integrasi Bersama)**:  
  `feature/PKBC-<nomor>-<nama-fitur>` (gunakan issue key tim, misalnya PKBC-8)  
  Contoh: `feature/PKBC-8-quick-exit`, `feature/PKBC-8-dashboard-integration`
- **Perbaikan Bug**:  
  `bugfix/PKBC-<nomor>-<deskripsi-singkat>`  
  Contoh: `bugfix/PKBC-24-upload-size-validation`, `bugfix/PKBC-8-sidebar-responsive`
- **Dokumentasi**:  
  `docs/PKBC-<nomor>-<topik>`  
  Contoh: `docs/PKBC-8-aturan-commit-issue-key`
- **Eksperimen / Spike**:  
  `spike/PKBC-<nomor>-<topik>`  
  Contoh: `spike/PKBC-8-chartjs-tailwind-integration`

---

## 2. Alur Kerja Pengembangan Fitur (Workflow Step-by-Step)

1. **Pastikan branch lokal `develop` up-to-date**:
   ```bash
   git checkout develop
   git pull origin develop
   ```

2. **Buat branch baru sesuai issue Jira**:
   ```bash
   git checkout -b feature/PKBC-24-laporan-kasus
   ```

3. **Kembangkan fitur dengan prinsip independensi modul**:
   - Kerjakan hanya pada file dan tabel yang menjadi kewenangan PBI Anda (lihat matriks kepemilikan di `README.md` dan `docs/sprints/sprint-backlog.md`).
   - Jangan mengubah migrasi tabel milik PBI lain. Jika memerlukan data relasi, gunakan Seeder dummy untuk pengujian lokal.

4. **Jalankan pengujian dan formatting lokal**:
   ```bash
   # Jalankan pengujian dan kompilasi asset
   php artisan test
   npm run build
   ```

5. **Commit perubahan secara berkala** menggunakan format *Conventional Commits* dan sertakan issue key Jira (lihat bagian 3).

6. **Push branch ke GitHub**:
   ```bash
   git push -u origin feature/PKBC-24-laporan-kasus
   ```

7. **Buka Pull Request (PR)** ke branch `develop` menggunakan template PR yang disediakan.

---

## 3. Konvensi Pesan Commit (Conventional Commits)

Format commit:
```
[PKBC-XX] <tipe>(<lingkup>): <deskripsi singkat dalam bentuk imperatif>

[penjelasan rinci opsional]
```

- `<lingkup>` adalah modul atau nomor PBI, misalnya `PBI-16`, `laporan`, `auth`, atau `repo`.
- `[PKBC-XX]` wajib ada di setiap commit, ditulis dalam kurung siku di awal pesan. Bila satu commit menyentuh beberapa issue, tulis berurutan: `[PKBC-7] [PKBC-8]`.
- Hindari pesan tanpa issue key seperti `update`, `fix`, atau `fitur pbi pelaporan`.

### Tipe Commit yang Diizinkan:
- `feat`: Penambahan fitur baru (misal: `[PKBC-24] feat(PBI-16): create report submission form with urgency levels`)
- `fix`: Perbaikan bug (misal: `[PKBC-25] fix(PBI-17): prevent alias collision in user registration`)
- `docs`: Pembaruan dokumentasi teknis atau README (misal: `[PKBC-8] docs(api): update report status workflow diagram`)
- `style`: Penyesuaian tampilan Blade / styling Tailwind tanpa mengubah logika (misal: `[PKBC-17] style(PBI-09): adjust faq accordion spacing`)
- `refactor`: Restrukturisasi kode tanpa mengubah fungsionalitas (misal: `[PKBC-12] refactor(PBI-04): simplify case assignment policy logic`)
- `test`: Penambahan atau perbaikan unit test / feature test (misal: `[PKBC-24] test(PBI-16): add test case for private attachment access`)
- `chore`: Tugas pemeliharaan rutin, konfigurasi dependensi composer/npm (misal: `[PKBC-8] chore(deps): update tailwind config`)

---

## 4. Standar Kode & Kualitas (Code Style Guidelines)

- **Backend (PHP / Laravel)**:
  - Mengikuti standar **PSR-12** dan konvensi resmi Laravel.
  - Penamaan Model: PascalCase tunggal (`LaporanKasus`, `JadwalKonseling`).
  - Penamaan Tabel Database: snake_case jamak/deskriptif (`laporan_kasus`, `jadwal_konseling`, `profil_pelapor`).
  - Penamaan Controller: PascalCase (`LaporanKasusController`).
  - Penamaan Route: kebab-case (`/laporan-kasus/buat`, `/konseling/jadwal`).
  - Validasi masukan **wajib** menggunakan Form Request class terpisah (`StoreLaporanKasusRequest`).
  - Hak akses **wajib** divalidasi melalui Laravel Policy / Gate.

- **Frontend (Blade, Tailwind CSS, Alpine.js)**:
  - Gunakan class utilitas Tailwind CSS secara konsisten.
  - Komponen umum (seperti tombol Quick Exit, modal konfirmasi, badge status) disimpan dalam direktori `resources/views/components/`.
  - Pastikan tampilan responsif dan dapat diakses dengan baik pada layar ponsel (lebar minimum 360 px sesuai spesifikasi TR-12).

---

## 5. Prosedur Code Review & Pull Request (PR)

1. PR harus memiliki judul yang jelas dan memuat issue key Jira: `[PKBC-XX] Judul Singkat Fitur` (contoh: `[PKBC-16] Layanan & Kontak Bantuan`).
2. Isi formulir deskripsi PR sesuai checklist yang otomatis muncul dari template `.github/PULL_REQUEST_TEMPLATE.md`.
3. Minimal **1 anggota tim** harus me-review dan memberikan persetujuan (*Approve*) sebelum PR di-merge.
4. Gunakan metode **Squash and Merge** atau **Rebase and Merge** di GitHub untuk menjaga riwayat commit di `develop` tetap bersih dan runut.
5. Hapus branch fitur setelah PR berhasil di-merge.
6. Perbarui status issue di Jira (misalnya ke Done) setelah PR ter-merge.
