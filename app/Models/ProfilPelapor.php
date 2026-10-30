<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilPelapor extends Model
{
    protected $table = 'profil_pelapor';

    protected $primaryKey = 'profil_id';

    protected $fillable = [
        'user_id',
        'alias',
        'kontak_opsional',
        'preferensi_anonim',
    ];

    protected function casts(): array
    {
        return [
            'preferensi_anonim' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
