<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLogPenangananRequest;
use App\Http\Requests\UpdateLogPenangananRequest;
use App\Models\LogPenanganan;
use App\Models\PenangananKasus;
use App\Models\User;
use App\Services\LogPenangananService;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogPenangananController extends Controller
{
    public function __construct(
        private readonly LogPenangananService $logService
    ) {}

    /**
     * Menampilkan daftar riwayat log penanganan kasus kronologis dengan filter dan pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', LogPenanganan::class);

        $user = $request->user();
        $search = trim((string) $request->input('search', ''));
        $jenisLog = trim((string) $request->input('jenis_log', ''));
        $pembuatId = $request->input('pembuat_id');
        $penangananId = $request->input('penanganan_id');
        $dariTanggal = trim((string) $request->input('dari_tanggal', ''));
        $sampaiTanggal = trim((string) $request->input('sampai_tanggal', ''));

        $logList = LogPenanganan::query()
            ->accessibleBy($user)
            ->with([
                'user:user_id,role_id,nama_tampilan',
                'user.role:role_id,nama_role',
                'penangananKasus.laporanKasus:laporan_id,status_id,kode_laporan,judul,kategori_kekerasan',
                'penangananKasus.laporanKasus.statusKasus:status_id,nama_status',
                'penangananKasus.petugas:user_id,nama_tampilan',
                'laporanKasus:laporan_id,status_id,kode_laporan,judul',
                'laporanKasus.statusKasus:status_id,nama_status',
            ])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $sub) use ($search): void {
                    $sub->where('aktivitas', 'like', "%{$search}%")
                        ->orWhereHas('laporanKasus', function (Builder $lap) use ($search): void {
                            $lap->where('kode_laporan', 'like', "%{$search}%")
                                ->orWhere('judul', 'like', "%{$search}%");
                        })
                        ->orWhereHas('penangananKasus.laporanKasus', function (Builder $lap) use ($search): void {
                            $lap->where('kode_laporan', 'like', "%{$search}%")
                                ->orWhere('judul', 'like', "%{$search}%");
                        });
                });
            })
            ->when($jenisLog !== '', function (Builder $query) use ($jenisLog): void {
                if ($jenisLog === LogPenanganan::JENIS_MANUAL) {
                    $query->whereIn('jenis_log', [LogPenanganan::JENIS_MANUAL, 'manual_catatan']);
                } elseif ($jenisLog === LogPenanganan::JENIS_SISTEM) {
                    $query->whereIn('jenis_log', [LogPenanganan::JENIS_SISTEM, 'otomatis_status']);
                }
            })
            ->when(! empty($pembuatId), function (Builder $query) use ($pembuatId): void {
                $query->where('user_id', (int) $pembuatId);
            })
            ->when(! empty($penangananId), function (Builder $query) use ($penangananId): void {
                $query->where('penanganan_id', (int) $penangananId);
            })
            ->when($dariTanggal !== '', function (Builder $query) use ($dariTanggal): void {
                $query->whereDate('waktu', '>=', $dariTanggal);
            })
            ->when($sampaiTanggal !== '', function (Builder $query) use ($sampaiTanggal): void {
                $query->whereDate('waktu', '<=', $sampaiTanggal);
            })
            ->orderByDesc('waktu')
            ->orderByDesc('log_id')
            ->paginate(10)
            ->withQueryString();

        $daftarPembuat = User::query()
            ->whereHas('role', fn (Builder $q) => $q->whereIn('nama_role', ['Admin Kasus', 'Konselor']))
            ->select(['user_id', 'role_id', 'nama_tampilan'])
            ->with('role:role_id,nama_role')
            ->orderBy('nama_tampilan')
            ->get();

        $daftarPenanganan = PenangananKasus::query()
            ->with([
                'laporanKasus:laporan_id,status_id,kode_laporan,judul',
                'laporanKasus.statusKasus:status_id,nama_status',
                'petugas:user_id,nama_tampilan',
            ])
            ->when($user->isKonselor(), fn (Builder $q) => $q->where('petugas_id', $user->user_id))
            ->orderByDesc('penanganan_id')
            ->get();

        return view('log-penanganan.index', [
            'title' => 'Log Penanganan Kasus - RuangAman',
            'logList' => $logList,
            'daftarPembuat' => $daftarPembuat,
            'daftarPenanganan' => $daftarPenanganan,
            'jenisOptions' => LogPenanganan::JENIS_OPTIONS,
            'filters' => [
                'search' => $search,
                'jenis_log' => $jenisLog,
                'pembuat_id' => $pembuatId,
                'penanganan_id' => $penangananId,
                'dari_tanggal' => $dariTanggal,
                'sampai_tanggal' => $sampaiTanggal,
            ],
        ]);
    }

    /**
     * Menampilkan formulir tambah catatan manual penanganan kasus.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', LogPenanganan::class);

        $user = $request->user();
        $selectedPenangananId = $request->input('penanganan_id');

        if (! empty($selectedPenangananId)) {
            $selected = PenangananKasus::with('laporanKasus.statusKasus')->findOrFail((int) $selectedPenangananId);
            $this->authorize('createForPenanganan', [LogPenanganan::class, $selected]);
        }

        $penangananAktif = PenangananKasus::query()
            ->with([
                'laporanKasus:laporan_id,status_id,kode_laporan,judul',
                'laporanKasus.statusKasus:status_id,nama_status',
                'petugas:user_id,nama_tampilan',
            ])
            ->when($user->isKonselor(), fn (Builder $q) => $q->where('petugas_id', $user->user_id))
            ->orderByDesc('penanganan_id')
            ->get()
            ->filter(fn (PenangananKasus $penanganan): bool => $penanganan->isKasusAktif())
            ->values();

        return view('log-penanganan.create', [
            'title' => 'Tambah Catatan Manual Penanganan - RuangAman',
            'log' => new LogPenanganan([
                'penanganan_id' => $selectedPenangananId,
            ]),
            'penangananList' => $penangananAktif,
            'selectedPenangananId' => $selectedPenangananId,
        ]);
    }

    /**
     * Menyimpan catatan aktivitas manual baru.
     */
    public function store(StoreLogPenangananRequest $request): RedirectResponse
    {
        $penanganan = PenangananKasus::query()
            ->with('laporanKasus.statusKasus')
            ->findOrFail((int) $request->validated('penanganan_id'));

        $this->authorize('createForPenanganan', [LogPenanganan::class, $penanganan]);

        try {
            $log = $this->logService->buatLogManual(
                $penanganan,
                $request->user(),
                $request->validated('aktivitas'),
                $request->validated('waktu')
            );

            return redirect()
                ->route('log-penanganan.show', $log)
                ->with('success', 'Catatan manual penanganan kasus berhasil ditambahkan.');
        } catch (DomainException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan detail entri log beserta timeline kasus terkait.
     */
    public function show(LogPenanganan $logPenanganan): View
    {
        $this->authorize('view', $logPenanganan);

        $logPenanganan->load([
            'user:user_id,role_id,nama_tampilan',
            'user.role:role_id,nama_role',
            'penangananKasus.laporanKasus:laporan_id,status_id,kode_laporan,judul,kategori_kekerasan',
            'penangananKasus.laporanKasus.statusKasus:status_id,nama_status',
            'penangananKasus.petugas:user_id,nama_tampilan',
            'laporanKasus:laporan_id,status_id,kode_laporan,judul,kategori_kekerasan',
            'laporanKasus.statusKasus:status_id,nama_status',
        ]);

        $timelineLogs = LogPenanganan::query()
            ->with([
                'user:user_id,role_id,nama_tampilan',
                'user.role:role_id,nama_role',
                'penangananKasus.laporanKasus:laporan_id,status_id,kode_laporan,judul',
                'penangananKasus.laporanKasus.statusKasus:status_id,nama_status',
            ])
            ->when(
                $logPenanganan->penanganan_id !== null,
                fn (Builder $q) => $q->where('penanganan_id', $logPenanganan->penanganan_id),
                fn (Builder $q) => $q->where('laporan_id', $logPenanganan->laporan_id)
            )
            ->orderByDesc('waktu')
            ->orderByDesc('log_id')
            ->get();

        return view('log-penanganan.show', [
            'title' => "Detail Log #{$logPenanganan->log_id} - RuangAman",
            'log' => $logPenanganan,
            'timelineLogs' => $timelineLogs,
        ]);
    }

    /**
     * Menampilkan formulir edit catatan manual.
     */
    public function edit(LogPenanganan $logPenanganan): View
    {
        $logPenanganan->load([
            'penangananKasus.laporanKasus:laporan_id,status_id,kode_laporan,judul',
            'penangananKasus.laporanKasus.statusKasus:status_id,nama_status',
            'penangananKasus.petugas:user_id,nama_tampilan',
        ]);

        $this->authorize('update', $logPenanganan);

        return view('log-penanganan.edit', [
            'title' => "Ubah Catatan Log #{$logPenanganan->log_id} - RuangAman",
            'log' => $logPenanganan,
        ]);
    }

    /**
     * Memperbarui catatan aktivitas manual.
     */
    public function update(UpdateLogPenangananRequest $request, LogPenanganan $logPenanganan): RedirectResponse
    {
        $logPenanganan->load(['penangananKasus.laporanKasus.statusKasus', 'laporanKasus.statusKasus']);
        $this->authorize('update', $logPenanganan);

        try {
            $updated = $this->logService->perbaruiLogManual(
                $logPenanganan,
                $request->validated('aktivitas'),
                $request->validated('waktu')
            );

            return redirect()
                ->route('log-penanganan.show', $updated)
                ->with('success', 'Catatan manual penanganan kasus berhasil diperbarui.');
        } catch (DomainException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Menghapus catatan aktivitas manual.
     */
    public function destroy(LogPenanganan $logPenanganan): RedirectResponse
    {
        $logPenanganan->load(['penangananKasus.laporanKasus.statusKasus', 'laporanKasus.statusKasus']);
        $this->authorize('delete', $logPenanganan);

        try {
            $this->logService->hapusLogManual($logPenanganan);

            return redirect()
                ->route('log-penanganan.index')
                ->with('success', 'Catatan manual penanganan kasus berhasil dihapus.');
        } catch (DomainException $e) {
            return redirect()
                ->back(fallback: route('log-penanganan.index'))
                ->with('error', $e->getMessage());
        }
    }
}
