<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenangananKasusRequest;
use App\Http\Requests\UpdatePenangananKasusRequest;
use App\Models\LaporanKasus;
use App\Models\PenangananKasus;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenangananKasusController extends Controller
{
    /**
     * Menampilkan daftar penugasan kasus dengan filter/pencarian dan pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', PenangananKasus::class);

        $search = trim((string) $request->input('search', ''));
        $petugasId = $request->input('petugas_id');

        $penangananList = PenangananKasus::query()
            ->with(['laporanKasus.statusKasus', 'petugas.role'])
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('laporanKasus', function ($q) use ($search) {
                    $q->where('kode_laporan', 'like', "%{$search}%")
                        ->orWhere('judul', 'like', "%{$search}%");
                })->orWhereHas('petugas', function ($q) use ($search) {
                    $q->where('nama_tampilan', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(! empty($petugasId), function ($query) use ($petugasId) {
                $query->where('petugas_id', $petugasId);
            })
            ->orderBy('penanganan_id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $daftarPetugas = $this->getPetugasList();

        return view('penanganan-kasus.index', [
            'title' => 'Manajemen Penugasan Kasus - RuangAman',
            'penangananList' => $penangananList,
            'daftarPetugas' => $daftarPetugas,
            'search' => $search,
            'selectedPetugas' => $petugasId,
        ]);
    }

    /**
     * Menampilkan formulir penugasan kasus baru.
     * Hanya laporan yang belum memiliki penanganan yang ditampilkan.
     */
    public function create(): View
    {
        $this->authorize('create', PenangananKasus::class);

        // Ambil laporan yang belum memiliki penanganan kasus
        $laporanBelumDitugaskan = LaporanKasus::query()
            ->doesntHave('penangananKasus')
            ->with('statusKasus')
            ->orderBy('created_at', 'desc')
            ->get();

        $daftarPetugas = $this->getPetugasList();

        return view('penanganan-kasus.create', [
            'title' => 'Penugasan Kasus Baru - RuangAman',
            'penanganan' => new PenangananKasus(),
            'laporanList' => $laporanBelumDitugaskan,
            'daftarPetugas' => $daftarPetugas,
        ]);
    }

    /**
     * Menyimpan data penugasan kasus baru.
     */
    public function store(StorePenangananKasusRequest $request): RedirectResponse
    {
        $this->authorize('create', PenangananKasus::class);

        $penanganan = PenangananKasus::create($request->validated());

        return redirect()
            ->route('admin.penanganan-kasus.show', $penanganan)
            ->with('success', 'Penugasan kasus berhasil dibuat.');
    }

    /**
     * Menampilkan detail penugasan kasus (termasuk titik integrasi PBI-06).
     */
    public function show(PenangananKasus $penangananKasus): View
    {
        $this->authorize('view', $penangananKasus);

        $penangananKasus->load([
            'laporanKasus.statusKasus',
            'laporanKasus.user.profilPelapor',
            'petugas.role',
            'logPenanganan.user',
        ]);

        return view('penanganan-kasus.show', [
            'title' => 'Detail Penugasan Kasus - RuangAman',
            'penanganan' => $penangananKasus,
        ]);
    }

    /**
     * Menampilkan formulir edit penugasan kasus.
     */
    public function edit(PenangananKasus $penangananKasus): View
    {
        $this->authorize('update', $penangananKasus);

        $penangananKasus->load(['laporanKasus', 'petugas']);

        // Sertakan laporan saat ini plus laporan lain yang belum memiliki penugasan
        $laporanList = LaporanKasus::query()
            ->where('laporan_id', $penangananKasus->laporan_id)
            ->orWhereDoesntHave('penangananKasus')
            ->with('statusKasus')
            ->orderBy('created_at', 'desc')
            ->get();

        $daftarPetugas = $this->getPetugasList();

        return view('penanganan-kasus.edit', [
            'title' => 'Ubah Penugasan Kasus - RuangAman',
            'penanganan' => $penangananKasus,
            'laporanList' => $laporanList,
            'daftarPetugas' => $daftarPetugas,
        ]);
    }

    /**
     * Memperbarui data penugasan kasus.
     */
    public function update(UpdatePenangananKasusRequest $request, PenangananKasus $penangananKasus): RedirectResponse
    {
        $this->authorize('update', $penangananKasus);

        $penangananKasus->update($request->validated());

        return redirect()
            ->route('admin.penanganan-kasus.show', $penangananKasus)
            ->with('success', 'Penugasan kasus berhasil diperbarui.');
    }

    /**
     * Menghapus atau membatalkan penugasan kasus.
     */
    public function destroy(PenangananKasus $penangananKasus): RedirectResponse
    {
        $this->authorize('delete', $penangananKasus);

        $kodeLaporan = $penangananKasus->laporanKasus?->kode_laporan ?? "#{$penangananKasus->penanganan_id}";
        $penangananKasus->delete();

        return redirect()
            ->route('admin.penanganan-kasus.index')
            ->with('success', "Penugasan untuk laporan {$kodeLaporan} berhasil dibatalkan/dihapus.");
    }

    /**
     * Mengambil daftar user yang berhak menjadi petugas/konselor penanganan.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    private function getPetugasList()
    {
        return User::query()
            ->whereHas('role', function ($query) {
                $query->whereIn('nama_role', ['Admin Kasus', 'Konselor']);
            })
            ->where('status', 'aktif')
            ->orderBy('nama_tampilan', 'asc')
            ->get();
    }
}

