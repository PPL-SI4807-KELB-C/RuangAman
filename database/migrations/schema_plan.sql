-- SQL Schema Migration Plan for RuangAman (26 Unique Tables)
-- Engine: InnoDB, Charset: utf8mb4, Collation: utf8mb4_unicode_ci
--
-- Sumber kebenaran kolom: docs/database/data-dictionary.md
-- Urutan pembuatan di bawah ini sudah memenuhi dependensi foreign key
-- (induk dibuat sebelum anak). Petunjuk lengkap: Bagian 4 kamus data.

-- KEPUTUSAN SKEMA (29 Oktober 2026)
-- 1. Nama PK mengikuti Tabel 4.3.1: <entitas>_id. PK users = user_id.
--    Setiap model: protected $primaryKey = '<entitas>_id';
-- 2. Kolom sandi: password_hash. Model User wajib override getAuthPassword().
-- 3. users.remember_token ditambahkan (kebutuhan FR-02 / remember me).
-- 4. laporan_kasus.judul ditambahkan (FR-11, UC-01, Mockup 4.5.3).
-- 5. log_penanganan.penanganan_id NULL-able.
-- 6. log_penanganan.laporan_id ditambahkan (audit penolakan laporan).
-- 7. log_penanganan.jenis_log: hanya 'otomatis_status' dan 'manual_catatan'.
-- 8. artikel.slug dipertahankan; jadwal_konseling.user_id NULL-able.

-- DOMAIN 1: USERS, ACCESS & NOTIFICATIONS
-- 1.  roles                PK role_id        | FK -
-- 2.  permissions          PK permission_id  | FK -
-- 3.  role_permissions     PK (role_id, permission_id) | FK roles, permissions
-- 4.  users                PK user_id        | FK roles
-- 5.  profil_pelapor       PK profil_id      | FK users (PBI-17 Fauzi)
-- 6.  notifikasi           PK notifikasi_id  | FK users (PBI-18 Fauzi)

-- DOMAIN 2: PELAPORAN & PENANGANAN KASUS
-- 7.  status_kasus         PK status_id      | FK -                                    (PBI-04 Glen)
-- 8.  laporan_kasus        PK laporan_id     | FK users, status_kasus                   (PBI-16 Fauzi)
-- 9.  lampiran_laporan     PK lampiran_id    | FK laporan_kasus                         (PBI-16 Fauzi)
-- 10. penanganan_kasus     PK penanganan_id  | FK laporan_kasus (unique), users         (PBI-04 Glen)
-- 11. log_penanganan       PK log_id         | FK laporan_kasus, penanganan_kasus (NULL-able), users (PBI-06 Glen)

-- DOMAIN 3: KONSELING
-- 12. konselor             PK konselor_id    | FK users (unique)                        (PBI-13 Altaf)
-- 13. jadwal_konseling     PK jadwal_id      | FK konselor, users (NULL-able)           (PBI-14 Altaf)
-- 14. sesi_konseling       PK sesi_id        | FK jadwal_konseling (unique)             (PBI-15 Altaf)

-- DOMAIN 4: EDUKASI & PUBLIKASI
-- 15. halaman_informasi    PK halaman_id     | FK -                                     (PBI-01 Ghalib)
-- 16. glosarium_kbg        PK glosarium_id   | FK -                                     (PBI-02 Ghalib)
-- 17. pengumuman           PK pengumuman_id  | FK users                                 (PBI-03 Ghalib)
-- 18. kategori_artikel     PK kategori_id    | FK -                                     (PBI-11 Niky)
-- 19. artikel              PK artikel_id     | FK kategori_artikel, users               (PBI-10 Niky)
-- 20. feedback_artikel     PK feedback_id    | FK artikel, users                        (PBI-12 Niky)

-- DOMAIN 5: KEGIATAN & INSTANSI BANTUAN
-- 21. kegiatan_edukasi     PK kegiatan_id    | FK -                                     (PBI-05 Glen)
-- 22. pendaftaran_kegiatan PK pendaftaran_id | FK kegiatan_edukasi, users (unique pair) (PBI-05 Glen)
-- 23. instansi_bantuan     PK instansi_id    | FK -                                     (PBI-07 Haqi)
-- 24. layanan_bantuan      PK layanan_id     | FK instansi_bantuan                      (PBI-08 Haqi)
-- 25. kontak_bantuan       PK kontak_id      | FK instansi_bantuan                      (PBI-08 Haqi)
-- 26. faq                  PK faq_id         | FK -                                     (PBI-09 Haqi)

-- CATATAN: file ini hanya rencana. Migrasi Laravel yang sebenarnya harus dibuat
-- per PBI pada database/migrations/YYYY_MM_DD_HHMMSS_create_<tabel>_table.php.
-- Bila tabel framework diperlukan (sessions, cache, cache_locks, jobs, job_batches,
-- failed_jobs, password_reset_tokens), generasikan lewat fondasi bersama, BUKAN PBI individu.
