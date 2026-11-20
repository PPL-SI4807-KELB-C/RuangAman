<?php

namespace App\Policies;

use App\Models\HalamanInformasi;
use App\Models\User;

class HalamanInformasiPolicy
{
    /**
     * Hanya Admin Kasus yang dapat melihat daftar manajemen halaman informasi.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-halaman-informasi');
    }

    /**
     * Halaman published dapat dibaca publik. Halaman draft hanya untuk Admin Kasus.
     */
    public function view(?User $user, HalamanInformasi $halaman): bool
    {
        if ($halaman->isPublished()) {
            return true;
        }

        return $user !== null && ($user->isAdminKasus() || $user->hasPermission('kelola-halaman-informasi'));
    }

    public function create(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-halaman-informasi');
    }

    public function update(User $user, HalamanInformasi $halaman): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-halaman-informasi');
    }

    public function delete(User $user, HalamanInformasi $halaman): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-halaman-informasi');
    }
}
