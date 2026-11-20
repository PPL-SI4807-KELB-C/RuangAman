# Rencana Sprint & Product Backlog Item (PBI) RuangAman 📋

Dokumen ini merangkum pembagian kerja, jadwal sprint, dan matriks pertanggungjawaban enam anggota pengembang berdasarkan Bab 5 Proposal Proyek RuangAman.

---

## 1. Jadwal Sprint (Scrum Framework)

Pengembangan berlangsung dalam 2 Sprint sesuai kalender perkuliahan:

### 🚀 Sprint 1: Fondasi Sistem & Fitur Inti
- **Periode**: Week 6 – 10 (19 Oktober 2026 – 22 November 2026)
  - *Week 6*: 19 – 25 Oktober 2026
  - *Week 7*: 26 Oktober – 1 November 2026
  - *Week 8*: 2 – 8 November 2026
  - *Week 9*: 9 – 15 November 2026
  - *Week 10*: 16 – 22 November 2026
- **Sprint Goal**:
  Membangun fondasi bersama (autentikasi, otorisasi RBAC, skema database inti) dan modul inti pelaporan kasus, profil privasi pelapor, master penanganan, instansi bantuan, artikel edukasi, data konselor, halaman statis, glosarium, serta pengumuman.
- **PBI yang Dikerjakan**:
  - Fondasi Bersama (Seluruh Tim)
  - PBI-01, PBI-02, PBI-03 (Ghalib)
  - PBI-04 (Glen)
  - PBI-07 (Haqi)
  - PBI-10, PBI-11 (Niky)
  - PBI-13 (Altaf)
  - PBI-16, PBI-17 (Fauzi)

---

### 🚀 Sprint 2: Fitur Lanjutan, Integrasi Bersama, Pengujian & Demo
- **Periode**: Week 11 – 16 (23 November 2026 – 3 Januari 2027)
  - *Week 11*: 23 – 29 November 2026
  - *Week 12*: 30 November – 6 Desember 2026
  - *Week 13*: 7 – 13 Desember 2026
  - *Week 14*: 14 – 20 Desember 2026
  - *Week 15*: 21 – 27 Desember 2026
  - *Week 16*: 28 Desember 2026 – 3 Januari 2027
- **Sprint Goal**:
  Menyelesaikan fitur lanjutan (kegiatan/webinar, log penanganan, layanan/kontak bantuan, FAQ, feedback artikel, jadwal & sesi konseling, notifikasi sistem) serta mengintegrasikan fitur lintas modul: Dashboard statistik tiga peran, alur persetujuan status terpusat, pelacakan kode publik, tombol Keluar Cepat, UAT, perbaikan bug, dan deployment.
- **PBI yang Dikerjakan**:
  - PBI-05, PBI-06 (Glen)
  - PBI-08, PBI-09 (Haqi)
  - PBI-12 (Niky)
  - PBI-14, PBI-15 (Altaf)
  - PBI-18 (Fauzi)
  - PBI Tim: Integrasi Bersama (Seluruh Anggota Tim)

---

## 2. Matriks Rinci 18 PBI Individu & 2 PBI Tim

