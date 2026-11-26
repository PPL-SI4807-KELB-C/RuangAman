<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModerasiFeedbackArtikelRequest;
use App\Models\Artikel;
use App\Models\FeedbackArtikel;
use App\Services\FeedbackArtikelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PBI-12: Moderasi feedback artikel oleh Admin Kasus.
 */
class FeedbackArtikelController extends Controller
{
    public function __construct(
        private readonly FeedbackArtikelService $feedbackService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', FeedbackArtikel::class);

        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $artikelId = $request->integer('artikel_id');

        $feedbackList = FeedbackArtikel::query()
            ->with([
                'artikel:artikel_id,judul,slug',
                'user:user_id,nama_tampilan',
                'user.profilPelapor:profil_id,user_id,alias',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('isi_feedback', 'like', "%{$search}%")
                        ->orWhereHas('artikel', fn ($artikel) => $artikel->where('judul', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($user) => $user->where('nama_tampilan', 'like', "%{$search}%"))
                        ->orWhereHas('user.profilPelapor', fn ($profil) => $profil->where('alias', 'like', "%{$search}%"));
                });
            })
            ->when(array_key_exists($status, FeedbackArtikel::STATUS_OPTIONS), function ($query) use ($status) {
                $query->where('status_moderasi', $status);
            })
            ->when($artikelId > 0, function ($query) use ($artikelId) {
                $query->where('artikel_id', $artikelId);
            })
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.feedback-artikel.index', [
            'title' => 'Moderasi Feedback Artikel - RuangAman',
            'feedbackList' => $feedbackList,
            'search' => $search,
            'selectedStatus' => $status,
            'selectedArtikel' => $artikelId,
            'statusOptions' => FeedbackArtikel::STATUS_OPTIONS,
            'artikelOptions' => Artikel::query()->orderBy('judul')->pluck('judul', 'artikel_id'),
        ]);
    }

    public function show(FeedbackArtikel $feedback): View
    {
        $this->authorize('view', $feedback);

        $feedback->load([
            'artikel:artikel_id,judul,slug,status_publikasi',
            'user:user_id,nama_tampilan',
            'user.profilPelapor:profil_id,user_id,alias',
        ]);

        return view('admin.feedback-artikel.show', [
            'title' => 'Detail Feedback Artikel - RuangAman',
            'feedback' => $feedback,
            'statusOptions' => FeedbackArtikel::STATUS_OPTIONS,
        ]);
    }

    public function moderate(ModerasiFeedbackArtikelRequest $request, FeedbackArtikel $feedback): RedirectResponse
    {
        $this->authorize('moderate', $feedback);

        $this->feedbackService->moderasi($feedback, $request->validated('status_moderasi'));

        return redirect()
            ->route('admin.feedback-artikel.show', $feedback)
            ->with('success', 'Status moderasi feedback berhasil diperbarui.');
    }
}
