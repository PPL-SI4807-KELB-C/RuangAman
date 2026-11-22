<?php

namespace App\Http\Requests;

use App\Models\HalamanInformasi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class HalamanInformasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && ($this->user()->isAdminKasus() || $this->user()->hasPermission('kelola-halaman-informasi'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseRules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:200'],
            'isi' => ['required', 'string', 'min:10'],
            'status_publikasi' => ['required', Rule::in(array_keys(HalamanInformasi::STATUS_OPTIONS))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul halaman wajib diisi.',
            'judul.max' => 'Judul halaman maksimal 200 karakter.',
            'isi.required' => 'Isi halaman wajib diisi.',
            'isi.min' => 'Isi halaman terlalu singkat, minimal 10 karakter.',
            'status_publikasi.required' => 'Status publikasi wajib dipilih.',
            'status_publikasi.in' => 'Status publikasi hanya boleh "draft" atau "published".',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'judul' => 'judul halaman',
            'isi' => 'isi halaman',
            'status_publikasi' => 'status publikasi',
        ];
    }
}
