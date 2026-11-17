<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenangananKasus extends Model
{
    protected $table = 'penanganan_kasus';

    protected $primaryKey = 'penanganan_id';

    protected $fillable = [
        'laporan_id',
        'petugas_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'catatan',
    ];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(LaporanKasus::class, 'laporan_id', 'laporan_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id', 'user_id');
    }
}
