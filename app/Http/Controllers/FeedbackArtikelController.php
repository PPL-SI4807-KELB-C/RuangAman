<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackArtikelRequest;
use App\Http\Requests\UpdateFeedbackArtikelRequest;
use App\Models\Artikel;
use App\Models\FeedbackArtikel;
use App\Services\FeedbackArtikelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PBI-12: CRUD feedback artikel milik Pelapor.
 */
class FeedbackArtikelController extends Controller
{
    public function __construct(
        private readonly FeedbackArtikelService $feedbackService
    ) {}

    public function store(StoreFeedbackArtikelRequest $request, Artikel $artikel): RedirectResponse
    {
        $this->authorize('create', $artikel);

        $this->feedbackService->buat($artikel, $request->user(), $request->validated());

        return redirect()
            ->route('artikel.show', $artikel).'#feedback'
            ->with('success', 'Feedback berhasil dikirim dan menunggu moderasi Admin Kasus.');
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAnyOwn', FeedbackArtikel::class);

        $status = trim((string) $request->input('status', ''));

        $feedbackList = FeedbackArtikel::query()
            ->with('artikel:artikel_id,judul,slug,status_publikasi')
            ->where('user_id', $request->user()->user_id)
            ->when(array_key_exists($status, FeedbackArtikel::STATUS_OPTIONS), function ($query) use ($status) {
                $query->where('status_moderasi', $status);
            })
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('feedback-saya.index', [
            'title' => 'Feedback Artikel Saya - RuangAman',
            'feedbackList' => $feedbackList,
            'selectedStatus' => $status,
            'statusOptions' => FeedbackArtikel::STATUS_OPTIONS,
        ]);
    }

    public function edit(FeedbackArtikel $feedback): View
    {
        $this->authorize('update', $feedback);

        $feedback->load('artikel:artikel_id,judul,slug,status_publikasi');

        return view('feedback-saya.edit', [
            'title' => 'Ubah Feedback Artikel - RuangAman',
            'feedback' => $feedback,
        ]);
    }

    public function update(UpdateFeedbackArtikelRequest $request, FeedbackArtikel $feedback): RedirectResponse
    {
        $this->authorize('update', $feedback);

        $this->feedbackService->perbarui($feedback, $request->validated());

        return redirect()
            ->route('feedback-saya.index')
            ->with('success', 'Feedback berhasil diperbarui dan akan dimoderasi ulang.');
    }

    public function destroy(FeedbackArtikel $feedback): RedirectResponse
    {
        $this->authorize('delete', $feedback);

        $feedback->delete();

        return redirect()
            ->route('feedback-saya.index')
            ->with('success', 'Feedback berhasil dihapus.');
    }
}
