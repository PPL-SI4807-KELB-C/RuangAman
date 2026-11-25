<?php

namespace App\Http\Controllers;

use App\Models\KegiatanEdukasi;
use App\Models\PendaftaranKegiatan;
use App\Services\KegiatanEdukasiService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PendaftaranKegiatanController extends Controller
{
    public function __construct(
        private readonly KegiatanEdukasiService $kegiatanService
    ) {}

    /**
     * Menampilkan daftar pendaftaran milik pelapor yang sedang login ("Pendaftaran Saya").
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAnyOwn', PendaftaranKegiatan::class);

        $pendaftaranList = PendaftaranKegiatan::query()
            ->with([
                'kegiatanEdukasi' => function ($query) {
                    $query->withTerisiCount();
                },
            ])
            ->where('user_id', $request->user()->user_id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pendaftaran-kegiatan.index', [
            'title' => 'Pendaftaran Kegiatan Saya - RuangAman',
            'pendaftaranList' => $pendaftaranList,
        ]);
    }

    /**
     * Mendaftarkan pelapor ke dalam kegiatan edukasi.
     */
    public function store(Request $request, KegiatanEdukasi $kegiatan): RedirectResponse
    {
        $this->authorize('register', $kegiatan);

        try {
            $this->kegiatanService->daftar($kegiatan, $request->user());

            return redirect()
                ->route('kegiatan.show', $kegiatan)
                ->with('success', "Anda berhasil mendaftar pada kegiatan \"{$kegiatan->judul}\".");
        } catch (DomainException $e) {
            return redirect()
                ->route('kegiatan.show', $kegiatan)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Membatalkan pendaftaran milik pelapor.
     */
    public function cancel(Request $request, PendaftaranKegiatan $pendaftaran): RedirectResponse
    {
        $this->authorize('cancel', $pendaftaran);

        try {
            $updated = $this->kegiatanService->batalkanPendaftaran($pendaftaran, $request->user());

            return redirect()
                ->back(fallback: route('pendaftaran-kegiatan.index'))
                ->with('success', "Pendaftaran untuk kegiatan \"{$updated->kegiatanEdukasi->judul}\" berhasil dibatalkan.");
        } catch (DomainException $e) {
            return redirect()
                ->back(fallback: route('pendaftaran-kegiatan.index'))
                ->with('error', $e->getMessage());
        }
    }
}
