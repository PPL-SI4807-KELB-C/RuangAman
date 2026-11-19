<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Menyediakan data referensi yang dibutuhkan setiap PBI agar dapat diuji
 * secara mandiri tanpa menunggu modul lain selesai.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    private const PERMISSIONS = [
        'kelola-pengguna' => 'Mengelola akun dan status akun pengguna (FR-07).',
        'kelola-role-permission' => 'Mengelola role, permission, dan pemetaannya (FR-08).',
        'kelola-halaman-informasi' => 'CRUD halaman informasi statis (PBI-01).',
        'kelola-glosarium' => 'CRUD istilah glosarium KBG (PBI-02).',
        'kelola-pengumuman' => 'CRUD pengumuman publik (PBI-03).',
        'kelola-status-kasus' => 'CRUD master status kasus (PBI-04).',
        'kelola-penugasan-kasus' => 'Menugaskan atau membatalkan petugas pada kasus (PBI-04).',
        'kelola-kegiatan' => 'CRUD kegiatan/webinar edukasi dan verifikasi kehadiran (PBI-05).',
        'kelola-log-manual' => 'CRUD catatan aktivitas manual penanganan (PBI-06).',
        'kelola-instansi-bantuan' => 'CRUD direktori instansi bantuan (PBI-07).',
        'kelola-layanan-kontak' => 'CRUD layanan dan kontak bantuan (PBI-08).',
        'kelola-faq' => 'CRUD FAQ/pusat bantuan (PBI-09).',
        'kelola-artikel' => 'CRUD artikel edukasi (PBI-10).',
        'kelola-kategori-artikel' => 'CRUD kategori artikel (PBI-11).',
        'moderasi-feedback' => 'Memoderasi feedback artikel (PBI-12).',
        'kelola-konselor' => 'CRUD data konselor/psikolog (PBI-13).',
        'kelola-jadwal-konseling' => 'CRUD slot jadwal konseling (PBI-14).',
        'kelola-sesi-konseling' => 'CRUD sesi dan hasil konseling (PBI-15).',
        'verifikasi-laporan' => 'Menyetujui, menolak, dan menyelesaikan laporan kasus (FR-15).',
        'lihat-lampiran-kasus' => 'Melihat lampiran bukti laporan sesuai penugasan.',
        'buat-laporan' => 'Membuat laporan kasus baru (PBI-16).',
        'kelola-laporan-pending' => 'Mengubah atau membatalkan laporan milik sendiri saat Pending (PBI-16).',
        'unggah-lampiran' => 'Mengunggah lampiran bukti pada laporan milik sendiri (PBI-16).',
        'kelola-profil-pelapor' => 'Mengelola profil alias dan preferensi anonim (PBI-17).',
        'pesan-konseling' => 'Memesan dan membatalkan jadwal konseling.',
        'daftar-kegiatan' => 'Mendaftar dan membatalkan pendaftaran kegiatan edukasi.',
        'beri-feedback' => 'Memberi dan mengelola feedback artikel milik sendiri (PBI-12).',
        'lacak-laporan' => 'Melacak status laporan menggunakan kode pelacakan (FR-14).',
        'kelola-notifikasi' => 'Membaca, menandai, dan menghapus notifikasi milik sendiri (PBI-18).',
    ];

    /**
     * @var array<string, list<string>>
     */
    private const ROLE_PERMISSIONS = [
        'Admin Kasus' => [
            'kelola-pengguna', 'kelola-role-permission', 'kelola-halaman-informasi', 'kelola-glosarium',
            'kelola-pengumuman', 'kelola-status-kasus', 'kelola-penugasan-kasus', 'kelola-kegiatan',
            'kelola-log-manual', 'kelola-instansi-bantuan', 'kelola-layanan-kontak', 'kelola-faq',
            'kelola-artikel', 'kelola-kategori-artikel', 'moderasi-feedback', 'kelola-konselor',
            'kelola-jadwal-konseling', 'kelola-sesi-konseling', 'verifikasi-laporan', 'lihat-lampiran-kasus',
            'lacak-laporan', 'kelola-notifikasi',
        ],
        'Konselor' => [
            'lihat-lampiran-kasus', 'kelola-log-manual', 'kelola-jadwal-konseling',
            'kelola-sesi-konseling', 'kelola-notifikasi',
        ],
        'Pelapor' => [
            'buat-laporan', 'kelola-laporan-pending', 'unggah-lampiran', 'kelola-profil-pelapor',
            'pesan-konseling', 'daftar-kegiatan', 'beri-feedback', 'lacak-laporan', 'kelola-notifikasi',
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const STATUS_KASUS = [
        'Pending' => 'Laporan baru menunggu verifikasi Admin Kasus.',
        'Proses' => 'Laporan valid dan sedang ditangani konselor pendamping.',
        'Selesai' => 'Penanganan kasus telah disetujui selesai oleh Admin Kasus.',
        'Ditolak' => 'Laporan tidak valid atau tidak dapat diproses.',
    ];

    /**
     * @var list<string>
     */
    private const KATEGORI_ARTIKEL = [
        'Pencegahan',
        'Pemulihan',
        'Hukum dan Hak Korban',
        'Kesehatan Mental',
    ];

    public function run(): void
    {
        $roles = $this->seedRoles();
        $this->seedPermissions($roles);
        $this->seedStatusKasus();
        $this->seedKategoriArtikel();
        $this->seedAkunDemo($roles);
        $this->call(KonselorSeeder::class);
        $this->call(KegiatanEdukasiSeeder::class);
        $this->call(ArtikelSeeder::class);
    }

    /**
     * @return array<string, Role>
     */
    private function seedRoles(): array
    {
        $deskripsi = [
            'Admin Kasus' => 'Mengelola layanan, memverifikasi laporan, dan mengelola data serta konten.',
            'Konselor' => 'Tenaga profesional pendamping yang menangani kasus yang ditugaskan.',
            'Pelapor' => 'Pengguna terdaftar dengan alias yang membuat dan memantau laporan.',
        ];

        $roles = [];

        foreach ($deskripsi as $namaRole => $keterangan) {
            $roles[$namaRole] = Role::query()->updateOrCreate(
                ['nama_role' => $namaRole],
                ['deskripsi' => $keterangan]
            );
        }

        return $roles;
    }

    /**
     * @param  array<string, Role>  $roles
     */
    private function seedPermissions(array $roles): void
    {
        $permissions = [];

        foreach (self::PERMISSIONS as $nama => $keterangan) {
            $permissions[$nama] = Permission::query()->updateOrCreate(
                ['nama_permission' => $nama],
                ['deskripsi' => $keterangan]
            );
        }

        foreach (self::ROLE_PERMISSIONS as $namaRole => $daftarPermission) {
            $ids = collect($daftarPermission)
                ->map(fn (string $nama) => $permissions[$nama]->permission_id)
                ->all();

            $roles[$namaRole]->permissions()->sync($ids);
        }
    }

    private function seedStatusKasus(): void
    {
        $this->call(StatusKasusSeeder::class);
    }

    private function seedKategoriArtikel(): void
    {
        foreach (self::KATEGORI_ARTIKEL as $namaKategori) {
            DB::table('kategori_artikel')->updateOrInsert(
                ['nama_kategori' => $namaKategori],
                ['deskripsi' => null, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /**
     * @param  array<string, Role>  $roles
     */
    private function seedAkunDemo(array $roles): void
    {
        $akun = [
            ['nama' => 'Admin RuangAman', 'email' => 'admin@ruangaman.test', 'role' => 'Admin Kasus'],
            ['nama' => 'Konselor Demo', 'email' => 'konselor@ruangaman.test', 'role' => 'Konselor'],
            ['nama' => 'Bintang Laut', 'email' => 'pelapor@ruangaman.test', 'role' => 'Pelapor'],
        ];

        foreach ($akun as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'role_id' => $roles[$data['role']]->role_id,
                    'nama_tampilan' => $data['nama'],
                    'password_hash' => 'password',
                    'status' => 'aktif',
                ]
            );

            if ($data['role'] === 'Konselor') {
                DB::table('konselor')->updateOrInsert(
                    ['user_id' => $user->user_id],
                    [
                        'nama' => 'Rina Kusuma, M.Psi.',
                        'spesialisasi' => 'Psikologi Klinis Dewasa',
                        'bio' => 'Pendamping psikososial untuk penyintas kekerasan berbasis gender.',
                        'status' => 'aktif',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            if ($data['role'] === 'Pelapor') {
                DB::table('profil_pelapor')->updateOrInsert(
                    ['user_id' => $user->user_id],
                    [
                        'alias' => 'Bintang Laut',
                        'kontak_opsional' => null,
                        'preferensi_anonim' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
