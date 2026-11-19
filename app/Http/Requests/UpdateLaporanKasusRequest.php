<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLaporanKasusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $laporan = $this->route('laporan');
        return auth()->check()
            && $laporan->user_id === auth()->id()
            && $laporan->status?->nama_status === 'Pending';
    }

    public function rules(): array
    {
        return [
            'kategori_kekerasan' => ['required', 'string', 'max:100'],
            'lokasi_kejadian' => ['required', 'string', 'max:500'],
            'waktu_kejadian' => ['required', 'date'],
            'tingkat_urgensi' => ['required', 'in:Rendah,Sedang,Tinggi'],
            'kronologi' => ['required', 'string', 'min:10'],
            'mode_anonim' => ['nullable', 'boolean'],
            'lampiran' => ['nullable', 'array'],
            'lampiran.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}