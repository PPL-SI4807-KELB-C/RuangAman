<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilPelaporRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'alias' => ['required', 'string', 'min:3', 'max:50'],
            'kontak_opsional' => ['nullable', 'string', 'max:100'],
            'preferensi_anonim' => ['nullable', 'boolean'],
        ];
    }
}