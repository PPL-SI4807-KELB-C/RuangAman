<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LampiranLaporan extends Model
{
    protected $table = 'lampiran_laporan';

    protected $primaryKey = 'lampiran_id';

    public $timestamps = false;

    protected $fillable = [
        'laporan_id',
        'nama_file',
        'path_privat',
        'tipe_file',
        'ukuran_file',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function laporanKasus(): BelongsTo
    {
        return $this->belongsTo(LaporanKasus::class, 'laporan_id', 'laporan_id');
    }
}
