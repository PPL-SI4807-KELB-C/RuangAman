<?php

namespace App\Http\Requests;

class UpdateFeedbackArtikelRequest extends FeedbackArtikelRequest
{
    public function authorize(): bool
    {
        $feedback = $this->route('feedback');

        return $this->user()?->can('update', $feedback) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->contentRules();
    }
}
