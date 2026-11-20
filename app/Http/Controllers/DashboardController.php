<?php

namespace App\Http\Controllers;

use App\Models\Konselor;
use App\Models\LaporanKasus;
use App\Models\LogPenanganan;
use App\Models\PenangananKasus;
use App\Models\StatusKasus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * FR-02: setelah login pengguna diarahkan ke halaman sesuai perannya.
     */
    public function index(): View
    {
        $user = auth()->user();

        if ($user->isAdminKasus()) {
            return $this->admin();
        }

        if ($user->isKonselor()) {
            return $this->konselor($user);
        }

        if ($user->isPelapor()) {
            return $this->pelapor($user);
        }

        abort(403, 'Dashboard belum tersedia untuk peran Anda.');
    }

    /**
     * FR-04: jumlah laporan per status, grafik konseling per bulan, laporan terbaru.
     */
    private function admin(): View
    {
        $statusOrder = ['Pending', 'Proses', 'Selesai', 'Ditolak'];

        $statusList = StatusKasus::query()
            ->withCount('laporanKasus')
            ->get()
            ->sortBy(fn (StatusKasus $status) => array_search($status->nama_status, $statusOrder) ?? 99)
            ->values();

        $totalLaporan = $statusList->sum('laporan_kasus_count');

        return view('dashboard.admin', [
            'title' => 'Dashboard Admin Kasus — RuangAman',
            'statusList' => $statusList,
            'totalLaporan' => $totalLaporan,
            'menungguVerifikasi' => $statusList->firstWhere('nama_status', 'Pending')?->laporan_kasus_count ?? 0,
            'konselingPerBulan' => $this->konselingPerBulan(),
            'laporanTerbaru' => LaporanKasus::query()
                ->with(['statusKasus', 'penangananKasus.petugas'])
                ->latest('created_at')
                ->limit(5)
                ->get(),
        ]);
    }

    /**
     * FR-05: kasus yang ditugaskan, jadwal konseling mendatang, aktivitas penanganan terbaru.
     */
    private function konselor(User $user): View
    {
        $profilKonselor = Konselor::query()->where('user_id', $user->user_id)->first();

        $kasusSaya = PenangananKasus::query()
            ->with(['laporanKasus.statusKasus'])
            ->where('petugas_id', $user->user_id)
            ->orderByDesc('tanggal_mulai')
            ->limit(5)
            ->get();

        $laporanSaya = $kasusSaya->pluck('laporan_id');

        return view('dashboard.konselor', [
            'title' => 'Dashboard Konselor — RuangAman',
            'namaKonselor' => $profilKonselor?->nama ?? $user->nama_tampilan,
            'kasusSaya' => $kasusSaya,
            'totalKasus' => PenangananKasus::query()->where('petugas_id', $user->user_id)->count(),
            'jadwalMendatang' => $profilKonselor === null
                ? collect()
                : DB::table('jadwal_konseling')
                    ->where('konselor_id', $profilKonselor->konselor_id)
                    ->where('tanggal', '>=', now()->toDateString())
                    ->where('status', '!=', 'dibatalkan')
                    ->orderBy('tanggal')
                    ->orderBy('waktu_mulai')
                    ->limit(5)
                    ->get(),
            'aktivitasTerbaru' => LogPenanganan::query()
                ->with(['laporanKasus', 'user'])
                ->whereIn('laporan_id', $laporanSaya)
                ->orderByDesc('waktu')
                ->limit(5)
                ->get(),
            'totalAktivitas' => LogPenanganan::query()->whereIn('laporan_id', $laporanSaya)->count(),
            'totalJadwal' => $profilKonselor === null
                ? 0
                : DB::table('jadwal_konseling')
                    ->where('konselor_id', $profilKonselor->konselor_id)
                    ->where('tanggal', '>=', now()->toDateString())
                    ->where('status', '!=', 'dibatalkan')
                    ->count(),
        ]);
    }

    /**
     * FR-06: status laporan, jadwal konseling, dan pemberitahuan perubahan status.
     */
    private function pelapor(User $user): View
    {
        return view('dashboard.pelapor', [
            'title' => 'Dashboard Pelapor — RuangAman',
            'alias' => $user->profilPelapor?->alias ?? $user->nama_tampilan,
            'laporanSaya' => LaporanKasus::query()
                ->with('statusKasus')
                ->where('user_id', $user->user_id)
                ->latest('created_at')
                ->limit(5)
                ->get(),
            'totalLaporan' => LaporanKasus::query()->where('user_id', $user->user_id)->count(),
            'totalProses' => LaporanKasus::query()
                ->where('user_id', $user->user_id)
                ->whereHas('statusKasus', fn ($query) => $query->where('nama_status', 'Proses'))
                ->count(),
            'jadwalMendatang' => DB::table('jadwal_konseling')
                ->where('user_id', $user->user_id)
                ->where('tanggal', '>=', now()->toDateString())
                ->where('status', '!=', 'dibatalkan')
                ->orderBy('tanggal')
                ->orderBy('waktu_mulai')
                ->limit(5)
                ->get(),
            'totalJadwal' => DB::table('jadwal_konseling')
                ->where('user_id', $user->user_id)
                ->where('tanggal', '>=', now()->toDateString())
                ->where('status', '!=', 'dibatalkan')
                ->count(),
            'belumDibaca' => DB::table('notifikasi')
                ->where('user_id', $user->user_id)
                ->whereNull('read_at')
                ->count(),
            'notifikasi' => DB::table('notifikasi')
                ->where('user_id', $user->user_id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ]);
    }

    /**
     * Enam bulan terakhir sebagai deret ['label' => 'Nov 2026', 'jumlah' => int].
     * Tabel jadwal/sesi konseling belum punya modul CRUD, jadi dibaca lewat query builder
     * dan pengelompokan per bulan dilakukan di PHP agar portabel ke sqlite (pengujian).
     *
     * @return \Illuminate\Support\Collection<int, array{label: string, jumlah: int}>
     */
    private function konselingPerBulan(): \Illuminate\Support\Collection
    {
        $tanggalSesi = DB::table('sesi_konseling')
            ->join('jadwal_konseling', 'jadwal_konseling.jadwal_id', '=', 'sesi_konseling.jadwal_id')
            ->pluck('jadwal_konseling.tanggal');

        $perBulan = $tanggalSesi
            ->filter()
            ->groupBy(fn (string $tanggal) => substr($tanggal, 0, 7))
            ->map->count();

        return collect(range(5, 0))
            ->map(function (int $offset) use ($perBulan) {
                $bulan = now()->startOfMonth()->subMonthsNoOverflow($offset);

                return [
                    'label' => $bulan->translatedFormat('M Y'),
                    'jumlah' => (int) ($perBulan[$bulan->format('Y-m')] ?? 0),
                ];
            });
    }
}
