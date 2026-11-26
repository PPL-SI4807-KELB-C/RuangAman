<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontakBantuan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kontak_bantuan';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'kontak_id';

    /**
     * Indicates if the model's ID auto-increments.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'kontak_id';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'instansi_id',
        'jenis_kontak',
        'nilai_kontak',
        'keterangan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jenis_kontak' => 'string',
    ];

    /**
     * Get the institution this contact belongs to.
     */
    public function instansi(): BelongsTo
    {
        return $this->belongsTo(InstansiBantuan::class, 'instansi_id', 'instansi_id');
    }

    /**
     * Get human-readable label for jenis_kontak.
     */
    public function getLabelJenisKontakAttribute(): string
    {
        return match ($this->jenis_kontak) {
            'telepon'   => 'Telepon',
            'whatsapp'  => 'WhatsApp',
            'email'     => 'Email',
            'hotline'   => 'Hotline',
            'website'   => 'Situs web',
            default     => ucfirst($this->jenis_kontak),
        };
    }
}
