<?php

namespace App\Policies;

use App\Models\PenangananKasus;
use App\Models\User;

class PenangananKasusPolicy
{
    /**
     * Menentukan apakah user dapat melihat daftar penanganan kasus.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-penugasan-kasus');
    }

    /**
     * Menentukan apakah user dapat melihat detail penanganan kasus.
     */
    public function view(User $user, PenangananKasus $penangananKasus): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-penugasan-kasus');
    }

    /**
     * Menentukan apakah user dapat membuat penanganan kasus.
     */
    public function create(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-penugasan-kasus');
    }

    /**
     * Menentukan apakah user dapat mengubah penanganan kasus.
     */
    public function update(User $user, PenangananKasus $penangananKasus): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-penugasan-kasus');
    }

    /**
     * Menentukan apakah user dapat menghapus/membatalkan penanganan kasus.
     */
    public function delete(User $user, PenangananKasus $penangananKasus): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-penugasan-kasus');
    }
}

