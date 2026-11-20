<?php

namespace App\Http\Controllers;

use App\Models\HalamanInformasi;
use Illuminate\View\View;

class HalamanInformasiPublikController extends Controller
{
    /**
     * Merender halaman informasi statis publik berdasarkan slug.
     * Halaman draft hanya dapat dilihat oleh Admin Kasus.
     */
    public function show(string $slug): View
    {
        /** @var HalamanInformasi $halaman */
        $halaman = HalamanInformasi::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $this->authorize('view', $halaman);

        return view('halaman.show', [
            'title' => "{$halaman->judul} - RuangAman",
            'halaman' => $halaman,
        ]);
    }
}
