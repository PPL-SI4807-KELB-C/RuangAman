# Sprint 1 Review: RuangAman

Dokumen ini merangkum hasil **PKBC Sprint 1** dan hasil verifikasi terhadap isi repository pada penutupan sprint (22 November 2026). Rencana awal ada di [sprint-backlog.md](sprint-backlog.md); rencana lanjutan ada di [sprint-2-planning.md](sprint-2-planning.md).

---

## 1. Ringkasan

| Item | Keterangan |
| :--- | :--- |
| Sprint (Jira) | PKBC Sprint 1 |
| Periode | 19 Oktober – 22 November 2026 (Week 6 – 10) |
| Sprint Goal | Fondasi aplikasi + PBI Sprint 1: auth/RBAC, pelaporan kasus & lampiran, konten edukasi dasar, instansi bantuan, data konselor. |
| Status Jira | 11 dari 11 issue berstatus **Done** (100%) |
| Verifikasi repo | 154 tes lulus (610 assertion), lint PHP bersih (lihat bagian 3) |
| Temuan | Temuan PBI-10 dan PBI-11 sudah ditutup lewat PR #10 (CRUD kategori + tes). Sisa temuan di bagian 4. |

---

## 2. Hasil per PBI

Jumlah tes dihitung dari fungsi `test_*` pada berkas tes yang memuat PBI tersebut.

| Issue | PBI | PIC | Jira | Tes di repo | Catatan |
| :--- | :--- | :--- | :---: | :---: | :--- |
| PKBC-7 | Tim 1: Fondasi (Auth, RBAC, layout, dashboard per peran) | Seluruh Tim | Done | 16 (`BatasRoleDanDashboardTest`) | - |
| PKBC-9 | PBI-01 Halaman Informasi Statis | Ghalib | Done | 12 | - |
| PKBC-10 | PBI-02 Glosarium Istilah KBG | Ghalib | Done | 8 | - |
| PKBC-11 | PBI-03 Pengumuman | Ghalib | Done | 11 | - |
| PKBC-12 | PBI-04 Status & Penugasan Kasus | Glen | Done | 11 | - |
| PKBC-15 | PBI-07 Instansi Bantuan | Haqi | Done | 13 | - |
| PKBC-18 | PBI-10 Artikel Edukasi | Niky | Done | 2 (`ArtikelHalamanTest`) + 8 (`Pbi10ManajemenArtikelEdukasiTest`) | CRUD admin, validasi, RBAC 403 teruji. Kategori opsional (default "Umum") sehingga mandiri dari PBI-11. |
| PKBC-19 | PBI-11 Kategori Artikel | Niky | Done | 9 (`Pbi11ManajemenKategoriArtikelTest`) | CRUD lengkap untuk Admin Kasus (403 untuk peran lain); kategori bawaan "Umum" terlindungi; kategori terpakai tidak dapat dihapus. |
| PKBC-21 | PBI-13 Data Konselor | Altaf | Done | 17 | - |
| PKBC-24 | PBI-16 Laporan Kasus & Lampiran | Fauzi | Done | 21 | Tes baru berjalan setelah berkas diganti namanya (bagian 3). |
| PKBC-25 | PBI-17 Mode Anonim & Profil Pelapor | Fauzi | Done | 9 | - |

PBI-05 (Kegiatan Edukasi, 32 tes) sudah terimplementasi lebih awal dan dilacak sebagai bagian Sprint 2.

---

## 3. Verifikasi Repository

Dijalankan pada 22 November 2026 dengan PHP 8.3.6 dan konfigurasi `phpunit.xml` (SQLite in-memory).

| Pemeriksaan | Sebelum perbaikan | Sesudah perbaikan |
| :--- | :--- | :--- |
| `php artisan test` | 154 tes, 152 lulus, 2 gagal | 154 tes, 154 lulus, 610 assertion |
| Lint PHP (`php -l` pada `app`, `config`, `database`, `routes`) | Bersih | Bersih |

CI GitHub Actions memakai MySQL 8, sehingga hasilnya perlu dikonfirmasi pada Pull Request yang memuat dokumen ini.

### Perbaikan yang dilakukan saat penutupan

