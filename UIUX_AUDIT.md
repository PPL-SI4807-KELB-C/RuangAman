# Audit UI/UX — RuangAman

**Tanggal:** 20 November 2026
**Branch:** `feature/PKBC-7-dashboards-dan-batas-peran`
**Cakupan:** 83 file Blade (`resources/views`), 15 komponen di `resources/views/components`, token di `resources/css/app.css`, dan perilaku Alpine di `resources/js/app.js`.

### Metode

- **Analisis kode** semua view dan komponen, ditambah hitungan otomatis (grep) untuk warna, ukuran font, radius, shadow, dan inline style.
- **Kontras** dihitung dengan rumus luminans relatif WCAG 2.2 memakai nilai hex token. Netral Tailwind v4 dikonversi ke sRGB: neutral-400 `#a1a1a1`, neutral-500 `#737373`, neutral-600 `#525252`.
- **Aplikasi dijalankan** (`php artisan serve` + Vite) dan diukur lewat DOM di **1440px, 768px, dan 375px**: font terhitung, overflow horizontal, ukuran target sentuh, dan tinggi header.
  - Screenshot tidak bisa diambil karena browser pane terus timeout. **Penilaian estetika visual murni (rasa, keseimbangan ilustrasi) karena itu berdasar kode dan pengukuran DOM, bukan tangkapan layar.**
- Halaman yang diukur langsung memakai akun Pelapor: beranda, `/laporan/create`, `/artikel`, dan `/dashboard`. Halaman admin dinilai dari kode.

### Asumsi

1. Pengguna utama adalah **korban/penyintas KBG**: kemungkinan sedang cemas, sering memakai ponsel, kadang di perangkat yang tidak sepenuhnya aman. Pengguna sekunder adalah Admin Kasus dan Konselor di desktop.
2. Kesan yang dituju: **tenang, netral, tepercaya, tidak menghakimi**. Ini konsisten dengan copy dan komentar "Figma Exact" di layout.
3. Desain Figma (panel abu `#f2f2f2`, hairline, tanpa shadow, aksen hijau) adalah acuan yang disepakati tim. Audit ini menilai implementasinya, bukan menggugat arah Figma.

---

## 1. Ringkasan Eksekutif

**Skor total: 5,6 / 10.** Fondasinya sudah benar, tetapi aksesibilitas dan tipografi menurunkan kualitas secara nyata.

**3 kekuatan utama**

1. **Fitur keselamatan dipikirkan serius.**
   - Ada tombol Keluar Cepat di setiap layout plus pintasan Esc dua kali (`components/quick-exit.blade.php`, `resources/js/app.js`).
   - Banner keselamatan dan skip link tersedia (`layouts/app.blade.php:15-26`).
   - Kode lacak tidak pernah muncul di URL.
   - Ini sangat cocok untuk konteks KBG.
2. **Sudah ada pustaka komponen yang dipakai luas.**
   - `x-button` dipakai di 52 file. Ada `x-input`, `x-select`, `x-textarea`, `x-alert`, `x-badge`, `x-card`, dan `x-confirm-modal` global.
   - Komponen form sudah memasang `label for`, `aria-invalid`, dan `aria-describedby` untuk error (`components/input.blade.php:22-47`).
3. **Layout stabil dan responsif.**
   - Kontainer `max-w-6xl` konsisten.
   - Tidak ada overflow horizontal di 1440, 768, maupun 375px (terukur `scrollWidth` ≤ viewport).
   - Tabel dibungkus `overflow-x-auto` (14 file).

**3 masalah terbesar**

1. **Tombol utama gagal kontras (Kritis).**
   - Teks putih di `brand-600 #10a349` = **3,30:1**, padahal ukurannya 12–14px.
   - Tombol bahaya putih di `safety-rose #f43f5e` = **3,67:1**.
   - Keduanya di bawah AA 4,5:1, padahal inilah tombol "Kirim laporan" dan "Hapus".
2. **Tipografi tidak sesuai desain dan terlalu kecil.**
   - Font **Inter tidak pernah dimuat**, dan nama tokennya salah untuk Tailwind v4 (`--font-family-sans` seharusnya `--font-sans`). Akibatnya font terhitung di browser adalah system stack: Segoe UI di Windows, Roboto di Android, SF di Mac.
   - Teks 11px (`text-[11px]`) dipakai **146 kali**, dan `text-xs` (12px) **312 kali** sebagai ukuran label, tombol, dan isi.
3. **Batas komponen form hampir tak terlihat dan fokus keyboard lemah (Tinggi).**
   - Ring input `neutral-300/70` di atas putih = **1,31:1** (syarat WCAG 1.4.11 adalah 3:1).
   - Ring fokus `neutral-400` = **2,58:1**.
   - Target sentuh Keluar Cepat hanya **30px**, padahal ini tombol darurat.

