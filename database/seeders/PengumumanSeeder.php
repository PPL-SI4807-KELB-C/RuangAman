<?php

namespace Database\Seeders;

use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Menyediakan contoh pengumuman (PBI-03) dengan kondisi periode tayang yang
 * berbeda-beda: sedang tayang, belum mulai, sudah berakhir, dan diarsipkan.
 */
class PengumumanSeeder extends Seeder
{
    public function run(): void
    {
        $adminKasus = User::query()
            ->whereHas('role', fn ($query) => $query->where('nama_role', 'Admin Kasus'))
            ->orderBy('user_id', 'asc')
            ->first();

        if (! $adminKasus) {
            return;
        }

        $daftarPengumuman = [
            [
                'judul' => 'Layanan konseling daring dibuka setiap hari kerja',
                'isi' => 'Konseling daring melalui RuangAman kini tersedia setiap hari kerja pukul 09.00-16.00 WIB. Silakan ajukan permintaan jadwal melalui menu konseling pada akun Anda agar dapat dicocokkan dengan ketersediaan konselor pendamping.',
                'mulai_tayang' => now()->subDays(5)->toDateString(),
                'akhir_tayang' => null,
                'status' => Pengumuman::STATUS_AKTIF,
            ],
            [
                'judul' => 'Pendaftaran webinar KBGO dibuka minggu depan',
                'isi' => 'Pendaftaran kegiatan edukasi mengenai kekerasan berbasis gender online akan dibuka dalam periode berikut. Pengumuman ini sengaja dibuat belum masuk periode tayang agar perilaku filter publik dapat diuji.',
                'mulai_tayang' => now()->addDays(7)->toDateString(),
                'akhir_tayang' => now()->addDays(21)->toDateString(),
                'status' => Pengumuman::STATUS_AKTIF,
            ],
            [
                'judul' => 'Pemeliharaan sistem pada akhir bulan',
                'isi' => 'Layanan pelaporan pernah tidak dapat diakses sementara karena pemeliharaan sistem. Periode tayang pengumuman ini telah lewat sehingga tidak lagi muncul untuk publik.',
                'mulai_tayang' => now()->subDays(30)->toDateString(),
                'akhir_tayang' => now()->subDays(2)->toDateString(),
                'status' => Pengumuman::STATUS_AKTIF,
            ],
            [
                'judul' => 'Pengumuman lama yang telah diarsipkan',
                'isi' => 'Materi pengumuman ini sudah tidak relevan dan diarsipkan oleh Admin Kasus sehingga tidak ditampilkan pada daftar publik.',
                'mulai_tayang' => now()->subDays(60)->toDateString(),
                'akhir_tayang' => null,
                'status' => Pengumuman::STATUS_ARSIP,
            ],
        ];

        foreach ($daftarPengumuman as $data) {
            Pengumuman::query()->updateOrCreate(
                ['judul' => $data['judul']],
                $data + ['dibuat_oleh' => $adminKasus->user_id]
            );
        }
    }
}
