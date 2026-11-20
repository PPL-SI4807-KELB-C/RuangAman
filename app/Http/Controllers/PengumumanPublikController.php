<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengumumanPublikController extends Controller
{
    /**
     * Daftar pengumuman yang sedang tayang untuk publik.
     */
    public function index(Request $request): View
    {
        $pengumumanList = Pengumuman::query()
            ->tayang()
            ->orderByDesc('mulai_tayang')
            ->paginate(10)
            ->withQueryString();

        return view('pengumuman.index', [
            'title' => 'Pengumuman - RuangAman',
            'pengumumanList' => $pengumumanList,
        ]);
    }

    /**
     * Detail satu pengumuman. Di luar periode tayang, hanya Admin Kasus yang dapat membukanya.
     */
    public function show(Pengumuman $pengumuman): View
    {
        $this->authorize('view', $pengumuman);

        return view('pengumuman.show', [
            'title' => "{$pengumuman->judul} - RuangAman",
            'pengumuman' => $pengumuman,
        ]);
    }
}
