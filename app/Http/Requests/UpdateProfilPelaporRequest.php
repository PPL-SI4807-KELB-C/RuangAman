<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilPelaporRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isPelapor();
    }

    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'alias' => ['required', 'string', 'max:100'],
            'kontak_opsional' => ['nullable', 'string', 'max:100'],
            'preferensi_anonim' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'alias' => 'alias',
            'kontak_opsional' => 'kontak tambahan',
            'preferensi_anonim' => 'preferensi mode anonim',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'alias' => is_string($this->alias) ? trim($this->alias) : $this->alias,
            'preferensi_anonim' => $this->boolean('preferensi_anonim'),
        ]);
    }
}
