<?php

namespace App\Policies;

use App\Models\Artikel;
use App\Models\User;

class ArtikelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-artikel');
    }

    /**
     * Artikel terpublikasi dapat dibaca publik; status lain khusus pengelola.
     */
    public function view(?User $user, Artikel $artikel): bool
    {
        if ($artikel->status_publikasi === Artikel::STATUS_PUBLISHED) {
            return true;
        }

        return $user !== null
            && ($user->isAdminKasus() || $user->hasPermission('kelola-artikel'));
    }

    public function create(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-artikel');
    }

    public function update(User $user, Artikel $artikel): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-artikel');
    }

    public function delete(User $user, Artikel $artikel): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-artikel');
    }
}
