<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PBI-18: Notifikasi in-app untuk kejadian spesifik (bukan broadcast pengumuman).
 */
class Notifikasi extends Model
{
    public const TIPE_STATUS_BERUBAH = 'status_berubah';
    public const TIPE_PENUGASAN_KASUS = 'penugasan_kasus';
    public const TIPE_JADWAL_KONSELING = 'jadwal_konseling';
    public const TIPE_KEGIATAN = 'kegiatan_diterima';

    /**
     * Label tampilan untuk setiap tipe notifikasi.
     *
     * @var array<string, string>
     */
    public const LABEL_TIPE = [
        self::TIPE_STATUS_BERUBAH => 'Status Kasus',
        self::TIPE_PENUGASAN_KASUS => 'Penugasan Kasus',
        self::TIPE_JADWAL_KONSELING => 'Jadwal Konseling',
        self::TIPE_KEGIATAN => 'Kegiatan Edukasi',
    ];

    /**
     * Tabel notifikasi hanya memiliki created_at.
     */
    public const UPDATED_AT = null;

    protected $table = 'notifikasi';

    protected $primaryKey = 'notifikasi_id';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'read_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function scopeMilik(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->user_id);
    }

    public function scopeBelumDibaca(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeSudahDibaca(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    public function sudahDibaca(): bool
    {
        return $this->read_at !== null;
    }

    public function labelTipe(): string
    {
        return self::LABEL_TIPE[$this->tipe] ?? ucwords(str_replace('_', ' ', (string) $this->tipe));
    }
}
