<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendaftaranKegiatan extends Model
{
    use HasFactory;

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const STATUS_HADIR = 'hadir';

    public const STATUS_TIDAK_HADIR = 'tidak_hadir';

    public const STATUS_OPTIONS = [
        self::STATUS_AKTIF => 'Terdaftar (Aktif)',
        self::STATUS_DIBATALKAN => 'Dibatalkan',
        self::STATUS_HADIR => 'Hadir',
        self::STATUS_TIDAK_HADIR => 'Tidak Hadir',
    ];

    protected $table = 'pendaftaran_kegiatan';

    protected $primaryKey = 'pendaftaran_id';

    protected $fillable = [
        'kegiatan_id',
        'user_id',
        'status_pendaftaran',
    ];

    public function getRouteKeyName(): string
    {
        return 'pendaftaran_id';
    }

    public function kegiatanEdukasi(): BelongsTo
    {
        return $this->belongsTo(KegiatanEdukasi::class, 'kegiatan_id', 'kegiatan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Memeriksa apakah pelapor masih boleh membatalkan pendaftaran ini.
     */
    public function bisaDibatalkan(): bool
    {
        return $this->status_pendaftaran === self::STATUS_AKTIF
            && $this->kegiatanEdukasi !== null
            && ! $this->kegiatanEdukasi->sudahDimulai();
    }

    public function statusLabel(): string
    {
        return self::STATUS_OPTIONS[$this->status_pendaftaran] ?? ucfirst((string) $this->status_pendaftaran);
    }

    public function statusBadgeVariant(): string
    {
        return match ($this->status_pendaftaran) {
            self::STATUS_AKTIF => 'info',
            self::STATUS_HADIR => 'success',
            self::STATUS_TIDAK_HADIR => 'warning',
            self::STATUS_DIBATALKAN => 'danger',
            default => 'slate',
        };
    }

    /**
     * Kode anonim peserta agar identitas resmi & email tetap terlindungi.
     */
    public function namaPesertaAman(): string
    {
        $alias = $this->user?->profilPelapor?->alias ?: $this->user?->nama_tampilan;

        return $alias ?: 'Peserta Anonim';
    }
}
