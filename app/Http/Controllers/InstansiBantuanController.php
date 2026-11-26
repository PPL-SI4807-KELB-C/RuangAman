<?php

namespace App\Http\Controllers;

use App\Models\InstansiBantuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstansiBantuanController extends Controller
{
    /**
     * Display a listing of the institutions for admin.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = InstansiBantuan::query();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_instansi', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if (! empty($status) && in_array($status, ['aktif', 'nonaktif'], true)) {
            $query->where('status', $status);
        }

        $totalCount = InstansiBantuan::count();
        $aktifCount = InstansiBantuan::where('status', 'aktif')->count();
        $nonaktifCount = InstansiBantuan::where('status', 'nonaktif')->count();

        $instansis = $query->orderBy('created_at', 'desc')->get();

        return view('instansi.index', [
            'title' => 'Manajemen instansi bantuan - RuangAman',
            'instansis' => $instansis,
            'totalCount' => $totalCount,
            'aktifCount' => $aktifCount,
            'nonaktifCount' => $nonaktifCount,
            'search' => $search,
            'statusFilter' => $status,
        ]);
    }

    /**
     * Display public directory of active institutions.
     */
    public function publicDirectory(Request $request): View
    {
        $search = $request->query('search');

        $query = InstansiBantuan::where('status', 'aktif')->with(['layananBantuan', 'kontakBantuan']);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_instansi', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $instansis = $query->orderBy('nama_instansi', 'asc')->get();
        $totalAktif = $instansis->count();

        return view('instansi.public', [
            'title' => 'Temukan pilihan bantuan Anda - RuangAman',
            'instansis' => $instansis,
            'totalAktif' => $totalAktif,
            'search' => $search,
        ]);
    }

    /**
     * Display public detail of a single active institution (Screen 7 Figma).
     */
    public function publicShow(InstansiBantuan $instansi): View
    {
        abort_if($instansi->status !== 'aktif', 404);

        $instansi->load(['layananBantuan', 'kontakBantuan']);

        return view('instansi.public-show', [
            'title' => $instansi->nama_instansi . ' - Instansi bantuan - RuangAman',
            'instansi' => $instansi,
        ]);
    }

    /**
     * Show the form for creating a new institution.
     */
    public function create(): View
    {
        return view('instansi.create', [
            'title' => 'Tambah instansi bantuan - RuangAman',
        ]);
    }

    /**
     * Store a newly created institution in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        InstansiBantuan::create($this->validatedData($request));

        return redirect()
            ->route('instansi.index')
            ->with('success', 'Instansi bantuan berhasil ditambahkan.');
    }

    /**
     * Display the specified institution for admin.
     */
    public function show(InstansiBantuan $instansi): View
    {
        $instansi->load(['layananBantuan', 'kontakBantuan']);

        return view('instansi.show', [
            'title' => $instansi->nama_instansi . ' - Detail instansi bantuan',
            'instansi' => $instansi,
        ]);
    }

    /**
     * Show the form for editing the specified institution.
     */
    public function edit(InstansiBantuan $instansi): View
    {
        return view('instansi.edit', [
            'title' => 'Ubah instansi bantuan - RuangAman',
            'instansi' => $instansi,
        ]);
    }

    /**
     * Update the specified institution in storage.
     */
    public function update(Request $request, InstansiBantuan $instansi): RedirectResponse
    {
        $instansi->update($this->validatedData($request));

        return redirect()
            ->route('instansi.index')
            ->with('success', 'Instansi bantuan berhasil diperbarui.');
    }

    /**
     * Remove the specified institution from storage.
     */
    public function destroy(InstansiBantuan $instansi): RedirectResponse
    {
        $instansi->delete();

        return redirect()
            ->route('instansi.index')
            ->with('success', 'Instansi bantuan berhasil dihapus.');
    }

    /**
     * Validate institution create/update payload.
     *
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama_instansi' => 'required|string|max:200',
            'alamat' => 'required|string',
            'deskripsi' => 'required|string',
            'website' => 'nullable|url|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama_instansi.required' => 'Nama instansi harus diisi.',
            'nama_instansi.max' => 'Nama instansi maksimal 200 karakter.',
            'alamat.required' => 'Alamat instansi harus diisi.',
            'deskripsi.required' => 'Deskripsi instansi harus diisi.',
            'website.url' => 'Format website tidak valid.',
            'status.required' => 'Status instansi harus dipilih.',
            'status.in' => 'Status instansi harus aktif atau nonaktif.',
        ]);
    }
}
