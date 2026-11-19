<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfilPelaporRequest;
use App\Models\ProfilPelapor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfilPelaporController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profil = ProfilPelapor::firstOrCreate(
            ['user_id' => $user->user_id],
            [
                'alias' => $user->nama_tampilan,
                'kontak_opsional' => null,
                'preferensi_anonim' => false,
            ]
        );

        return view('pelapor.profil.edit', compact('user', 'profil'));
    }

    public function update(UpdateProfilPelaporRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated, $request) {
            ProfilPelapor::updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'alias' => $validated['alias'],
                    'kontak_opsional' => $validated['kontak_opsional'] ?? null,
                    'preferensi_anonim' => $request->boolean('preferensi_anonim'),
                ]
            );

            $user->update([
                'nama_tampilan' => $validated['alias'],
            ]);
        });

        return redirect()
            ->route('profil-pelapor.edit')
            ->with('success', 'Profil alias dan preferensi mode anonim berhasil disimpan.');
    }
}