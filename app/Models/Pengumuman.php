<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    use HasFactory;

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_ARSIP = 'arsip';

    public const STATUS_OPTIONS = [
        self::STATUS_AKTIF => 'Aktif',
        self::STATUS_ARSIP => 'Arsip',
    ];

    protected $table = 'pengumuman';

    protected $primaryKey = 'pengumuman_id';

    protected $fillable = [
        'dibuat_oleh',
        'judul',
        'isi',
        'mulai_tayang',
        'akhir_tayang',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mulai_tayang' => 'date',
            'akhir_tayang' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'pengumuman_id';
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'user_id');
    }

    /**
     * Scope pengumuman yang sedang tayang: status aktif dan tanggal hari ini
     * berada dalam periode tayang. Akhir tayang kosong berarti tanpa batas.
     */
    public function scopeTayang(Builder $query): Builder
    {
        $hariIni = now()->toDateString();

        return $query->where('status', self::STATUS_AKTIF)
            ->whereDate('mulai_tayang', '<=', $hariIni)
            ->where(function ($sub) use ($hariIni) {
                $sub->whereNull('akhir_tayang')
                    ->orWhereDate('akhir_tayang', '>=', $hariIni);
            });
    }

    /**
     * Menentukan apakah pengumuman sedang tampil untuk publik saat ini.
     */
    public function sedangTayang(): bool
    {
        $hariIni = now()->toDateString();

        return $this->status === self::STATUS_AKTIF
            && $this->mulai_tayang?->toDateString() <= $hariIni
            && ($this->akhir_tayang === null || $this->akhir_tayang->toDateString() >= $hariIni);
    }

    /**
     * Alasan singkat mengapa pengumuman belum/tidak tampil di daftar publik.
     */
    public function statusTayangLabel(): string
    {
        if ($this->status === self::STATUS_ARSIP) {
            return 'Diarsipkan';
        }

        $hariIni = now()->toDateString();

        if ($this->mulai_tayang?->toDateString() > $hariIni) {
            return 'Jadwal tayang belum dimulai';
        }

        if ($this->akhir_tayang !== null && $this->akhir_tayang->toDateString() < $hariIni) {
            return 'Periode tayang telah berakhir';
        }

        return 'Sedang tayang';
    }

    public function statusLabel(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function statusBadgeVariant(): string
    {
        return match ($this->status) {
            self::STATUS_AKTIF => 'success',
            self::STATUS_ARSIP => 'slate',
            default => 'slate',
        };
    }
}
