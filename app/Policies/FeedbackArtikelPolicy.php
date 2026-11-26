<?php

namespace App\Policies;

use App\Models\Artikel;
use App\Models\FeedbackArtikel;
use App\Models\User;

class FeedbackArtikelPolicy
{
    private function dapatMemoderasi(User $user): bool
    {
        return $user->isAdminKasus() || $user->hasPermission('moderasi-feedback');
    }

    private function pemilikFeedback(User $user, FeedbackArtikel $feedback): bool
    {
        return (int) $feedback->user_id === (int) $user->user_id;
    }

    private function dapatMengelolaFeedbackSendiri(User $user): bool
    {
        return $user->isPelapor() && $user->hasPermission('beri-feedback');
    }

    public function viewAny(User $user): bool
    {
        return $this->dapatMemoderasi($user);
    }

    public function view(User $user, FeedbackArtikel $feedback): bool
    {
        return $this->dapatMemoderasi($user);
    }

    public function moderate(User $user, FeedbackArtikel $feedback): bool
    {
        return $this->dapatMemoderasi($user);
    }

    public function viewAnyOwn(User $user): bool
    {
        return $this->dapatMengelolaFeedbackSendiri($user);
    }

    public function create(User $user, Artikel $artikel): bool
    {
        return $this->dapatMengelolaFeedbackSendiri($user)
            && $artikel->status_publikasi === Artikel::STATUS_PUBLISHED;
    }

    public function update(User $user, FeedbackArtikel $feedback): bool
    {
        return $this->dapatMengelolaFeedbackSendiri($user)
            && $this->pemilikFeedback($user, $feedback);
    }

    public function delete(User $user, FeedbackArtikel $feedback): bool
    {
        return $this->dapatMengelolaFeedbackSendiri($user)
            && $this->pemilikFeedback($user, $feedback);
    }
}
