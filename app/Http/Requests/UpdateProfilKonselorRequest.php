<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilKonselorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isKonselor();
    }

    /**
     * Konselor hanya boleh mengubah biodata profesionalnya sendiri.
     * Status ketersediaan dan akun pengguna tetap dikelola Admin Kasus.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'spesialisasi' => ['required', 'string', 'max:200'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap konselor wajib diisi.',
            'nama.max' => 'Nama lengkap konselor maksimal 150 karakter.',
            'spesialisasi.required' => 'Spesialisasi/kualifikasi wajib diisi.',
            'spesialisasi.max' => 'Spesialisasi/kualifikasi maksimal 200 karakter.',
            'bio.max' => 'Bio profesional maksimal 2000 karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama' => 'nama lengkap',
            'spesialisasi' => 'spesialisasi',
            'bio' => 'bio profesional',
        ];
    }
}
