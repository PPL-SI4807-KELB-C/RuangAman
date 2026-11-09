<?php

namespace App\Http\Controllers;

use App\Models\InstansiBantuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstansiBantuanController extends Controller
{
    /**
     * Display a listing of the institutions.
     */
    public function index(): View
    {
        $instansis = InstansiBantuan::orderBy('created_at', 'desc')->get();

        return view('instansi.index', [
            'title' => 'Manajemen Instansi Bantuan - RuangAman',
            'instansis' => $instansis,
        ]);
    }

    /**
     * Show the form for creating a new institution.
     */
    public function create(): View
    {
        return view('instansi.create', [
            'title' => 'Tambah Instansi Bantuan - RuangAman',
        ]);
    }

    /**
     * Store a newly created institution in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_instansi' => 'required|string|max:200',
            'alamat' => 'required|string',
            'deskripsi' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama_instansi.required' => 'Nama instansi harus diisi.',
            'nama_instansi.max' => 'Nama instansi maksimal 200 karakter.',
            'alamat.required' => 'Alamat instansi harus diisi.',
            'website.url' => 'Format website tidak valid.',
            'status.required' => 'Status instansi harus dipilih.',
            'status.in' => 'Status instansi harus aktif atau nonaktif.',
        ]);

        InstansiBantuan::create($validated);

        return redirect()
            ->route('instansi.index')
            ->with('success', 'Instansi bantuan berhasil ditambahkan.');
    }
}
