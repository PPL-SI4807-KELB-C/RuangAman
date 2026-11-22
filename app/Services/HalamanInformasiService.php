<?php

namespace App\Services;

use App\Models\HalamanInformasi;
use Illuminate\Support\Str;

class HalamanInformasiService
{
    /**
     * Membuat halaman informasi dengan slug yang dibangkitkan dari judul
     * dan dijamin unik terhadap baris lain.
     *
     * @param  array<string, mixed>  $data
     */
    public function buat(array $data): HalamanInformasi
    {
        $data['slug'] = $this->slugUnik((string) $data['judul']);

        return HalamanInformasi::query()->create($data);
    }

    /**
     * Memperbarui halaman. Slug dibangkitkan ulang hanya bila judul berubah,
     * agar tautan publik yang sudah dibagikan tidak berpindah tanpa alasan.
     *
     * @param  array<string, mixed>  $data
     */
    public function perbarui(HalamanInformasi $halaman, array $data): HalamanInformasi
    {
        if (($data['judul'] ?? null) !== null && $data['judul'] !== $halaman->judul) {
            $data['slug'] = $this->slugUnik((string) $data['judul'], $halaman->halaman_id);
        }

        $halaman->update($data);

        return $halaman->fresh();
    }

    /**
     * Menghapus permanen halaman informasi.
     */
    public function hapusPermanen(HalamanInformasi $halaman): void
    {
        $halaman->delete();
    }

    /**
     * Membentuk slug unik dari judul, diberi akhiran -2, -3, dst. bila sudah dipakai.
     */
    private function slugUnik(string $judul, ?int $kecualiId = null): string
    {
        $dasar = Str::slug($judul) ?: 'halaman';
        $slug = $dasar;
        $nomor = 2;

        while (HalamanInformasi::query()
            ->where('slug', $slug)
            ->when($kecualiId !== null, function ($query) use ($kecualiId) {
                $query->where('halaman_id', '<>', $kecualiId);
            })
            ->exists()) {
            $slug = "{$dasar}-{$nomor}";
            $nomor++;
        }

        return $slug;
    }
}
