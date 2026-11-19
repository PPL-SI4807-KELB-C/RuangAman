<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStatusKasusRequest;
use App\Http\Requests\UpdateStatusKasusRequest;
use App\Models\StatusKasus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusKasusController extends Controller
{
    /**
     * Menampilkan daftar master status kasus dengan pencarian dan pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', StatusKasus::class);

        $search = trim((string) $request->input('search', ''));

        $statusKasus = StatusKasus::query()
            ->withCount('laporanKasus')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nama_status', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->orderBy('status_id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('status-kasus.index', [
            'title' => 'Manajemen Status Kasus - RuangAman',
            'statusKasus' => $statusKasus,
            'search' => $search,
        ]);
    }

    /**
     * Menampilkan formulir tambah status kasus baru.
     */
    public function create(): View
    {
        $this->authorize('create', StatusKasus::class);

        return view('status-kasus.create', [
            'title' => 'Tambah Status Kasus - RuangAman',
            'statusKasus' => new StatusKasus(),
        ]);
    }

    /**
     * Menyimpan data status kasus baru ke database.
     */
    public function store(StoreStatusKasusRequest $request): RedirectResponse
    {
        $this->authorize('create', StatusKasus::class);

        $statusKasus = StatusKasus::create($request->validated());

        return redirect()
            ->route('admin.status-kasus.index')
            ->with('success', "Status kasus '{$statusKasus->nama_status}' berhasil ditambahkan.");
    }

    /**
     * Menampilkan formulir edit status kasus.
     */
    public function edit(StatusKasus $statusKasus): View
    {
        $this->authorize('update', $statusKasus);

        return view('status-kasus.edit', [
            'title' => "Ubah Status Kasus: {$statusKasus->nama_status} - RuangAman",
            'statusKasus' => $statusKasus,
        ]);
    }

    /**
     * Memperbarui data status kasus.
     */
    public function update(UpdateStatusKasusRequest $request, StatusKasus $statusKasus): RedirectResponse
    {
        $this->authorize('update', $statusKasus);

        $statusKasus->update($request->validated());

        return redirect()
            ->route('admin.status-kasus.index')
            ->with('success', "Status kasus '{$statusKasus->nama_status}' berhasil diperbarui.");
    }

    /**
     * Menghapus status kasus jika tidak sedang digunakan oleh laporan kasus.
     */
    public function destroy(StatusKasus $statusKasus): RedirectResponse
    {
        $this->authorize('delete', $statusKasus);

        $laporanCount = $statusKasus->laporanKasus()->count();

        if ($laporanCount > 0) {
            return redirect()
                ->route('admin.status-kasus.index')
                ->with('error', "Status '{$statusKasus->nama_status}' tidak dapat dihapus karena sedang digunakan oleh {$laporanCount} laporan kasus.");
        }

        $nama = $statusKasus->nama_status;
        $statusKasus->delete();

        return redirect()
            ->route('admin.status-kasus.index')
            ->with('success', "Status kasus '{$nama}' berhasil dihapus.");
    }
}

