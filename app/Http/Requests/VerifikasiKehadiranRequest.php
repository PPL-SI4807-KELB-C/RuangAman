<?php

namespace App\Http\Requests;

use App\Models\PendaftaranKegiatan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifikasiKehadiranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && ($this->user()->isAdminKasus() || $this->user()->hasPermission('kelola-kegiatan'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status_pendaftaran' => [
                'required',
                Rule::in([
                    PendaftaranKegiatan::STATUS_HADIR,
                    PendaftaranKegiatan::STATUS_TIDAK_HADIR,
                ]),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status_pendaftaran.required' => 'Status kehadiran peserta wajib dipilih.',
            'status_pendaftaran.in' => 'Status kehadiran hanya boleh diisi "hadir" atau "tidak_hadir".',
        ];
    }
}
