<?php

namespace App\Http\Requests;

use App\Models\Faq;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdminKasus() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'pertanyaan' => ['required', 'string'],
            'jawaban' => ['required', 'string'],
            'urutan' => ['required', 'integer', 'min:1'],
            'status_publikasi' => ['required', Rule::in([Faq::STATUS_DRAFT, Faq::STATUS_PUBLISHED])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'jawaban.required' => 'Jawaban wajib diisi.',
            'urutan.required' => 'Urutan tampil wajib diisi.',
            'urutan.integer' => 'Urutan tampil harus berupa bilangan bulat.',
            'urutan.min' => 'Urutan tampil minimal 1.',
            'status_publikasi.required' => 'Status publikasi wajib dipilih.',
            'status_publikasi.in' => 'Status publikasi harus Draf atau Dipublikasikan.',
        ];
    }
}
