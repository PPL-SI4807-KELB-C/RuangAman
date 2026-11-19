<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Artikel extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUS_OPTIONS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Terpublikasi',
        self::STATUS_ARCHIVED => 'Diarsipkan',
    ];

    protected $table = 'artikel';

    protected $primaryKey = 'artikel_id';

    protected $fillable = [
        'kategori_id',
        'penulis_id',
        'judul',
        'slug',
        'isi',
        'status_publikasi',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriArtikel::class, 'kategori_id', 'kategori_id');
    }

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penulis_id', 'user_id');
    }

    /**
     * Hanya artikel terpublikasi yang dapat ditemukan melalui halaman publik.
     */
    public function scopeTerpublikasi(Builder $query): Builder
    {
        return $query->where('status_publikasi', self::STATUS_PUBLISHED);
    }

    public function ringkasan(int $limit = 180): string
    {
        $teks = trim((string) preg_replace('/\s+/', ' ', $this->isi));

        return Str::limit($teks, $limit);
    }

    public function statusLabel(): string
    {
        return self::STATUS_OPTIONS[$this->status_publikasi] ?? ucfirst((string) $this->status_publikasi);
    }

    public function statusBadgeVariant(): string
    {
        return match ($this->status_publikasi) {
            self::STATUS_PUBLISHED => 'success',
            self::STATUS_DRAFT => 'warning',
            self::STATUS_ARCHIVED => 'slate',
            default => 'slate',
        };
    }
}
