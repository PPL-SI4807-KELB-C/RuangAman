<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKonselorRequest;
use App\Http\Requests\UpdateKonselorRequest;
use App\Models\Konselor;
use App\Services\KonselorService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KonselorController extends Controller
{
    public function __construct(
        private readonly KonselorService $konselorService
    ) {}

    /**
     * Menampilkan daftar profil konselor/psikolog beserta pencarian, filter status, dan pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Konselor::class);

        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));

        $konselorList = Konselor::query()
            ->select('konselor.*')
            ->with('user:user_id,nama_tampilan,status')
            ->selectSub(
                DB::table('jadwal_konseling')
                    ->selectRaw('count(*)')
                    ->whereColumn('jadwal_konseling.konselor_id', 'konselor.konselor_id'),
                'jadwal_count'
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('spesialisasi', 'like', "%{$search}%");
                });
            })
            ->when($status !== '' && array_key_exists($status, Konselor::STATUS_OPTIONS), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.konselor.index', [
            'title' => 'Manajemen Data Konselor - RuangAman',
            'konselorList' => $konselorList,
            'search' => $search,
            'selectedStatus' => $status,
            'statusOptions' => Konselor::STATUS_OPTIONS,
        ]);
    }

    /**
     * Menampilkan form pendaftaran profil konselor untuk akun ber-role Konselor.
     */
    public function create(): View
    {
        $this->authorize('create', Konselor::class);

        return view('admin.konselor.create', [
            'title' => 'Tambah Data Konselor - RuangAman',
            'konselor' => new Konselor(['status' => Konselor::STATUS_AKTIF]),
            'calonPenggunaOptions' => $this->calonPenggunaOptions(),
            'statusOptions' => Konselor::STATUS_OPTIONS,
        ]);
    }

    /**
     * Menyimpan profil konselor baru.
     */
    public function store(StoreKonselorRequest $request): RedirectResponse
    {
        $this->authorize('create', Konselor::class);

        try {
            $konselor = $this->konselorService->buat($request->validated());
        } catch (DomainException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.konselor.show', $konselor)
            ->with('success', "Profil konselor \"{$konselor->nama}\" berhasil ditambahkan.");
    }

    /**
     * Menampilkan detail profil konselor beserta informasi akun dan jumlah slot jadwal.
     */
    public function show(Konselor $konselor): View
    {
        $this->authorize('view', $konselor);

        $konselor->load('user:user_id,nama_tampilan,status');

        return view('admin.konselor.show', [
            'title' => "Detail Konselor: {$konselor->nama} - RuangAman",
            'konselor' => $konselor,
            'jumlahJadwal' => $this->konselorService->jumlahJadwal($konselor),
        ]);
    }

    /**
     * Menampilkan form edit profil konselor.
     */
    public function edit(Konselor $konselor): View
    {
        $this->authorize('update', $konselor);

        return view('admin.konselor.edit', [
            'title' => "Ubah Data Konselor: {$konselor->nama} - RuangAman",
            'konselor' => $konselor->load('user:user_id,nama_tampilan,status'),
            'statusOptions' => Konselor::STATUS_OPTIONS,
            'jumlahJadwal' => $this->konselorService->jumlahJadwal($konselor),
        ]);
    }

    /**
     * Memperbarui biodata dan status ketersediaan konselor.
     */
    public function update(UpdateKonselorRequest $request, Konselor $konselor): RedirectResponse
    {
        $this->authorize('update', $konselor);

        $konselor = $this->konselorService->perbarui($konselor, $request->validated());

        return redirect()
            ->route('admin.konselor.show', $konselor)
            ->with('success', "Profil konselor \"{$konselor->nama}\" berhasil diperbarui.");
    }

    /**
     * Menghapus profil konselor yang belum memiliki slot jadwal konseling.
     */
    public function destroy(Konselor $konselor): RedirectResponse
    {
        $this->authorize('delete', $konselor);

        $nama = $konselor->nama;

        try {
            $this->konselorService->hapusPermanen($konselor);

            return redirect()
                ->route('admin.konselor.index')
                ->with('success', "Profil konselor \"{$nama}\" berhasil dihapus permanen.");
        } catch (DomainException $e) {
            return redirect()
                ->route('admin.konselor.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Opsi akun Konselor yang belum memiliki profil, tanpa menampilkan email.
     *
     * @return array<int, string>
     */
    private function calonPenggunaOptions(): array
    {
        return $this->konselorService
            ->calonPengguna()
            ->mapWithKeys(fn ($user) => [
                $user->user_id => "{$user->nama_tampilan} (akun #{$user->user_id})",
            ])
            ->all();
    }
}
