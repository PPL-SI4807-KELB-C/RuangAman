<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

/**
 * Service terpusat untuk alur persetujuan dan transisi status laporan kasus (FR-15).
 * Menulis secara atomik ke: laporan_kasus, penanganan_kasus, dan log_penanganan.
 */
class CaseApprovalService
{
    /**
     * Transisi yang sah menurut Tabel 4.4.1 proposal.
     */
    private const ALLOWED_TRANSITIONS = [
        'Pending' => ['Proses', 'Ditolak'],
        'Proses' => ['Selesai', 'Proses'],
    ];

    /**
     * Memproses transisi status laporan kasus secara aman dan tercatat dalam audit log.
     *
     * @param  string|null  $catatan  Wajib diisi saat menolak laporan atau meminta tindak lanjut.
     * @param  int|null  $assignedPetugasId  Wajib diisi saat transisi Pending -> Proses.
     *
     * @throws InvalidArgumentException Bila transisi tidak sah atau data wajib tidak lengkap.
     * @throws RuntimeException Bila laporan/status tujuan tidak ditemukan.
     */
    public function transitionStatus(
        int $laporanId,
        int $targetStatusId,
        int $actorUserId,
        ?string $catatan = null,
        ?int $assignedPetugasId = null
    ): bool {
        return DB::transaction(function () use ($laporanId, $targetStatusId, $actorUserId, $catatan, $assignedPetugasId) {
            $laporan = DB::table('laporan_kasus')
                ->where('laporan_id', $laporanId)
                ->lockForUpdate()
                ->first();

            if ($laporan === null) {
                throw new RuntimeException("Laporan kasus {$laporanId} tidak ditemukan.");
            }

            $statusAwal = $this->namaStatus((int) $laporan->status_id);
            $statusTujuan = $this->namaStatus($targetStatusId);

            if (! in_array($statusTujuan, self::ALLOWED_TRANSITIONS[$statusAwal] ?? [], true)) {
                throw new InvalidArgumentException("Transisi status {$statusAwal} ke {$statusTujuan} tidak diizinkan.");
            }

            if (($statusTujuan === 'Ditolak' || $this->isTindakLanjut($statusAwal, $statusTujuan)) && $this->catatanKosong($catatan)) {
                throw new InvalidArgumentException('Catatan wajib diisi untuk penolakan atau permintaan tindak lanjut.');
            }

            $penangananId = DB::table('penanganan_kasus')
                ->where('laporan_id', $laporanId)
                ->value('penanganan_id');

            if ($statusAwal === 'Pending' && $statusTujuan === 'Proses') {
                $penangananId = $this->tugaskanPetugas($laporanId, $penangananId, $assignedPetugasId, $catatan);
            } elseif ($this->isTindakLanjut($statusAwal, $statusTujuan)) {
                $this->catatTindakLanjut($penangananId, $catatan);
            } elseif ($statusTujuan === 'Selesai') {
                $this->tutupPenanganan($penangananId);
            }

            DB::table('laporan_kasus')
                ->where('laporan_id', $laporanId)
                ->update(['status_id' => $targetStatusId, 'updated_at' => now()]);

            $this->catatLog($laporanId, $penangananId, $actorUserId, $statusAwal, $statusTujuan, $catatan);

            return true;
        });
    }

    private function namaStatus(int $statusId): string
    {
        $nama = DB::table('status_kasus')->where('status_id', $statusId)->value('nama_status');

        if ($nama === null) {
            throw new RuntimeException("Status kasus {$statusId} tidak ditemukan.");
        }

        return $nama;
    }

    private function isTindakLanjut(string $statusAwal, string $statusTujuan): bool
    {
        return $statusAwal === 'Proses' && $statusTujuan === 'Proses';
    }

    private function catatanKosong(?string $catatan): bool
    {
        return trim((string) $catatan) === '';
    }

    private function tugaskanPetugas(int $laporanId, ?int $penangananId, ?int $petugasId, ?string $catatan): int
    {
        if ($petugasId === null) {
            throw new InvalidArgumentException('Petugas/konselor wajib dipilih saat memverifikasi laporan.');
        }

        if (! DB::table('users')->where('user_id', $petugasId)->where('status', 'aktif')->exists()) {
            throw new InvalidArgumentException("Petugas {$petugasId} tidak ditemukan atau tidak aktif.");
        }

        if ($penangananId !== null) {
            DB::table('penanganan_kasus')->where('penanganan_id', $penangananId)->update([
                'petugas_id' => $petugasId,
                'catatan' => $catatan,
                'updated_at' => now(),
            ]);

            return (int) $penangananId;
        }

        return (int) DB::table('penanganan_kasus')->insertGetId([
            'laporan_id' => $laporanId,
            'petugas_id' => $petugasId,
            'tanggal_mulai' => now()->toDateString(),
            'catatan' => $catatan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function catatTindakLanjut(?int $penangananId, ?string $catatan): void
    {
        if ($penangananId === null) {
            throw new RuntimeException('Permintaan tindak lanjut membutuhkan penanganan kasus yang aktif.');
        }

        DB::table('penanganan_kasus')->where('penanganan_id', $penangananId)->update([
            'catatan' => $catatan,
            'updated_at' => now(),
        ]);
    }

    private function tutupPenanganan(?int $penangananId): void
    {
        if ($penangananId === null) {
            throw new RuntimeException('Kasus belum memiliki penanganan sehingga tidak dapat diselesaikan.');
        }

        DB::table('penanganan_kasus')->where('penanganan_id', $penangananId)->update([
            'tanggal_selesai' => now()->toDateString(),
            'updated_at' => now(),
        ]);
    }

    private function catatLog(
        int $laporanId,
        ?int $penangananId,
        int $actorUserId,
        string $statusAwal,
        string $statusTujuan,
        ?string $catatan
    ): void {
        $aktivitas = $this->isTindakLanjut($statusAwal, $statusTujuan)
            ? 'Admin meminta tindak lanjut; status kasus tetap Proses.'
            : "Status laporan diubah dari {$statusAwal} menjadi {$statusTujuan}.";

        if (! $this->catatanKosong($catatan)) {
            $aktivitas .= " Catatan: {$catatan}";
        }

        DB::table('log_penanganan')->insert([
            'laporan_id' => $laporanId,
            'penanganan_id' => $penangananId,
            'user_id' => $actorUserId,
            'jenis_log' => 'otomatis_status',
            'aktivitas' => $aktivitas,
            'waktu' => now(),
            'created_at' => now(),
        ]);
    }
}
