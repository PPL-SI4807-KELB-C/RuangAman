<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaporanKasusRequest;
use App\Models\LaporanKasus;
use App\Services\LaporanSubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanKasusController extends Controller
{
    public function __construct(
        private LaporanSubmissionService $submissionService,
    ) {
        //
    }

    public function create(): View
    {
        $profil = Auth::user()->profilPelapor;

        return view('laporan.create', [
            'title' => 'Buat Laporan - RuangAman',
            'alias' => $profil?->alias ?? Auth::user()->nama_tampilan,
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

        return redirect()
            ->route('laporan.success')
            ->with('kode_laporan', $result['kode_laporan']);
    }

    public function success(): View
    {
        $kode = session('kode_laporan');

        if ($kode === null) {
            abort(404);
        }

        return view('laporan.success', [
            'title' => 'Laporan Terkirim - RuangAman',
            'kode_laporan' => $kode,
        ]);
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
            ->with('statusKasus')
            ->first();

        return view('laporan.track-result', [
            'title' => 'Hasil Pelacakan - RuangAman',
            'laporan' => $laporan,
            'kode_laporan' => $request->input('kode_laporan'),
        ]);
    }
}
