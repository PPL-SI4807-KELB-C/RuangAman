<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaporanKasusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'step' => ['nullable', 'integer', 'min:1', 'max:4'],
            'judul' => ['required', 'string', 'max:200'],
            'kategori_kekerasan' => ['required', 'string', 'in:Fisik,Psikis,Seksual,Ekonomi,Siber,Lainnya'],
            'lokasi_kejadian' => ['required', 'string', 'max:1000'],
            'kronologi' => ['required', 'string'],
            'waktu_kejadian' => ['required', 'date', 'before_or_equal:now'],
            'tingkat_urgensi' => ['required', 'in:rendah,sedang,tinggi,darurat'],
            'mode_anonim' => ['sometimes', 'boolean'],
            'lampiran' => ['nullable', 'array', 'max:5'],
            'lampiran.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,mp3,mp4,mov', 'max:10240'],
            'persetujuan' => ['required', 'accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'judul' => 'judul laporan',
            'kategori_kekerasan' => 'kategori kekerasan',
            'lokasi_kejadian' => 'lokasi kejadian',
            'kronologi' => 'kronologi kejadian',
            'waktu_kejadian' => 'waktu kejadian',
            'tingkat_urgensi' => 'tingkat urgensi',
            'mode_anonim' => 'mode anonim',
            'lampiran' => 'lampiran bukti',
            'persetujuan' => 'pernyataan kebenaran data',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'mode_anonim' => $this->boolean('mode_anonim'),
        ]);
    }
}