---

## 2. Tabel Skor

| # | Aspek | Skor | Alasan singkat |
|---|---|---|---|
| 1 | Warna | **5** | Palet sempit dan sesuai kesan tenang, tetapi warna aksi utama gagal kontras AA dan `#f2f2f2` di-hardcode 57 kali. |
| 2 | Tipografi | **4** | Inter tidak termuat, skala didominasi 11–12px, dan ada dua gaya H1 yang bersaing. |
| 3 | Komposisi & Layout | **7** | Grid 12 kolom dan kontainer konsisten tanpa overflow, tetapi chrome atas di mobile memakan 122px dan navigasi desktop baru muncul di ≥1280px. |
| 4 | Konsistensi | **6** | Komponen bersama ada, tetapi modul Artikel memakai palet `slate`, input mentah, dan shadow sendiri. `x-page-header` baru dipakai di 6 dari 83 file. |
| 5 | Kesesuaian tujuan & pengguna | **7** | Alias, Keluar Cepat, dan copy empatik sudah tepat, tetapi tidak ada kontak darurat (mis. SAPA 129) dan Keluar Cepat tampil seperti tombol sekunder abu. |
| 6 | Usability (UX) | **6** | Flash message dan modal konfirmasi baik, tetapi tidak ada state loading/anti dobel-submit dan empty state belum seragam. |
| 7 | Aksesibilitas | **4** | Label, skip link, dan `aria-invalid` baik, tetapi kontras teks/UI/fokus gagal, target sentuh kecil, 13 SVG tanpa `aria-hidden`, dan modal tanpa focus trap. |
| 8 | Kerapian implementasi | **6** | Hanya 1 inline style (wajar), tetapi menu navigasi diduplikasi penuh desktop/mobile, nilai arbitrer tersebar, dan heading admin ditulis ulang tanpa komponen. |
| | **Rata-rata** | **5,6** | |

---

## 3. Temuan Detail

Tingkat keparahan: **Kritis** (menghalangi atau melanggar AA pada alur inti) · **Tinggi** · **Sedang** · **Rendah**.

### 3.1 Warna

**Tabel kontras terukur**

| Pasangan | Rasio | Target | Hasil |
|---|---|---|---|
| Putih di `brand-600 #10a349` (tombol primary, CTA hero) | 3,30:1 | 4,5:1 | ❌ |
| Putih di `brand-700 #0e8e40` (hover) | 4,23:1 | 4,5:1 | ❌ |
| `brand-700` di putih (teks badge brand) | 4,23:1 | 4,5:1 | ❌ |
| Putih di `safety-rose #f43f5e` (tombol danger) | 3,67:1 | 4,5:1 | ❌ |
| `safety-rose` di putih (teks error 11px) | 3,67:1 | 4,5:1 | ❌ |
| `safety-rose` di `#f2f2f2` | 3,28:1 | 4,5:1 | ❌ |
| `neutral-500` di putih (helper) | 4,74:1 | 4,5:1 | ✅ tipis |
| `neutral-500` di `#f2f2f2` (helper di dalam `x-card`) | 4,24:1 | 4,5:1 | ❌ |
| `neutral-600` di `#f2f2f2` | 6,98:1 | 4,5:1 | ✅ |
| `neutral-400` di putih (placeholder, ikon cari) | 2,58:1 | 4,5:1 / 3:1 | ❌ |
| `slate-400` di putih (tanggal artikel) | 2,63:1 | 4,5:1 | ❌ |
| `amber-600` di putih (tautan "Ubah" di admin kegiatan) | 3,20:1 | 4,5:1 | ❌ |
| `#f2f2f2` (panel) vs putih | 1,12:1 | — | Panel hanya terbaca lewat border |

**Temuan**

