<?php

namespace App\Http\Controllers;

use App\Models\InstansiBantuan;
use App\Models\LayananBantuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LayananBantuanController extends Controller
{
    /**
     * Display a listing of the services for the given institution.
     */
    public function index(InstansiBantuan $instansi): View
    {
        $layanans = $instansi->layananBantuan()->orderBy('created_at', 'desc')->get();

        return view('layanan.index', [
            'title'   => 'Layanan Bantuan — ' . $instansi->nama_instansi,
            'instansi' => $instansi,
            'layanans' => $layanans,
        ]);
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(InstansiBantuan $instansi): View
    {
        return view('layanan.create', [
            'title'   => 'Tambah Layanan — ' . $instansi->nama_instansi,
            'instansi' => $instansi,
        ]);
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request, InstansiBantuan $instansi): RedirectResponse
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:150',
            'deskripsi'    => 'nullable|string',
        ], [
            'nama_layanan.required' => 'Nama layanan harus diisi.',
            'nama_layanan.max'      => 'Nama layanan maksimal 150 karakter.',
        ]);

        $instansi->layananBantuan()->create($validated);

        return redirect()
            ->route('instansi.layanan.index', $instansi)
            ->with('success', 'Layanan bantuan berhasil ditambahkan.');
    }

    /**
     * Display the specified service.
     */
    public function show(InstansiBantuan $instansi, LayananBantuan $layanan): View
    {
        abort_if($layanan->instansi_id !== $instansi->instansi_id, 404);

        return view('layanan.show', [
            'title'    => $layanan->nama_layanan . ' — Detail Layanan',
            'instansi' => $instansi,
            'layanan'  => $layanan,
        ]);
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(InstansiBantuan $instansi, LayananBantuan $layanan): View
    {
        abort_if($layanan->instansi_id !== $instansi->instansi_id, 404);

        return view('layanan.edit', [
            'title'    => 'Ubah Layanan — ' . $instansi->nama_instansi,
            'instansi' => $instansi,
            'layanan'  => $layanan,
        ]);
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, InstansiBantuan $instansi, LayananBantuan $layanan): RedirectResponse
    {
        abort_if($layanan->instansi_id !== $instansi->instansi_id, 404);

        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:150',
            'deskripsi'    => 'nullable|string',
        ], [
            'nama_layanan.required' => 'Nama layanan harus diisi.',
            'nama_layanan.max'      => 'Nama layanan maksimal 150 karakter.',
        ]);

        $layanan->update($validated);

        return redirect()
            ->route('instansi.layanan.index', $instansi)
            ->with('success', 'Layanan bantuan berhasil diperbarui.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(InstansiBantuan $instansi, LayananBantuan $layanan): RedirectResponse
    {
        abort_if($layanan->instansi_id !== $instansi->instansi_id, 404);

        $layanan->delete();

        return redirect()
            ->route('instansi.layanan.index', $instansi)
            ->with('success', 'Layanan bantuan berhasil dihapus.');
    }
}
