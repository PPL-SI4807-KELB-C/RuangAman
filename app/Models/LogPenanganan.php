<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model LogPenanganan (PBI-06: Log Penanganan Kasus).
 */
class LogPenanganan extends Model
{
    use HasFactory;

    public const JENIS_MANUAL = 'manual';

    public const JENIS_SISTEM = 'sistem';

    public const JENIS_OPTIONS = [
        self::JENIS_MANUAL => 'Catatan Manual',
        self::JENIS_SISTEM => 'Sistem Otomatis',
    ];

    protected $table = 'log_penanganan';

    protected $primaryKey = 'log_id';

    public $timestamps = true;

    protected $fillable = [
        'penanganan_id',
        'laporan_id',
        'user_id',
        'jenis_log',
        'aktivitas',
        'waktu',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'waktu' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'log_id';
    }

    protected static function booted(): void
    {
        static::creating(function (self $log): void {
            if ($log->waktu === null) {
                $log->waktu = now();
            }

            if ($log->laporan_id === null && $log->penanganan_id !== null) {
                $log->laporan_id = PenangananKasus::query()
                    ->where('penanganan_id', $log->penanganan_id)
                    ->value('laporan_id');
            }
        });
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

    /**
     * Membatasi daftar log sesuai aktor: Admin Kasus melihat seluruh log,
     * Konselor hanya melihat log pada penanganan kasus yang ditugaskan kepadanya.
     */
    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        if ($user->isAdminKasus()) {
            return $query;
        }

        if ($user->isKonselor()) {
            return $query->whereHas('penangananKasus', function (Builder $sub) use ($user): void {
                $sub->where('petugas_id', $user->user_id);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    public function isManual(): bool
    {
        return in_array($this->jenis_log, [self::JENIS_MANUAL, 'manual_catatan'], true);
    }

    public function isSistem(): bool
    {
        return in_array($this->jenis_log, [self::JENIS_SISTEM, 'otomatis_status'], true);
    }

    /**
     * Memeriksa apakah kasus terkait log ini masih aktif.
     */
    public function isKasusAktif(): bool
    {
        if ($this->penangananKasus !== null) {
            return $this->penangananKasus->isKasusAktif();
        }

        $namaStatus = strtolower((string) $this->laporanKasus?->statusKasus?->nama_status);

        return $namaStatus !== '' && ! in_array($namaStatus, ['selesai', 'ditolak'], true);
    }

    /**
     * Menampilkan nama pembuat log secara aman tanpa membocorkan email.
     */
    public function namaPembuatAman(): string
    {
        return $this->user?->nama_tampilan ?: 'Sistem RuangAman';
    }

    public function peranPembuatLabel(): string
    {
        return $this->user?->role?->nama_role ?: 'Sistem';
    }

    public function jenisLabel(): string
    {
        if ($this->isSistem()) {
            return self::JENIS_OPTIONS[self::JENIS_SISTEM];
        }

        if ($this->isManual()) {
            return self::JENIS_OPTIONS[self::JENIS_MANUAL];
        }

        return ucfirst((string) $this->jenis_log);
    }
}
