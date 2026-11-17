<?php

namespace App\Http\Requests;

use App\Models\KegiatanEdukasi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NonaktifkanKegiatanRequest extends FormRequest
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
            'target_status' => [
                'nullable',
                Rule::in([
                    KegiatanEdukasi::STATUS_DIBATALKAN,
                    KegiatanEdukasi::STATUS_DRAFT,
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
            'target_status.in' => 'Status penonaktifan hanya boleh diisi "dibatalkan" atau "draft".',
        ];
    }
}
