# Deploy RuangAman Gratis (Render + TiDB Cloud)

Arsitektur: **Render** (web service Docker, paket Free) + **TiDB Cloud Serverless** (database kompatibel MySQL, gratis 5 GB).
Alternatif database: **Aiven for MySQL** (paket Free).

## 1. Siapkan database (TiDB Cloud Serverless)

1. Daftar di <https://tidbcloud.com> → **Create Cluster** → pilih *Serverless / Starter* (gratis), region **Singapore**.
2. Buka cluster → **Connect** → pilih *Connect with: General*, lalu **Generate Password**.
3. Catat: `HOST`, `PORT` (4000), `USER` (format `xxxx.root`), `PASSWORD`.
4. Buka **SQL Editor** dan jalankan `CREATE DATABASE ruangaman;`.

> TiDB memakai sertifikat publik, jadi cukup isi `MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt`.
> Jika memakai Aiven, unduh `ca.pem`, lalu tempel **seluruh isinya** ke env `DB_SSL_CA_CONTENT` (kosongkan `MYSQL_ATTR_SSL_CA`).

## 2. Buat web service di Render

1. Daftar di <https://render.com> dengan akun GitHub, lalu izinkan akses ke repo RuangAman.
2. **New → Blueprint** → pilih repo ini → Render membaca `render.yaml`.
   (Atau **New → Web Service** → Runtime *Docker* → Instance *Free*.)
3. Pilih branch yang akan dideploy (disarankan `develop`).
4. Isi environment variable yang ditandai `sync: false`:

| Key | Nilai |
|---|---|
| `APP_KEY` | hasil `php artisan key:generate --show` di laptop |
| `APP_URL` | `https://<nama-service>.onrender.com` |
| `DB_HOST` | host TiDB |
| `DB_PORT` | `4000` |
| `DB_DATABASE` | `ruangaman` |
| `DB_USERNAME` | user TiDB (`xxxx.root`) |
| `DB_PASSWORD` | password TiDB |
| `MYSQL_ATTR_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` (tambahkan manual) |

5. **Deploy**. Saat container start, `docker/entrypoint.sh` otomatis:
   cache config/route/view → `migrate --force` → seed data awal **hanya jika** tabel `users` kosong.
6. Buka `https://<nama-service>.onrender.com/up`. Jika tampil halaman sehat, deploy berhasil.

## 3. Setelah online (wajib)

- **Ganti password akun demo** (`admin@ruangaman.test`, `konselor@…`, `pelapor@…`), karena password bawaannya `password`.
- Setiap kali push ke branch yang dipilih, Render akan deploy ulang otomatis.

## Batasan paket gratis

| Hal | Dampak |
|---|---|
| Render Free tidur setelah ±15 menit tanpa trafik | Akses pertama butuh ±30–60 detik untuk bangun |
| Disk Render bersifat sementara | **Lampiran laporan yang diunggah hilang** saat redeploy/restart. Untuk produksi, pakai S3/R2 |
| 750 jam instance/bulan | Cukup untuk satu service yang menyala terus |
| TiDB Serverless | Batas 5 GB penyimpanan dan kuota Request Unit bulanan |

Ini cocok untuk demo atau sprint review, belum untuk data korban sungguhan.

## Uji image secara lokal (opsional)

```bash
docker build -t ruangaman .
docker run -p 8080:8080 --env-file .env.production ruangaman
```
