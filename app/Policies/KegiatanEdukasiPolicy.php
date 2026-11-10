<?php

namespace App\Policies;

use App\Models\KegiatanEdukasi;
use App\Models\User;

class KegiatanEdukasiPolicy
{
    /**
     * Hanya Admin Kasus yang dapat melihat seluruh data manajemen kegiatan.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-kegiatan');
    }

    /**
     * Publik dan pengguna terautentikasi dapat melihat kegiatan yang berstatus tayang.
     * Kegiatan non-tayang hanya dapat dilihat oleh Admin Kasus.
     */
    public function view(?User $user, KegiatanEdukasi $kegiatan): bool
    {
        if ($kegiatan->status === KegiatanEdukasi::STATUS_TAYANG) {
            return true;
        }

        return $user !== null && ($user->isAdminKasus() || $user->hasPermission('kelola-kegiatan'));
    }

    public function create(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-kegiatan');
    }

    public function update(User $user, KegiatanEdukasi $kegiatan): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-kegiatan');
    }

    public function delete(User $user, KegiatanEdukasi $kegiatan): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-kegiatan');
    }

    public function verifyAttendance(User $user, KegiatanEdukasi $kegiatan): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-kegiatan');
    }

    /**
     * Hanya Pelapor yang dapat mendaftar ke kegiatan.
     */
    public function register(User $user, KegiatanEdukasi $kegiatan): bool
    {
        return $user->isPelapor() && $user->hasPermission('daftar-kegiatan');
    }
}
