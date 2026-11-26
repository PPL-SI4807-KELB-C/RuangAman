<?php

namespace App\Providers;

use App\Models\Artikel;
use App\Models\FeedbackArtikel;
use App\Models\GlosariumKbg;
use App\Models\KategoriArtikel;
use App\Models\HalamanInformasi;
use App\Models\KegiatanEdukasi;
use App\Models\Konselor;
use App\Models\LaporanKasus;
use App\Models\LogPenanganan;
use App\Models\PenangananKasus;
use App\Models\PendaftaranKegiatan;
use App\Models\Pengumuman;
use App\Models\StatusKasus;
use App\Models\User;
use App\Policies\ArtikelPolicy;
use App\Policies\FeedbackArtikelPolicy;
use App\Policies\GlosariumKbgPolicy;
use App\Policies\KategoriArtikelPolicy;
use App\Policies\HalamanInformasiPolicy;
use App\Policies\KegiatanEdukasiPolicy;
use App\Policies\KonselorPolicy;
use App\Policies\LaporanKasusPolicy;
use App\Policies\LogPenangananPolicy;
use App\Policies\PenangananKasusPolicy;
use App\Policies\PendaftaranKegiatanPolicy;
use App\Policies\PengumumanPolicy;
use App\Policies\StatusKasusPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        LogPenanganan::class => LogPenangananPolicy::class,
        Konselor::class => KonselorPolicy::class,
        HalamanInformasi::class => HalamanInformasiPolicy::class,
        GlosariumKbg::class => GlosariumKbgPolicy::class,
        Pengumuman::class => PengumumanPolicy::class,
        Artikel::class => ArtikelPolicy::class,
        FeedbackArtikel::class => FeedbackArtikelPolicy::class,
        KategoriArtikel::class => KategoriArtikelPolicy::class,
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

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::policy(LaporanKasus::class, LaporanKasusPolicy::class);
        Gate::policy(Artikel::class, ArtikelPolicy::class);
        Gate::policy(FeedbackArtikel::class, FeedbackArtikelPolicy::class);
        Gate::policy(KategoriArtikel::class, KategoriArtikelPolicy::class);
        Gate::policy(StatusKasus::class, StatusKasusPolicy::class);
        Gate::policy(PenangananKasus::class, PenangananKasusPolicy::class);
        Gate::policy(LogPenanganan::class, LogPenangananPolicy::class);
        Gate::policy(KegiatanEdukasi::class, KegiatanEdukasiPolicy::class);
        Gate::policy(PendaftaranKegiatan::class, PendaftaranKegiatanPolicy::class);
        Gate::policy(Konselor::class, KonselorPolicy::class);

        Gate::define('admin-kasus', fn (User $user) => $user->isAdminKasus());
        Gate::define('konselor', fn (User $user) => $user->isKonselor());
        Gate::define('pelapor', fn (User $user) => $user->isPelapor());

        Gate::define('kelola-status-kasus', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-status-kasus'));
        Gate::define('kelola-penugasan-kasus', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-penugasan-kasus'));
        Gate::define('akses-log-penanganan', fn (User $user) => ! $user->isPelapor() && ($user->isAdminKasus() || $user->isKonselor()));
        Gate::define('kelola-log-manual', fn (User $user) => ! $user->isPelapor() && ($user->role?->permissions()->where('nama_permission', 'kelola-log-manual')->exists() ?? false));
        Gate::define('kelola-kegiatan', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-kegiatan'));
        Gate::define('kelola-konselor', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-konselor'));
        Gate::define('kelola-halaman-informasi', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-halaman-informasi'));
        Gate::define('kelola-glosarium', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-glosarium'));
        Gate::define('kelola-pengumuman', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-pengumuman'));
        Gate::define('kelola-artikel', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-artikel'));
        Gate::define('kelola-kategori-artikel', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-kategori-artikel'));
        Gate::define('beri-feedback', fn (User $user) => $user->isPelapor() && $user->hasPermission('beri-feedback'));
        Gate::define('moderasi-feedback', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('moderasi-feedback'));
        Gate::define('daftar-kegiatan', fn (User $user) => $user->isPelapor() && $user->hasPermission('daftar-kegiatan'));

        // Batasi percobaan pelacakan kode & pengiriman pesan publik (cegah tebak kode dan spam).
        RateLimiter::for('lacak', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
    }
}