| ID PBI | Nama PBI & Deskripsi | PIC | Sprint | Tabel Utama | Referensi Hanya-Baca (Seeder) |
| :--- | :--- | :--- | :---: | :--- | :--- |
| **Tim 1** | **Fondasi Bersama**: Auth, User, Role & Permissions RBAC, reset password, layout utama. | Seluruh Tim | 1–2 | `users`, `roles`, `permissions`, `role_permissions` | - |
| **Tim 2** | **Integrasi Bersama**: Dashboard 3 peran, pelacakan publik, alur persetujuan status kasus terpusat (`CaseApprovalService`), notifikasi & log otomatis lintas modul, Quick Exit, UAT & Testing. | Seluruh Tim | 2 | Seluruh tabel (hanya-baca); mutasi status ke `laporan_kasus`, `penanganan_kasus`, `log_penanganan` | - |
| **PBI-01** | **Manajemen Halaman Informasi Statis**: CRUD halaman statis (Tentang Kami, Visi Misi, Panduan, Kebijakan Privasi). | Ghalib | 1 | `halaman_informasi` | - |
| **PBI-02** | **Manajemen Glosarium Istilah KBG**: CRUD istilah dan definisi seputar kekerasan berbasis gender. | Ghalib | 1 | `glosarium_kbg` | - |
| **PBI-03** | **Manajemen Pengumuman**: CRUD pengumuman publik (periode tayang, status). Tidak mengirim notifikasi otomatis. | Ghalib | 1 | `pengumuman` | `users` |
| **PBI-04** | **Manajemen Status & Penugasan Kasus**: CRUD master status dan CRUD penugasan kasus ke konselor pendamping (dipecah 2 sub-task). | Glen | 1 | `status_kasus`, `penanganan_kasus` | `laporan_kasus`, `konselor`, `users` |
| **PBI-05** | **Manajemen Kegiatan/Webinar Edukasi**: CRUD webinar edukasi beserta pendaftaran peserta mandiri & verifikasi kehadiran. | Glen | 2 | `kegiatan_edukasi`, `pendaftaran_kegiatan` | `users` |
| **PBI-06** | **Log Penanganan Kasus**: CRUD catatan aktivitas manual per laporan; `penanganan_id` nullable. Log status otomatis (immutable) dibuat oleh Tim 2. | Glen | 2 | `log_penanganan` | `laporan_kasus`, `users` |
| **PBI-07** | **Manajemen Instansi Bantuan**: CRUD direktori lembaga penyedia layanan bantuan & rujukan korban. | Haqi | 1 | `instansi_bantuan` | - |
| **PBI-08** | **Manajemen Layanan & Kontak Bantuan**: CRUD rincian layanan spesifik dan hotline/kontak darurat per instansi (dipecah 2 sub-task). | Haqi | 2 | `layanan_bantuan`, `kontak_bantuan` | `instansi_bantuan` |
| **PBI-09** | **Manajemen FAQ / Pusat Bantuan**: CRUD pertanyaan yang sering diajukan dan jawaban informasi umum. | Haqi | 2 | `faq` | - |
| **PBI-10** | **Manajemen Artikel Edukasi**: CRUD materi artikel edukasi pencegahan & pemulihan KBG; kategori default "Umum" dari seeder. | Niky | 1 | `artikel` | `kategori_artikel`, `users` |
| **PBI-11** | **Manajemen Kategori Artikel**: CRUD pengelompokan tema artikel edukasi; kategori yang dipakai artikel tidak bisa dihapus. | Niky | 1 | `kategori_artikel` | - |
| **PBI-12** | **Manajemen Feedback Artikel**: CRUD komentar & ulasan pengguna terhadap artikel serta moderasi admin. | Niky | 2 | `feedback_artikel` | `artikel`, `users` |
| **PBI-13** | **Manajemen Data Konselor/Psikolog**: CRUD biodata, kualifikasi/spesialisasi, dan status ketersediaan konselor. | Altaf | 1 | `konselor` | `users` |
| **PBI-14** | **Manajemen Jadwal Konseling**: CRUD slot jadwal ketersediaan sesi tatap muka online maupun offline. | Altaf | 2 | `jadwal_konseling` | `konselor`, `users` |
| **PBI-15** | **Manajemen Sesi Konseling**: CRUD riwayat sesi, catatan konseling, dan rekomendasi hasil akhir; merujuk `konselor_id` + `laporan_id`, `jadwal_id` nullable. | Altaf | 2 | `sesi_konseling` | `konselor`, `laporan_kasus`, `jadwal_konseling` (opsional) |
| **PBI-16** | **Manajemen Laporan Kasus & Lampiran Bukti**: C: formulir pengaduan + kode lacak + lampiran privat; R: "Laporan saya" & lacak; U: tambah keterangan/lampiran saat *Menunggu peninjauan*; D: tarik laporan sebelum ditinjau (soft delete). | Fauzi | 1 | `laporan_kasus`, `lampiran_laporan` | `users`, `status_kasus` |
| **PBI-17** | **Manajemen Mode Anonim & Profil Pelapor**: C: profil alias saat registrasi; R/U: "Profil saya" (alias & preferensi anonim); D: reset alias/hapus akun (laporan tetap anonim). | Fauzi | 1 | `profil_pelapor` | `users` |
| **PBI-18** | **Manajemen Notifikasi**: C: Admin membuat notifikasi (per user/role/semua); R: daftar & badge belum dibaca; U: tandai dibaca, Admin edit; D: hapus/tarik. Notifikasi otomatis dipindah ke Tim 2. | Fauzi | 2 | `notifikasi` | `users`, `roles` |

---

## 3. Aturan Independensi & CRUD PBI (revisi 20 November 2026)

1. **Setiap PBI wajib punya Create, Read, Update, Delete** dengan aktor yang jelas, dan aktor lain mendapat 403.
2. **Data dari PBI lain hanya dibaca** dan disediakan lewat seeder/factory (kolom "Referensi Hanya-Baca"). Setiap PBI harus bisa dikembangkan, diuji, dan didemokan tanpa menunggu PBI lain.
3. **Foreign key ke modul lain dibuat nullable** bila memungkinkan (mis. `log_penanganan.penanganan_id`, `sesi_konseling.jadwal_id`, `artikel.kategori_id` default "Umum").
4. **Perilaku lintas modul otomatis** (notifikasi peristiwa, log perubahan status) menjadi tanggung jawab PBI Tim 2.
5. **Setiap PBI punya feature test** `PbiXX...Test` yang mencakup CRUD dan penolakan akses.
6. **Commit, branch, dan judul PR memuat issue key Jira** (`[PKBC-XX]`).

Deskripsi lengkap (user story, CRUD, dependensi, acceptance criteria) ada di masing-masing issue Jira project PKBC.
