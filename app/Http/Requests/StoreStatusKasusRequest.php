<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatusKasusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdminKasus() || $this->user()?->hasPermission('kelola-status-kasus') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_status' => ['required', 'string', 'max:50', 'unique:status_kasus,nama_status'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_status.required' => 'Nama status kasus wajib diisi.',
            'nama_status.string' => 'Nama status kasus harus berupa teks.',
            'nama_status.max' => 'Nama status kasus tidak boleh lebih dari 50 karakter.',
            'nama_status.unique' => 'Nama status kasus sudah terdaftar.',
            'deskripsi.string' => 'Deskripsi status harus berupa teks.',
        ];
    }
}

