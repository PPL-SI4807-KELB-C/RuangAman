<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArtikelController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $kategoriId = $request->integer('kategori_id');

        $artikelList = Artikel::query()
            ->terpublikasi()
            ->with(['kategori:kategori_id,nama_kategori', 'penulis:user_id,nama_tampilan'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhere('isi', 'like', "%{$search}%");
                });
            })
            ->when($kategoriId > 0, function ($query) use ($kategoriId) {
                $query->where('kategori_id', $kategoriId);
            })
            ->latest('updated_at')
            ->paginate(9)
            ->withQueryString();

        return view('artikel.index', [
            'title' => 'Artikel Edukasi KBG - RuangAman',
            'artikelList' => $artikelList,
            'search' => $search,
            'selectedKategori' => $kategoriId,
            'kategoriOptions' => KategoriArtikel::query()
                ->whereHas('artikel', fn ($query) => $query->terpublikasi())
                ->orderBy('nama_kategori')
                ->pluck('nama_kategori', 'kategori_id'),
        ]);
    }

    public function show(Artikel $artikel): View
    {
        $this->authorize('view', $artikel);

        $artikel->load(['kategori:kategori_id,nama_kategori', 'penulis:user_id,nama_tampilan']);

        $artikelTerkait = Artikel::query()
            ->terpublikasi()
            ->where('kategori_id', $artikel->kategori_id)
            ->where('artikel_id', '!=', $artikel->artikel_id)
            ->latest('updated_at')
            ->limit(3)
            ->get();

        return view('artikel.show', [
            'title' => "{$artikel->judul} - Artikel Edukasi RuangAman",
            'artikel' => $artikel,
            'artikelTerkait' => $artikelTerkait,
        ]);
    }
}
