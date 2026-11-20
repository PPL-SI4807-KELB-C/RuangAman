<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriArtikel extends Model
{
    use HasFactory;

    protected $table = 'kategori_artikel';

    protected $primaryKey = 'kategori_id';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function artikel(): HasMany
    {
        return $this->hasMany(Artikel::class, 'kategori_id', 'kategori_id');
    }
}
