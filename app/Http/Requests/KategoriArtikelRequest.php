<?php

namespace App\Http\Requests;

use App\Models\KategoriArtikel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class KategoriArtikelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && ($this->user()->isAdminKasus() || $this->user()->hasPermission('kelola-kategori-artikel'));
    }

    /**
     * Nilai rute bisa berupa model hasil route binding maupun angka mentah.
     */
    protected function kategoriSaatIni(): ?KategoriArtikel
    {
        $kategori = $this->route('kategori');

        if ($kategori instanceof KategoriArtikel) {
            return $kategori;
        }

        return $kategori === null ? null : KategoriArtikel::query()->find((int) $kategori);
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseRules(): array
    {
        $kategori = $this->kategoriSaatIni();

        $nama = [
            'required',
            'string',
            'max:100',
            Rule::unique('kategori_artikel', 'nama_kategori')->ignore($kategori?->kategori_id, 'kategori_id'),
        ];

        // Kategori bawaan dipakai sebagai cadangan oleh PBI-10, jadi namanya dikunci.
        if ($kategori?->isDefault()) {
            $nama[] = Rule::in([KategoriArtikel::NAMA_DEFAULT]);
        }

        return [
            'nama_kategori' => $nama,
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter.',
            'nama_kategori.unique' => 'Nama kategori tersebut sudah ada. Gunakan nama lain atau ubah kategori yang sudah ada.',
            'nama_kategori.in' => 'Kategori bawaan "'.KategoriArtikel::NAMA_DEFAULT.'" tidak dapat diganti namanya.',
            'deskripsi.max' => 'Deskripsi maksimal 255 karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_kategori' => 'nama kategori',
            'deskripsi' => 'deskripsi',
        ];
    }
}
