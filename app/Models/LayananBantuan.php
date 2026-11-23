<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LayananBantuan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'layanan_bantuan';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'layanan_id';

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
        return 'layanan_id';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'instansi_id',
        'nama_layanan',
        'deskripsi',
    ];

    /**
     * Get the institution this service belongs to.
     */
    public function instansi(): BelongsTo
    {
        return $this->belongsTo(InstansiBantuan::class, 'instansi_id', 'instansi_id');
    }
}
