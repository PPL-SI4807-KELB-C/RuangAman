<?php

namespace Database\Seeders;

use App\Models\GlosariumKbg;
use Illuminate\Database\Seeder;

/**
 * Menyediakan istilah dan definisi KBG (PBI-02) untuk daftar glosarium publik.
 */
class GlosariumKbgSeeder extends Seeder
{
    /**
     * @var list<array{istilah: string, definisi: string, kategori: string, status_publikasi: string}>
     */
    private const DATA = [
        [
            'istilah' => 'Kekerasan Berbasis Gender',
            'definisi' => 'Segala tindakan merugikan yang ditujukan kepada seseorang berdasarkan gender atau jenis kelaminnya, mencakup kekerasan fisik, psikis, seksual, ekonomi, dan penelantaran.',
            'kategori' => 'Umum',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Kekerasan Dalam Pacaran',
            'definisi' => 'Pola perilaku mengontrol, merendahkan, atau menyakiti yang dilakukan pasangan romantis, baik secara fisik, verbal, seksual, maupun digital.',
            'kategori' => 'Umum',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Kekerasan Seksual',
            'definisi' => 'Tindakan yang bermuatan seksual yang dilakukan tanpa persetujuan (consent) korban, termasuk pemerkosaan, percobaan perkosaan, pelecehan, dan eksploitasi seksual.',
            'kategori' => 'Hukum',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Pelecehan Seksual',
            'definisi' => 'Perilaku seksual yang tidak diminta dan membuat korban merasa tidak nyaman, terintimidasi, atau direndahkan, seperti sentuhan, siulan, komentar tubuh, atau menampilkan konten seksual.',
            'kategori' => 'Umum',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Kekerasan Berbasis Gender Online (KBGO)',
            'definisi' => 'Kekerasan yang difasilitasi teknologi digital, misalnya penyebaran foto atau video intim tanpa izin, doxing, pemerasan seksual (sextortion), dan serangan terkoordinasi di media sosial.',
            'kategori' => 'Digital',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Doxing',
            'definisi' => 'Tindakan mencari dan menyebarkan data pribadi seseorang ke publik tanpa izin dengan tujuan membungkam, mengintimidasi, atau memancing serangan massal.',
            'kategori' => 'Digital',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Relasi Kuasa',
            'definisi' => 'Hubungan tidak setara yang memberi satu pihak kemampuan menekan pihak lain, misalnya atasan-bawahan, dosen-mahasiswa, atau dewasa-anak; relevan karena persetujuan sulit diberikan secara bebas dalam relasi ini.',
            'kategori' => 'Umum',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Persetujuan (Consent)',
            'definisi' => 'Pernyataan setuju yang diberikan secara sadar, sukarela, dapat ditarik kembali, dan spesifik terhadap aktivitas yang disetujui. Ketidakadaan penolakan bukan berarti persetujuan.',
            'kategori' => 'Umum',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Trauma Bonding',
            'definisi' => 'Ikatan emosional kuat yang terbentuk antara korban dan pelaku akibat siklus kekerasan bergantian dengan perlakuan manis, sehingga korban sulit meninggalkan relasi.',
            'kategori' => 'Psikologi',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Pendampingan Psikososial',
            'definisi' => 'Dukungan berkelanjutan untuk memulihkan fungsi emosional dan sosial penyintas, mencakup konseling, pendampingan pada proses hukum, dan rujukan layanan.',
            'kategori' => 'Pendampingan',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Alur Rujukan',
            'definisi' => 'Urutan pengantaran korban antarlayanan, misalnya dari layanan kesehatan ke pusat krisis, lalu ke pendampingan hukum, dengan persetujuan dan informasi yang diberikan korban.',
            'kategori' => 'Pendampingan',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Undang-Undang TPKS',
            'definisi' => 'Payung hukum penanganan tindak pidana kekerasan seksual di Indonesia yang memuat hak penanganan, pelindungan, dan pemulihan korban serta mekanisme pembuktian khusus.',
            'kategori' => 'Hukum',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Kode Pelacakan',
            'definisi' => 'Kode unik yang diberikan setelah laporan dibuat dan dipakai untuk memantau status laporan tanpa perlu menunjukkan identitas pelapor.',
            'kategori' => 'Layanan',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ],
        [
            'istilah' => 'Kebenaran Versi Terduga Pelaku',
            'definisi' => 'Istilah internal yang masih dibahas tim redaksi sebelum dipublikasikan sebagai bagian glosarium publik.',
            'kategori' => 'Umum',
            'status_publikasi' => GlosariumKbg::STATUS_DRAFT,
        ],
    ];

    public function run(): void
    {
        foreach (self::DATA as $data) {
            GlosariumKbg::query()->updateOrCreate(
                ['istilah' => $data['istilah']],
                [
                    'definisi' => $data['definisi'],
                    'kategori' => $data['kategori'],
                    'status_publikasi' => $data['status_publikasi'],
                ]
            );
        }
    }
}
