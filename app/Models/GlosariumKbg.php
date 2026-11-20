<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlosariumKbg extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_OPTIONS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Published',
    ];

    protected $table = 'glosarium_kbg';

    protected $primaryKey = 'glosarium_id';

    protected $fillable = [
        'istilah',
        'definisi',
        'kategori',
        'status_publikasi',
    ];

    public function getRouteKeyName(): string
    {
        return 'glosarium_id';
    }

    /**
     * Scope hanya istilah berstatus published untuk tampilan publik.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status_publikasi', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return $this->status_publikasi === self::STATUS_PUBLISHED;
    }

    /**
     * Huruf awal istilah untuk pengelompokan daftar glosarium A-Z.
     */
    public function inisialHuruf(): string
    {
        $inisial = mb_strtoupper(trim((string) $this->istilah));

        return $inisial === '' ? '#' : mb_substr($inisial, 0, 1);
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