| Masalah | Bukti | Dampak | Keparahan |
|---|---|---|---|
| Warna aksi primary gagal kontras AA | `components/button.blade.php:13` (`bg-brand-600 text-white`), CTA hero `welcome.blade.php:25`, `app.css:16` | Tombol terpenting ("Buat laporan", "Kirim laporan") sulit dibaca bagi pengguna low vision atau di layar ponsel yang terang di luar ruangan | **Kritis** |
| Warna bahaya/error gagal kontras | `button.blade.php:15`, pesan error `input.blade.php:47`, `select.blade.php:52`, `textarea.blade.php:46`, `radio-cards.blade.php:60` | Pesan validasi 11px berwarna rose sulit terbaca tepat saat pengguna paling butuh petunjuk | **Tinggi** |
| Helper text abu di atas panel abu gagal kontras | `neutral-500` di dalam `x-card` (`card.blade.php:4` `bg-[#f2f2f2]`), mis. `laporan/create.blade.php:43,89,101`, `:137` | Teks panduan keselamatan dan privasi justru yang paling redup | **Tinggi** |
| Warna panel di-hardcode, bukan token | `bg-[#f2f2f2]` muncul **57×** (mis. `card.blade.php:4`, `alert.blade.php:21`, `quick-exit.blade.php:8`, `layouts/app.blade.php:18`) | Mengganti tone permukaan butuh 57 edit dan rawan terlewat | **Sedang** |
| Palet kedua (`slate`) bocor | `slate` dipakai 71× di 7 file modul Artikel (`admin/artikel/index.blade.php` 23×, `artikel/index.blade.php` 13×). Gradien `from-brand-50 to-slate-50` di `artikel/index.blade.php:7` | Abu kebiruan berbeda dari netral abu murni di modul lain, jadi Artikel terasa seperti aplikasi lain | **Sedang** |
| Warna semantik di luar token | `bg-amber-500` (alert, badge, `pengumuman/show.blade.php:22`), `bg-blue-500` (`laporan/track-result.blade.php:93`), sedangkan token `safety-warning`/`safety-info` sudah ada di `app.css:23-24` | Token warning/info praktis tidak dipakai (`safety-info` 2×), sehingga makna warna tidak terpusat | **Rendah** |
| Dua warna "primary" bersaing | Tombol "Daftar" di header hitam `bg-neutral-900` (`layouts/app.blade.php:118`), sedangkan CTA hero dan form hijau `brand-600` | Pengguna baru melihat dua gaya tombol utama, dan hierarki aksi kabur | **Rendah** |

Catatan positif: makna status memakai **titik berwarna + teks** (`badge.blade.php`), bukan warna saja. Itu sudah memenuhi WCAG 1.4.1.

### 3.2 Tipografi

| Masalah | Bukti | Dampak | Keparahan |
|---|---|---|---|
| Inter tidak pernah dimuat, dan token salah nama | `app.css:27` `--font-family-sans` (Tailwind v4 memakai `--font-sans`). Tidak ada `<link>` font atau `@font-face`. Font terhitung di browser: `-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto…` | Tampilan berbeda di tiap OS dan tidak sesuai Figma; metrik huruf dan line-break tidak bisa diprediksi | **Tinggi** |
| Skala teks terlalu kecil untuk isi | `text-[11px]` 146×, `text-xs` 312×, `text-sm` 181×, `text-base` hanya 14×. Label form `text-xs` (`input.blade.php:22`), tombol md `text-xs` (`button.blade.php:23`), helper 11px | Untuk pengguna cemas di ponsel, 11–12px melelahkan. Di iOS, input <16px memicu auto-zoom (input sekarang 14px) | **Tinggi** |
| Dua gaya H1 bersaing | Halaman publik: `text-3xl sm:text-4xl font-extrabold leading-[1.15]` (mis. `glosarium/index.blade.php:15`). Admin: `text-2xl font-extrabold`. Modul Artikel: `font-bold text-slate-900` (`admin/artikel/index.blade.php:10`) | Hierarki tidak terprediksi; bobot `bold` vs `extrabold` dipakai acak | **Sedang** |
| Lompatan skala tidak teratur | Hero 48px (`welcome.blade.php:16`) langsung ke deskripsi 16px, lalu kartu 12px. `text-lg`/`text-xl` hampir tidak dipakai (2× dan 2×) | Tidak ada level H3/H4 yang konsisten, sehingga judul kartu (`card.blade.php:6`, 14px semibold) hampir sama dengan body | **Sedang** |
| Gaya kapital judul tidak seragam | Sentence case "Buat laporan", "Daftar instansi" vs Title Case "Manajemen Instansi Bantuan", "Temukan Informasi untuk Memahami dan Bertindak Aman" (`artikel/index.blade.php:11`) | Tone terasa tidak konsisten antar modul | **Rendah** |

Yang sudah baik: `leading-relaxed` pada paragraf, `max-w-2xl`/`max-w-xl` membatasi panjang baris (sekitar 60–75 karakter), dan `antialiased`.

### 3.3 Komposisi & Layout

