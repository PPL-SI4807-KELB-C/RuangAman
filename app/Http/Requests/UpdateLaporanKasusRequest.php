<?php

namespace App\Http\Requests;

use App\Models\LaporanKasus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLaporanKasusRequest extends FormRequest
{
    /**
     * Pemilik laporan, dan hanya saat status masih Pending (lihat LaporanKasusPolicy).
     */
    public function authorize(): bool
    {
        $laporan = $this->route('laporan');

        return $laporan instanceof LaporanKasus
            && $this->user() !== null
            && $this->user()->can('update', $laporan);
    }

    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => ['sometimes', 'required', 'string', 'max:200'],
            'kategori_kekerasan' => ['required', 'string', Rule::in(LaporanKasus::KATEGORI)],
            'lokasi_kejadian' => ['required', 'string', 'max:1000'],
            'kronologi' => ['required', 'string'],
            'waktu_kejadian' => ['required', 'date', 'before_or_equal:now'],
            'tingkat_urgensi' => ['required', Rule::in(LaporanKasus::URGENSI)],
            'mode_anonim' => ['sometimes', 'boolean'],
            'lampiran' => ['nullable', 'array', 'max:' . LaporanKasus::MAKS_LAMPIRAN],
            'lampiran.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,mp3,mp4,mov', 'max:10240'],
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
        ];
    }

    protected function prepareForValidation(): void
    {
        // Checkbox yang tidak dicentang tidak dikirim browser, artinya mode anonim dimatikan.
        $this->merge([
            'mode_anonim' => $this->boolean('mode_anonim'),
        ]);
    }
}