<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Menyediakan 6 data contoh FAQ sesuai rancangan Pusat Bantuan RuangAman.
     */
    public function run(): void
    {
        $items = [
            [
                'pertanyaan' => 'Apakah saya harus menggunakan nama asli?',
                'jawaban' => 'Tidak. Gunakan alias sebagai nama tampilan. Nama resmi tidak diwajibkan. Email diperlukan untuk akun dan tidak ditampilkan kepada publik.',
                'status_publikasi' => Faq::STATUS_PUBLISHED,
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Apakah saya perlu akun untuk membuat laporan?',
                'jawaban' => 'Ya. Daftar atau masuk menggunakan email dan kata sandi. Alias menjadi nama tampilan Anda. Informasi bantuan tetap dapat dibaca tanpa akun.',
                'status_publikasi' => Faq::STATUS_PUBLISHED,
                'urutan' => 2,
            ],
            [
                'pertanyaan' => 'Bagaimana cara melacak laporan saya?',
                'jawaban' => 'Gunakan kode pelacakan acak yang diterima setelah laporan dikirim. Pelacakan tanpa login hanya menampilkan status dan waktu pembaruan. Kronologi, lampiran, serta catatan penanganan tetap terlindungi.',
                'status_publikasi' => Faq::STATUS_PUBLISHED,
                'urutan' => 3,
            ],
            [
                'pertanyaan' => 'Apakah saya harus menyertakan bukti?',
                'jawaban' => 'Tidak. Lampiran bukti bersifat opsional. Anda dapat menambah, mengganti, atau menghapus lampiran selama laporan masih berstatus Pending.',
                'status_publikasi' => Faq::STATUS_PUBLISHED,
                'urutan' => 4,
            ],
            [
                'pertanyaan' => 'Apa yang dilakukan tombol Keluar Cepat?',
                'jawaban' => 'Tombol ini mengalihkan Anda ke situs netral dan mengakhiri sesi. Anda juga dapat menekan Esc. Keluar Cepat tidak menghapus riwayat peramban; gunakan perangkat yang aman.',
                'status_publikasi' => Faq::STATUS_PUBLISHED,
                'urutan' => 5,
            ],
            [
                'pertanyaan' => 'Bagaimana memilih instansi bantuan?',
                'jawaban' => 'Buka halaman Instansi Bantuan untuk meninjau daftar lembaga pendamping aktif, jenis layanan yang tersedia, dan kontak resmi yang dapat dihubungi tanpa perlu login.',
                'status_publikasi' => Faq::STATUS_DRAFT,
                'urutan' => 6,
            ],
        ];

        foreach ($items as $item) {
            Faq::query()->firstOrCreate(
                ['pertanyaan' => $item['pertanyaan']],
                [
                    'jawaban' => $item['jawaban'],
                    'status_publikasi' => $item['status_publikasi'],
                    'urutan' => $item['urutan'],
                ]
            );
        }
    }
}