| Masalah | Bukti | Dampak | Keparahan |
|---|---|---|---|
| Chrome atas memakan layar mobile | Terukur di 375px: banner 49px + header 73px = **122px**. H1 beranda baru mulai di y=226px dari 812px | Di ponsel kecil, seperempat layar habis sebelum konten | **Sedang** |
| Navigasi desktop baru muncul di ≥1280px | `layouts/app.blade.php:40` `hidden xl:flex` | Laptop 1024–1279px dan tablet landscape mendapat hamburger berisi 9+ item; navigasi tersembunyi dan butuh dua klik | **Sedang** |
| Navigasi terlalu padat | 8 tautan publik + menu Admin + Akun + Keluar Cepat dalam satu baris (`layouts/app.blade.php:40-125`) | Beban kognitif tinggi; tidak ada pengelompokan (mis. "Edukasi" berisi Artikel, Glosarium, Kegiatan) | **Sedang** |
| Tabel admin di mobile hanya bisa di-scroll horizontal | Tabel dashboard Pelapor lebar 543px di viewport 375px (terukur) | Data tetap terbaca, tetapi kolom aksi tersembunyi di kanan | **Rendah** |
| Skala spacing campuran | `py-2.5`, `py-3.5`, `p-3.5`, `gap-2.5`, `gap-1.5`, `mt-1.5` sangat sering | Masih di kelipatan 2px, tetapi bukan skala 4/8px yang tegas; ritme vertikal antar halaman sedikit berbeda | **Rendah** |

Yang sudah baik: grid `lg:grid-cols-12` dengan pola 8/4 (form + panduan) konsisten di Laporan dan Instansi; tidak ada overflow horizontal di tiga ukuran layar; footer dan kontainer seragam.

### 3.4 Konsistensi

| Masalah | Bukti | Dampak | Keparahan |
|---|---|---|---|
| Modul Artikel keluar dari design system | `admin/artikel/*` dan `artikel/*`: `rounded-lg border-slate-300` input mentah (`artikel/index.blade.php:22,27`), kartu `bg-white shadow-sm hover:shadow-md` (`:52`), tombol aksi buatan sendiri (`admin/artikel/index.blade.php:84-94`) | Satu-satunya modul dengan shadow dan border slate; terlihat jelas saat berpindah menu | **Tinggi** |
| Komponen kepala halaman jarang dipakai | `x-page-header` hanya di 6 file (instansi ×4, laporan ×2). 40+ halaman lain menulis `<h1>` sendiri dengan class berbeda | Setiap halaman baru menambah varian; spacing "kembali ke…" juga berbeda (`mt-5`, `mt-3`, `mt-2`, `mt-1`) | **Sedang** |
| Empty state belum seragam | `x-empty-state` hanya dipakai di 1 file; Artikel memakai versi sendiri (`artikel/index.blade.php:42-48`), tabel memakai `<td colspan>` teks polos | Pesan "kosong" tampil dengan 3+ gaya | **Sedang** |
| Radius dan shadow terlalu beragam | Radius: `rounded` 58×, `rounded-lg` 54×, `rounded-xl` 47×, `rounded-md` 1×, `rounded-2xl` 2×. Shadow: 5 varian (`shadow-xs/sm/md/lg/shadow`) padahal Figma "tanpa shadow" (`card.blade.php:3`) | Tidak ada aturan kapan memakai radius 4/8/12px | **Sedang** |
| Tombol mentah di luar `x-button` | Tombol aksi tabel buatan sendiri di `admin/artikel/index.blade.php:84-94`, `admin/kegiatan-edukasi/index.blade.php:159` (`text-amber-600`), modal (`confirm-modal.blade.php:103-117`) | Varian "aksi baris tabel" belum ada di komponen, jadi tiap modul membuat sendiri | **Rendah** |

### 3.5 Kesesuaian dengan Tujuan & Pengguna

| Masalah | Bukti | Dampak | Keparahan |
|---|---|---|---|
| Tidak ada kontak darurat yang terlihat | Hanya teks "Bukan layanan darurat… cari bantuan setempat" (`welcome.blade.php:52`); tidak ada nomor SAPA 129 / 112 / hotline di header, footer, atau halaman sukses | Korban dalam bahaya langsung tidak mendapat jalan keluar yang konkret | **Tinggi** |
| Tombol Keluar Cepat kurang menonjol | `quick-exit.blade.php:8`: abu `bg-[#f2f2f2]` 12px, tinggi 30px, sejajar tombol lain | Dalam panik, tombol ini harus paling mudah ditemukan, tetapi sekarang tampil seperti tombol sekunder | **Tinggi** |
| CTA utama sudah jelas | Hero "Buat laporan" (hijau) + "Lacak laporan Anda" (tautan) di `welcome.blade.php:24-31` | Positif, dengan catatan kontrasnya (3.1) | — |
| Tone visual sudah tepat | Netral, tanpa foto stok dramatis, copy empatik ("Tidak harus lengkap", `laporan/create.blade.php:124`) | Positif dan sesuai pengguna rentan | — |

