<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model LogPenanganan (Stub Integrasi PBI-06).
 *
 * Mendukung pencatatan log aktivitas penanganan kasus berdasarkan
 * penanganan_id, laporan_id, dan user_id.
 */
class LogPenanganan extends Model
{
    protected $table = 'log_penanganan';

    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'laporan_id',
        'penanganan_id',
        'user_id',
        'jenis_log',
        'aktivitas',
        'waktu',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'waktu' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function penangananKasus(): BelongsTo
    {
        return $this->belongsTo(PenangananKasus::class, 'penanganan_id', 'penanganan_id');
    }

    public function laporanKasus(): BelongsTo
    {
        return $this->belongsTo(LaporanKasus::class, 'laporan_id', 'laporan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}

