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
        $user = Auth::user();
        $alias = $user->profilPelapor?->alias ?? $user->nama_tampilan;

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
        $user = $request->user();

        $result = $this->submissionService->submit(
            $user,
            $request->validated(),
            $request->file('lampiran', [])
        );

        return redirect()
            ->route('laporan.success')
            ->with('kode_laporan', $result['kode_laporan'])
            ->with('alias', $user->profilPelapor?->alias ?? $user->nama_tampilan);
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
        return view('laporan.track-result', [
            'title' => 'Lacak Laporan — RuangAman',
            'laporan' => null,
            'kode_laporan' => 'Q7M2K9',
        ]);
    }

    public function trackResult(Request $request): View
    {
        $request->validate([
            'kode_laporan' => ['required', 'string', 'max:50'],
        ]);

        $kode = $request->input('kode_laporan');

        $laporan = LaporanKasus::query()
            ->where('kode_laporan', $kode)
            ->with(['statusKasus', 'lampiran', 'penangananKasus'])
            ->first();

        return view('laporan.track-result', [
            'title' => 'Lacak Laporan — RuangAman',
            'laporan' => $laporan,
            'kode_laporan' => $kode,
            'tidakDitemukan' => $laporan === null,
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

        if (! Auth::user()->isPelapor()) {
            abort(403, 'Hanya Pelapor yang dapat menautkan laporan ke akunnya.');
        }

        $laporan = LaporanKasus::query()->where('kode_laporan', $request->input('kode_laporan'))->firstOrFail();

        if (! $laporan->bisaDitautkan()) {
            return redirect()->route('lacak.hasil', ['kode_laporan' => $laporan->kode_laporan])
                ->with('error', 'Laporan ini sudah terhubung dengan akun lain sehingga tidak dapat ditautkan ulang.');
        }

        $laporan->update(['user_id' => Auth::id()]);

        return redirect()->route('lacak.hasil', ['kode_laporan' => $laporan->kode_laporan])
            ->with('success', 'Laporan berhasil ditautkan ke akun Anda. Riwayat sekarang tersimpan di dashboard.');
    }
}
