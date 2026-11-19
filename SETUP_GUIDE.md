# Setup Guide - PBI-07 Instansi Bantuan

## ✅ Konfigurasi Database Sudah Selesai

File `.env` sudah dibuat dengan kredensial TiDB Cloud:
```
DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com
DB_PORT=4000
DB_DATABASE=ruangaman
DB_USERNAME=n5FPq1gszYzvgNw.root
DB_PASSWORD=ATnA88fuduu8m5dr
MYSQL_ATTR_SSL_CA=cacert.pem
```

---

## 🚀 Langkah Selanjutnya (Lakukan di Lokal Anda)

### 1. Download SSL Certificate untuk TiDB Cloud

Download file `cacert.pem` dari TiDB Cloud Dashboard atau gunakan URL resmi:

**Option A: Download Manual**
1. Login ke [TiDB Cloud Dashboard](https://tidbcloud.com/)
2. Pilih cluster → Security → Download CA certificate
3. Simpan file `cacert.pem` di root project (`d:\SEMESTER 5\GITHUB\RuangAman\`)

**Option B: Gunakan PowerShell**
```powershell
Invoke-WebRequest -Uri "https://download.cdn.tidb.cloud/public/ca-certs/cacert.pem" -OutFile "cacert.pem"
```

**Option C: Create Empty File (jika tidak tersedia)**
```powershell
New-Item -Path "cacert.pem" -ItemType File
```

> **Note:** Jika menggunakan TiDB Cloud Serverless Free Tier, koneksi mungkin tidak memerlukan SSL. Bisa dicoba tanpa `MYSQL_ATTR_SSL_CA`.

---

### 2. Install Dependencies (Jika Belum)

```powershell
# Install Composer (jika belum ada)
# Download dari https://getcomposer.org/

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm install

# Build assets
npm run build
```

---

### 3. Generate Laravel Application Key

**Option A: Menggunakan Artisan Command**
```powershell
php artisan key:generate
```

**Option B: Generate Manual (alternatif)**
Buka file `.env` dan tambahkan baris berikut:
```env
APP_KEY=base64:[generate-dengan-composer]
```

Atau gunakan PHP langsung:
```powershell
php -r "echo 'base64:' . base64_encode(random_bytes(32));"
```

Kemudian paste hasilnya ke `.env` sebagai nilai `APP_KEY`

---

### 4. Jalankan Migration

```powershell
php artisan migrate
```

Ini akan membuat semua tabel termasuk `instansi_bantuan` dan tabel lainnya.

---

### 5. Seed Data (Opsional)

Jika ada seeders yang perlu dijalankan:
```powershell
php artisan db:seed
```

---

### 6. Start Development Server

```powershell
php artisan serve
```

Server akan running di: `http://localhost:8000`

---

### 7. Access Aplikasi

1. Buka browser
2. Akses: `http://localhost:8000`
3. Register akun baru atau login (jika sudah ada)
4. Setelah login, menu **"Instansi Bantuan"** akan muncul di navigasi
5. Klik menu untuk mengakses fitur manajemen instansi

---

## 🧪 Testing Flow

### Test Create Fitur:
```
1. Navigate ke: http://localhost:8000/instansi
2. Click tombol "Tambah Instansi"
3. Isi form:
   - Nama Instansi: Dinas Sosial Provinsi Jawa Barat
   - Alamat: Jl. Contoh No. 123, Bandung
   - Deskripsi: Lembaga sosial penyedia bantuan
   - Website: https://social.jabarprov.go.id
   - Status: Aktif
4. Click "Simpan"
5. Should redirect ke index page dengan success message
```

### Test Validation:
```
Test validasi server-side:
1. Submit form tanpa mengisi required fields
2. Expected: Error messages muncul
3. Test website field dengan URL invalid
4. Expected: Error "Format website tidak valid"
```

---

## 🔧 Troubleshooting

### Issue: Connection Refused
**Symptom:** SQLSTATE[HY000] [2002] Connection refused
**Solution:** 
- Cek apakah TiDB Cloud instance running
- Verify credentials di `.env`
- Check firewall settings

### Issue: SSL/TLS Error
**Symptom:** SSL connection error
**Solution:**
- Ensure `cacert.pem` ada di root directory
- Try comment out `MYSQL_ATTR_SSL_CA=cacert.pem` temporarily
- Download cert from TiDB dashboard

### Issue: Class Not Found
**Symptom:** InstansiBantuan class not found
**Solution:**
```powershell
php artisan config:clear
php artisan route:clear-cache
php artisan view:clear
```

### Issue: Migration Failed
**Symptom:** Table already exists
**Solution:**
```powershell
php artisan migrate:fresh --seed
```
> **WARNING:** This will delete all existing data!

### Issue: Method Not Allowed (405)
**Symptom:** POST /instansi method not allowed
**Solution:**
- Check CSRF token in forms
- Ensure form uses `method="POST"`
- Clear route cache: `php artisan route:clear-cache`

---

## 📋 Checklist Pre-Production

Sebelum deploy ke production, pastikan:
- [ ] `APP_DEBUG=false` di `.env`
- [ ] `APP_ENV=production` di `.env`
- [ ] SSL certificates properly configured
- [ ] Database backup enabled
- [ ] CORS headers configured
- [ ] Environment variables secured
- [ ] Debug logging disabled
- [ ] Assets optimized (minified)

---

## 📚 Additional Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run specific migration
php artisan migrate --path=/database/migrations/2026_10_29_000023_create_instansi_bantuan_table.php

# View all routes
php artisan route:list

# View specific route
php artisan route:show /instansi

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

---

## 🔐 Security Notes

⚠️ **IMPORTANT:**
- Never commit `.env` file to Git
- Add `.env` to `.gitignore` (should already be there)
- Use different credentials for production
- Enable TiDB Cloud firewall rules (IP whitelist)
- Regularly rotate database passwords

Current `.gitignore` already excludes `.env`, so safe to push.

---

## 💡 Pro Tips

### Quick Start Script
Buat file `start.ps1`:
```powershell
# Quick start script for RuangAman
Set-Location $PSScriptRoot
Write-Host "Starting RuangAman..."
php artisan key:generate
php artisan migrate
php artisan serve
```

Then run:
```powershell
.\start.ps1
```

### Hot Reload (Development)
For Vite hot module reloading:
```powershell
# Terminal 1 - Run Laravel server
php artisan serve

# Terminal 2 - Run Vite dev server
npm run dev
```

---

## 📞 Support

If you encounter any issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode: `APP_DEBUG=true`
3. Check PHP version: `php -v` (should be >= 8.0)
4. Verify composer.lock exists
5. Check memory limit: `memory_limit` in php.ini

---

**Status:** Ready to setup  
**Next:** Execute steps 1-6 above  
**Expected Time:** 5-10 minutes (depends on internet speed for downloads)
