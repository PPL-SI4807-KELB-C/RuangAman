<?php

namespace App\Policies;

use App\Models\Notifikasi;
use App\Models\User;

/**
 * PBI-18: Pengguna hanya dapat mengelola notifikasi miliknya sendiri.
 */
class NotifikasiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->status === 'aktif' && $user->hasPermission('kelola-notifikasi');
    }

    public function view(User $user, Notifikasi $notifikasi): bool
    {
        return $this->milik($user, $notifikasi);
    }

    public function update(User $user, Notifikasi $notifikasi): bool
    {
        return $this->milik($user, $notifikasi);
    }

    public function delete(User $user, Notifikasi $notifikasi): bool
    {
        return $this->milik($user, $notifikasi);
    }

    private function milik(User $user, Notifikasi $notifikasi): bool
    {
        return $this->viewAny($user) && (int) $notifikasi->user_id === (int) $user->user_id;
    }
}
