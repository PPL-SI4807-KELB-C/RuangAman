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
    public const SESSION_KODE_LACAK = 'lacak_kode_laporan';

    public const SESSION_DRAF = 'draf_laporan';

    private const KATEGORI = [
        'Siber' => 'Kekerasan digital',
        'Seksual' => 'Kekerasan seksual',
        'Psikis' => 'Kekerasan psikis',
        'Fisik' => 'Kekerasan fisik',
        'Ekonomi' => 'Kekerasan ekonomi',
        'Lainnya' => 'Belum yakin / lainnya',
    ];

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
            'draf' => session(self::SESSION_DRAF, []),
            'kategoriOptions' => self::KATEGORI,
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

        $request->session()->forget(self::SESSION_DRAF);

        return redirect()
            ->route('laporan.success')
            ->with('kode_laporan', $result['kode_laporan'])
            ->with('alias', $user->profilPelapor?->alias ?? $user->nama_tampilan);
    }

    public function success(): View|RedirectResponse
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

    /**
     * Simpan draf dari mini-form beranda di session (bukan di URL), lalu arahkan
     * ke formulir laporan, dashboard, atau registrasi sesuai status pengguna.
     */
    public function simpanDraf(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kategori' => ['nullable', 'string', 'in:' . implode(',', array_keys(self::KATEGORI))],
            'kronologi' => ['nullable', 'string', 'max:5000'],
        ]);

        $request->session()->put(self::SESSION_DRAF, array_filter($data, fn ($v) => $v !== null && $v !== ''));

        $user = $request->user();

        if ($user?->isPelapor()) {
            return redirect()->route('laporan.create');
        }

        return $user ? redirect()->route('dashboard') : redirect()->route('register');
    }

    public function trackForm(): View
    {
        return view('laporan.track-result', [
            'title' => 'Lacak Laporan — RuangAman',
            'laporan' => null,
            'kode_laporan' => null,
        ]);
    }

    /**
     * Terima kode lewat POST, simpan di session, lalu tampilkan hasil lewat GET
     * tanpa kode di query string (tidak tercatat di riwayat peramban).
     */
    public function cariLaporan(Request $request): RedirectResponse
    {
        $request->validate([
            'kode_laporan' => ['required', 'string', 'max:50'],
        ]);

        return $this->arahkanKeHasil($request->input('kode_laporan'));
    }

    public function trackResult(Request $request): View|RedirectResponse
    {
        $kode = $request->session()->get(self::SESSION_KODE_LACAK);

        if ($kode === null) {
            return redirect()->route('lacak.form')
                ->withErrors(['kode_laporan' => 'Masukkan kode laporan untuk melihat statusnya.']);
        }

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

        return $this->arahkanKeHasil($laporan->kode_laporan)
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
            return $this->arahkanKeHasil($laporan->kode_laporan)
                ->with('error', 'Laporan ini sudah terhubung dengan akun lain sehingga tidak dapat ditautkan ulang.');
        }

        $laporan->update(['user_id' => Auth::id()]);

        return $this->arahkanKeHasil($laporan->kode_laporan)
            ->with('success', 'Laporan berhasil ditautkan ke akun Anda. Riwayat sekarang tersimpan di dashboard.');
    }

    private function arahkanKeHasil(string $kode): RedirectResponse
    {
        session()->put(self::SESSION_KODE_LACAK, $kode);

        return redirect()->route('lacak.hasil');
    }
}
