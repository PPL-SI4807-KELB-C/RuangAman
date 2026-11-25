<?php

namespace App\Services;

use App\Models\LaporanKasus;
use App\Models\LogPenanganan;
use App\Models\PenangananKasus;
use App\Models\User;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LogPenangananService
{
    /**
     * Daftar transisi status FR-15 yang didukung oleh hook log sistem otomatis.
     *
     * @var list<string>
     */
    public const TRANSISI_VALID = [
        'Pending->Proses',
        'Proses->Selesai',
        'Pending->Ditolak',
    ];

    /**
     * Mencatat aktivitas manual baru pada suatu penanganan kasus aktif.
     *
     * @throws DomainException
     */
    public function buatLogManual(
        PenangananKasus $penanganan,
        User $aktor,
        string $aktivitas,
        mixed $waktu = null
    ): LogPenanganan {
        return DB::transaction(function () use ($penanganan, $aktor, $aktivitas, $waktu): LogPenanganan {
            /** @var PenangananKasus $lockedPenanganan */
            $lockedPenanganan = PenangananKasus::query()
                ->with('laporanKasus.statusKasus')
                ->where('penanganan_id', $penanganan->penanganan_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedPenanganan->isKasusAktif()) {
                throw new DomainException('Catatan manual tidak dapat ditambahkan karena kasus sudah selesai atau tidak aktif.');
            }

            return LogPenanganan::query()->create([
                'penanganan_id' => $lockedPenanganan->penanganan_id,
                'laporan_id' => $lockedPenanganan->laporan_id,
                'user_id' => $aktor->user_id,
                'jenis_log' => LogPenanganan::JENIS_MANUAL,
                'aktivitas' => trim($aktivitas),
                'waktu' => $waktu ? Carbon::parse($waktu) : now(),
            ]);
        });
    }

    /**
     * Memperbarui catatan log manual selama bukan log sistem dan kasus masih aktif.
     *
     * @throws DomainException
     */
    public function perbaruiLogManual(LogPenanganan $log, string $aktivitas, mixed $waktu = null): LogPenanganan
    {
        return DB::transaction(function () use ($log, $aktivitas, $waktu): LogPenanganan {
            /** @var LogPenanganan $lockedLog */
            $lockedLog = LogPenanganan::query()
                ->with(['penangananKasus.laporanKasus.statusKasus', 'laporanKasus.statusKasus'])
                ->where('log_id', $log->log_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedLog->isSistem()) {
                throw new DomainException('Entri log sistem bersifat permanen dan tidak dapat diubah.');
            }

            if (! $lockedLog->isKasusAktif()) {
                throw new DomainException('Catatan manual tidak dapat diubah karena kasus sudah selesai atau tidak aktif.');
            }

            $payload = [
                'aktivitas' => trim($aktivitas),
                'jenis_log' => LogPenanganan::JENIS_MANUAL,
            ];

            if ($waktu !== null && $waktu !== '') {
                $payload['waktu'] = Carbon::parse($waktu);
            }

            $lockedLog->update($payload);

            return $lockedLog->fresh();
        });
    }

    /**
     * Menghapus catatan log manual selama bukan log sistem dan kasus masih aktif.
     *
     * @throws DomainException
     */
    public function hapusLogManual(LogPenanganan $log): void
    {
        DB::transaction(function () use ($log): void {
            /** @var LogPenanganan $lockedLog */
            $lockedLog = LogPenanganan::query()
                ->with(['penangananKasus.laporanKasus.statusKasus', 'laporanKasus.statusKasus'])
                ->where('log_id', $log->log_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedLog->isSistem()) {
                throw new DomainException('Entri log sistem bersifat permanen dan tidak dapat dihapus.');
            }

            if (! $lockedLog->isKasusAktif()) {
                throw new DomainException('Catatan manual tidak dapat dihapus karena kasus sudah selesai atau tidak aktif.');
            }

            $lockedLog->delete();
        });
    }

    /**
     * Mencatat log sistem otomatis secara langsung untuk penanganan tertentu.
     */
    public function catatLogSistem(int $penangananId, int $userId, string $aktivitas): LogPenanganan
    {
        /** @var PenangananKasus $penanganan */
        $penanganan = PenangananKasus::query()->findOrFail($penangananId);

        return LogPenanganan::query()->create([
            'penanganan_id' => $penanganan->penanganan_id,
            'laporan_id' => $penanganan->laporan_id,
            'user_id' => $userId,
            'jenis_log' => LogPenanganan::JENIS_SISTEM,
            'aktivitas' => trim($aktivitas),
            'waktu' => now(),
        ]);
    }

    /**
     * Hook integrasi aman bagi workflow FR-15 (CaseApprovalService) untuk mencatat
     * log sistem pada transisi status yang diizinkan.
     *
     * @throws InvalidArgumentException
     */
    public function catatTransisiStatus(
        string $statusAsal,
        string $statusTujuan,
        int $userId,
        ?int $penangananId = null,
        ?int $laporanId = null,
        ?string $catatan = null
    ): LogPenanganan {
        $asal = ucfirst(strtolower(trim($statusAsal)));
        $tujuan = ucfirst(strtolower(trim($statusTujuan)));
        $kunciTransisi = "{$asal}->{$tujuan}";

        if (! in_array($kunciTransisi, self::TRANSISI_VALID, true)) {
            throw new InvalidArgumentException("Transisi status {$asal} ke {$tujuan} tidak valid untuk pencatatan log sistem FR-15.");
        }

        $aktivitas = "Status kasus berubah dari {$asal} menjadi {$tujuan}.";
        if (trim((string) $catatan) !== '') {
            $aktivitas .= ' Catatan: '.trim((string) $catatan);
        }

        if ($penangananId !== null) {
            return $this->catatLogSistem($penangananId, $userId, $aktivitas);
        }

        if ($kunciTransisi !== 'Pending->Ditolak' || $laporanId === null) {
            throw new InvalidArgumentException('Transisi selain Pending ke Ditolak wajib menyertakan penanganan_id yang valid.');
        }

        /** @var LaporanKasus $laporan */
        $laporan = LaporanKasus::query()->findOrFail($laporanId);

        return LogPenanganan::query()->create([
            'penanganan_id' => null,
            'laporan_id' => $laporan->laporan_id,
            'user_id' => $userId,
            'jenis_log' => LogPenanganan::JENIS_SISTEM,
            'aktivitas' => $aktivitas,
            'waktu' => now(),
        ]);
    }

    public function catatPendingKeProses(int $penangananId, int $userId, ?string $namaPetugas = null): LogPenanganan
    {
        $catatan = $namaPetugas ? "Kasus ditugaskan kepada pendamping ({$namaPetugas})." : null;

        return $this->catatTransisiStatus('Pending', 'Proses', $userId, $penangananId, null, $catatan);
    }

    public function catatProsesKeSelesai(int $penangananId, int $userId, ?string $ringkasan = null): LogPenanganan
    {
        return $this->catatTransisiStatus('Proses', 'Selesai', $userId, $penangananId, null, $ringkasan);
    }

    public function catatPendingKeDitolak(int $laporanId, int $userId, string $alasanPenolakan, ?int $penangananId = null): LogPenanganan
    {
        return $this->catatTransisiStatus('Pending', 'Ditolak', $userId, $penangananId, $laporanId, $alasanPenolakan);
    }
}
