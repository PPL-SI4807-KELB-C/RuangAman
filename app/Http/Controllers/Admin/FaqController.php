<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Menampilkan daftar FAQ admin dengan pencarian, filter status, dan statistik.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));

        $faqs = Faq::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('pertanyaan', 'like', "%{$search}%")
                        ->orWhere('jawaban', 'like', "%{$search}%");
                });
            })
            ->when($status !== '' && array_key_exists($status, Faq::STATUS_OPTIONS), function ($query) use ($status) {
                $query->where('status_publikasi', $status);
            })
            ->ordered()
            ->get();

        $totalCount = Faq::count();
        $publishedCount = Faq::where('status_publikasi', Faq::STATUS_PUBLISHED)->count();
        $draftCount = Faq::where('status_publikasi', Faq::STATUS_DRAFT)->count();

        return view('admin.faq.index', [
            'title' => 'Manajemen FAQ / Pusat Bantuan - RuangAman',
            'faqs' => $faqs,
            'totalCount' => $totalCount,
            'publishedCount' => $publishedCount,
            'draftCount' => $draftCount,
            'search' => $search,
            'statusFilter' => $status,
        ]);
    }

    /**
     * Menampilkan form tambah FAQ baru.
     */
    public function create(): View
    {
        $nextUrutan = ((int) Faq::max('urutan')) + 1;

        return view('admin.faq.create', [
            'title' => 'Tambah FAQ - RuangAman',
            'faq' => new Faq([
                'urutan' => $nextUrutan,
                'status_publikasi' => Faq::STATUS_DRAFT,
            ]),
        ]);
    }

    /**
     * Menyimpan FAQ baru.
     */
    public function store(FaqRequest $request): RedirectResponse
    {
        Faq::query()->create($request->validated());

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail FAQ untuk admin.
     */
    public function show(Faq $faq): View
    {
        return view('admin.faq.show', [
            'title' => 'Detail FAQ - RuangAman',
            'faq' => $faq,
        ]);
    }

    /**
     * Menampilkan form ubah FAQ.
     */
    public function edit(Faq $faq): View
    {
        return view('admin.faq.edit', [
            'title' => 'Ubah FAQ - RuangAman',
            'faq' => $faq,
        ]);
    }

    /**
     * Memperbarui FAQ yang sudah ada.
     */
    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Menghapus permanen FAQ.
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }
}
