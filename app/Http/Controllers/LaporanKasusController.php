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
        $profil = Auth::user()->profilPelapor;

        return view('laporan.create', [
            'title' => 'Buat Laporan - RuangAman',
            'alias' => $profil?->alias ?? Auth::user()->nama_tampilan,
            // Nilai awal checkbox mode anonim mengikuti preferensi pada profil (PBI-17).
            'defaultAnonim' => $profil?->preferensi_anonim ?? true,
            'kategoriOptions' => [
                'Fisik' => 'Kekerasan Fisik',
                'Psikis' => 'Kekerasan Psikis/Emosional',
                'Seksual' => 'Kekerasan Seksual',
                'Ekonomi' => 'Kekerasan Ekonomi',
                'Siber' => 'Kekerasan Siber/Bullying',
                'Lainnya' => 'Lainnya',
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
            ->with('kode_laporan', $kode);
    }

    public function success(): View|RedirectResponse
    {
        $kode = session('kode_laporan');

        // Alih-alih abort(404), arahkan kembali ke daftar laporan jika session kosong/di-refresh
        if (! $kode) {
            return redirect()->route('laporan.index');
        }

        return view('laporan.success', [
            'title' => 'Laporan Terkirim - RuangAman',
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
        return view('laporan.track-form', [
            'title' => 'Lacak Status Laporan - RuangAman',
        ]);
    }

    public function trackResult(Request $request): View
    {
        $request->validate([
            'kode_laporan' => ['required', 'string', 'max:50'],
        ]);

        $laporan = LaporanKasus::query()
            ->where('kode_laporan', $request->input('kode_laporan'))
            ->with('status')
            ->first();

        return view('laporan.track-result', [
            'title' => 'Hasil Pelacakan - RuangAman',
            'laporan' => $laporan,
            'kode_laporan' => $request->input('kode_laporan'),
        ]);
    }
}