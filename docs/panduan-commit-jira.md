# Panduan Commit agar Terbaca di Jira

Dokumen ini acuan untuk **anggota tim dan AI agent** yang membuat branch, commit, atau Pull Request di repo ini. Tujuannya: setiap perubahan tertaut otomatis ke issue Jira dan muncul di panel **Development** pada issue tersebut.

- Repo: `PPL-SI4807-KELB-C/RuangAman`
- Jira: project **PKBC** (`ghalibsfrz27.atlassian.net`)
- Integrasi: GitHub for Atlassian

## 1. Aturan utama

Jira menautkan kode ke issue lewat **issue key** yang tertulis di nama branch, pesan commit, atau judul PR. Cukup salah satunya, tetapi pakai ketiganya.

Format key wajib **`PKBC-<angka>`**: huruf besar, ada tanda hubung, tanpa spasi.

| Benar | Salah (tidak terbaca Jira) |
|---|---|
| `PKBC-19` | `PKBC 19`, `pkbc-19x`, `PBI-11`, `PKBC19` |

> `PBI-11` adalah nomor PBI, bukan key Jira. Key Jira PBI-11 adalah `PKBC-19`. Pakai tabel di bagian 5.

## 2. Nama branch

```
feature/PKBC-<angka>-<deskripsi-singkat>
bugfix/PKBC-<angka>-<deskripsi-singkat>
docs/PKBC-<angka>-<deskripsi-singkat>
```

Contoh: `feature/PKBC-20-feedback-artikel`

## 3. Pesan commit

Letakkan key di awal pesan, lalu ikuti Conventional Commits:

```
[PKBC-20] feat(PBI-12): tambah form feedback artikel
[PKBC-20] test(PBI-12): uji validasi dan RBAC feedback
[PKBC-7] docs: perbarui panduan commit
```

Satu commit boleh memuat beberapa key: `[PKBC-19] [PKBC-18] feat: ...`

## 4. Judul Pull Request

Judul PR wajib memuat key, karena panel Jira membaca judul PR:

```
[PKBC-20] PBI-12 Manajemen Feedback Artikel
```

PR ke `develop`. Branch `main` dilindungi, jadi tidak ada push langsung dan CI wajib hijau.

## 5. Daftar issue Jira

| Key | Isi | | Key | Isi |
|---|---|---|---|---|
| PKBC-7 | Fondasi Bersama (Auth, RBAC, layout) | | PKBC-18 | PBI-10 Artikel Edukasi |
| PKBC-8 | Integrasi Bersama (dashboard, UAT) | | PKBC-19 | PBI-11 Kategori Artikel |
| PKBC-9 | PBI-01 Halaman Informasi | | PKBC-20 | PBI-12 Feedback Artikel |
| PKBC-10 | PBI-02 Glosarium KBG | | PKBC-21 | PBI-13 Data Konselor |
| PKBC-11 | PBI-03 Pengumuman | | PKBC-22 | PBI-14 Jadwal Konseling |
| PKBC-12 | PBI-04 Status & Penugasan Kasus | | PKBC-23 | PBI-15 Sesi Konseling |
| PKBC-13 | PBI-05 Kegiatan/Webinar | | PKBC-24 | PBI-16 Laporan Kasus & Lampiran |
| PKBC-14 | PBI-06 Log Penanganan Kasus | | PKBC-25 | PBI-17 Mode Anonim & Profil Pelapor |
| PKBC-15 | PBI-07 Instansi Bantuan | | PKBC-26 | PBI-18 Notifikasi |
| PKBC-16 | PBI-08 Layanan & Kontak Bantuan | | | |
| PKBC-17 | PBI-09 FAQ / Pusat Bantuan | | | |

Epic: PKBC-1 Fondasi & Autentikasi, PKBC-2 Pelaporan & Penanganan Kasus, PKBC-3 Layanan Konseling, PKBC-4 Edukasi & Konten, PKBC-5 Pusat Bantuan, PKBC-6 Integrasi, UAT & Deployment. Commit ditautkan ke PBI-nya, bukan ke epic.

## 6. Agar commit terhitung atas nama Anda

Jira dan GitHub mencocokkan commit ke akun lewat email Git. Sebelum commit, pastikan:

```bash
git config user.name  "Nama Anda"
git config user.email "email-yang-terdaftar-di-akun-GitHub-Anda"
```

Email harus sama dengan salah satu email di GitHub Settings, Emails. Kalau tidak, commit tampil sebagai kontributor asing dan tidak terhubung ke akun Anda.

## 7. Aturan khusus untuk AI agent

1. Ambil key dari tabel bagian 5. Jangan mengarang key. Kalau PBI yang dikerjakan tidak ada di tabel, tanyakan ke manusia.
2. Tulis key di nama branch, semua pesan commit, dan judul PR.
3. Pakai identitas Git milik manusia yang meminta pekerjaan (bagian 6). Jangan menambahkan baris `Co-Authored-By:` atau penanda agent lain di pesan commit atau deskripsi PR. Tim memutuskan agar kontributor repo hanya anggota tim.
4. Jangan menulis ulang riwayat (`rebase`, `--amend`, `force push`) pada branch bersama seperti `develop` dan `main`.
5. Jalankan `php artisan test` sebelum push. CI harus hijau sebelum merge.
6. Satu PBI per branch, dan hanya ubah file milik PBI itu. Data dari PBI lain dibaca saja (lihat `docs/sprints/sprint-backlog.md`).

## 8. Cek hasilnya

Buka issue di Jira, lihat panel **Development**. Branch, commit, dan PR harus tampil dalam beberapa menit. Kalau tidak tampil:

- Pastikan key tertulis `PKBC-<angka>` dengan tanda hubung.
- Pastikan branch sudah di-push ke GitHub.
- Pastikan integrasi GitHub for Atlassian masih terhubung ke repo ini. Tanyakan ke pemilik Jira site.
