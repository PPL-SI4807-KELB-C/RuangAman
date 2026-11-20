<?php

namespace App\Policies;

use App\Models\Konselor;
use App\Models\User;

class KonselorPolicy
{
    /**
     * Hanya Admin Kasus (atau pemegang permission) yang dapat melihat seluruh data konselor.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-konselor');
    }

    /**
     * Admin Kasus dapat melihat semua profil; konselor hanya profilnya sendiri.
     */
    public function view(User $user, Konselor $konselor): bool
    {
        if ($user->isAdminKasus() || $user->hasPermission('kelola-konselor')) {
            return true;
        }

        return $user->isKonselor() && (int) $konselor->user_id === (int) $user->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-konselor');
    }

    public function update(User $user, Konselor $konselor): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-konselor');
    }

    public function delete(User $user, Konselor $konselor): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-konselor');
    }

    /**
     * Konselor hanya boleh memperbarui biodata profilnya sendiri.
     */
    public function updateProfil(User $user, Konselor $konselor): bool
    {
        return $user->isKonselor() && (int) $konselor->user_id === (int) $user->user_id;
    }
}
