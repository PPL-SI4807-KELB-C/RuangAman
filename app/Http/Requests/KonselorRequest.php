<?php

namespace App\Http\Requests;

use App\Models\Konselor;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

abstract class KonselorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && ($this->user()->isAdminKasus() || $this->user()->hasPermission('kelola-konselor'));
    }

    /**
     * Aturan validasi dasar untuk pembuatan dan pembaruan profil konselor.
     *
     * @return array<string, mixed>
     */
    protected function baseRules(bool $isUpdate = false): array
    {
        $rules = [
            'nama' => ['required', 'string', 'max:150'],
            'spesialisasi' => ['required', 'string', 'max:200'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(array_keys(Konselor::STATUS_OPTIONS))],
        ];

        if (! $isUpdate) {
            $rules['user_id'] = [
                'required',
                'integer',
                'exists:users,user_id',
                Rule::unique('konselor', 'user_id'),
            ];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Akun pengguna calon konselor wajib dipilih.',
            'user_id.integer' => 'Akun pengguna yang dipilih tidak valid.',
            'user_id.exists' => 'Akun pengguna yang dipilih tidak ditemukan.',
            'user_id.unique' => 'Akun tersebut sudah memiliki profil konselor.',
            'nama.required' => 'Nama lengkap konselor wajib diisi.',
            'nama.max' => 'Nama lengkap konselor maksimal 150 karakter.',
            'spesialisasi.required' => 'Spesialisasi/kualifikasi wajib diisi.',
            'spesialisasi.max' => 'Spesialisasi/kualifikasi maksimal 200 karakter.',
            'bio.max' => 'Bio profesional maksimal 2000 karakter.',
            'status.required' => 'Status ketersediaan konselor wajib dipilih.',
            'status.in' => 'Status ketersediaan tidak valid (harus aktif, cuti, atau nonaktif).',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'akun pengguna',
            'nama' => 'nama lengkap',
            'spesialisasi' => 'spesialisasi',
            'bio' => 'bio profesional',
            'status' => 'status ketersediaan',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this instanceof UpdateKonselorRequest) {
                return;
            }

            if ($validator->errors()->isNotEmpty() || ! $this->filled('user_id')) {
                return;
            }

            $calonUser = User::query()->with('role')->find($this->input('user_id'));

            if ($calonUser && ! $calonUser->isKonselor()) {
                $validator->errors()->add(
                    'user_id',
                    'Akun yang dipilih bukan pengguna dengan role Konselor. Ubah role akun terlebih dahulu.'
                );
            }
        });
    }
}
