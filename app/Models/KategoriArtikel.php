<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriArtikel extends Model
{
    use HasFactory;

    /**
     * Kategori bawaan dari seeder. Dipakai sebagai cadangan agar PBI-10 (Artikel)
     * tetap berjalan mandiri tanpa menunggu PBI-11. Tidak boleh diubah namanya
     * maupun dihapus.
     */
    public const NAMA_DEFAULT = 'Umum';

    protected $table = 'kategori_artikel';

    protected $primaryKey = 'kategori_id';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function isDefault(): bool
    {
        return $this->nama_kategori === self::NAMA_DEFAULT;
    }

    public static function default(): self
    {
        return self::query()->firstOrCreate(
            ['nama_kategori' => self::NAMA_DEFAULT],
            ['deskripsi' => 'Kategori bawaan untuk artikel yang belum dikelompokkan.'],
        );
    }

    public function artikel(): HasMany
    {
        return $this->hasMany(Artikel::class, 'kategori_id', 'kategori_id');
    }
}
