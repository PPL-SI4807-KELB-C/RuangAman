<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaporanKasusRequest;
use App\Http\Requests\UpdateLaporanKasusRequest;
use App\Models\LampiranLaporan;
use App\Models\LaporanKasus;
use App\Services\LaporanSubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LaporanKasusController extends Controller
{
    public function __construct(
        private LaporanSubmissionService $submissionService,
    ) {
        //
    }

    /**
     * PBI-16: Daftar laporan milik pelapor yang sedang login.
     */
    public function index(): View
    {
        $laporans = Auth::user()
            ->laporanKasus()
            ->with('status')
            ->latest()
            ->paginate(10);

        return view('pelapor.profil.laporan.index', compact('laporans'));
    }

    public function create(): View
    {
        $profil = Auth::user()?->profilPelapor;
        $alias = $profil?->alias ?? (Auth::user()?->nama_tampilan ?? 'Pelapor A');

        return view('laporan.create', [
            'title' => 'Buat Laporan — RuangAman',
            'alias' => $alias,
            // Nilai awal checkbox mode anonim mengikuti preferensi pada profil (PBI-17).
            'defaultAnonim' => $profil?->preferensi_anonim ?? true,
            'kategoriOptions' => [
                'Siber' => 'Kekerasan digital',
                'Seksual' => 'Kekerasan seksual',
                'Psikis' => 'Kekerasan psikis',
                'Fisik' => 'Kekerasan fisik',
                'Ekonomi' => 'Kekerasan ekonomi',
                'Lainnya' => 'Belum yakin / lainnya',
            ],
            'urgensiOptions' => [
                'rendah' => 'Rendah',
                'sedang' => 'Sedang',
                'tinggi' => 'Tinggi',
                'darurat' => 'Darurat',
            ],
        ]);
    }

    public function store(StoreLaporanKasusRequest $request): RedirectResponse
    {
        $result = $this->submissionService->submit(
            Auth::user(),
            $request->validated(),
            $request->file('lampiran', [])
        );

        // Mengambil kode_laporan secara aman baik $result berupa array maupun object Model
        $kode = is_array($result)
            ? ($result['kode_laporan'] ?? $result['laporan']?->kode_laporan ?? null)
            : ($result->kode_laporan ?? null);

        if (! $kode) {
            return redirect()
                ->route('laporan.index')
                ->with('success', 'Laporan kasus berhasil dikirim.');
        }

        return redirect()
            ->route('laporan.success')
            ->with('kode_laporan', $kode)
            ->with('alias', $request->input('alias', 'Pelapor A'));
    }

    public function success(): View|RedirectResponse
    {
        $kode = session('kode_laporan');

        // Alih-alih abort(404), arahkan kembali ke daftar laporan jika session kosong/di-refresh
        if (! $kode) {
            return redirect()->route('laporan.index');
        }

        return view('laporan.success', [
            'title' => 'Laporan Anda telah diterima — RuangAman',
            'kode_laporan' => $kode,
        ]);
    }

    public function show(LaporanKasus $laporan): View
    {
        $this->authorize('view', $laporan);

        $laporan->load(['status', 'lampirans']);

        return view('pelapor.profil.laporan.show', compact('laporan'));
    }

    public function edit(LaporanKasus $laporan): View
    {
        $this->authorize('update', $laporan);

        $laporan->load(['status', 'lampirans']);

        return view('pelapor.profil.laporan.edit', [
            'laporan' => $laporan,
            'kategoriList' => LaporanKasus::KATEGORI,
        ]);
    }

    public function update(UpdateLaporanKasusRequest $request, LaporanKasus $laporan): RedirectResponse
    {
        // Otorisasi (pemilik + status Pending) sudah dilakukan oleh UpdateLaporanKasusRequest.
        $this->submissionService->update(
            $laporan,
            $request->validated(),
            $request->file('lampiran', [])
        );

        return redirect()
            ->route('laporan.show', $laporan)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(LaporanKasus $laporan): RedirectResponse
    {
        $this->authorize('delete', $laporan);

        $this->submissionService->cancel($laporan);

        return redirect()
            ->route('laporan.index')
            ->with('success', 'Laporan berhasil dibatalkan dan dihapus.');
    }

    /**
     * Unduh lampiran dari storage privat; hanya pemilik laporan.
     */
    public function downloadLampiran(LampiranLaporan $lampiran): Response
    {
        $this->authorize('view', $lampiran->laporanKasus);

        $disk = Storage::disk('local');

        abort_unless($disk->exists($lampiran->path_privat), 404, 'Berkas lampiran tidak ditemukan.');

        return $disk->download($lampiran->path_privat, $lampiran->nama_file);
    }

    public function destroyLampiran(LampiranLaporan $lampiran): RedirectResponse
    {
        $laporan = $lampiran->laporanKasus;

        $this->authorize('update', $laporan);

        $this->submissionService->deleteLampiran($lampiran);

        return redirect()
            ->route('laporan.edit', $laporan)
            ->with('success', 'Lampiran berhasil dihapus.');
    }

    public function trackForm(): View
    {
        $dummyLaporan = LaporanKasus::query()->latest('created_at')->first();

        return view('laporan.track-result', [
            'title' => 'Lacak Laporan — RuangAman',
            'laporan' => $dummyLaporan,
            'kode_laporan' => $dummyLaporan?->kode_laporan ?? 'Q7M2K9',
        ]);
    }

    public function trackResult(Request $request): View
    {
        $kode = $request->input('kode_laporan');

        $laporan = LaporanKasus::query()
            ->where('kode_laporan', $kode)
            ->with(['statusKasus', 'lampiran', 'penangananKasus'])
            ->first();

        return view('laporan.track-result', [
            'title' => 'Lacak Laporan — RuangAman',
            'laporan' => $laporan,
            'kode_laporan' => $kode ?? 'Q7M2K9',
        ]);
    }

    public function kirimPesan(Request $request): RedirectResponse
    {
        $request->validate([
            'kode_laporan' => ['required', 'string'],
            'pesan' => ['required', 'string', 'max:1000'],
        ]);

        $laporan = LaporanKasus::query()->where('kode_laporan', $request->input('kode_laporan'))->firstOrFail();

        DB::table('log_penanganan')->insert([
            'laporan_id' => $laporan->laporan_id,
            'penanganan_id' => $laporan->penangananKasus?->penanganan_id,
            'user_id' => Auth::id() ?? $laporan->user_id,
            'jenis_log' => 'manual_catatan',
            'aktivitas' => 'Pesan dari ' . (Auth::check() ? Auth::user()->nama_tampilan : 'Pelapor (Anonim)') . ': ' . $request->input('pesan'),
            'waktu' => now(),
            'created_at' => now(),
        ]);

        return redirect()->route('lacak.hasil', ['kode_laporan' => $laporan->kode_laporan])
            ->with('success', 'Pesan berhasil dikirim ke tim pendamping.');
    }

    public function tautkanAkun(Request $request): RedirectResponse
    {
        $request->validate([
            'kode_laporan' => ['required', 'string'],
        ]);

        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk atau daftar terlebih dahulu untuk menautkan laporan ini.');
        }

        $laporan = LaporanKasus::query()->where('kode_laporan', $request->input('kode_laporan'))->firstOrFail();
        $laporan->update(['user_id' => Auth::id()]);

        return redirect()->route('lacak.hasil', ['kode_laporan' => $laporan->kode_laporan])
            ->with('success', 'Laporan berhasil ditautkan ke akun Anda. Riwayat sekarang tersimpan di dashboard.');
    }
}