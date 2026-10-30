<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusKasus extends Model
{
    protected $table = 'status_kasus';

    protected $primaryKey = 'status_id';

    protected $fillable = [
        'nama_status',
        'deskripsi',
    ];

    public function laporanKasus(): HasMany
    {
        return $this->hasMany(LaporanKasus::class, 'status_id', 'status_id');
    }
}
