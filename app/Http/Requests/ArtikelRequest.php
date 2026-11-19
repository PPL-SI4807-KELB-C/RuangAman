<?php

namespace App\Http\Requests;

use App\Models\Artikel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class ArtikelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && ($this->user()->isAdminKasus() || $this->user()->hasPermission('kelola-artikel'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseRules(): array
    {
        return [
            'kategori_id' => ['required', 'integer', 'exists:kategori_artikel,kategori_id'],
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'status_publikasi' => ['required', Rule::in(array_keys(Artikel::STATUS_OPTIONS))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori artikel wajib dipilih.',
            'kategori_id.integer' => 'Kategori artikel tidak valid.',
            'kategori_id.exists' => 'Kategori artikel tidak ditemukan.',
            'judul.required' => 'Judul artikel wajib diisi.',
            'judul.max' => 'Judul artikel maksimal 255 karakter.',
            'isi.required' => 'Isi artikel wajib diisi.',
            'status_publikasi.required' => 'Status publikasi wajib dipilih.',
            'status_publikasi.in' => 'Status publikasi tidak valid.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kategori_id' => 'kategori artikel',
            'judul' => 'judul artikel',
            'isi' => 'isi artikel',
            'status_publikasi' => 'status publikasi',
        ];
    }
}
