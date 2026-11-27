<?php

namespace App\Providers;

use App\Models\LaporanKasus;
use App\Models\Notifikasi;
use App\Models\PenangananKasus;
use App\Models\PendaftaranKegiatan;
use App\Models\StatusKasus;
use App\Models\User;
use App\Policies\NotifikasiPolicy;
use App\Services\NotifikasiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * PBI-18: Seluruh integrasi Manajemen Notifikasi Sistem dipusatkan di sini
 * (rute, policy, gate, badge navbar, dan pemicu notifikasi otomatis), sehingga
 * kode milik PBI lain tidak perlu diubah.
 */
class NotifikasiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerAuthorization();
        $this->registerNavbarBadge();
        $this->registerPemicuOtomatis();
    }

    private function registerRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware('web')->group(base_path('routes/notifikasi.php'));
    }

    private function registerAuthorization(): void
    {
        Gate::policy(Notifikasi::class, NotifikasiPolicy::class);
        Gate::define('kelola-notifikasi', fn (User $user) => $user->hasPermission('kelola-notifikasi'));
    }

    /**
     * Jumlah notifikasi belum dibaca untuk lonceng di navbar.
     */
    private function registerNavbarBadge(): void
    {
        View::composer('layouts.app', function ($view) {
            $jumlah = Auth::check()
                ? Notifikasi::query()->milik(Auth::user())->belumDibaca()->count()
                : 0;

            $view->with('jumlahNotifikasiBelumDibaca', $jumlah);
        });
    }

    /**
     * Notifikasi dibuat otomatis karena kejadian spesifik (FR-29) dengan
     * mengamati event Eloquent pada model milik modul lain (hanya-baca).
     */
    private function registerPemicuOtomatis(): void
    {
        // Perubahan status laporan kasus -> pelapor.
        LaporanKasus::updated(function (LaporanKasus $laporan) {
            if (! $laporan->wasChanged('status_id')) {
                return;
            }

            $statusAwal = StatusKasus::query()->whereKey($laporan->getOriginal('status_id'))->value('nama_status') ?? '-';
            $statusTujuan = StatusKasus::query()->whereKey($laporan->status_id)->value('nama_status') ?? '-';

            $this->notifikasi()->statusKasusBerubah(
                $laporan->user_id !== null ? (int) $laporan->user_id : null,
                (string) $laporan->kode_laporan,
                $statusAwal,
                $statusTujuan
            );
        });

        // Penugasan kasus baru -> petugas & pelapor.
        PenangananKasus::created(function (PenangananKasus $penanganan) {
            $laporan = LaporanKasus::query()->find($penanganan->laporan_id);
            $kode = $this->kodeLaporan($penanganan, $laporan);

            $this->notifikasi()->kasusDitugaskan($this->idAtauNull($penanganan->petugas_id), $kode);
            $this->notifikasi()->pendampingDitetapkan($this->idAtauNull($laporan?->user_id), $kode);
        });

        // Petugas penugasan berganti -> petugas baru & petugas lama.
        PenangananKasus::updated(function (PenangananKasus $penanganan) {
            if (! $penanganan->wasChanged('petugas_id')) {
                return;
            }

            $kode = $this->kodeLaporan($penanganan);

            $this->notifikasi()->kasusDitugaskan($this->idAtauNull($penanganan->petugas_id), $kode);
            $this->notifikasi()->penugasanDicabut($this->idAtauNull($penanganan->getOriginal('petugas_id')), $kode);
        });

        // Penugasan dihapus -> petugas.
        PenangananKasus::deleted(function (PenangananKasus $penanganan) {
            $this->notifikasi()->penugasanDicabut($this->idAtauNull($penanganan->petugas_id), $this->kodeLaporan($penanganan));
        });

        // Pendaftaran kegiatan diterima -> pelapor.
        PendaftaranKegiatan::created(function (PendaftaranKegiatan $pendaftaran) {
            if ($pendaftaran->status_pendaftaran === PendaftaranKegiatan::STATUS_AKTIF) {
                $this->notifikasiKegiatan($pendaftaran, 'Pendaftaran Anda pada kegiatan ":judul" telah diterima. Pantau detailnya di menu Pendaftaran Saya.');
            }
        });

        // Pendaftaran diaktifkan kembali / kehadiran diverifikasi -> pelapor.
        PendaftaranKegiatan::updated(function (PendaftaranKegiatan $pendaftaran) {
            if (! $pendaftaran->wasChanged('status_pendaftaran')) {
                return;
            }

            $pesan = match ($pendaftaran->status_pendaftaran) {
                PendaftaranKegiatan::STATUS_AKTIF => 'Pendaftaran Anda pada kegiatan ":judul" telah diterima. Pantau detailnya di menu Pendaftaran Saya.',
                PendaftaranKegiatan::STATUS_HADIR, PendaftaranKegiatan::STATUS_TIDAK_HADIR => 'Kehadiran Anda pada kegiatan ":judul" telah diverifikasi sebagai: '.$pendaftaran->statusLabel().'.',
                default => null, // Pembatalan oleh pelapor sendiri tidak perlu notifikasi.
            };

            if ($pesan !== null) {
                $this->notifikasiKegiatan($pendaftaran, $pesan);
            }
        });
    }

    private function notifikasi(): NotifikasiService
    {
        return $this->app->make(NotifikasiService::class);
    }

    private function notifikasiKegiatan(PendaftaranKegiatan $pendaftaran, string $pesan): void
    {
        $judul = (string) ($pendaftaran->kegiatanEdukasi()->value('judul') ?? 'Kegiatan Edukasi');

        $this->notifikasi()->kegiatan((int) $pendaftaran->user_id, $judul, str_replace(':judul', $judul, $pesan));
    }

    private function kodeLaporan(PenangananKasus $penanganan, ?LaporanKasus $laporan = null): string
    {
        $kode = $laporan?->kode_laporan
            ?? LaporanKasus::query()->whereKey($penanganan->laporan_id)->value('kode_laporan');

        return (string) ($kode ?? "#{$penanganan->laporan_id}");
    }

    private function idAtauNull(mixed $id): ?int
    {
        return $id !== null ? (int) $id : null;
    }
}
