<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NonaktifkanKegiatanRequest;
use App\Http\Requests\StoreKegiatanEdukasiRequest;
use App\Http\Requests\UpdateKegiatanEdukasiRequest;
use App\Http\Requests\VerifikasiKehadiranRequest;
use App\Models\KegiatanEdukasi;
use App\Models\PendaftaranKegiatan;
use App\Services\KegiatanEdukasiService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KegiatanEdukasiController extends Controller
{
    public function __construct(
        private readonly KegiatanEdukasiService $kegiatanService
    ) {}

    /**
     * Menampilkan daftar seluruh kegiatan edukasi beserta pencarian, filter, dan pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', KegiatanEdukasi::class);

        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $metode = trim((string) $request->input('metode', ''));

        $kegiatanList = KegiatanEdukasi::query()
            ->withTerisiCount()
            ->withCount('pendaftaranKegiatan')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhere('narasumber', 'like', "%{$search}%");
                });
            })
            ->when($status !== '' && array_key_exists($status, KegiatanEdukasi::STATUS_OPTIONS), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($metode !== '' && array_key_exists($metode, KegiatanEdukasi::METODE_OPTIONS), function ($query) use ($metode) {
                $query->where('metode', $metode);
            })
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('waktu_mulai')
            ->paginate(10)
            ->withQueryString();

        return view('admin.kegiatan-edukasi.index', [
            'title' => 'Manajemen Kegiatan & Webinar Edukasi - RuangAman',
            'kegiatanList' => $kegiatanList,
            'search' => $search,
            'selectedStatus' => $status,
            'selectedMetode' => $metode,
            'statusOptions' => KegiatanEdukasi::STATUS_OPTIONS,
            'metodeOptions' => KegiatanEdukasi::METODE_OPTIONS,
        ]);
    }

    /**
     * Menampilkan form tambah kegiatan/webinar baru.
     */
    public function create(): View
    {
        $this->authorize('create', KegiatanEdukasi::class);

        return view('admin.kegiatan-edukasi.create', [
            'title' => 'Tambah Kegiatan Edukasi - RuangAman',
            'kegiatan' => new KegiatanEdukasi([
                'metode' => KegiatanEdukasi::METODE_ONLINE,
                'status' => KegiatanEdukasi::STATUS_DRAFT,
                'kuota' => 30,
            ]),
            'metodeOptions' => KegiatanEdukasi::METODE_OPTIONS,
            'statusOptions' => KegiatanEdukasi::STATUS_OPTIONS,
        ]);
    }

    /**
     * Menyimpan kegiatan edukasi baru.
     */
    public function store(StoreKegiatanEdukasiRequest $request): RedirectResponse
    {
        $this->authorize('create', KegiatanEdukasi::class);

        $kegiatan = KegiatanEdukasi::query()->create($request->validated());

        return redirect()
            ->route('admin.kegiatan-edukasi.show', $kegiatan)
            ->with('success', "Kegiatan \"{$kegiatan->judul}\" berhasil ditambahkan.");
    }

    /**
     * Menampilkan detail kegiatan beserta daftar peserta & kontrol verifikasi kehadiran.
     */
    public function show(KegiatanEdukasi $kegiatan): View
    {
        $this->authorize('view', $kegiatan);

        $kegiatan->load([
            'pendaftaranKegiatan' => function ($query) {
                $query->with(['user:user_id,nama_tampilan', 'user.profilPelapor:profil_id,user_id,alias'])
                    ->orderBy('created_at', 'asc');
            },
        ]);

        $statistik = [
            'total_pendaftar' => $kegiatan->pendaftaranKegiatan->count(),
            'terisi' => $kegiatan->pendaftaranKegiatan->whereIn('status_pendaftaran', [
                PendaftaranKegiatan::STATUS_AKTIF,
                PendaftaranKegiatan::STATUS_HADIR,
            ])->count(),
            'hadir' => $kegiatan->pendaftaranKegiatan->where('status_pendaftaran', PendaftaranKegiatan::STATUS_HADIR)->count(),
            'tidak_hadir' => $kegiatan->pendaftaranKegiatan->where('status_pendaftaran', PendaftaranKegiatan::STATUS_TIDAK_HADIR)->count(),
            'dibatalkan' => $kegiatan->pendaftaranKegiatan->where('status_pendaftaran', PendaftaranKegiatan::STATUS_DIBATALKAN)->count(),
        ];

        return view('admin.kegiatan-edukasi.show', [
            'title' => "Detail Kegiatan: {$kegiatan->judul} - RuangAman",
            'kegiatan' => $kegiatan,
            'statistik' => $statistik,
            'sisaKuota' => max(0, (int) $kegiatan->kuota - $statistik['terisi']),
            'sudahDimulai' => $kegiatan->sudahDimulai(),
        ]);
    }

    /**
     * Menampilkan form edit kegiatan edukasi.
     */
    public function edit(KegiatanEdukasi $kegiatan): View
    {
        $this->authorize('update', $kegiatan);

        return view('admin.kegiatan-edukasi.edit', [
            'title' => "Ubah Kegiatan: {$kegiatan->judul} - RuangAman",
            'kegiatan' => $kegiatan,
            'metodeOptions' => KegiatanEdukasi::METODE_OPTIONS,
            'statusOptions' => KegiatanEdukasi::STATUS_OPTIONS,
        ]);
    }

    /**
     * Memperbarui data kegiatan edukasi.
     */
    public function update(UpdateKegiatanEdukasiRequest $request, KegiatanEdukasi $kegiatan): RedirectResponse
    {
        $this->authorize('update', $kegiatan);

        $kegiatan->update($request->validated());

        return redirect()
            ->route('admin.kegiatan-edukasi.show', $kegiatan)
            ->with('success', "Kegiatan \"{$kegiatan->judul}\" berhasil diperbarui.");
    }

    /**
     * Menghapus permanen kegiatan jika belum memiliki pendaftar.
     * Diblokir dengan pesan error jelas jika sudah memiliki pendaftar.
     */
    public function destroy(KegiatanEdukasi $kegiatan): RedirectResponse
    {
        $this->authorize('delete', $kegiatan);

        $judul = $kegiatan->judul;

        try {
            $this->kegiatanService->hapusPermanen($kegiatan);

            return redirect()
                ->route('admin.kegiatan-edukasi.index')
                ->with('success', "Kegiatan \"{$judul}\" berhasil dihapus permanen.");
        } catch (DomainException $e) {
            return redirect()
                ->route('admin.kegiatan-edukasi.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Menonaktifkan kegiatan menjadi 'dibatalkan' atau 'draft'.
     */
    public function nonaktifkan(NonaktifkanKegiatanRequest $request, KegiatanEdukasi $kegiatan): RedirectResponse
    {
        $this->authorize('update', $kegiatan);

        $targetStatus = $request->validated('target_status') ?: KegiatanEdukasi::STATUS_DIBATALKAN;
        $updated = $this->kegiatanService->nonaktifkan($kegiatan, $targetStatus);

        return redirect()
            ->back(fallback: route('admin.kegiatan-edukasi.show', $kegiatan))
            ->with('success', "Status kegiatan \"{$updated->judul}\" berhasil diubah menjadi {$updated->statusLabel()}.");
    }

    /**
     * Memverifikasi kehadiran peserta ('hadir' atau 'tidak_hadir').
     */
    public function verifikasiKehadiran(
        VerifikasiKehadiranRequest $request,
        KegiatanEdukasi $kegiatan,
        PendaftaranKegiatan $pendaftaran
    ): RedirectResponse {
        $this->authorize('verifyAttendance', $pendaftaran);

        if ((int) $pendaftaran->kegiatan_id !== (int) $kegiatan->kegiatan_id) {
            abort(404);
        }

        try {
            $updated = $this->kegiatanService->verifikasiKehadiran(
                $pendaftaran,
                $request->validated('status_pendaftaran')
            );

            return redirect()
                ->route('admin.kegiatan-edukasi.show', $kegiatan)
                ->with('success', "Kehadiran peserta #{$updated->pendaftaran_id} berhasil ditandai sebagai {$updated->statusLabel()}.");
        } catch (DomainException $e) {
            return redirect()
                ->route('admin.kegiatan-edukasi.show', $kegiatan)
                ->with('error', $e->getMessage());
        }
    }
}
