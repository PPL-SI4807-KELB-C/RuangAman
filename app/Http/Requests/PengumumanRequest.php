<?php

namespace App\Http\Requests;

use App\Models\Pengumuman;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class PengumumanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && ($this->user()->isAdminKasus() || $this->user()->hasPermission('kelola-pengumuman'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseRules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:200'],
            'isi' => ['required', 'string', 'min:10'],
            'mulai_tayang' => ['required', 'date'],
            'akhir_tayang' => ['nullable', 'date', 'after_or_equal:mulai_tayang'],
            'status' => ['required', Rule::in(array_keys(Pengumuman::STATUS_OPTIONS))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'judul.max' => 'Judul pengumuman maksimal 200 karakter.',
            'isi.required' => 'Isi pengumuman wajib diisi.',
            'isi.min' => 'Isi pengumuman terlalu singkat, minimal 10 karakter.',
            'mulai_tayang.required' => 'Tanggal mulai tayang wajib diisi.',
            'mulai_tayang.date' => 'Format tanggal mulai tayang tidak valid.',
            'akhir_tayang.date' => 'Format akhir periode tayang tidak valid.',
            'akhir_tayang.after_or_equal' => 'Akhir periode tayang tidak boleh lebih awal daripada tanggal mulai tayang.',
            'status.required' => 'Status pengumuman wajib dipilih.',
            'status.in' => 'Status pengumuman hanya boleh "aktif" atau "arsip".',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'judul' => 'judul pengumuman',
            'isi' => 'isi pengumuman',
            'mulai_tayang' => 'tanggal mulai tayang',
            'akhir_tayang' => 'akhir periode tayang',
            'status' => 'status pengumuman',
        ];
    }
}
