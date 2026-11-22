<?php

namespace App\Policies;

use App\Models\GlosariumKbg;
use App\Models\User;

class GlosariumKbgPolicy
{
    /**
     * Hanya Admin Kasus yang dapat melihat daftar manajemen glosarium.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-glosarium');
    }

    /**
     * Istilah published dapat dibaca publik. Istilah draft hanya untuk Admin Kasus.
     */
    public function view(?User $user, GlosariumKbg $glosarium): bool
    {
        if ($glosarium->isPublished()) {
            return true;
        }

        return $user !== null && ($user->isAdminKasus() || $user->hasPermission('kelola-glosarium'));
    }

    public function create(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-glosarium');
    }

    public function update(User $user, GlosariumKbg $glosarium): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-glosarium');
    }

    public function delete(User $user, GlosariumKbg $glosarium): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-glosarium');
    }
}
