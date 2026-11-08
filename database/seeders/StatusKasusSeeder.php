<?php

namespace Database\Seeders;

use App\Models\StatusKasus;
use Illuminate\Database\Seeder;

class StatusKasusSeeder extends Seeder
{
    /**
     * Data master status kasus sesuai spesifikasi PBI-04.
     *
     * @var array<string, string>
     */
    public const STATUS_KASUS = [
        'Pending' => 'Laporan baru menunggu verifikasi Admin Kasus.',
        'Proses' => 'Laporan valid dan sedang ditangani konselor pendamping.',
        'Selesai' => 'Penanganan kasus telah disetujui selesai oleh Admin Kasus.',
        'Ditolak' => 'Laporan tidak valid atau tidak dapat diproses.',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::STATUS_KASUS as $namaStatus => $keterangan) {
            StatusKasus::query()->updateOrCreate(
                ['nama_status' => $namaStatus],
                ['deskripsi' => $keterangan]
            );
        }
    }
}

