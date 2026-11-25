# Panduan Logo RuangAman

## Ide

**Ruang terbuka:** sebuah ruang dengan satu sudut terbuka, dan seseorang berada di sudut aman di dalamnya.
Anda aman di dalam, dan selalu bebas untuk pergi. Ini selaras dengan fitur Keluar cepat dan prinsip "kendali tetap pada Anda".

Logo sengaja dibuat **abstrak dan diskret**. Korban bisa membuka situs ini di perangkat yang diawasi, jadi logo tidak boleh terlihat seperti layanan kekerasan.

## Konstruksi

| Bagian | Nilai |
|---|---|
| Kanvas | 256 × 256 |
| Ruang | garis 32, ujung membulat, radius sudut 58, bukaan seperempat (kanan atas) |
| Orang | lingkaran r 30, di pusat (112, 144) → sedikit ke kiri-bawah, menjauhi bukaan |
| Versi kecil | garis 40, r 34; dipakai untuk ≤ 32 px (favicon, ikon) |

## Warna

| Peran | HEX | RGB | Pakai untuk |
|---|---|---|---|
| Hijau RuangAman | `#15803d` | 21 128 61 | Ruang (bingkai) |
| Netral gelap | `#171717` | 23 23 23 | Orang (titik) dan wordmark |
| Putih | `#ffffff` | 255 255 255 | Versi terbalik di latar gelap/hijau |

Kontras hijau terhadap putih 5,02:1 (lolos WCAG AA).

## File

| File | Kegunaan |
|---|---|
| `ruangaman-symbol.svg` | Simbol utama (2 warna) |
| `ruangaman-symbol-small.svg` | Simbol untuk ukuran ≤ 32 px |
| `ruangaman-lockup-horizontal.svg` | Header situs, dokumen, slide |
| `ruangaman-lockup-stacked.svg` | Poster, sampul, ruang sempit-tinggi |
| `ruangaman-wordmark.svg` | Tulisan saja |
| `*-black.svg`, `*-white.svg`, `*-mono-15803d.svg` | Versi satu warna |
| `web/` | favicon.ico/.svg, PNG ikon, apple-touch-icon, manifest, `head-snippet.html` |
| `slides/`, `presentation.html` | Board presentasi dengan mockup |

Wordmark: Inter ExtraBold (800), tracking −3/104, sudah dikonversi ke outline (lisensi Inter: SIL OFL, boleh untuk logo).

## Aturan pakai

- **Ruang kosong:** minimal setebal satu garis bingkai (1/8 tinggi simbol) di semua sisi.
- **Ukuran minimum:** simbol 16 px (pakai versi kecil); lockup horizontal 96 px lebar.
- **Latar yang disetujui:** putih, `#f2f2f2` (surface), hijau `#15803d` (pakai versi putih), netral gelap (pakai versi putih).

## Jangan

- Memutar atau mencerminkan logo: bukaan harus tetap di kanan atas.
- Memindahkan titik ke tengah, karena logo akan terlihat seperti ikon kamera.
- Menutup bukaan, karena maknanya ("bebas pergi") hilang.
- Mengganti warna di luar palet, memberi bayangan, gradien, atau outline.
- Menambahkan simbol kekerasan, pita, tangan, atau perisai di sekitar logo.

## Catatan

- Belum dicek ke basis data merek dagang (PDKI/DJKI). Lakukan pengecekan sebelum dipakai publik.
- Dibuat dengan skill `logo-design`. Konsep dan iterasi ada di `docs/brand/v2/`.
