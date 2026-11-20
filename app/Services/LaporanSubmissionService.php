<?php

namespace App\Services;

use App\Models\LampiranLaporan;
use App\Models\LaporanKasus;
use App\Models\StatusKasus;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LaporanSubmissionService
{
    /**
     * @param  array<string, mixed>  $data
     * @param  UploadedFile[]  $lampiran
     * @return array{laporan: LaporanKasus, kode_laporan: string}
     */
    public function submit(User $user, array $data, array $lampiran = []): array
    {
        return DB::transaction(function () use ($user, $data, $lampiran) {
            $pending = StatusKasus::query()->where('nama_status', 'Pending')->firstOrFail();

            $laporan = LaporanKasus::query()->create([
                'user_id' => $user->user_id,
                'status_id' => $pending->status_id,
                'kode_laporan' => $this->generateKodeLaporan(),
                'judul' => $data['judul'] ?? 'Laporan Kasus',
                'kategori_kekerasan' => $data['kategori_kekerasan'],
                'lokasi_kejadian' => $data['lokasi_kejadian'] ?? 'Tidak disebutkan',
                'kronologi' => $data['kronologi'],
                'waktu_kejadian' => $data['waktu_kejadian'] ?? now(),
                'tingkat_urgensi' => $data['tingkat_urgensi'] ?? 'sedang',
                'mode_anonim' => $data['mode_anonim'] ?? true,
            ]);

            foreach ($lampiran as $file) {
                $this->storeLampiran($laporan, $file);
            }

            return [
                'laporan' => $laporan,
                'kode_laporan' => $laporan->kode_laporan,
            ];
        });
    }

    private function generateKodeLaporan(): string
    {
        do {
            $code = Str::upper(Str::random(6));
        } while (LaporanKasus::query()->where('kode_laporan', $code)->exists());

        return $code;
    }

    private function storeLampiran(LaporanKasus $laporan, UploadedFile $file): void
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($originalName) . '-' . Str::random(8) . '.' . $extension;
        $directory = 'private/laporan/' . $laporan->laporan_id;

        $path = $file->storeAs($directory, $fileName, 'local');

        LampiranLaporan::query()->create([
            'laporan_id' => $laporan->laporan_id,
            'nama_file' => $file->getClientOriginalName(),
            'path_privat' => $path,
            'tipe_file' => $file->getMimeType() ?? 'application/octet-stream',
            'ukuran_file' => $file->getSize(),
            'created_at' => now(),
        ]);
    }
}
