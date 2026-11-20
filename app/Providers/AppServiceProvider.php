<?php

namespace App\Providers;

use App\Models\Artikel;
use App\Models\GlosariumKbg;
use App\Models\HalamanInformasi;
use App\Models\KegiatanEdukasi;
use App\Models\Konselor;
use App\Models\PenangananKasus;
use App\Models\PendaftaranKegiatan;
use App\Models\Pengumuman;
use App\Models\StatusKasus;
use App\Models\User;
use App\Policies\ArtikelPolicy;
use App\Policies\GlosariumKbgPolicy;
use App\Policies\HalamanInformasiPolicy;
use App\Policies\KegiatanEdukasiPolicy;
use App\Policies\KonselorPolicy;
use App\Policies\PenangananKasusPolicy;
use App\Policies\PendaftaranKegiatanPolicy;
use App\Policies\PengumumanPolicy;
use App\Policies\StatusKasusPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected array $policies = [
        KegiatanEdukasi::class => KegiatanEdukasiPolicy::class,
        PendaftaranKegiatan::class => PendaftaranKegiatanPolicy::class,
        StatusKasus::class => StatusKasusPolicy::class,
        PenangananKasus::class => PenangananKasusPolicy::class,
        Konselor::class => KonselorPolicy::class,
        HalamanInformasi::class => HalamanInformasiPolicy::class,
        GlosariumKbg::class => GlosariumKbgPolicy::class,
        Pengumuman::class => PengumumanPolicy::class,
        Artikel::class => ArtikelPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.tailwind');

        Gate::define('admin-kasus', fn (User $user) => $user->isAdminKasus());
        Gate::define('konselor', fn (User $user) => $user->isKonselor());
        Gate::define('pelapor', fn (User $user) => $user->isPelapor());

        Gate::define('kelola-status-kasus', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-status-kasus'));
        Gate::define('kelola-penugasan-kasus', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-penugasan-kasus'));
        Gate::define('kelola-kegiatan', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-kegiatan'));
        Gate::define('kelola-konselor', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-konselor'));
        Gate::define('kelola-halaman-informasi', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-halaman-informasi'));
        Gate::define('kelola-glosarium', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-glosarium'));
        Gate::define('kelola-pengumuman', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-pengumuman'));
        Gate::define('kelola-artikel', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-artikel'));
        Gate::define('daftar-kegiatan', fn (User $user) => $user->isPelapor() && $user->hasPermission('daftar-kegiatan'));
    }
}
