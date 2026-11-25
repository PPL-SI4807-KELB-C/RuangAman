<?php

namespace App\Services;

use App\Models\Artikel;
use App\Models\KategoriArtikel;
use App\Models\User;
use Illuminate\Support\Str;

class ArtikelService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function buat(array $data, User $penulis): Artikel
    {
        $data['penulis_id'] = $penulis->user_id;
        $data['slug'] = $this->slugUnik($data['judul']);
        $data['kategori_id'] = $this->kategoriIdAtauDefault($data);

        return Artikel::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function perbarui(Artikel $artikel, array $data): Artikel
    {
        if ($artikel->judul !== $data['judul']) {
            $data['slug'] = $this->slugUnik($data['judul'], $artikel->artikel_id);
        }

        $data['kategori_id'] = $this->kategoriIdAtauDefault($data);

        $artikel->update($data);

        return $artikel->refresh();
    }

    /**
     * Kategori adalah data referensi hanya-baca milik PBI-11. Bila tidak dipilih,
     * artikel masuk ke kategori bawaan "Umum" agar PBI-10 berjalan mandiri.
     *
     * @param  array<string, mixed>  $data
     */
    private function kategoriIdAtauDefault(array $data): int
    {
        return (int) ($data['kategori_id'] ?? KategoriArtikel::default()->kategori_id);
    }

    private function slugUnik(string $judul, ?int $abaikanArtikelId = null): string
    {
        $dasar = Str::limit(Str::slug($judul), 220, '');
        $dasar = $dasar !== '' ? $dasar : 'artikel';
        $slug = $dasar;
        $nomor = 2;

        while ($this->slugTerpakai($slug, $abaikanArtikelId)) {
            $slug = $dasar.'-'.$nomor;
            $nomor++;
        }

        return $slug;
    }

    private function slugTerpakai(string $slug, ?int $abaikanArtikelId = null): bool
    {
        return Artikel::query()
            ->where('slug', $slug)
            ->when($abaikanArtikelId !== null, function ($query) use ($abaikanArtikelId) {
                $query->where('artikel_id', '!=', $abaikanArtikelId);
            })
            ->exists();
    }
}
