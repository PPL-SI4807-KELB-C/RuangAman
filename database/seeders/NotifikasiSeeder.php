<?php

namespace Database\Seeders;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * PBI-18: Data contoh notifikasi untuk akun demo agar modul dapat
 * didemonstrasikan tanpa menunggu modul lain selesai.
 */
class NotifikasiSeeder extends Seeder
{
    public function run(): void
    {
        $contoh = [
            'pelapor@ruangaman.test' => [
                ['Status laporan RA-DEMO-0001 diperbarui', 'Status laporan RA-DEMO-0001 berubah dari Pending menjadi Proses.', Notifikasi::TIPE_STATUS_BERUBAH, null, 2],
                ['Pendamping laporan RA-DEMO-0001 ditetapkan', 'Laporan RA-DEMO-0001 telah mendapatkan petugas/konselor pendamping dan sedang ditindaklanjuti.', Notifikasi::TIPE_PENUGASAN_KASUS, null, 2],
                ['Jadwal konseling dikonfirmasi', 'Jadwal konseling Anda telah dikonfirmasi oleh konselor. Silakan cek detail jadwal.', Notifikasi::TIPE_JADWAL_KONSELING, 1, 5],
            ],
            'konselor@ruangaman.test' => [
                ['Penugasan kasus RA-DEMO-0001', 'Anda ditugaskan sebagai petugas/konselor pendamping untuk laporan RA-DEMO-0001. Silakan tinjau detail penanganan kasus.', Notifikasi::TIPE_PENUGASAN_KASUS, null, 2],
            ],
        ];

        foreach ($contoh as $email => $daftar) {
            $user = User::query()->where('email', $email)->first();

            if ($user === null || Notifikasi::query()->where('user_id', $user->user_id)->exists()) {
                continue;
            }

            foreach ($daftar as [$judul, $pesan, $tipe, $dibacaHariLalu, $dibuatHariLalu]) {
                Notifikasi::query()->forceCreate([
                    'user_id' => $user->user_id,
                    'judul' => $judul,
                    'pesan' => $pesan,
                    'tipe' => $tipe,
                    'read_at' => $dibacaHariLalu !== null ? now()->subDays($dibacaHariLalu) : null,
                    'created_at' => now()->subDays($dibuatHariLalu),
                ]);
            }
        }
    }
}
