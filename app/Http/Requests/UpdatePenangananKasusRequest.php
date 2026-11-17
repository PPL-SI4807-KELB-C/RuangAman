<?php

namespace App\Http\Requests;

use App\Models\PenangananKasus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePenangananKasusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdminKasus() || $this->user()?->hasPermission('kelola-penugasan-kasus') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var PenangananKasus|int|string|null $penanganan */
        $penanganan = $this->route('penangananKasus') ?? $this->route('penanganan_kasus') ?? $this->route('penanganan_kasu');
        $penangananId = $penanganan instanceof PenangananKasus ? $penanganan->penanganan_id : $penanganan;

        return [
            'laporan_id' => [
                'required',
                'integer',
                'exists:laporan_kasus,laporan_id',
                Rule::unique('penanganan_kasus', 'laporan_id')->ignore($penangananId, 'penanganan_id'),
            ],
            'petugas_id' => [
                'required',
                'integer',
                'exists:users,user_id',
            ],
            'tanggal_mulai' => [
                'required',
                'date',
            ],
            'tanggal_selesai' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
            'catatan' => [
                'nullable',
                'string',
            ],
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
            'laporan_id.required' => 'Laporan kasus wajib dipilih.',
            'laporan_id.integer' => 'ID laporan kasus harus berupa angka.',
            'laporan_id.exists' => 'Laporan kasus yang dipilih tidak valid atau tidak ditemukan.',
            'laporan_id.unique' => 'Laporan kasus ini sudah ditugaskan pada penanganan lain.',
            'petugas_id.required' => 'Petugas atau konselor wajib dipilih.',
            'petugas_id.integer' => 'ID petugas harus berupa angka.',
            'petugas_id.exists' => 'Petugas yang dipilih tidak ditemukan dalam sistem.',
            'tanggal_mulai.required' => 'Tanggal mulai penanganan wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            'catatan.string' => 'Catatan penanganan harus berupa teks.',
        ];
    }
}
