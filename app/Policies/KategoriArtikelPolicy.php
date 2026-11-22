<?php

namespace App\Policies;

use App\Models\KategoriArtikel;
use App\Models\User;

class KategoriArtikelPolicy
{
    /**
     * Hanya Admin Kasus (atau pemegang izin kelola-kategori-artikel) yang dapat
     * mengelola kategori artikel. Pelapor, Konselor, dan tamu mendapat 403.
     */
    private function dapatMengelola(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('kelola-kategori-artikel');
    }

    public function viewAny(User $user): bool
    {
        return $this->dapatMengelola($user);
    }

    public function create(User $user): bool
    {
        return $this->dapatMengelola($user);
    }

    public function update(User $user, KategoriArtikel $kategori): bool
    {
        return $this->dapatMengelola($user);
    }

    public function delete(User $user, KategoriArtikel $kategori): bool
    {
        return $this->dapatMengelola($user);
    }
}
