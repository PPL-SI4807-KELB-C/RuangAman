<?php

namespace App\Policies;

use App\Models\StatusKasus;
use App\Models\User;

class StatusKasusPolicy
{
    /**
     * Menentukan apakah user dapat melihat daftar status kasus.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-status-kasus');
    }

    /**
     * Menentukan apakah user dapat melihat detail status kasus.
     */
    public function view(User $user, StatusKasus $statusKasus): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-status-kasus');
    }

    /**
     * Menentukan apakah user dapat membuat status kasus.
     */
    public function create(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-status-kasus');
    }

    /**
     * Menentukan apakah user dapat mengubah status kasus.
     */
    public function update(User $user, StatusKasus $statusKasus): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-status-kasus');
    }

    /**
     * Menentukan apakah user dapat menghapus status kasus.
     */
    public function delete(User $user, StatusKasus $statusKasus): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-status-kasus');
    }
}

