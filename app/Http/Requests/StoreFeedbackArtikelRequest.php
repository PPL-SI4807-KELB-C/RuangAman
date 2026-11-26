<?php

namespace App\Http\Requests;

class StoreFeedbackArtikelRequest extends FeedbackArtikelRequest
{
    public function authorize(): bool
    {
        $artikel = $this->route('artikel');

        return $this->user()?->can('create', $artikel) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->contentRules();
    }
}
