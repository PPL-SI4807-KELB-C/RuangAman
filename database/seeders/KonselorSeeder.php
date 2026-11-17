<?php

namespace Database\Seeders;

use App\Models\Konselor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Menyediakan data demo profil konselor/psikolog (PBI-13) agar modul dapat
 * diuji dan didemonstrasikan tanpa menunggu akun konselor dibuat manual.
 */
class KonselorSeeder extends Seeder
{
    /**
     * @var list<array<string, string>>
     */
    private const DATA = [
        [
            'email' => 'konselor.bagas@ruangaman.test',
            'nama_tampilan' => 'Bagas Prasetya',
            'nama' => 'Bagas Prasetya, S.Psi.',
            'spesialisasi' => 'Psikologi Remaja & Keluarga',
            'bio' => 'Pendamping psikososial remaja dan keluarga dengan pendekatan konseling suportif.',
            'status' => 'aktif',
        ],
        [
            'email' => 'konselor.maya@ruangaman.test',
            'nama_tampilan' => 'Maya Larasati',
            'nama' => 'Maya Larasati, M.Psi.',
            'spesialisasi' => 'Trauma & Pemulihan Korban KBG',
            'bio' => 'Berfokus pada pendampingan trauma dan pemulihan penyintas kekerasan berbasis gender.',
            'status' => 'cuti',
        ],
    ];

    public function run(): void
    {
        $roleKonselor = Role::query()->where('nama_role', 'Konselor')->first();

        if (! $roleKonselor) {
            return;
        }

        foreach (self::DATA as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'role_id' => $roleKonselor->role_id,
                    'nama_tampilan' => $data['nama_tampilan'],
                    'password_hash' => 'password',
                    'status' => 'aktif',
                ]
            );

            Konselor::query()->updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'nama' => $data['nama'],
                    'spesialisasi' => $data['spesialisasi'],
                    'bio' => $data['bio'],
                    'status' => $data['status'],
                ]
            );
        }
    }
}
