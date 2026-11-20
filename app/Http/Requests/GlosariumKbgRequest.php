<?php

namespace App\Http\Requests;

use App\Models\GlosariumKbg;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class GlosariumKbgRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && ($this->user()->isAdminKasus() || $this->user()->hasPermission('kelola-glosarium'));
    }

    /**
     * Identifikasi baris glosarium yang sedang diubah. Nilai rute bisa berupa
     * model hasil route binding maupun angka mentah, jadi keduanya ditangani.
     */
    protected function glosariumIdSaatIni(): ?int
    {
        $glosarium = $this->route('glosarium');

        if ($glosarium instanceof GlosariumKbg) {
            return (int) $glosarium->glosarium_id;
        }

        return $glosarium === null ? null : (int) $glosarium;
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseRules(): array
    {
        return [
            'istilah' => [
                'required',
                'string',
                'max:150',
                Rule::unique('glosarium_kbg', 'istilah')->ignore(
                    $this->glosariumIdSaatIni(),
                    'glosarium_id'
                ),
            ],
            'definisi' => ['required', 'string', 'min:10'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'status_publikasi' => ['required', Rule::in(array_keys(GlosariumKbg::STATUS_OPTIONS))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'istilah.required' => 'Istilah wajib diisi.',
            'istilah.max' => 'Istilah maksimal 150 karakter.',
            'istilah.unique' => 'Istilah tersebut sudah ada di glosarium. Gunakan istilah lain atau ubah entri yang sudah ada.',
            'definisi.required' => 'Definisi wajib diisi.',
            'definisi.min' => 'Definisi terlalu singkat, minimal 10 karakter.',
            'kategori.max' => 'Kategori maksimal 100 karakter.',
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
            'istilah' => 'istilah',
            'definisi' => 'definisi',
            'kategori' => 'kategori',
            'status_publikasi' => 'status publikasi',
        ];
    }
}
