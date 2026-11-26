<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'role_id',
        'nama_tampilan',
        'email',
        'password_hash',
        'status',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function profilPelapor(): HasOne
    {
        return $this->hasOne(ProfilPelapor::class, 'user_id', 'user_id');
    }

    public function konselor(): HasOne
    {
        return $this->hasOne(Konselor::class, 'user_id', 'user_id');
    }

    public function laporanKasus(): HasMany
    {
        return $this->hasMany(LaporanKasus::class, 'user_id', 'user_id');
    }

    public function penangananKasus(): HasMany
    {
        return $this->hasMany(PenangananKasus::class, 'petugas_id', 'user_id');
    }

    public function pendaftaranKegiatan(): HasMany
    {
        return $this->hasMany(PendaftaranKegiatan::class, 'user_id', 'user_id');
    }

    public function artikel(): HasMany
    {
        return $this->hasMany(Artikel::class, 'penulis_id', 'user_id');
    }

    public function logPenanganan(): HasMany
    {
        return $this->hasMany(LogPenanganan::class, 'user_id', 'user_id');
    }

    /**
     * Memeriksa apakah user memiliki role tertentu.
     *
     * @param string|array<string> $roles
     */
    public function hasRole(string|array $roles): bool
    {
        $roleName = $this->role?->nama_role;

        if (! $roleName) {
            return false;
        }

        if (is_array($roles)) {
            return in_array($roleName, $roles, true);
        }

        return $roleName === $roles;
    }

    /**
     * Memeriksa apakah user adalah Admin Kasus.
     */
    public function isAdminKasus(): bool
    {
        return $this->hasRole('Admin Kasus');
    }

    /**
     * Memeriksa apakah user adalah Pelapor.
     */
    public function isPelapor(): bool
    {
        return $this->hasRole('Pelapor');
    }

    /**
     * Memeriksa apakah user adalah Konselor/Psikolog.
     */
    public function isKonselor(): bool
    {
        return $this->hasRole('Konselor');
    }

    /**
     * Memeriksa apakah user memiliki permission tertentu melalui role miliknya.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdminKasus()) {
            return true;
        }

        return $this->role?->permissions->contains('nama_permission', $permission) ?? false;
    }
}
