<?php

namespace Database\Seeders;

use App\Models\InstansiBantuan;
use App\Models\KontakBantuan;
use App\Models\LayananBantuan;
use Illuminate\Database\Seeder;

class LayananKontakBantuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed layanan & kontak for each institution seeded by InstansiBantuanSeeder.
        // Uses firstOrCreate on nama_instansi so it works even if the seeder has already run.
        $data = [
            [
                'nama_instansi' => 'Pusat Bantuan Harmoni',
                'layanan' => [
                    ['nama_layanan' => 'Pendampingan psikologis', 'deskripsi' => 'Dukungan awal untuk memahami kebutuhan dan pilihan pemulihan.'],
                    ['nama_layanan' => 'Pendampingan hukum',      'deskripsi' => 'Informasi hak dan pilihan langkah hukum sesuai kebutuhan penyintas.'],
                    ['nama_layanan' => 'Rujukan layanan lanjutan', 'deskripsi' => 'Informasi rujukan ke lembaga dengan pendampingan yang sesuai.'],
                ],
                'kontak' => [
                    ['jenis_kontak' => 'email',    'nilai_kontak' => 'bantuan@harmoni.example.org', 'keterangan' => 'Untuk informasi awal'],
                    ['jenis_kontak' => 'telepon',  'nilai_kontak' => '(021) 0000 0000',            'keterangan' => 'Senin–Jumat, 09.00–16.00 WIB'],
                    ['jenis_kontak' => 'website',  'nilai_kontak' => 'https://harmoni.example.org', 'keterangan' => 'Informasi resmi instansi'],
                ],
            ],
            [
                'nama_instansi' => 'Yayasan Ruang Pulih',
                'layanan' => [
                    ['nama_layanan' => 'Dukungan Kelompok', 'deskripsi' => 'Sesi kelompok mingguan untuk berbagi pengalaman dan saling menguatkan bersama penyintas lain.'],
                    ['nama_layanan' => 'Hotline Krisis',    'deskripsi' => 'Layanan respons darurat 24 jam bagi penyintas yang membutuhkan bantuan segera.'],
                ],
                'kontak' => [
                    ['jenis_kontak' => 'hotline',   'nilai_kontak' => '1500-888', 'keterangan' => 'Tersedia 24 jam, 7 hari'],
                    ['jenis_kontak' => 'whatsapp',  'nilai_kontak' => '0812-9999-0001', 'keterangan' => 'Chat saja, tidak menerima panggilan biasa'],
                ],
            ],
            [
                'nama_instansi' => 'Lembaga Advokasi Nusantara',
                'layanan' => [
                    ['nama_layanan' => 'Advokasi & Litigasi', 'deskripsi' => 'Pendampingan litigasi mulai dari penyusunan berkas hingga persidangan.'],
                ],
                'kontak' => [
                    ['jenis_kontak' => 'telepon', 'nilai_kontak' => '022-7777-0202', 'keterangan' => 'Senin–Sabtu, 09.00–16.00 WIB'],
                    ['jenis_kontak' => 'email',   'nilai_kontak' => 'advokasi@lan.example.org', 'keterangan' => null],
                ],
            ],
            [
                'nama_instansi' => 'Rumah Perlindungan Sejati',
                'layanan' => [
                    ['nama_layanan' => 'Rumah Aman', 'deskripsi' => 'Penyediaan tempat tinggal sementara yang aman bagi penyintas dan anak-anak mereka.'],
                    ['nama_layanan' => 'Rehabilitasi Vokasional', 'deskripsi' => 'Pelatihan keterampilan kerja untuk mendukung kemandirian ekonomi penyintas.'],
                ],
                'kontak' => [
                    ['jenis_kontak' => 'hotline',  'nilai_kontak' => '1500-321', 'keterangan' => 'Tersedia 24 jam'],
                    ['jenis_kontak' => 'whatsapp', 'nilai_kontak' => '0813-1111-9999', 'keterangan' => null],
                ],
            ],
        ];

        foreach ($data as $item) {
            $instansi = InstansiBantuan::where('nama_instansi', $item['nama_instansi'])->first();

            if (! $instansi) {
                continue; // Skip if institution not found (InstansiBantuanSeeder not run)
            }

            foreach ($item['layanan'] as $layananData) {
                LayananBantuan::firstOrCreate(
                    ['instansi_id' => $instansi->instansi_id, 'nama_layanan' => $layananData['nama_layanan']],
                    ['deskripsi' => $layananData['deskripsi']],
                );
            }

            foreach ($item['kontak'] as $kontakData) {
                KontakBantuan::firstOrCreate(
                    ['instansi_id' => $instansi->instansi_id, 'nilai_kontak' => $kontakData['nilai_kontak']],
                    ['jenis_kontak' => $kontakData['jenis_kontak'], 'keterangan' => $kontakData['keterangan']],
                );
            }
        }
    }
}