### 3.6 Usability (UX)

| Masalah | Heuristik Nielsen | Bukti | Dampak | Keparahan |
|---|---|---|---|---|
| Tidak ada state loading atau pencegah dobel-submit | #1 Visibilitas status sistem | `x-button` tidak punya prop `loading`; form upload lampiran 10 MB (`laporan/create.blade.php:25`) tidak memberi tanda proses | Di jaringan lambat, pengguna menekan "Kirim laporan" berkali-kali dan berisiko membuat laporan ganda atau bingung | **Tinggi** |
| Placeholder kronologi terlalu panjang | #6 Recognition | `laporan/create.blade.php:62`: placeholder 2 kalimat 2,58:1 | Bisa disangka isian sudah terisi; teksnya hilang saat mengetik | **Rendah** |
| Modal konfirmasi baik | #3 Kontrol pengguna, #5 Pencegahan error | `confirm-modal.blade.php`: judul, pesan, Batal/Konfirmasi, Esc menutup | Positif | — |
| Ringkasan error di atas form | #9 Bantu pulih dari error | `laporan/create.blade.php:18-22` | Positif, tetapi belum dipakai di semua form admin | **Rendah** |
| Konfirmasi Keluar Cepat (Esc 2×) tanpa petunjuk visual | #10 Bantuan & dokumentasi | Petunjuk Esc hanya di `title` tooltip (`quick-exit.blade.php:9`), tidak terlihat di layar sentuh | Fitur berguna tetapi tersembunyi | **Rendah** |

### 3.7 Aksesibilitas

| Masalah | WCAG | Bukti | Keparahan |
|---|---|---|---|
| Kontras teks gagal (tombol primary, danger, error, helper di panel, placeholder) | 1.4.3 | Lihat tabel 3.1 | **Kritis** |
| Batas input tidak terlihat | 1.4.11 (3:1) | `ring-neutral-300/70` ≈ `#e1e1e1` = **1,31:1** (`input.blade.php:17`, `select.blade.php:17`, `textarea.blade.php:17`, `search-input.blade.php:26`) | **Tinggi** |
| Indikator fokus lemah | 2.4.7 / 2.4.11 | `focus-visible:ring-neutral-400` = 2,58:1 dan `focus:ring-1` (1px) di input | **Tinggi** |
| Target sentuh kecil | 2.5.8 (min 24px) & rekomendasi 44px | Terukur di 1440px: 22 dari 24 elemen interaktif di beranda <44px, 16 elemen <24px (tautan nav 20px). Keluar Cepat 30px, tombol menu 40px, checkbox/radio 16px (`checkbox.blade.php:28`) | **Tinggi** |
| SVG dekoratif tanpa `aria-hidden` | 1.1.1 | 37 `<svg>`, hanya 24 ber-`aria-hidden`; mis. ikon gembok `layouts/app.blade.php:20`, `welcome.blade.php:38` | **Rendah** |
| Modal tanpa focus trap dan `aria-labelledby` | 2.4.3, 4.1.2 | `confirm-modal.blade.php:33-35`: `role="dialog"` tanpa nama aksesibel; Tab bisa keluar ke halaman di belakang | **Sedang** |
| Dropdown menu tanpa dukungan keyboard penuh | 2.1.1 | Menu Admin/Akun (`layouts/app.blade.php:57-111`) tidak menutup dengan Esc (Esc dipakai Keluar Cepat) dan tanpa `aria-controls` | **Sedang** |
| Yang sudah baik | | Skip link, `lang` HTML, label pada semua input komponen, `fieldset/legend` di radio-cards, `aria-invalid` + `aria-describedby`, `sr-only` label pencarian, badge dengan teks | — |

### 3.8 Kerapian Implementasi UI

| Masalah | Bukti | Dampak | Keparahan |
|---|---|---|---|
| Menu navigasi ditulis dua kali | Desktop `layouts/app.blade.php:40-82` dan mobile `:138-188`; daftar link admin 9 item diduplikasi | Menambah menu harus diedit di 2 tempat, dan sudah ada selisih (mobile tidak punya indikator halaman aktif) | **Sedang** |
| Class panjang berulang tanpa abstraksi | String class item menu `block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50` berulang 25+ kali | Sulit dirawat; cocok dijadikan komponen `x-nav-link` | **Rendah** |
| Nilai arbitrer menggantikan token | `text-[11px]` 146×, `bg-[#f2f2f2]` 57×, `leading-[1.15]` berulang | Design system de facto ada di class, bukan di token | **Sedang** |
| Inline style minimal | Hanya 1 (`dashboard/admin.blade.php:81`, tinggi bar grafik), dan itu wajar | Positif | — |

