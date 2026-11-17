<?php

namespace App\Http\Requests;

use App\Models\KegiatanEdukasi;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

abstract class KegiatanEdukasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && ($this->user()->isAdminKasus() || $this->user()->hasPermission('kelola-kegiatan'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseRules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'narasumber' => ['nullable', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'waktu_mulai' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'waktu_selesai' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'metode' => ['required', Rule::in([KegiatanEdukasi::METODE_ONLINE, KegiatanEdukasi::METODE_OFFLINE])],
            'lokasi_atau_tautan' => ['nullable', 'string', 'max:2000'],
            'kuota' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::in(array_keys(KegiatanEdukasi::STATUS_OPTIONS))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul kegiatan wajib diisi.',
            'judul.max' => 'Judul kegiatan maksimal 255 karakter.',
            'narasumber.max' => 'Nama narasumber maksimal 255 karakter.',
            'tanggal_mulai.required' => 'Tanggal mulai kegiatan wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'waktu_mulai.required' => 'Waktu mulai kegiatan wajib diisi.',
            'waktu_mulai.regex' => 'Format waktu mulai harus valid (contoh: 09:00).',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal daripada tanggal mulai.',
            'waktu_selesai.regex' => 'Format waktu selesai harus valid (contoh: 12:00).',
            'metode.required' => 'Metode pelaksanaan kegiatan wajib dipilih.',
            'metode.in' => 'Metode pelaksanaan hanya boleh "online" atau "offline".',
            'lokasi_atau_tautan.max' => 'Lokasi atau tautan maksimal 2000 karakter.',
            'kuota.required' => 'Kuota peserta wajib diisi.',
            'kuota.integer' => 'Kuota peserta harus berupa angka bulat.',
            'kuota.min' => 'Kuota peserta minimal 1 orang.',
            'status.required' => 'Status kegiatan wajib dipilih.',
            'status.in' => 'Status kegiatan tidak valid (harus draft, tayang, selesai, atau dibatalkan).',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'judul' => 'judul kegiatan',
            'deskripsi' => 'deskripsi',
            'narasumber' => 'narasumber',
            'tanggal_mulai' => 'tanggal mulai',
            'waktu_mulai' => 'waktu mulai',
            'tanggal_selesai' => 'tanggal selesai',
            'waktu_selesai' => 'waktu selesai',
            'metode' => 'metode pelaksanaan',
            'lokasi_atau_tautan' => 'lokasi atau tautan',
            'kuota' => 'kuota peserta',
            'status' => 'status kegiatan',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $tanggalMulai = $this->input('tanggal_mulai');
            $waktuMulai = $this->input('waktu_mulai');
            $tanggalSelesai = $this->input('tanggal_selesai') ?: $tanggalMulai;
            $waktuSelesai = $this->input('waktu_selesai');

            if ($tanggalMulai && $waktuMulai && $waktuSelesai) {
                $mulai = Carbon::parse("{$tanggalMulai} {$waktuMulai}");
                $selesai = Carbon::parse("{$tanggalSelesai} {$waktuSelesai}");

                if ($selesai->lessThanOrEqualTo($mulai)) {
                    $validator->errors()->add('waktu_selesai', 'Waktu selesai harus lebih lambat daripada waktu mulai.');
                }
            }
        });
    }
}
