<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HalamanInformasi extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_OPTIONS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Published',
    ];

    protected $table = 'halaman_informasi';

    protected $primaryKey = 'halaman_id';

    protected $fillable = [
        'judul',
        'slug',
        'isi',
        'status_publikasi',
    ];

    public function getRouteKeyName(): string
    {
        return 'halaman_id';
    }

    /**
     * Scope hanya halaman berstatus published untuk tampilan publik.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status_publikasi', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return $this->status_publikasi === self::STATUS_PUBLISHED;
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
            default => 'slate',
        };
    }
}