---

## 4. Rekomendasi Perbaikan (Urutan Prioritas)

### Quick wins (≤ 1 hari, dampak besar)

**1. Perbaiki token warna dan font di `resources/css/app.css`**

Ini memperbaiki kontras di seluruh aplikasi tanpa menyentuh view.

```css
@theme {
    /* Brand — 600/700 digelapkan agar teks putih ≥ 4,5:1 */
    --color-brand-50:  #f0fdf4;
    --color-brand-100: #dcfce7;
    --color-brand-500: #22c55e;   /* hanya dekorasi/titik, bukan teks */
    --color-brand-600: #15803d;   /* putih di atasnya = 5,02:1 ✅ */
    --color-brand-700: #166534;   /* hover, putih = 7,13:1 ✅ */
    --color-brand-800: #14532d;

    /* Semantik */
    --color-safety-rose:    #be123c;  /* danger/error: 6,29:1 di putih, 5,61:1 di #f2f2f2 ✅ */
    --color-safety-alert:   #9f1239;  /* hover danger */
    --color-safety-warning: #b45309;  /* teks warning 5,02:1 ✅ */
    --color-safety-info:    #1d4ed8;  /* 6,70:1 ✅ */

    /* Permukaan & garis — gantikan bg-[#f2f2f2] */
    --color-surface:        #f2f2f2;
    --color-line:           #d4d4d4;  /* hairline dekoratif */
    --color-line-strong:    #8a8a8a;  /* batas input, 3,45:1 ✅ (WCAG 1.4.11) */

    /* Font: nama token Tailwind v4 yang benar */
    --font-sans: 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
}
```

Lalu muat Inter di `<head>` kedua layout:

```html
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
```

Bunny Fonts dipilih karena tanpa pelacakan, sesuai prinsip privasi proyek. Alternatif: self-host lewat `@fontsource/inter`.

**2. Ganti warna redup pada helper dan placeholder**

- Helper di dalam panel abu: `text-neutral-500` → `text-neutral-600` (6,98:1).
- Placeholder: `placeholder:text-neutral-400` → `placeholder:text-neutral-500` (4,74:1).
- Tanggal artikel: `text-slate-400` → `text-neutral-600`.

**3. Perkuat batas input dan fokus di 4 komponen form**

Komponennya `input`, `select`, `textarea`, dan `search-input`.

```php
$field = 'block w-full rounded-lg bg-white px-3.5 py-3 text-base sm:text-sm text-neutral-900 '
    . 'ring-1 ring-inset placeholder:text-neutral-500 transition '
    . 'focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 '
    . ($hasError ? 'ring-safety-rose' : 'ring-line-strong');
```

Untuk tombol (`button.blade.php:10`):

```php
'focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2'
```

`text-base` di mobile mencegah auto-zoom iOS.

**4. Jadikan Keluar Cepat tombol paling menonjol**

Ubah di `components/quick-exit.blade.php:8`:

```html
class="inline-flex min-h-11 items-center gap-1.5 rounded-lg bg-neutral-900 px-4 text-sm font-semibold
       text-white hover:bg-neutral-700 focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-neutral-900"
```

Tambahkan juga teks kecil "Esc 2×" yang terlihat di desktop: `<kbd class="hidden md:inline …">Esc×2</kbd>`.

**5. Tambah kontak darurat**

Pasang di banner keselamatan atau footer, dan di halaman sukses laporan:

> Dalam bahaya sekarang? Hubungi **112** (darurat) atau **SAPA 129** (KemenPPPA, WhatsApp 08111-129-129).

Gunakan `<a href="tel:129">` agar langsung bisa ditekan di ponsel. Verifikasi nomor dengan tim sebelum rilis.

### Prioritas tinggi (1–3 hari)

**6. Naikkan skala teks dasar**

| Elemen | Sekarang | Disarankan |
|---|---|---|
| Label form | `text-xs` 12px | `text-sm` 14px, `font-medium` |
| Helper / error | `text-[11px]` | `text-xs` 12px minimum, `text-sm` untuk error |
| Tombol md | `text-xs` | `text-sm` |
| Isi kartu / paragraf | `text-xs` | `text-sm` (dan `text-base` untuk isi artikel) |

Cari dan ganti `text-[11px]` → `text-xs` di 146 lokasi. Setelah itu tinjau ulang sisa `text-xs` yang dipakai untuk isi.

**7. State loading anti dobel-submit di `x-button`**

Tambahkan di `resources/js/app.js`:

```js
Alpine.data('submitGuard', () => ({
    sending: false,
    submit(e) { if (this.sending) { e.preventDefault(); return; } this.sending = true; },
}));
```

