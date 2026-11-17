<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaporanKasusRequest;
use App\Models\LaporanKasus;
use App\Services\LaporanSubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $profil = Auth::user()?->profilPelapor;
        $alias = $profil?->alias ?? (Auth::user()?->nama_tampilan ?? 'Pelapor A');

        return view('laporan.create', [
            'title' => 'Buat Laporan — RuangAman',
            'alias' => $alias,
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

        return redirect()
            ->route('laporan.success')
            ->with('kode_laporan', $result['kode_laporan'])
            ->with('alias', $request->input('alias', 'Pelapor A'));
    }

    public function success(): View
    {
        $kode = session('kode_laporan');

        if ($kode === null) {
            return redirect()->route('lacak.form');
        }

        return view('laporan.success', [
            'title' => 'Laporan Anda telah diterima — RuangAman',
            'kode_laporan' => $kode,
        ]);
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
