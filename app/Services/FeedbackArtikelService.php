<?php

namespace App\Services;

use App\Models\Artikel;
use App\Models\FeedbackArtikel;
use App\Models\User;

class FeedbackArtikelService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function buat(Artikel $artikel, User $user, array $data): FeedbackArtikel
    {
        $data['artikel_id'] = $artikel->artikel_id;
        $data['user_id'] = $user->user_id;
        $data['status_moderasi'] = FeedbackArtikel::STATUS_PENDING;

        return FeedbackArtikel::query()->create($data);
    }

    /**
     * Perubahan isi oleh Pelapor selalu memerlukan moderasi ulang.
     *
     * @param  array<string, mixed>  $data
     */
    public function perbarui(FeedbackArtikel $feedback, array $data): FeedbackArtikel
    {
        $data['status_moderasi'] = FeedbackArtikel::STATUS_PENDING;

        $feedback->update($data);

        return $feedback->refresh();
    }

    public function moderasi(FeedbackArtikel $feedback, string $status): FeedbackArtikel
    {
        $feedback->update(['status_moderasi' => $status]);

        return $feedback->refresh();
    }
}