Pakai di form:

```html
<form x-data="submitGuard" @submit="submit">
  …
  <x-button type="submit" ::disabled="sending">
      <span x-show="!sending">Kirim laporan</span>
      <span x-show="sending" x-cloak>Mengirim…</span>
  </x-button>
</form>
```

**8. Target sentuh minimal 44px**

- `x-button` ukuran md → `min-h-11`, sm → `min-h-10`.
- Tautan navigasi desktop → `py-2` (target 36–40px masih dapat diterima di desktop, tetapi wajib ≥ 44px di menu mobile).
- Checkbox/radio → `h-5 w-5`, dan seluruh label tetap bisa diklik (sudah).

**9. Seragamkan modul Artikel**

- `slate-*` → `neutral-*`.
- Input mentah → `x-search-input` + `x-select`.
- Kartu → `x-card` tanpa shadow.
- Tombol aksi baris → varian baru `x-button variant="secondary" size="sm"` dan `variant="outline-danger" size="sm"`.
- Empty state → `x-empty-state`.

### Prioritas sedang (≤ 1 minggu)

10. **Pakai `x-page-header` di semua halaman.** Tambahkan prop `size="lg"` untuk halaman publik (`text-3xl sm:text-4xl`) dan default `text-2xl` untuk admin. Dengan begitu cukup ada dua gaya H1 resmi.
11. **Tetapkan aturan radius.** `rounded-md` (6px) untuk tombol/input/badge, `rounded-xl` (12px) untuk kartu/panel/modal, `rounded-full` untuk titik/avatar. Hapus `rounded-2xl`, `shadow-sm`, dan `shadow-md` dari kartu. Shadow hanya untuk elemen melayang (dropdown, modal: `shadow-lg`).
12. **Ekstrak navigasi** ke array di view composer, lalu render dengan komponen `x-nav-link`. Menu desktop dan mobile memakai satu sumber, dan indikator halaman aktif ikut muncul di mobile.
13. **Turunkan breakpoint navigasi** ke `lg` (1024px) dengan mengelompokkan menu: **Beranda · Lapor · Lacak · Edukasi ▾ (Artikel, Kegiatan, Glosarium, Pengumuman) · Bantuan**.
14. **Ringkas chrome mobile.** Banner keselamatan cukup satu baris (`text-[13px] truncate`, atau tutup per sesi), sehingga tinggi total <96px.
15. **Perbaiki modal.** Tambahkan `aria-labelledby="confirm-title"` dan focus trap dengan plugin `@alpinejs/focus` (`x-trap="isOpen"`). Kembalikan fokus ke tombol pemicu saat modal ditutup.

### Prioritas rendah

16. Tambahkan `aria-hidden="true"` pada 13 SVG dekoratif yang belum punya.
17. Pilih satu gaya kapital. Saran: **sentence case** untuk semua judul dan tombol ("Tambah instansi bantuan"), karena terasa lebih ramah dan selaras dengan copy empatik.
18. Pindahkan warna amber/blue mentah ke token `safety-warning` dan `safety-info`.
19. Untuk tabel admin di mobile, pertimbangkan tampilan kartu bertumpuk (`md:table` + `<dl>` per baris) agar kolom aksi tidak tersembunyi.

---

## 5. Palet Warna & Type Scale yang Disarankan

### Palet

Kesan hijau-netral dari Figma tetap dipertahankan; yang berubah hanya kegelapannya agar lolos AA.

| Token | Hex | Pemakaian | Kontras kunci |
|---|---|---|---|
| `brand-50` | `#f0fdf4` | Latar sorotan lembut | — |
| `brand-100` | `#dcfce7` | Latar badge/step aktif | — |
| `brand-500` | `#22c55e` | **Hanya** dekorasi (titik status) | 2,28:1, jangan untuk teks |
| `brand-600` | `#15803d` | Tombol primary, tautan aktif | Putih 5,02:1 ✅ · di `brand-50` 4,79:1 ✅ |
| `brand-700` | `#166534` | Hover primary, teks brand | Putih 7,13:1 ✅ |
| `neutral-900` | `#171717` | Judul, teks utama, ring fokus, Keluar Cepat | 17,9:1 |
| `neutral-600` | `#525252` | Teks sekunder, helper | 6,98:1 di `#f2f2f2` ✅ |
| `neutral-500` | `#737373` | Placeholder, meta (hanya di latar putih) | 4,74:1 ✅ |
| `line-strong` | `#8a8a8a` | Batas input/kontrol | 3,45:1 ✅ (UI) |
| `line` | `#d4d4d4` | Hairline dekoratif, pemisah | — |
| `surface` | `#f2f2f2` | Panel/kartu (pengganti `bg-[#f2f2f2]`) | — |
| `safety-rose` | `#be123c` | Danger, error | Putih 6,29:1 ✅ |
| `safety-warning` | `#b45309` | Peringatan | 5,02:1 ✅ |
| `safety-info` | `#1d4ed8` | Info | 6,70:1 ✅ |

