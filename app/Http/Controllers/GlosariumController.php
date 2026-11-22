<?php

namespace App\Http\Controllers;

use App\Models\GlosariumKbg;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GlosariumController extends Controller
{
    /**
     * Daftar glosarium publik yang dikelompokkan berdasarkan huruf awal istilah
     * dan dapat dicari lewat kolom pencarian.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));

        $glosariumList = GlosariumKbg::query()
            ->published()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('istilah', 'like', "%{$search}%")
                        ->orWhere('definisi', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%");
                });
            })
            ->orderBy('istilah')
            ->paginate(30)
            ->withQueryString();

        $grupHuruf = $glosariumList->getCollection()
            ->groupBy(fn (GlosariumKbg $item) => $item->inisialHuruf())
            ->sortKeys();

        return view('glosarium.index', [
            'title' => 'Glosarium Istilah Kekerasan Berbasis Gender - RuangAman',
            'glosariumList' => $glosariumList,
            'grupHuruf' => $grupHuruf,
            'search' => $search,
        ]);
    }
}
