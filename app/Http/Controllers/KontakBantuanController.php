<?php

namespace App\Http\Controllers;

use App\Models\InstansiBantuan;
use App\Models\KontakBantuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KontakBantuanController extends Controller
{
    /**
     * Valid contact types (mirrors the DB enum).
     */
    private const JENIS_KONTAK = ['telepon', 'whatsapp', 'email', 'hotline'];

    /**
     * Display a listing of the contacts for the given institution.
     */
    public function index(InstansiBantuan $instansi): View
    {
        $kontaks = $instansi->kontakBantuan()->orderBy('created_at', 'desc')->get();

        return view('kontak.index', [
            'title'    => 'Kontak Bantuan — ' . $instansi->nama_instansi,
            'instansi' => $instansi,
            'kontaks'  => $kontaks,
        ]);
    }

    /**
     * Show the form for creating a new contact.
     */
    public function create(InstansiBantuan $instansi): View
    {
        return view('kontak.create', [
            'title'       => 'Tambah Kontak — ' . $instansi->nama_instansi,
            'instansi'    => $instansi,
            'jenisKontak' => self::JENIS_KONTAK,
        ]);
    }

    /**
     * Store a newly created contact in storage.
     */
    public function store(Request $request, InstansiBantuan $instansi): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_kontak' => 'required|in:' . implode(',', self::JENIS_KONTAK),
            'nilai_kontak' => 'required|string|max:150',
            'keterangan'   => 'nullable|string|max:200',
        ], [
            'jenis_kontak.required' => 'Jenis kontak harus dipilih.',
            'jenis_kontak.in'       => 'Jenis kontak tidak valid.',
            'nilai_kontak.required' => 'Nilai kontak harus diisi.',
            'nilai_kontak.max'      => 'Nilai kontak maksimal 150 karakter.',
            'keterangan.max'        => 'Keterangan maksimal 200 karakter.',
        ]);

        $instansi->kontakBantuan()->create($validated);

        return redirect()
            ->route('instansi.kontak.index', $instansi)
            ->with('success', 'Kontak bantuan berhasil ditambahkan.');
    }

    /**
     * Display the specified contact.
     */
    public function show(InstansiBantuan $instansi, KontakBantuan $kontak): View
    {
        abort_if($kontak->instansi_id !== $instansi->instansi_id, 404);

        return view('kontak.show', [
            'title'    => 'Detail Kontak — ' . $instansi->nama_instansi,
            'instansi' => $instansi,
            'kontak'   => $kontak,
        ]);
    }

    /**
     * Show the form for editing the specified contact.
     */
    public function edit(InstansiBantuan $instansi, KontakBantuan $kontak): View
    {
        abort_if($kontak->instansi_id !== $instansi->instansi_id, 404);

        return view('kontak.edit', [
            'title'       => 'Ubah Kontak — ' . $instansi->nama_instansi,
            'instansi'    => $instansi,
            'kontak'      => $kontak,
            'jenisKontak' => self::JENIS_KONTAK,
        ]);
    }

    /**
     * Update the specified contact in storage.
     */
    public function update(Request $request, InstansiBantuan $instansi, KontakBantuan $kontak): RedirectResponse
    {
        abort_if($kontak->instansi_id !== $instansi->instansi_id, 404);

        $validated = $request->validate([
            'jenis_kontak' => 'required|in:' . implode(',', self::JENIS_KONTAK),
            'nilai_kontak' => 'required|string|max:150',
            'keterangan'   => 'nullable|string|max:200',
        ], [
            'jenis_kontak.required' => 'Jenis kontak harus dipilih.',
            'jenis_kontak.in'       => 'Jenis kontak tidak valid.',
            'nilai_kontak.required' => 'Nilai kontak harus diisi.',
            'nilai_kontak.max'      => 'Nilai kontak maksimal 150 karakter.',
            'keterangan.max'        => 'Keterangan maksimal 200 karakter.',
        ]);

        $kontak->update($validated);

        return redirect()
            ->route('instansi.kontak.index', $instansi)
            ->with('success', 'Kontak bantuan berhasil diperbarui.');
    }

    /**
     * Remove the specified contact from storage.
     */
    public function destroy(InstansiBantuan $instansi, KontakBantuan $kontak): RedirectResponse
    {
        abort_if($kontak->instansi_id !== $instansi->instansi_id, 404);

        $kontak->delete();

        return redirect()
            ->route('instansi.kontak.index', $instansi)
            ->with('success', 'Kontak bantuan berhasil dihapus.');
    }
}