### Type scale

Rasio sekitar 1,25 dengan basis 16px.

| Peran | Kelas Tailwind | Ukuran / line-height | Bobot |
|---|---|---|---|
| Display (hero) | `text-4xl sm:text-5xl` | 36→48px / 1.15 | 800 |
| H1 halaman publik | `text-3xl sm:text-4xl` | 30→36px / 1.2 | 800 |
| H1 halaman admin | `text-2xl` | 24px / 1.3 | 800 |
| H2 seksi | `text-xl` | 20px / 1.4 | 700 |
| H3 judul kartu | `text-base` | 16px / 1.5 | 600 |
| Body | `text-base` (artikel) / `text-sm` (UI) | 16 / 14px, 1.6 | 400 |
| Label form, tombol | `text-sm` | 14px / 1.4 | 500–600 |
| Caption, helper, meta | `text-xs` | 12px / 1.5 | 400–500 |
| **Minimum** | — | **12px**, tidak ada 11px | — |

### Spacing

Gunakan skala 4px: 4 · 8 · 12 · 16 · 24 · 32 · 48 · 64.

- Padding kartu: `p-6` (24px).
- Jarak antar field form: `space-y-5` (20px) atau `space-y-6` (24px).
- Jarak antar seksi halaman: `space-y-10` (40px) atau `space-y-12` (48px).
- Kurangi nilai ganjil seperti `p-3.5`, `gap-2.5`, `mt-1.5` jika tidak diperlukan.

---

## 6. Checklist Perbaikan

### Quick wins
- [x] Ganti nama token font `--font-family-sans` → `--font-sans` dan muat Inter (Bunny Fonts / self-host)
- [x] `brand-600` → `#15803d`, `brand-700` → `#166534`
- [x] `safety-rose` → `#be123c`, `safety-alert` → `#9f1239`
- [x] Tambah token `surface`, `line`, `line-strong`; ganti 57× `bg-[#f2f2f2]` → `bg-surface`
- [x] Helper di panel abu `neutral-500` → `neutral-600`; placeholder `neutral-400` → `neutral-500`
- [x] Ring input → `ring-line-strong`; fokus → `focus-visible:ring-2 ring-neutral-900`
- [x] Ring fokus tombol → `ring-neutral-900 ring-offset-2`
- [x] Keluar Cepat: hitam, `min-h-11`, `text-sm`, tampilkan petunjuk "Esc×2"
- [x] Tambah kontak darurat 112 / SAPA 129 dengan `tel:` link (verifikasi nomor)

### Prioritas tinggi
- [ ] `text-[11px]` → minimal `text-xs` (146 lokasi)
- [ ] Label form dan tombol md → `text-sm`
- [ ] Input `text-base sm:text-sm` (cegah auto-zoom iOS)
- [ ] `submitGuard` + teks "Mengirim…" di semua form, terutama `laporan/create`
- [ ] `x-button` md `min-h-11`; checkbox/radio `h-5 w-5`
- [ ] Modul Artikel: `slate` → `neutral`, input → komponen, hapus shadow kartu, pakai `x-empty-state`

### Prioritas sedang
- [ ] `x-page-header` dipakai di semua halaman (prop `size="lg"` untuk publik)
- [ ] Aturan radius: `rounded-md` kontrol · `rounded-xl` kartu · shadow hanya dropdown/modal
- [ ] Navigasi satu sumber data + `x-nav-link`, indikator aktif juga di mobile
- [ ] Kelompokkan navigasi ("Edukasi ▾") dan tampilkan di ≥1024px
- [ ] Chrome mobile < 96px
- [ ] Modal: `aria-labelledby` + focus trap (`@alpinejs/focus`) + kembalikan fokus

### Prioritas rendah
- [ ] `aria-hidden="true"` pada 13 SVG dekoratif
- [ ] Satu gaya kapital (sentence case) untuk judul & tombol
- [ ] Amber/blue mentah → token `safety-warning` / `safety-info`
- [ ] Tabel admin → kartu bertumpuk di mobile
- [ ] Ringkasan error di atas semua form admin

---

*Quick wins sudah diterapkan (lihat checklist). Audit awal tidak mengubah kode. Semua angka kontras dapat diverifikasi ulang dengan WebAIM Contrast Checker; jumlah kemunculan class diperoleh dengan `grep` pada `resources/views`.*
