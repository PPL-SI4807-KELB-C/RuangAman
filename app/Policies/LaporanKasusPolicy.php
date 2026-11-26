<?php

namespace App\Policies;

use App\Models\LaporanKasus;
use App\Models\User;

class LaporanKasusPolicy
{
    /**
     * Pelapor hanya dapat melihat detail laporan miliknya sendiri.
     */
    public function view(User $user, LaporanKasus $laporan): bool
    {
        return (int) $user->user_id === (int) $laporan->user_id;
    }

    /**
     * Pelapor hanya dapat mengedit laporan miliknya jika statusnya masih Pending.
     */
    public function update(User $user, LaporanKasus $laporan): bool
    {
        if ((int) $user->user_id !== (int) $laporan->user_id) {
            return false;
        }

        $status = $laporan->statusKasus?->nama_status ?? 'Pending';

        return strtolower($status) === 'pending';
    }

    /**
     * Pelapor hanya dapat membatalkan/menghapus laporan miliknya jika statusnya masih Pending.
     */
    public function delete(User $user, LaporanKasus $laporan): bool
    {
        return $this->update($user, $laporan);
    }
}