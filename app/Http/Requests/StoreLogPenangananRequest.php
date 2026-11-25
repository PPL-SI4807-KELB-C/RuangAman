<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLogPenangananRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && ! $user->isPelapor()
            && ($user->isAdminKasus() || $user->isKonselor());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'penanganan_id' => ['required', 'integer', 'exists:penanganan_kasus,penanganan_id'],
            'aktivitas' => ['required', 'string', 'min:5', 'max:5000'],
            'waktu' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'penanganan_id.required' => 'Kasus yang ditangani wajib dipilih.',
            'penanganan_id.integer' => 'ID penanganan kasus harus berupa angka yang valid.',
            'penanganan_id.exists' => 'Data penanganan kasus yang dipilih tidak ditemukan.',
            'aktivitas.required' => 'Catatan aktivitas penanganan wajib diisi.',
            'aktivitas.string' => 'Catatan aktivitas harus berupa teks yang valid.',
            'aktivitas.min' => 'Catatan aktivitas minimal 5 karakter agar informatif.',
            'aktivitas.max' => 'Catatan aktivitas maksimal 5000 karakter.',
            'waktu.date' => 'Format waktu aktivitas tidak valid.',
            'waktu.before_or_equal' => 'Waktu aktivitas tidak boleh berada di masa depan.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'penanganan_id' => 'penanganan kasus',
            'aktivitas' => 'catatan aktivitas',
            'waktu' => 'waktu aktivitas',
        ];
    }
}
