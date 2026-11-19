<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KegiatanEdukasi extends Model
{
    use HasFactory;

    public const METODE_ONLINE = 'online';

    public const METODE_OFFLINE = 'offline';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_TAYANG = 'tayang';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const METODE_OPTIONS = [
        self::METODE_ONLINE => 'Online (Daring)',
        self::METODE_OFFLINE => 'Offline (Tatap Muka)',
    ];

    public const STATUS_OPTIONS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_TAYANG => 'Tayang',
        self::STATUS_SELESAI => 'Selesai',
        self::STATUS_DIBATALKAN => 'Dibatalkan',
    ];

    protected $table = 'kegiatan_edukasi';

    protected $primaryKey = 'kegiatan_id';

    protected $fillable = [
        'judul',
        'deskripsi',
        'narasumber',
        'tanggal_mulai',
        'waktu_mulai',
        'tanggal_selesai',
        'waktu_selesai',
        'metode',
        'lokasi_atau_tautan',
        'kuota',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'kuota' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'kegiatan_id';
    }

    public function pendaftaranKegiatan(): HasMany
    {
        return $this->hasMany(PendaftaranKegiatan::class, 'kegiatan_id', 'kegiatan_id');
    }

    /**
     * Scope hanya kegiatan berstatus tayang untuk tampilan publik/pelapor.
     */
    public function scopeTayang(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_TAYANG);
    }

    /**
     * Scope eager-load jumlah pendaftaran yang memakan kuota ('aktif' atau 'hadir').
     */
    public function scopeWithTerisiCount(Builder $query): Builder
    {
        return $query->withCount([
            'pendaftaranKegiatan as terisi_count' => function (Builder $sub) {
                $sub->whereIn('status_pendaftaran', [
                    PendaftaranKegiatan::STATUS_AKTIF,
                    PendaftaranKegiatan::STATUS_HADIR,
                ]);
            },
        ]);
    }

    /**
     * Mengembalikan objek Carbon waktu mulai kegiatan.
     */
    public function waktuMulaiCarbon(): Carbon
    {
        $tanggal = $this->tanggal_mulai instanceof Carbon
            ? $this->tanggal_mulai->format('Y-m-d')
            : (string) $this->tanggal_mulai;

        $waktu = substr((string) $this->waktu_mulai, 0, 8);

        return Carbon::parse("{$tanggal} {$waktu}", config('app.timezone'));
    }

    /**
     * Mengembalikan objek Carbon waktu selesai kegiatan jika tersedia.
     */
    public function waktuSelesaiCarbon(): ?Carbon
    {
        if (! $this->tanggal_selesai && ! $this->waktu_selesai) {
            return null;
        }

        $tanggal = $this->tanggal_selesai instanceof Carbon
            ? $this->tanggal_selesai->format('Y-m-d')
            : ($this->tanggal_selesai ?: $this->tanggal_mulai?->format('Y-m-d'));

        $waktu = $this->waktu_selesai ? substr((string) $this->waktu_selesai, 0, 8) : '23:59:59';

        return Carbon::parse("{$tanggal} {$waktu}", config('app.timezone'));
    }

    /**
     * Mengecek apakah waktu pelaksanaan kegiatan sudah dimulai/terlewati.
     */
    public function sudahDimulai(): bool
    {
        return now(config('app.timezone'))->greaterThanOrEqualTo($this->waktuMulaiCarbon());
    }

    /**
     * Menghitung jumlah kuota terisi ('aktif' + 'hadir').
     */
    public function terisiKuotaCount(): int
    {
        if (array_key_exists('terisi_count', $this->attributes)) {
            return (int) $this->attributes['terisi_count'];
        }

        return $this->pendaftaranKegiatan()
            ->whereIn('status_pendaftaran', [
                PendaftaranKegiatan::STATUS_AKTIF,
                PendaftaranKegiatan::STATUS_HADIR,
            ])
            ->count();
    }

    /**
     * Menghitung sisa kuota yang masih tersedia.
     */
    public function sisaKuota(): int
    {
        return max(0, (int) $this->kuota - $this->terisiKuotaCount());
    }

    public function isPenuh(): bool
    {
        return $this->sisaKuota() === 0;
    }

    /**
     * Menentukan apakah kegiatan dapat didaftar oleh pelapor saat ini.
     */
    public function bisaDidaftar(): bool
    {
        return $this->status === self::STATUS_TAYANG
            && ! $this->sudahDimulai()
            && ! $this->isPenuh();
    }

    public function statusLabel(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function statusBadgeVariant(): string
    {
        return match ($this->status) {
            self::STATUS_TAYANG => 'success',
            self::STATUS_DRAFT => 'warning',
            self::STATUS_SELESAI => 'info',
            self::STATUS_DIBATALKAN => 'danger',
            default => 'slate',
        };
    }

    public function metodeLabel(): string
    {
        return $this->metode === self::METODE_ONLINE ? 'Online' : 'Offline';
    }
}
