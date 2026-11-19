<?php

namespace App\Providers;

use App\Models\Artikel;
use App\Models\KegiatanEdukasi;
use App\Models\Konselor;
use App\Models\PenangananKasus;
use App\Models\PendaftaranKegiatan;
use App\Models\StatusKasus;
use App\Models\User;
use App\Policies\ArtikelPolicy;
use App\Policies\KegiatanEdukasiPolicy;
use App\Policies\KonselorPolicy;
use App\Policies\PenangananKasusPolicy;
use App\Policies\PendaftaranKegiatanPolicy;
use App\Policies\StatusKasusPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        Paginator::useTailwind();

        Gate::policy(Artikel::class, ArtikelPolicy::class);
        Gate::policy(StatusKasus::class, StatusKasusPolicy::class);
        Gate::policy(PenangananKasus::class, PenangananKasusPolicy::class);
        Gate::policy(KegiatanEdukasi::class, KegiatanEdukasiPolicy::class);
        Gate::policy(PendaftaranKegiatan::class, PendaftaranKegiatanPolicy::class);
        Gate::policy(Konselor::class, KonselorPolicy::class);

        Gate::define('admin-kasus', fn (User $user) => $user->isAdminKasus());
        Gate::define('konselor', fn (User $user) => $user->isKonselor());
        Gate::define('kelola-status-kasus', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-status-kasus'));
        Gate::define('kelola-penugasan-kasus', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-penugasan-kasus'));
        Gate::define('kelola-kegiatan', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-kegiatan'));
        Gate::define('kelola-konselor', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-konselor'));
        Gate::define('kelola-artikel', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-artikel'));
        Gate::define('daftar-kegiatan', fn (User $user) => $user->isPelapor() && $user->hasPermission('daftar-kegiatan'));
    }
}