1. **Berkas tes PBI-16 tidak pernah dijalankan.** Berkasnya bernama `Pbi16ManajemenLaporanKasus.php` (tanpa akhiran `Test`), padahal PHPUnit hanya memuat `*Test.php`. Berkas diganti nama menjadi `Pbi16ManajemenLaporanKasusTest.php`, sehingga 21 tes ikut berjalan di CI dan sesuai aturan penamaan pada [sprint-backlog.md](sprint-backlog.md).
2. **Format kode lacak menyimpang dari spesifikasi.** Acceptance Criteria PBI-16 dan `SECURITY.md` menetapkan format `RA-YYYY-XXXXXX`, tetapi generator hanya menghasilkan 6 karakter acak (awalan dihapus pada commit `9c75388`). Awalan dipulihkan di `LaporanSubmissionService::generateKodeLaporan()`. Kode lama tetap valid karena pencarian memakai kecocokan penuh dan kolom `kode_laporan` berukuran 50 karakter.
3. **Tes preferensi anonim usang.** Tes masih mencari state Alpine lama, padahal form kini memakai komponen `x-checkbox`. Tes diganti agar memeriksa atribut `checked` untuk preferensi `false` dan `true`.
4. **`aad.zip` (51 MB) dikeluarkan dari repo.** Isinya salinan file proyek sendiri (README, `.github`, proposal) dan tidak dirujuk di mana pun. Pola `/*.zip` ditambahkan ke `.gitignore`. Berkas masih ada di riwayat git.

---

## 4. Temuan & Tindak Lanjut

| # | Temuan | Usulan tindak lanjut | Usulan PIC |
| :-: | :--- | :--- | :--- |
| 1 | ~~PBI-11 (PKBC-19) belum ada CRUD.~~ **Selesai** (PR #10). | - | Niky |
| 2 | ~~PBI-10 (PKBC-18) minim tes.~~ **Selesai** (PR #10). | - | Niky |
| 3 | Sejumlah commit tidak memuat issue key Jira, sehingga tidak muncul di panel Development: `1fe8ce9` ("PBI-07 Pembuatan Create & Read"), `a782c1e` ("feat: implement PBI-10 article management"), `d3cd42b` ("PBI 16-17 Fauzi"), `b83f9c6` ("PKBC - 21"), `399b8a5`, `0e31a4b`, `d526ee3` ("fitur pbi pelaporan", "update", "update 2"), `e46f990` ("feat(pbi-07): ..."). | Mulai Sprint 2, wajib `[PKBC-XX]` di pesan commit dan judul PR. Jangan menulis ulang riwayat `develop` untuk memperbaikinya. | Seluruh tim |
| 4 | `CONTRIBUTING.md` menyatakan `main` terkunci, tetapi proteksi branch belum aktif. | Aktifkan branch protection untuk `main` (wajib PR; opsional wajib 1 review). | Admin repo |
| 5 | Banyak branch fitur lama masih ada di remote. | Hapus branch yang sudah ter-merge setelah masing-masing PIC mengonfirmasi. | Masing-masing PIC |
| 6 | Epic Jira PKBC-1 sampai PKBC-6 masih To Do. | Perbarui status epic sesuai progres, lalu tutup Sprint 1 dan mulai Sprint 2 di Jira. | Scrum Master |
| 7 | `aad.zip` masih tersimpan di riwayat git (clone lebih berat). | Tidak perlu tindakan; hindari menulis ulang riwayat bersama. | - |

---

## 5. Retrospektif Singkat

**Berjalan baik**
- Aturan independensi PBI (CRUD lengkap, data lintas modul hanya-baca lewat seeder) memungkinkan enam anggota bekerja paralel dengan konflik minimal.
- Cakupan tes untuk sebagian besar PBI sudah memadai (154 tes).
- Template PR, panduan kontribusi, dan CI sudah tersedia sejak awal sprint.

**Perlu diperbaiki**
- Konvensi issue key belum dipatuhi semua commit, sehingga pelacakan di Jira tidak lengkap.
- Berkas tes yang tidak mengikuti penamaan `*Test.php` diam-diam tidak dijalankan.
