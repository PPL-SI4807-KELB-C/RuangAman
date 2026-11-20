<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePengumumanRequest;
use App\Http\Requests\UpdatePengumumanRequest;
use App\Models\Pengumuman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    /**
     * Menampilkan daftar pengumuman beserta pencarian, filter status, dan pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Pengumuman::class);

        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));

        $pengumumanList = Pengumuman::query()
            ->with('pembuat:user_id,nama_tampilan')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhere('isi', 'like', "%{$search}%");
                });
            })
            ->when($status !== '' && array_key_exists($status, Pengumuman::STATUS_OPTIONS), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('mulai_tayang')
            ->orderByDesc('pengumuman_id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengumuman.index', [
            'title' => 'Manajemen Pengumuman - RuangAman',
            'pengumumanList' => $pengumumanList,
            'search' => $search,
            'selectedStatus' => $status,
            'statusOptions' => Pengumuman::STATUS_OPTIONS,
        ]);
    }

    /**
     * Menampilkan form tambah pengumuman baru.
     */
    public function create(): View
    {
        $this->authorize('create', Pengumuman::class);

        return view('admin.pengumuman.create', [
            'title' => 'Tambah Pengumuman - RuangAman',
            'pengumuman' => new Pengumuman([
                'mulai_tayang' => now()->toDateString(),
                'status' => Pengumuman::STATUS_AKTIF,
            ]),
            'statusOptions' => Pengumuman::STATUS_OPTIONS,
        ]);
    }

    /**
     * Menyimpan pengumuman baru atas nama admin yang membuatnya.
     */
    public function store(StorePengumumanRequest $request): RedirectResponse
    {
        $this->authorize('create', Pengumuman::class);

        $pengumuman = Pengumuman::query()->create([
            ...$request->validated(),
            'dibuat_oleh' => $request->user()->user_id,
        ]);

        return redirect()
            ->route('admin.pengumuman.show', $pengumuman)
            ->with('success', "Pengumuman \"{$pengumuman->judul}\" berhasil ditambahkan.");
    }

    /**
     * Menampilkan detail pengumuman beserta periode dan status tayangnya.
     */
    public function show(Pengumuman $pengumuman): View
    {
        $this->authorize('view', $pengumuman);

        $pengumuman->load('pembuat:user_id,nama_tampilan');

        return view('admin.pengumuman.show', [
            'title' => "Detail Pengumuman: {$pengumuman->judul} - RuangAman",
            'pengumuman' => $pengumuman,
            'tautanPublik' => route('pengumuman.show', $pengumuman),
        ]);
    }

    /**
     * Menampilkan form ubah pengumuman.
     */
    public function edit(Pengumuman $pengumuman): View
    {
        $this->authorize('update', $pengumuman);

        return view('admin.pengumuman.edit', [
            'title' => "Ubah Pengumuman: {$pengumuman->judul} - RuangAman",
            'pengumuman' => $pengumuman,
            'statusOptions' => Pengumuman::STATUS_OPTIONS,
        ]);
    }

    /**
     * Memperbarui pengumuman tanpa mengubah pembuatnya.
     */
    public function update(UpdatePengumumanRequest $request, Pengumuman $pengumuman): RedirectResponse
    {
        $this->authorize('update', $pengumuman);

        $pengumuman->update($request->validated());

        return redirect()
            ->route('admin.pengumuman.show', $pengumuman)
            ->with('success', "Pengumuman \"{$pengumuman->judul}\" berhasil diperbarui.");
    }

    /**
     * Menghapus permanen pengumuman.
     */
    public function destroy(Pengumuman $pengumuman): RedirectResponse
    {
        $this->authorize('delete', $pengumuman);

        $judul = $pengumuman->judul;
        $pengumuman->delete();

        return redirect()
            ->route('admin.pengumuman.index')
            ->with('success', "Pengumuman \"{$judul}\" berhasil dihapus permanen.");
    }
}
