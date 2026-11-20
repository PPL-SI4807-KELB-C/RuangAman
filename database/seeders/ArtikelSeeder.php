<?php

namespace Database\Seeders;

use App\Models\Artikel;
use App\Models\KategoriArtikel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArtikelSeeder extends Seeder
{
    /**
     * Data dummy artikel untuk pengujian PBI-10 oleh Niky.
     */
    public function run(): void
    {
        $penulis = User::query()
            ->whereHas('role', fn ($query) => $query->where('nama_role', 'Admin Kasus'))
            ->first();

        if (! $penulis) {
            return;
        }

        $kategoriMap = KategoriArtikel::query()->pluck('kategori_id', 'nama_kategori');

        $daftarArtikel = [
            [
                'judul' => 'Mengenal Bentuk-Bentuk Kekerasan Berbasis Gender',
                'kategori' => 'Pencegahan',
                'isi' => "Kekerasan berbasis gender dapat terjadi dalam bentuk fisik, psikis, seksual, maupun ekonomi. Kekerasan tidak selalu meninggalkan tanda yang terlihat dan tidak pernah menjadi kesalahan korban.\n\nMengenali bentuk kekerasan membantu kita memahami pengalaman tanpa menghakimi. Jika Anda atau seseorang yang Anda kenal mengalaminya, pertimbangkan untuk mencari tempat aman dan menghubungi pendamping tepercaya.\n\nRuangAman bukan layanan darurat. Jika terdapat bahaya langsung, segera hubungi layanan darurat setempat.",
                'status_publikasi' => Artikel::STATUS_PUBLISHED,
            ],
            [
                'judul' => 'Langkah Aman Menyimpan Bukti di Ranah Digital',
                'kategori' => 'Pencegahan',
                'isi' => "Bukti digital dapat berupa tangkapan layar, tautan, waktu kejadian, atau informasi akun. Simpan hanya jika kondisi aman dan hindari tindakan yang dapat meningkatkan risiko.\n\nGunakan perangkat yang tidak diawasi pelaku bila memungkinkan. Jangan membagikan kata sandi kepada orang yang tidak berwenang dan pertimbangkan lokasi penyimpanan cadangan yang aman.\n\nAnda tidak diwajibkan mengumpulkan semua bukti sebelum meminta bantuan. Keselamatan selalu menjadi prioritas utama.",
                'status_publikasi' => Artikel::STATUS_PUBLISHED,
            ],
            [
                'judul' => 'Hak Korban dalam Penanganan dan Pemulihan',
                'kategori' => 'Hukum dan Hak Korban',
                'isi' => "Korban memiliki hak atas penanganan, perlindungan, dan pemulihan sesuai ketentuan yang berlaku. Informasi korban perlu diperlakukan secara rahasia dan hanya dibagikan sesuai kebutuhan penanganan.\n\nPendampingan dapat mencakup dukungan psikologis, medis, hukum, dan rujukan layanan sosial. Setiap orang berhak memperoleh penjelasan mengenai pilihan yang tersedia tanpa dipaksa mengambil keputusan tertentu.\n\nKonten ini merupakan informasi umum dan tidak menggantikan nasihat dari pendamping hukum profesional.",
                'status_publikasi' => Artikel::STATUS_PUBLISHED,
            ],
            [
                'judul' => 'Pertolongan Pertama Psikologis untuk Pendamping',
                'kategori' => 'Kesehatan Mental',
                'isi' => "Ketika seseorang menceritakan pengalaman kekerasannya, dengarkan dengan tenang dan tanpa menyalahkan. Ungkapkan bahwa Anda memercayai pengalamannya dan bahwa kekerasan tersebut bukan kesalahannya.\n\nTanyakan apa yang ia butuhkan, bukan memaksakan tindakan. Jaga kerahasiaan informasi dan bantu menemukan layanan profesional jika ia menginginkannya.\n\nPendamping juga perlu menjaga batas diri dan mencari dukungan. Memberi perhatian tidak berarti harus menangani seluruh situasi seorang diri.",
                'status_publikasi' => Artikel::STATUS_PUBLISHED,
            ],
            [
                'judul' => 'Menyusun Rencana Keselamatan Pribadi',
                'kategori' => 'Pemulihan',
                'isi' => "Rencana keselamatan dapat dimulai dari mengenali tempat aman, orang tepercaya, dan cara menghubungi layanan bantuan. Siapkan hanya hal yang realistis serta sesuai dengan kondisi Anda.\n\nPertimbangkan keamanan perangkat dan riwayat penelusuran. Gunakan tombol Keluar Cepat ketika diperlukan, tetapi ingat bahwa fitur tersebut tidak menghapus riwayat peramban.\n\nTidak ada rencana yang berlaku untuk semua orang. Pendamping profesional dapat membantu menyusun pilihan yang lebih aman sesuai kebutuhan.",
                'status_publikasi' => Artikel::STATUS_PUBLISHED,
            ],
            [
                'judul' => 'Memahami Dukungan dalam Proses Pemulihan',
                'kategori' => 'Pemulihan',
                'isi' => "Draf ini membahas pilihan dukungan sosial, psikologis, dan komunitas dalam proses pemulihan. Materi masih ditinjau sebelum dipublikasikan.",
                'status_publikasi' => Artikel::STATUS_DRAFT,
            ],
        ];

        foreach ($daftarArtikel as $data) {
            $kategoriId = $kategoriMap[$data['kategori']] ?? null;

            if (! $kategoriId) {
                continue;
            }

            unset($data['kategori']);

            Artikel::query()->updateOrCreate(
                ['slug' => Str::slug($data['judul'])],
                array_merge($data, [
                    'kategori_id' => $kategoriId,
                    'penulis_id' => $penulis->user_id,
                ])
            );
        }
    }
}
