<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenangananKasus extends Model
{
    protected $table = 'penanganan_kasus';

    protected $primaryKey = 'penanganan_id';

    protected $fillable = [
        'laporan_id',
        'petugas_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'catatan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    /**
     * Relasi ke LaporanKasus yang ditangani.
     */
    public function laporanKasus(): BelongsTo
    {
        return $this->belongsTo(LaporanKasus::class, 'laporan_id', 'laporan_id');
    }

    /**
     * Relasi ke User sebagai petugas/konselor pendamping.
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id', 'user_id');
    }

    /**
     * Relasi ke log_penanganan (Titik integrasi dengan PBI-06).
     */
    public function logPenanganan(): HasMany
    {
        return $this->hasMany(LogPenanganan::class, 'penanganan_id', 'penanganan_id');
    }
}

