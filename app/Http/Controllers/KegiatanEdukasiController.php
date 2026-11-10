<?php

namespace App\Http\Controllers;

use App\Models\KegiatanEdukasi;
use App\Models\PendaftaranKegiatan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KegiatanEdukasiController extends Controller
{
    /**
     * Menampilkan daftar kegiatan edukasi yang berstatus 'tayang' untuk publik dan pelapor.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $metode = trim((string) $request->input('metode', ''));

        $kegiatanList = KegiatanEdukasi::query()
            ->tayang()
            ->withTerisiCount()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhere('narasumber', 'like', "%{$search}%");
                });
            })
            ->when($metode !== '' && array_key_exists($metode, KegiatanEdukasi::METODE_OPTIONS), function ($query) use ($metode) {
                $query->where('metode', $metode);
            })
            ->orderBy('tanggal_mulai', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->paginate(9)
            ->withQueryString();

        $pendaftaranUserMap = [];
        $user = $request->user();

        if ($user && $user->isPelapor()) {
            $pendaftaranUserMap = PendaftaranKegiatan::query()
                ->where('user_id', $user->user_id)
                ->whereIn('kegiatan_id', $kegiatanList->pluck('kegiatan_id'))
                ->get()
                ->keyBy('kegiatan_id')
                ->all();
        }

        return view('kegiatan-edukasi.index', [
            'title' => 'Kegiatan & Webinar Edukasi - RuangAman',
            'kegiatanList' => $kegiatanList,
            'search' => $search,
            'selectedMetode' => $metode,
            'metodeOptions' => KegiatanEdukasi::METODE_OPTIONS,
            'pendaftaranUserMap' => $pendaftaranUserMap,
        ]);
    }

    /**
     * Menampilkan detail kegiatan edukasi tertentu beserta kuota dan status pendaftaran pribadi.
     */
    public function show(Request $request, KegiatanEdukasi $kegiatan): View
    {
        $this->authorize('view', $kegiatan);

        $kegiatan->loadCount([
            'pendaftaranKegiatan as terisi_count' => function ($query) {
                $query->whereIn('status_pendaftaran', [
                    PendaftaranKegiatan::STATUS_AKTIF,
                    PendaftaranKegiatan::STATUS_HADIR,
                ]);
            },
        ]);

        $pendaftaranSaya = null;
        $user = $request->user();

        if ($user && $user->isPelapor()) {
            $pendaftaranSaya = PendaftaranKegiatan::query()
                ->where('kegiatan_id', $kegiatan->kegiatan_id)
                ->where('user_id', $user->user_id)
                ->first();
        }

        return view('kegiatan-edukasi.show', [
            'title' => "{$kegiatan->judul} - Kegiatan Edukasi RuangAman",
            'kegiatan' => $kegiatan,
            'pendaftaranSaya' => $pendaftaranSaya,
            'sisaKuota' => $kegiatan->sisaKuota(),
            'terisiCount' => $kegiatan->terisiKuotaCount(),
        ]);
    }
}
