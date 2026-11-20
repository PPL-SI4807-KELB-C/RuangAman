<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHalamanInformasiRequest;
use App\Http\Requests\UpdateHalamanInformasiRequest;
use App\Models\HalamanInformasi;
use App\Services\HalamanInformasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HalamanInformasiController extends Controller
{
    public function __construct(
        private readonly HalamanInformasiService $halamanService
    ) {}

    /**
     * Menampilkan daftar halaman informasi statis beserta pencarian, filter status, dan pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', HalamanInformasi::class);

        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));

        $halamanList = HalamanInformasi::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($status !== '' && array_key_exists($status, HalamanInformasi::STATUS_OPTIONS), function ($query) use ($status) {
                $query->where('status_publikasi', $status);
            })
            ->orderBy('judul')
            ->paginate(10)
            ->withQueryString();

        return view('admin.halaman-informasi.index', [
            'title' => 'Manajemen Halaman Informasi - RuangAman',
            'halamanList' => $halamanList,
            'search' => $search,
            'selectedStatus' => $status,
            'statusOptions' => HalamanInformasi::STATUS_OPTIONS,
        ]);
    }

    /**
     * Menampilkan form tambah halaman informasi baru.
     */
    public function create(): View
    {
        $this->authorize('create', HalamanInformasi::class);

        return view('admin.halaman-informasi.create', [
            'title' => 'Tambah Halaman Informasi - RuangAman',
            'halaman' => new HalamanInformasi([
                'status_publikasi' => HalamanInformasi::STATUS_DRAFT,
            ]),
            'statusOptions' => HalamanInformasi::STATUS_OPTIONS,
        ]);
    }

    /**
     * Menyimpan halaman informasi baru beserta slug yang dibangkitkan otomatis.
     */
    public function store(StoreHalamanInformasiRequest $request): RedirectResponse
    {
        $this->authorize('create', HalamanInformasi::class);

        $halaman = $this->halamanService->buat($request->validated());

        return redirect()
            ->route('admin.halaman-informasi.show', $halaman)
            ->with('success', "Halaman \"{$halaman->judul}\" berhasil ditambahkan.");
    }

    /**
     * Menampilkan detail halaman informasi beserta tautan publiknya.
     */
    public function show(HalamanInformasi $halamanInformasi): View
    {
        $this->authorize('view', $halamanInformasi);

        return view('admin.halaman-informasi.show', [
            'title' => "Detail Halaman: {$halamanInformasi->judul} - RuangAman",
            'halaman' => $halamanInformasi,
            'tautanPublik' => route('halaman.show', $halamanInformasi->slug),
        ]);
    }

    /**
     * Menampilkan form ubah halaman informasi.
     */
    public function edit(HalamanInformasi $halamanInformasi): View
    {
        $this->authorize('update', $halamanInformasi);

        return view('admin.halaman-informasi.edit', [
            'title' => "Ubah Halaman: {$halamanInformasi->judul} - RuangAman",
            'halaman' => $halamanInformasi,
            'statusOptions' => HalamanInformasi::STATUS_OPTIONS,
        ]);
    }

    /**
     * Memperbarui halaman informasi. Slug hanya berubah bila judul diubah.
     */
    public function update(UpdateHalamanInformasiRequest $request, HalamanInformasi $halamanInformasi): RedirectResponse
    {
        $this->authorize('update', $halamanInformasi);

        $updated = $this->halamanService->perbarui($halamanInformasi, $request->validated());

        return redirect()
            ->route('admin.halaman-informasi.show', $updated)
            ->with('success', "Halaman \"{$updated->judul}\" berhasil diperbarui.");
    }

    /**
     * Menghapus permanen halaman informasi.
     */
    public function destroy(HalamanInformasi $halamanInformasi): RedirectResponse
    {
        $this->authorize('delete', $halamanInformasi);

        $judul = $halamanInformasi->judul;
        $this->halamanService->hapusPermanen($halamanInformasi);

        return redirect()
            ->route('admin.halaman-informasi.index')
            ->with('success', "Halaman \"{$judul}\" berhasil dihapus permanen.");
    }
}
