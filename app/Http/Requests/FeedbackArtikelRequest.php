<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class FeedbackArtikelRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('isi_feedback'))) {
            $this->merge(['isi_feedback' => trim($this->input('isi_feedback'))]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function contentRules(): array
    {
        return [
            'isi_feedback' => ['required', 'string', 'min:5', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'isi_feedback.required' => 'Feedback wajib diisi.',
            'isi_feedback.string' => 'Feedback harus berupa teks.',
            'isi_feedback.min' => 'Feedback minimal 5 karakter.',
            'isi_feedback.max' => 'Feedback maksimal 2.000 karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'isi_feedback' => 'feedback',
        ];
    }
}
