<?php

namespace App\Policies;

use App\Models\PendaftaranKegiatan;
use App\Models\User;

class PendaftaranKegiatanPolicy
{
    /**
     * Hanya Pelapor yang dapat melihat daftar pendaftarannya sendiri.
     */
    public function viewAnyOwn(User $user): bool
    {
        return $user->isPelapor() && $user->hasPermission('daftar-kegiatan');
    }

    /**
     * Hanya Pelapor pemilik pendaftaran yang dapat membatalkan pendaftarannya sendiri.
     */
    public function cancel(User $user, PendaftaranKegiatan $pendaftaran): bool
    {
        return $user->isPelapor()
            && $user->hasPermission('daftar-kegiatan')
            && (int) $pendaftaran->user_id === (int) $user->user_id;
    }

    /**
     * Hanya Admin Kasus yang dapat memverifikasi kehadiran peserta.
     */
    public function verifyAttendance(User $user, PendaftaranKegiatan $pendaftaran): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-kegiatan');
    }
}
