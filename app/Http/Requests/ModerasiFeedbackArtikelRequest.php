<?php

namespace App\Http\Requests;

use App\Models\FeedbackArtikel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModerasiFeedbackArtikelRequest extends FormRequest
{
    public function authorize(): bool
    {
        $feedback = $this->route('feedback');

        return $this->user()?->can('moderate', $feedback) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status_moderasi' => ['required', Rule::in(array_keys(FeedbackArtikel::STATUS_OPTIONS))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status_moderasi.required' => 'Status moderasi wajib dipilih.',
            'status_moderasi.in' => 'Status moderasi tidak valid.',
        ];
    }
}
