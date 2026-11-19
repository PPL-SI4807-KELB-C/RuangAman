<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Konselor extends Model
{
    use HasFactory;

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_CUTI = 'cuti';

    public const STATUS_NONAKTIF = 'nonaktif';

    public const STATUS_OPTIONS = [
        self::STATUS_AKTIF => 'Aktif (Menerima Jadwal)',
        self::STATUS_CUTI => 'Cuti (Sementara Tidak Menerima Jadwal)',
        self::STATUS_NONAKTIF => 'Nonaktif',
    ];

    protected $table = 'konselor';

    protected $primaryKey = 'konselor_id';

    protected $fillable = [
        'user_id',
        'nama',
        'spesialisasi',
        'bio',
        'status',
    ];

    public function getRouteKeyName(): string
    {
        return 'konselor_id';
    }

    /**
     * Akun pengguna (role Konselor) yang memiliki profil ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Scope hanya konselor yang berstatus aktif (siap menerima jadwal).
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    public function isAktif(): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }

    /**
     * Konselor hanya dapat menerima slot jadwal baru saat berstatus aktif.
     */
    public function bisaMenerimaJadwal(): bool
    {
        return $this->isAktif();
    }

    public function statusLabel(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function statusBadgeVariant(): string
    {
        return match ($this->status) {
            self::STATUS_AKTIF => 'success',
            self::STATUS_CUTI => 'warning',
            self::STATUS_NONAKTIF => 'danger',
            default => 'slate',
        };
    }
}
