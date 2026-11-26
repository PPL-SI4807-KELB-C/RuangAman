<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArtikelRequest;
use App\Http\Requests\UpdateArtikelRequest;
use App\Models\Artikel;
use App\Models\KategoriArtikel;
use App\Services\ArtikelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArtikelController extends Controller
{
    public function __construct(
        private readonly ArtikelService $artikelService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Artikel::class);

        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $kategoriId = $request->integer('kategori_id');

        $artikelList = Artikel::query()
            ->with(['kategori:kategori_id,nama_kategori', 'penulis:user_id,nama_tampilan'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhere('isi', 'like', "%{$search}%");
                });
            })
            ->when(array_key_exists($status, Artikel::STATUS_OPTIONS), function ($query) use ($status) {
                $query->where('status_publikasi', $status);
            })
            ->when($kategoriId > 0, function ($query) use ($kategoriId) {
                $query->where('kategori_id', $kategoriId);
            })
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.artikel.index', [
            'title' => 'Manajemen Artikel Edukasi - RuangAman',
            'artikelList' => $artikelList,
            'search' => $search,
            'selectedStatus' => $status,
            'selectedKategori' => $kategoriId,
            'statusOptions' => Artikel::STATUS_OPTIONS,
            'kategoriOptions' => $this->kategoriOptions(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Artikel::class);

        return view('admin.artikel.create', [
            'title' => 'Tambah Artikel Edukasi - RuangAman',
            'artikel' => new Artikel(['status_publikasi' => Artikel::STATUS_DRAFT]),
            'statusOptions' => Artikel::STATUS_OPTIONS,
            'kategoriOptions' => $this->kategoriOptions(),
        ]);
    }

    public function store(StoreArtikelRequest $request): RedirectResponse
    {
        $this->authorize('create', Artikel::class);

        $artikel = $this->artikelService->buat($request->validated(), $request->user());

        return redirect()
            ->route('admin.artikel.show', $artikel)
            ->with('success', "Artikel \"{$artikel->judul}\" berhasil ditambahkan.");
    }

    public function show(Artikel $artikel): View
    {
        $this->authorize('view', $artikel);

        $artikel->load(['kategori:kategori_id,nama_kategori,deskripsi', 'penulis:user_id,nama_tampilan']);

        return view('admin.artikel.show', [
            'title' => "Detail Artikel: {$artikel->judul} - RuangAman",
            'artikel' => $artikel,
        ]);
    }

    public function edit(Artikel $artikel): View
    {
        $this->authorize('update', $artikel);

        $artikel->load('penulis:user_id,nama_tampilan');

        return view('admin.artikel.edit', [
            'title' => "Ubah Artikel: {$artikel->judul} - RuangAman",
            'artikel' => $artikel,
            'statusOptions' => Artikel::STATUS_OPTIONS,
            'kategoriOptions' => $this->kategoriOptions(),
        ]);
    }

    public function update(UpdateArtikelRequest $request, Artikel $artikel): RedirectResponse
    {
        $this->authorize('update', $artikel);

        $artikel = $this->artikelService->perbarui($artikel, $request->validated());

        return redirect()
            ->route('admin.artikel.show', $artikel)
            ->with('success', "Artikel \"{$artikel->judul}\" berhasil diperbarui.");
    }

    public function destroy(Artikel $artikel): RedirectResponse
    {
        $this->authorize('delete', $artikel);

        $judul = $artikel->judul;
        $artikel->delete();

        return redirect()
            ->route('admin.artikel.index')
            ->with('success', "Artikel \"{$judul}\" berhasil dihapus.");
    }

    /**
     * Kategori adalah data referensi hanya-baca milik PBI-11.
     *
     * @return array<int, string>
     */
    private function kategoriOptions(): array
    {
        return KategoriArtikel::query()
            ->orderBy('nama_kategori')
            ->pluck('nama_kategori', 'kategori_id')
            ->all();
    }
}
