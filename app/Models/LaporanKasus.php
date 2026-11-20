<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LaporanKasus extends Model
{
    /** Email akun anonim bersama peninggal laporan yang dulu dibuat tanpa login. */
    public const AKUN_ANONIM_BERSAMA = 'tamu@ruangaman.test';

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

    public function penangananKasus(): HasOne
    {
        return $this->hasOne(PenangananKasus::class, 'laporan_id', 'laporan_id');
    }

    /**
     * Hanya laporan peninggal alur tanpa akun yang boleh diklaim; laporan milik Pelapor lain tidak bisa direbut.
     */
    public function bisaDitautkan(): bool
    {
        return $this->user?->email === self::AKUN_ANONIM_BERSAMA;
    }
}
