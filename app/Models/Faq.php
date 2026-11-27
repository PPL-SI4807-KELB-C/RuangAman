<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_OPTIONS = [
        self::STATUS_DRAFT => 'Draf',
        self::STATUS_PUBLISHED => 'Dipublikasikan',
    ];

    protected $table = 'faq';

    protected $primaryKey = 'faq_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'pertanyaan',
        'jawaban',
        'status_publikasi',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'faq_id';
    }

    /**
     * Scope hanya FAQ berstatus published untuk pusat bantuan publik.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status_publikasi', self::STATUS_PUBLISHED);
    }

    /**
     * Scope urutan tampil default (terkecil lebih dahulu).
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('urutan')->orderBy('faq_id');
    }

    public function isPublished(): bool
    {
        return $this->status_publikasi === self::STATUS_PUBLISHED;
    }

    public function statusLabel(): string
    {
        return self::STATUS_OPTIONS[$this->status_publikasi] ?? ucfirst((string) $this->status_publikasi);
    }
}
