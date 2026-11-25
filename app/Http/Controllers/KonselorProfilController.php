<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfilKonselorRequest;
use App\Models\Konselor;
use App\Services\KonselorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KonselorProfilController extends Controller
{
    public function __construct(
        private readonly KonselorService $konselorService
    ) {}

    /**
     * Menampilkan form profil konselor milik pengguna yang sedang login.
     */
    public function edit(Request $request): View
    {
        $konselor = $this->profilMilikUser($request);

        $this->authorize('view', $konselor);

        return view('konselor.profil', [
            'title' => 'Profil Konselor Saya - RuangAman',
            'konselor' => $konselor->load('user:user_id,nama_tampilan,status'),
            'jumlahJadwal' => $this->konselorService->jumlahJadwal($konselor),
        ]);
    }

    /**
     * Memperbarui biodata profesional konselor milik pengguna yang sedang login.
     * Status ketersediaan hanya dapat diubah oleh Admin Kasus.
     */
    public function update(UpdateProfilKonselorRequest $request): RedirectResponse
    {
        $konselor = $this->profilMilikUser($request);

        $this->authorize('updateProfil', $konselor);

        $konselor = $this->konselorService->perbaruiProfil($konselor, $request->validated());

        return redirect()
            ->route('konselor.profil.edit')
            ->with('success', "Profil konselor \"{$konselor->nama}\" berhasil diperbarui.");
    }

    /**
     * Mengambil profil konselor milik user login, atau 404 bila belum tersedia.
     */
    private function profilMilikUser(Request $request): Konselor
    {
        /** @var Konselor|null $konselor */
        $konselor = $request->user()->konselor;

        abort_if(
            $konselor === null,
            404,
            'Profil konselor belum tersedia. Hubungi Admin Kasus untuk melengkapi data profil Anda.'
        );

        return $konselor;
    }
}
