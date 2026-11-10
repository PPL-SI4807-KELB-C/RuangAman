<?php

namespace Database\Seeders;

use App\Models\KegiatanEdukasi;
use Illuminate\Database\Seeder;

class KegiatanEdukasiSeeder extends Seeder
{
    /**
     * Menambahkan data dummy kegiatan/webinar edukasi untuk pengujian PBI-05.
     */
    public function run(): void
    {
        $daftarKegiatan = [
            [
                'judul' => 'Mengenal & Mencegah Kekerasan Berbasis Gender Online (KBGO)',
                'deskripsi' => 'Webinar interaktif untuk memahami bentuk-bentuk kekerasan di ranah digital, perlindungan privasi akun, serta langkah awal pengumpulan bukti dan pelaporan aman.',
                'narasumber' => 'Dr. Siti Rahmawati, M.Psi.',
                'tanggal_mulai' => now()->addDays(14)->toDateString(),
                'waktu_mulai' => '19:00:00',
                'tanggal_selesai' => now()->addDays(14)->toDateString(),
                'waktu_selesai' => '21:00:00',
                'metode' => KegiatanEdukasi::METODE_ONLINE,
                'lokasi_atau_tautan' => 'https://meet.ruangaman.test/webinar-kbgo-2026',
                'kuota' => 50,
                'status' => KegiatanEdukasi::STATUS_TAYANG,
            ],
            [
                'judul' => 'Workshop Pertolongan Pertama Psikologis (PFA) untuk Pendamping',
                'deskripsi' => 'Pelatihan tatap muka mengenai pendekatan empatik, prinsip kerahasiaan, dan dukungan awal tanpa menghakimi bagi rekan sebaya maupun penyintas.',
                'narasumber' => 'Rina Kusuma, M.Psi., Psikolog',
                'tanggal_mulai' => now()->addDays(21)->toDateString(),
                'waktu_mulai' => '09:00:00',
                'tanggal_selesai' => now()->addDays(21)->toDateString(),
                'waktu_selesai' => '12:30:00',
                'metode' => KegiatanEdukasi::METODE_OFFLINE,
                'lokasi_atau_tautan' => 'Aula Gedung B Lantai 3, Ruang Seminar Bandung',
                'kuota' => 30,
                'status' => KegiatanEdukasi::STATUS_TAYANG,
            ],
            [
                'judul' => 'Sosialisasi UU TPKS dan Hak Perlindungan Saksi & Korban',
                'deskripsi' => 'Membedah payung hukum Undang-Undang Tindak Pidana Kekerasan Seksual, hak atas pemulihan medis/psikologis, serta alur pendampingan hukum resmi.',
                'narasumber' => 'Adv. Budi Santoso, S.H., M.H.',
                'tanggal_mulai' => now()->addDays(7)->toDateString(),
                'waktu_mulai' => '15:30:00',
                'tanggal_selesai' => now()->addDays(7)->toDateString(),
                'waktu_selesai' => '17:00:00',
                'metode' => KegiatanEdukasi::METODE_ONLINE,
                'lokasi_atau_tautan' => 'https://meet.ruangaman.test/sosialisasi-uu-tpks',
                'kuota' => 100,
                'status' => KegiatanEdukasi::STATUS_TAYANG,
            ],
            [
                'judul' => 'Sesi Refleksi Pemulihan Trauma dan Regulasi Emosi',
                'deskripsi' => 'Materi lanjutan yang masih dalam tahap peninjauan internal sebelum dibuka untuk peserta umum.',
                'narasumber' => 'Nadia Putri, M.Psi.',
                'tanggal_mulai' => now()->addDays(30)->toDateString(),
                'waktu_mulai' => '10:00:00',
                'tanggal_selesai' => now()->addDays(30)->toDateString(),
                'waktu_selesai' => '11:30:00',
                'metode' => KegiatanEdukasi::METODE_ONLINE,
                'lokasi_atau_tautan' => 'https://meet.ruangaman.test/draft-refleksi-trauma',
                'kuota' => 25,
                'status' => KegiatanEdukasi::STATUS_DRAFT,
            ],
            [
                'judul' => 'Seminar Nasional: Membangun Ruang Aman Tanpa Stigma',
                'deskripsi' => 'Kegiatan edukasi publik yang telah dilaksanakan bersama jaringan konselor dan komunitas mahasiswa peduli kesetaraan.',
                'narasumber' => 'Tim Konselor RuangAman',
                'tanggal_mulai' => now()->subDays(10)->toDateString(),
                'waktu_mulai' => '13:00:00',
                'tanggal_selesai' => now()->subDays(10)->toDateString(),
                'waktu_selesai' => '16:00:00',
                'metode' => KegiatanEdukasi::METODE_OFFLINE,
                'lokasi_atau_tautan' => 'Auditorium Pusat Layanan Masyarakat',
                'kuota' => 80,
                'status' => KegiatanEdukasi::STATUS_SELESAI,
            ],
        ];

        foreach ($daftarKegiatan as $data) {
            KegiatanEdukasi::query()->updateOrCreate(
                ['judul' => $data['judul']],
                $data
            );
        }
    }
}
