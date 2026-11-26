<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGlosariumKbgRequest;
use App\Http\Requests\UpdateGlosariumKbgRequest;
use App\Models\GlosariumKbg;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GlosariumKbgController extends Controller
{
    /**
     * Menampilkan daftar istilah glosarium KBG beserta pencarian dan filter status.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', GlosariumKbg::class);

        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));

        $glosariumList = GlosariumKbg::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('istilah', 'like', "%{$search}%")
                        ->orWhere('definisi', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%");
                });
            })
            ->when($status !== '' && array_key_exists($status, GlosariumKbg::STATUS_OPTIONS), function ($query) use ($status) {
                $query->where('status_publikasi', $status);
            })
            ->orderBy('istilah')
            ->paginate(10)
            ->withQueryString();

        return view('admin.glosarium.index', [
            'title' => 'Manajemen Glosarium KBG - RuangAman',
            'glosariumList' => $glosariumList,
            'search' => $search,
            'selectedStatus' => $status,
            'statusOptions' => GlosariumKbg::STATUS_OPTIONS,
        ]);
    }

    /**
     * Menampilkan form tambah istilah glosarium baru.
     */
    public function create(): View
    {
        $this->authorize('create', GlosariumKbg::class);

        return view('admin.glosarium.create', [
            'title' => 'Tambah Istilah Glosarium - RuangAman',
            'glosarium' => new GlosariumKbg([
                'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
            ]),
            'statusOptions' => GlosariumKbg::STATUS_OPTIONS,
        ]);
    }

    /**
     * Menyimpan istilah glosarium baru.
     */
    public function store(StoreGlosariumKbgRequest $request): RedirectResponse
    {
        $this->authorize('create', GlosariumKbg::class);

        $glosarium = GlosariumKbg::query()->create($request->validated());

        return redirect()
            ->route('admin.glosarium.index')
            ->with('success', "Istilah \"{$glosarium->istilah}\" berhasil ditambahkan.");
    }

    /**
     * Menampilkan form ubah istilah glosarium.
     */
    public function edit(GlosariumKbg $glosarium): View
    {
        $this->authorize('update', $glosarium);

        return view('admin.glosarium.edit', [
            'title' => "Ubah Istilah: {$glosarium->istilah} - RuangAman",
            'glosarium' => $glosarium,
            'statusOptions' => GlosariumKbg::STATUS_OPTIONS,
        ]);
    }

    /**
     * Memperbarui istilah glosarium.
     */
    public function update(UpdateGlosariumKbgRequest $request, GlosariumKbg $glosarium): RedirectResponse
    {
        $this->authorize('update', $glosarium);

        $glosarium->update($request->validated());

        return redirect()
            ->route('admin.glosarium.index')
            ->with('success', "Istilah \"{$glosarium->istilah}\" berhasil diperbarui.");
    }

    /**
     * Menghapus permanen istilah glosarium.
     */
    public function destroy(GlosariumKbg $glosarium): RedirectResponse
    {
        $this->authorize('delete', $glosarium);

        $istilah = $glosarium->istilah;
        $glosarium->delete();

        return redirect()
            ->route('admin.glosarium.index')
            ->with('success', "Istilah \"{$istilah}\" berhasil dihapus permanen.");
    }
}
