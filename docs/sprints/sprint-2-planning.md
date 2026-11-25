# Sprint 2 Planning: RuangAman

Dokumen ini memuat rencana **PKBC Sprint 2**. Rencana jangka panjang dan matriks PBI ada di [sprint-backlog.md](sprint-backlog.md); hasil sprint sebelumnya ada di [sprint-1-review.md](sprint-1-review.md). Status issue diambil dari Jira pada 22 November 2026 dan perlu diperbarui seiring sprint berjalan.

---

## 1. Ringkasan

| Item | Keterangan |
| :--- | :--- |
| Sprint (Jira) | PKBC Sprint 2 |
| Periode | 23 November 2026 – 3 Januari 2027 (Week 11 – 16) |
| Sprint Goal | Penanganan kasus lanjutan, konseling (jadwal & sesi), konten lanjutan, notifikasi, integrasi dashboard, UAT & deployment. |
| Jumlah issue | 9 issue di sprint, ditambah 1 issue bawaan dari Sprint 1 bila PKBC-19 dibuka kembali |

---

## 2. Backlog Sprint 2

| Issue | PBI | PIC | Status Jira | Catatan awal |
| :--- | :--- | :--- | :---: | :--- |
| PKBC-8 | Tim 2: Integrasi (dashboard 3 peran, pelacakan publik, alur status, Quick Exit, UAT) | Seluruh Tim | In Progress | Belum ada PIC di Jira. Dashboard per peran sudah dikerjakan pada PKBC-7. Tetapkan penanggung jawab per bagian. |
| PKBC-13 | PBI-05 Kegiatan/Webinar Edukasi | Glen | In Progress | Implementasi sudah ada di `develop` (PR #3, 32 tes). Verifikasi acceptance criteria, lalu tutup. |
| PKBC-14 | PBI-06 Log Penanganan Kasus | Glen | To Do | Log status otomatis menjadi tanggung jawab Tim 2. |
| PKBC-16 | PBI-08 Layanan & Kontak Bantuan | Haqi | To Do | Bergantung pada data `instansi_bantuan` (PBI-07 selesai). |
| PKBC-17 | PBI-09 FAQ / Pusat Bantuan | Haqi | To Do | - |
| PKBC-20 | PBI-12 Feedback Artikel | Niky | To Do | Bergantung pada data `artikel` dari PBI-10. |
| PKBC-22 | PBI-14 Jadwal Konseling | Altaf | In Progress | Bergantung pada data `konselor` (PBI-13 selesai). |
| PKBC-23 | PBI-15 Sesi Konseling | Altaf | To Do | `jadwal_id` nullable, sehingga tidak menunggu PBI-14. |
| PKBC-26 | PBI-18 Notifikasi Sistem | Fauzi | To Do | Notifikasi otomatis antarmodul menjadi bagian Tim 2. |
| PKBC-19 | PBI-11 Kategori Artikel (bila dibuka kembali) | Niky | Done (perlu ditinjau) | CRUD kategori belum ada di `develop`. Lihat [Sprint 1 Review](sprint-1-review.md#4-temuan--tindak-lanjut). |

---

## 3. Usulan Urutan Kerja

Usulan berikut bersifat saran dan dapat disesuaikan tim pada sprint planning.

| Minggu | Fokus usulan |
| :--- | :--- |
| Week 11 – 12 (23 Nov – 6 Des) | Rapikan temuan Sprint 1 (PBI-11, tes PBI-10). Selesaikan PBI-05, PBI-14, dan mulai PBI-06, PBI-08, PBI-09, PBI-12, PBI-15, PBI-18 secara paralel. |
| Week 13 – 14 (7 – 20 Des) | Selesaikan seluruh PBI individu. Mulai integrasi Tim 2: pelacakan publik, alur persetujuan status, notifikasi dan log otomatis. |
| Week 15 (21 – 27 Des) | Integrasi dan perbaikan bug lintas modul, pengujian E2E, UAT. |
| Week 16 (28 Des – 3 Jan) | Deployment (konfigurasi Render + TiDB sudah ada, lihat [DEPLOY.md](../DEPLOY.md)), perbaikan akhir, dan demo. |

---

## 4. Definition of Done

Sebuah issue baru boleh dipindah ke Done bila seluruh poin berikut terpenuhi. Poin ini merangkum aturan pada [sprint-backlog.md](sprint-backlog.md), [CONTRIBUTING.md](../../CONTRIBUTING.md), dan [template PR](../../.github/PULL_REQUEST_TEMPLATE.md).

- [ ] CRUD lengkap dengan aktor yang jelas, dan aktor lain mendapat 403.
- [ ] Data dari PBI lain hanya dibaca (seeder/factory); foreign key lintas modul nullable bila memungkinkan.
- [ ] Ada feature test `PbiXX...Test.php` (nama berakhiran `Test.php`) yang mencakup CRUD, validasi, dan penolakan akses.
- [ ] `php artisan test` lulus di lokal dan CI hijau pada Pull Request.
- [ ] Commit, nama branch, dan judul PR memuat issue key Jira (`PKBC-XX`, memakai tanda hubung).
- [ ] PR ke `develop` dengan body template terisi dan checklist hanya dicentang bila benar-benar terpenuhi.
- [ ] Input form memakai `FormRequest`; otorisasi memakai Policy/Gate/Middleware.
- [ ] Berkas sensitif disimpan di storage privat; tidak ada kredensial di commit.
- [ ] Tampilan diuji responsif pada lebar 360 px.
- [ ] Perubahan dicatat di `CHANGELOG.md`.
- [ ] Status issue di Jira diperbarui setelah PR ter-merge.

---

## 5. Risiko

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| Integrasi Tim 2 bergantung pada banyak PBI yang berjalan paralel. | Integrasi menumpuk di akhir sprint. | Mulai integrasi bertahap sejak Week 13; tetapkan PIC per bagian pada PKBC-8. |
| Status Done di Jira tidak selalu sesuai isi `develop` (kasus PBI-11). | Pekerjaan yang belum ada terlihat selesai. | Verifikasi terhadap `develop` dan tes sebelum menandai Done. |
| Commit tanpa issue key. | Panel Development di Jira kosong; progres sulit dilacak. | Terapkan konvensi key; tinjau pada setiap PR. |
| PR berukuran besar (PR #8: 17 commit, 224 berkas). | Sulit direview; rawan konflik. | Satu PR per PBI atau per kelompok kecil. |
| UAT dan deployment dilakukan di akhir. | Bug lintas modul ditemukan terlambat. | Siapkan lingkungan staging lebih awal dan jalankan UAT bertahap. |
