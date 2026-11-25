<?php

namespace App\Policies;

use App\Models\LogPenanganan;
use App\Models\PenangananKasus;
use App\Models\User;

class LogPenangananPolicy
{
    /**
     * Memeriksa izin eksplisit 'kelola-log-manual' dari tabel role_permissions.
     */
    private function hasLogManualPermission(User $user): bool
    {
        if (! $user->role) {
            return false;
        }

        return $user->role->permissions()
            ->where('nama_permission', 'kelola-log-manual')
            ->exists();
    }

    /**
     * Hanya Admin Kasus dan Konselor/Psikolog yang dapat mengakses daftar log penanganan.
     */
    public function viewAny(User $user): bool
    {
        return ! $user->isPelapor() && ($user->isAdminKasus() || $user->isKonselor());
    }

    /**
     * Admin Kasus dapat melihat semua log; Konselor hanya log penanganan yang ditugaskan kepadanya.
     */
    public function view(User $user, LogPenanganan $log): bool
    {
        if ($user->isPelapor()) {
            return false;
        }

        if ($user->isAdminKasus()) {
            return true;
        }

        if ($user->isKonselor()) {
            return $log->penangananKasus !== null
                && (int) $log->penangananKasus->petugas_id === (int) $user->user_id;
        }

        return false;
    }

    /**
     * Admin Kasus dan Konselor dapat membuat catatan manual bila memiliki permission kelola-log-manual.
     */
    public function create(User $user): bool
    {
        return ! $user->isPelapor()
            && ($user->isAdminKasus() || $user->isKonselor())
            && $this->hasLogManualPermission($user);
    }

    /**
     * Konselor hanya dapat menambahkan log manual pada penanganan kasus yang ditugaskan kepadanya.
     */
    public function createForPenanganan(User $user, PenangananKasus $penanganan): bool
    {
        if (! $this->create($user)) {
            return false;
        }

        if ($user->isAdminKasus()) {
            return true;
        }

        if ($user->isKonselor()) {
            return (int) $penanganan->petugas_id === (int) $user->user_id;
        }

        return false;
    }

    /**
     * Log sistem terkunci permanen; log manual hanya dapat diubah saat kasus aktif
     * oleh pembuat (Konselor pada kasusnya) atau Admin sesuai permission.
     */
    public function update(User $user, LogPenanganan $log): bool
    {
        if ($user->isPelapor() || $log->isSistem() || ! $log->isKasusAktif()) {
            return false;
        }

        if (! $this->hasLogManualPermission($user)) {
            return false;
        }

        if ($user->isAdminKasus()) {
            return true;
        }

        if ($user->isKonselor()) {
            return (int) $log->user_id === (int) $user->user_id
                && $log->penangananKasus !== null
                && (int) $log->penangananKasus->petugas_id === (int) $user->user_id;
        }

        return false;
    }

    /**
     * Aturan penghapusan selaras dengan aturan pengubahan log manual.
     */
    public function delete(User $user, LogPenanganan $log): bool
    {
        return $this->update($user, $log);
    }
}
