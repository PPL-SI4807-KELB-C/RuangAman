<?php

namespace App\Services;

use App\Models\LampiranLaporan;
use App\Models\LaporanKasus;
use App\Models\StatusKasus;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

    /**
     * Mengubah laporan milik pelapor. Status dicek ulang di dalam transaksi
     * (dengan kunci baris) agar tidak bertabrakan dengan persetujuan Admin.
     *
     * @param  array<string, mixed>  $data
     * @param  UploadedFile[]  $lampiran
     */
    public function update(LaporanKasus $laporan, array $data, array $lampiran = []): LaporanKasus
    {
        return DB::transaction(function () use ($laporan, $data, $lampiran) {
            $terkunci = LaporanKasus::query()
                ->with('status')
                ->lockForUpdate()
                ->findOrFail($laporan->laporan_id);

            abort_unless($terkunci->isPending(), 403, 'Laporan tidak lagi berstatus Pending.');

            $this->pastikanKuotaLampiran($terkunci, count($lampiran));

            $terkunci->update(Arr::only($data, [
                'judul',
                'kategori_kekerasan',
                'lokasi_kejadian',
                'kronologi',
                'waktu_kejadian',
                'tingkat_urgensi',
                'mode_anonim',
            ]));

            foreach ($lampiran as $file) {
                $this->storeLampiran($terkunci, $file);
            }

            return $terkunci;
        });
    }

    /**
     * Membatalkan (menghapus) laporan yang masih Pending beserta berkas buktinya.
     */
    public function cancel(LaporanKasus $laporan): void
    {
        $direktori = $this->direktoriLampiran($laporan);

        DB::transaction(function () use ($laporan) {
            $terkunci = LaporanKasus::query()
                ->with('status')
                ->lockForUpdate()
                ->findOrFail($laporan->laporan_id);

            abort_unless($terkunci->isPending(), 403, 'Laporan tidak lagi berstatus Pending.');

            $terkunci->lampirans()->delete();
            $terkunci->delete();
        });

        // Berkas dihapus setelah transaksi berhasil agar tidak ada berkas hilang bila rollback.
        Storage::disk('local')->deleteDirectory($direktori);
    }

    public function deleteLampiran(LampiranLaporan $lampiran): void
    {
        $path = $lampiran->path_privat;

        DB::transaction(fn () => $lampiran->delete());

        Storage::disk('local')->delete($path);
    }

    private function generateKodeLaporan(): string
    {
        // Format sesuai acceptance criteria PBI-16 dan SECURITY.md: RA-YYYY-XXXXXX.
        $prefix = 'RA-'.now()->year.'-';

        do {
            $code = $prefix.Str::upper(Str::random(6));
        } while (LaporanKasus::query()->where('kode_laporan', $code)->exists());

        return $code;
    }

    private function direktoriLampiran(LaporanKasus $laporan): string
    {
        return 'private/laporan/' . $laporan->laporan_id;
    }

    private function pastikanKuotaLampiran(LaporanKasus $laporan, int $jumlahBaru): void
    {
        if ($laporan->lampirans()->count() + $jumlahBaru > LaporanKasus::MAKS_LAMPIRAN) {
            throw ValidationException::withMessages([
                'lampiran' => 'Maksimal ' . LaporanKasus::MAKS_LAMPIRAN . ' lampiran bukti per laporan.',
            ]);
        }
    }

    private function storeLampiran(LaporanKasus $laporan, UploadedFile $file): void
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($originalName) . '-' . Str::random(8) . '.' . $extension;
        $directory = $this->direktoriLampiran($laporan);

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
