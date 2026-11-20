<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusKasus extends Model
{
    protected $table = 'status_kasus';

    protected $primaryKey = 'status_id';

    protected $fillable = [
        'nama_status',
        'deskripsi',
    ];

    public function laporanKasus(): HasMany
    {
        return $this->hasMany(LaporanKasus::class, 'status_id', 'status_id');
    }

    /**
     * Memeriksa apakah status kasus ini sedang digunakan oleh laporan kasus.
     */
    public function isUsedByLaporan(): bool
    {
        return $this->laporanKasus()->exists();
    }

    /**
     * Titik status mengikuti bahasa desain: Pending kuning, Proses biru, Selesai hijau, Ditolak merah.
     */
    public function statusBadgeVariant(): string
    {
        return match ($this->nama_status) {
            'Proses' => 'info',
            'Selesai' => 'success',
            'Ditolak' => 'danger',
            default => 'warning',
        };
    }
}
