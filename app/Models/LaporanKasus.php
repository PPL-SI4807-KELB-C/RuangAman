<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanKasus extends Model
{
    protected $table = 'laporan_kasus';

    protected $primaryKey = 'laporan_id';

    protected $fillable = [
        'user_id',
        'status_id',
        'kode_laporan',
        'judul',
        'kategori_kekerasan',
        'lokasi_kejadian',
        'kronologi',
        'waktu_kejadian',
        'tingkat_urgensi',
        'mode_anonim',
    ];

    protected function casts(): array
    {
        return [
            'waktu_kejadian' => 'datetime',
            'mode_anonim' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function statusKasus(): BelongsTo
    {
        return $this->belongsTo(StatusKasus::class, 'status_id', 'status_id');
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(LampiranLaporan::class, 'laporan_id', 'laporan_id');
    }
}
