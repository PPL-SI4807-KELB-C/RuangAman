<?php

namespace App\Policies;

use App\Models\Pengumuman;
use App\Models\User;

class PengumumanPolicy
{
    /**
     * Hanya Admin Kasus yang dapat melihat daftar manajemen pengumuman.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-pengumuman');
    }

    /**
     * Publik hanya dapat membuka pengumuman yang sedang berada dalam periode tayangnya.
     * Pengumuman di luar periode atau berstatus arsip hanya untuk Admin Kasus.
     */
    public function view(?User $user, Pengumuman $pengumuman): bool
    {
        if ($pengumuman->sedangTayang()) {
            return true;
        }

        return $user !== null && ($user->isAdminKasus() || $user->hasPermission('kelola-pengumuman'));
    }

    public function create(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-pengumuman');
    }

    public function update(User $user, Pengumuman $pengumuman): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-pengumuman');
    }

    public function delete(User $user, Pengumuman $pengumuman): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-pengumuman');
    }
}
