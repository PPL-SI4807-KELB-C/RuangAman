<?php

namespace Database\Seeders;

use App\Models\LaporanKasus;
use App\Models\LogPenanganan;
use App\Models\PenangananKasus;
use App\Models\StatusKasus;
use App\Models\User;
use Illuminate\Database\Seeder;

class LogPenangananSeeder extends Seeder
{
    /**
     * Menambahkan data dummy log penanganan (sistem dan manual) secara idempotent.
     */
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@ruangaman.test')->first();
        $konselor = User::query()->where('email', 'konselor@ruangaman.test')->first();
        $pelapor = User::query()->where('email', 'pelapor@ruangaman.test')->first();
        $statusProses = StatusKasus::query()->where('nama_status', 'Proses')->first();

        if (! $admin || ! $konselor || ! $pelapor || ! $statusProses) {
            return;
        }

        $laporan = LaporanKasus::query()->firstOrCreate(
            ['kode_laporan' => 'RA-2026-PBI06'],
            [
                'user_id' => $pelapor->user_id,
                'status_id' => $statusProses->status_id,
                'judul' => 'Pendampingan Psikososial Kasus KBGO',
                'kategori_kekerasan' => 'KBGO',
                'lokasi_kejadian' => 'Ruang Digital / Daring',
                'kronologi' => 'Laporan contoh untuk pengujian integrasi PBI-04 dan PBI-06.',
                'waktu_kejadian' => now()->subDays(2),
                'tingkat_urgensi' => 'tinggi',
                'mode_anonim' => true,
            ]
        );

        $penanganan = PenangananKasus::query()->firstOrCreate(
            ['laporan_id' => $laporan->laporan_id],
            [
                'petugas_id' => $konselor->user_id,
                'tanggal_mulai' => now()->subDay()->toDateString(),
                'tanggal_selesai' => null,
                'catatan' => 'Segera lakukan asesmen awal psikososial.',
            ]
        );

        $entriDummy = [
            [
                'aktivitas' => 'Status kasus berubah dari Pending menjadi Proses. Catatan: Kasus ditugaskan kepada pendamping (Konselor Demo).',
                'jenis_log' => LogPenanganan::JENIS_SISTEM,
                'user_id' => $admin->user_id,
                'waktu' => now()->subHours(20),
            ],
            [
                'aktivitas' => 'Admin Kasus memberikan arahan awal pengamanan bukti digital bersama konselor pendamping.',
                'jenis_log' => LogPenanganan::JENIS_MANUAL,
                'user_id' => $admin->user_id,
                'waktu' => now()->subHours(16),
            ],
            [
                'aktivitas' => 'Konselor melakukan sesi asesmen awal dan menyusun rencana dukungan psikologis bersama pelapor.',
                'jenis_log' => LogPenanganan::JENIS_MANUAL,
                'user_id' => $konselor->user_id,
                'waktu' => now()->subHours(8),
            ],
        ];

        foreach ($entriDummy as $item) {
            LogPenanganan::query()->updateOrCreate(
                [
                    'penanganan_id' => $penanganan->penanganan_id,
                    'jenis_log' => $item['jenis_log'],
                    'aktivitas' => $item['aktivitas'],
                ],
                [
                    'laporan_id' => $laporan->laporan_id,
                    'user_id' => $item['user_id'],
                    'waktu' => $item['waktu'],
                ]
            );
        }
    }
}
