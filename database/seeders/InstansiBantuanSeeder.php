<?php

namespace Database\Seeders;

use App\Models\InstansiBantuan;
use Illuminate\Database\Seeder;

class InstansiBantuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_instansi' => 'Pusat Bantuan Harmoni',
                'alamat' => 'Jl. Melati No. 12, Jakarta Pusat',
                'deskripsi' => 'Pusat pendampingan bagi penyintas kekerasan berbasis gender. Membantu penyintas memahami pilihan dukungan dan menentukan langkah sesuai kebutuhan serta kesiapan mereka.',
                'website' => 'https://harmoni.example.org',
                'status' => 'aktif',
            ],
            [
                'nama_instansi' => 'Yayasan Ruang Pulih',
                'alamat' => 'Jl. Kenanga No. 8, Bandung',
                'deskripsi' => 'Ruang dukungan untuk membantu penyintas mengenali pilihan pendampingan dan langkah pemulihan.',
                'website' => 'https://ruangpulih.example.org',
                'status' => 'aktif',
            ],
            [
                'nama_instansi' => 'Lembaga Sahabat Setara',
                'alamat' => 'Jl. Mawar No. 21, Yogyakarta',
                'deskripsi' => 'Lembaga pendamping yang berfokus pada akses bantuan dan pemenuhan hak penyintas.',
                'website' => null,
                'status' => 'aktif',
            ],
            [
                'nama_instansi' => 'Pusat Pendampingan Asa',
                'alamat' => 'Jl. Anggrek No. 5, Surabaya',
                'deskripsi' => 'Dukungan awal dan informasi pilihan pendampingan dalam lingkungan yang menghargai privasi.',
                'website' => 'https://asa.example.org',
                'status' => 'aktif',
            ],
            [
                'nama_instansi' => 'Yayasan Langkah Baru',
                'alamat' => 'Jl. Cempaka No. 17, Semarang',
                'deskripsi' => 'Pusat layanan sosial dan pendampingan pemulihan psikologis bagi korban kekerasan.',
                'website' => null,
                'status' => 'nonaktif',
            ],
            [
                'nama_instansi' => 'Lembaga Pelita Bersama',
                'alamat' => 'Jl. Teratai No. 3, Malang',
                'deskripsi' => 'Lembaga kemitraan masyarakat untuk bantuan hukum dan perlindungan hak perempuan.',
                'website' => null,
                'status' => 'nonaktif',
            ],
        ];

        foreach ($data as $item) {
            InstansiBantuan::updateOrCreate(
                ['nama_instansi' => $item['nama_instansi']],
                $item
            );
        }
    }
}
