<?php

namespace Database\Seeders;

use App\Models\HalamanInformasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Menyediakan halaman informasi statis (PBI-01) yang dibutuhkan modul publik
 * agar dapat didemonstrasikan tanpa harus mengetik ulang isi konten.
 */
class HalamanInformasiSeeder extends Seeder
{
    /**
     * @var list<array{judul: string, isi: string, status_publikasi: string}>
     */
    private const DATA = [
        [
            'judul' => 'Tentang RuangAman',
            'isi' => "RuangAman adalah layanan pelaporan dan pendampingan kekerasan berbasis gender (KBG) yang dijalankan bersama tim konselor, pendamping hukum, dan relawan terlatih.\n\nLayanan ini dirancang agar setiap orang dapat melaporkan kejadian secara aman, termasuk tanpa membuka identitas, serta memantau perkembangan laporannya menggunakan kode pelacakan.\n\nSemua informasi yang Anda sampaikan diperlakukan sebagai data sensitif dan hanya dapat diakses oleh petugas yang diberi penugasan pada kasus Anda.",
            'status_publikasi' => HalamanInformasi::STATUS_PUBLISHED,
        ],
        [
            'judul' => 'Visi dan Misi',
            'isi' => "Visi: ruang digital yang aman bagi penyintas kekerasan berbasis gender untuk mencari bantuan tanpa takut dihakimi.\n\nMisi:\n1. Menyediakan jalur pelaporan yang mudah diakses, jelas, dan menjaga kerahasiaan.\n2. Menghubungkan penyintas dengan konselor dan pendamping yang kompeten.\n3. Menyebarkan edukasi pencegahan KBG melalui kegiatan, artikel, dan glosarium istilah.\n4. Mendampingi proses pemulihan dengan pendekatan yang berpihak pada korban.",
            'status_publikasi' => HalamanInformasi::STATUS_PUBLISHED,
        ],
        [
            'judul' => 'Panduan Menggunakan Layanan',
            'isi' => "1. Pastikan perangkat yang Anda gunakan aman. Gunakan tombol Keluar Cepat bila ada orang lain di dekat Anda.\n2. Buat laporan melalui menu Pelaporan. Anda dapat memilih opsi anonim; simpan kode pelacakan yang muncul karena kode itu satu-satunya cara menelusuri laporan Anda.\n3. Pantau perkembangan laporan melalui menu Lacak laporan.\n4. Ikuti kegiatan edukasi agar memahami hak, alur pendampingan, dan cara mengumpulkan bukti yang sah.\n5. Bila keadaan darurat dan ada ancaman langsung terhadap nyawa, hubungi layanan darurat resmi terlebih dahulu.",
            'status_publikasi' => HalamanInformasi::STATUS_PUBLISHED,
        ],
        [
            'judul' => 'Kebijakan Privasi',
            'isi' => "RuangAman hanya mengumpulkan data yang diperlukan untuk menangani laporan dan memberikan pendampingan.\n\nData yang Anda isi pada formulir laporan disimpan secara terpusat, dibatasi aksesnya berdasarkan penugasan kasus, dan tidak dibagikan kepada pihak ketiga tanpa persetujuan Anda, kecuali diwajibkan oleh peraturan perundang-undangan.\n\nAnda berhak meminta koreksi atau penghapusan data pribadi dengan menghubungi Admin Kasus. Kami tidak mengirim notifikasi otomatis berisi detail kasus ke kanal yang tidak Anda minta.",
            'status_publikasi' => HalamanInformasi::STATUS_PUBLISHED,
        ],
        [
            'judul' => 'Struktur Organisasi Pengelola',
            'isi' => 'Halaman ini masih dalam penyusunan bersama tim pengelola dan belum dipublikasikan untuk umum.',
            'status_publikasi' => HalamanInformasi::STATUS_DRAFT,
        ],
    ];

    public function run(): void
    {
        foreach (self::DATA as $data) {
            HalamanInformasi::query()->updateOrCreate(
                ['slug' => Str::slug($data['judul'])],
                [
                    'judul' => $data['judul'],
                    'isi' => $data['isi'],
                    'status_publikasi' => $data['status_publikasi'],
                ]
            );
        }
    }
}
