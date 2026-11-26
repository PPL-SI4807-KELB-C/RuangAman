<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackArtikel extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_OPTIONS = [
        self::STATUS_PENDING => 'Menunggu Moderasi',
        self::STATUS_DISETUJUI => 'Disetujui',
        self::STATUS_DITOLAK => 'Ditolak',
    ];

    protected $table = 'feedback_artikel';

    protected $primaryKey = 'feedback_id';

    protected $fillable = [
        'artikel_id',
        'user_id',
        'isi_feedback',
        'status_moderasi',
    ];

    public function artikel(): BelongsTo
    {
        return $this->belongsTo(Artikel::class, 'artikel_id', 'artikel_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status_moderasi', self::STATUS_DISETUJUI);
    }

    public function statusLabel(): string
    {
        return self::STATUS_OPTIONS[$this->status_moderasi] ?? ucfirst((string) $this->status_moderasi);
    }

    public function statusBadgeVariant(): string
    {
        return match ($this->status_moderasi) {
            self::STATUS_DISETUJUI => 'success',
            self::STATUS_DITOLAK => 'danger',
            default => 'warning',
        };
    }

    /**
     * Nama tampilan aman untuk feedback publik tanpa mengungkap email akun.
     */
    public function namaPengaruhAman(): string
    {
        $alias = $this->user?->profilPelapor?->alias ?: $this->user?->nama_tampilan;

        return $alias ?: 'Anggota RuangAman';
    }
}
