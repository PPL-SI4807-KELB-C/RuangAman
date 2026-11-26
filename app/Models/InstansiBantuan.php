<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstansiBantuan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'instansi_bantuan';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'instansi_id';

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
        return 'instansi_id';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_instansi',
        'alamat',
        'deskripsi',
        'website',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the layanan bantuan associated with the institution.
     */
    public function layananBantuan(): HasMany
    {
        return $this->hasMany(LayananBantuan::class, 'instansi_id', 'instansi_id');
    }

    /**
     * Get the kontak bantuan associated with the institution.
     */
    public function kontakBantuan(): HasMany
    {
        return $this->hasMany(KontakBantuan::class, 'instansi_id', 'instansi_id');
    }
}
