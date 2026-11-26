<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKategoriArtikelRequest;
use App\Http\Requests\UpdateKategoriArtikelRequest;
use App\Models\KategoriArtikel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PBI-11: Manajemen Kategori Artikel.
 *
 * Tabel milik PBI ini: kategori_artikel. Artikel (PBI-10) hanya membaca kategori
 * dan memakai kategori bawaan "Umum" bila tidak memilih, sehingga kedua PBI dapat
 * dikembangkan dan diuji mandiri.
 */
class KategoriArtikelController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', KategoriArtikel::class);

        $search = trim((string) $request->input('search', ''));

        $kategoriList = KategoriArtikel::query()
            ->withCount('artikel')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nama_kategori', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('admin.kategori-artikel.index', [
            'title' => 'Manajemen Kategori Artikel - RuangAman',
            'kategoriList' => $kategoriList,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', KategoriArtikel::class);

        return view('admin.kategori-artikel.create', [
            'title' => 'Tambah Kategori Artikel - RuangAman',
            'kategori' => new KategoriArtikel,
        ]);
    }

    public function store(StoreKategoriArtikelRequest $request): RedirectResponse
    {
        $this->authorize('create', KategoriArtikel::class);

        $kategori = KategoriArtikel::query()->create($request->validated());

        return redirect()
            ->route('admin.kategori-artikel.index')
            ->with('success', "Kategori \"{$kategori->nama_kategori}\" berhasil ditambahkan.");
    }

    public function edit(KategoriArtikel $kategori): View
    {
        $this->authorize('update', $kategori);

        return view('admin.kategori-artikel.edit', [
            'title' => "Ubah Kategori: {$kategori->nama_kategori} - RuangAman",
            'kategori' => $kategori,
        ]);
    }

    public function update(UpdateKategoriArtikelRequest $request, KategoriArtikel $kategori): RedirectResponse
    {
        $this->authorize('update', $kategori);

        $kategori->update($request->validated());

        return redirect()
            ->route('admin.kategori-artikel.index')
            ->with('success', "Kategori \"{$kategori->nama_kategori}\" berhasil diperbarui.");
    }

    /**
     * Kategori bawaan dan kategori yang masih dipakai artikel tidak boleh dihapus.
     */
    public function destroy(KategoriArtikel $kategori): RedirectResponse
    {
        $this->authorize('delete', $kategori);

        if ($kategori->isDefault()) {
            return redirect()
                ->route('admin.kategori-artikel.index')
                ->with('error', "Kategori bawaan \"{$kategori->nama_kategori}\" tidak dapat dihapus.");
        }

        $jumlahArtikel = $kategori->artikel()->count();

        if ($jumlahArtikel > 0) {
            return redirect()
                ->route('admin.kategori-artikel.index')
                ->with('error', "Kategori \"{$kategori->nama_kategori}\" masih dipakai {$jumlahArtikel} artikel. Pindahkan artikelnya ke kategori lain terlebih dahulu.");
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();

        return redirect()
            ->route('admin.kategori-artikel.index')
            ->with('success', "Kategori \"{$nama}\" berhasil dihapus.");
    }
}
