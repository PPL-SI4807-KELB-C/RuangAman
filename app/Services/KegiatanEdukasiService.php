<?php

namespace App\Services;

use App\Models\KegiatanEdukasi;
use App\Models\PendaftaranKegiatan;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class KegiatanEdukasiService
{
    /**
     * Menghitung jumlah kuota terpakai berdasarkan status 'aktif' dan 'hadir'.
     */
    public function hitungKuotaTerisi(KegiatanEdukasi $kegiatan): int
    {
        return PendaftaranKegiatan::query()
            ->where('kegiatan_id', $kegiatan->kegiatan_id)
            ->whereIn('status_pendaftaran', [
                PendaftaranKegiatan::STATUS_AKTIF,
                PendaftaranKegiatan::STATUS_HADIR,
            ])
            ->count();
    }

    /**
     * Menghitung sisa kuota kegiatan yang tersedia.
     */
    public function sisaKuota(KegiatanEdukasi $kegiatan): int
    {
        return max(0, (int) $kegiatan->kuota - $this->hitungKuotaTerisi($kegiatan));
    }

    /**
     * Mendaftarkan pelapor ke suatu kegiatan dalam transaksi database terkunci
     * untuk mencegah race condition kuota dan duplikasi.
     *
     * @throws DomainException
     */
    public function daftar(KegiatanEdukasi $kegiatan, User $user): PendaftaranKegiatan
    {
        return DB::transaction(function () use ($kegiatan, $user): PendaftaranKegiatan {
            /** @var KegiatanEdukasi $lockedKegiatan */
            $lockedKegiatan = KegiatanEdukasi::query()
                ->where('kegiatan_id', $kegiatan->kegiatan_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedKegiatan->status !== KegiatanEdukasi::STATUS_TAYANG) {
                throw new DomainException('Pendaftaran tidak tersedia karena kegiatan belum tayang atau telah ditutup.');
            }

            if ($lockedKegiatan->sudahDimulai()) {
                throw new DomainException('Pendaftaran ditutup karena waktu pelaksanaan kegiatan telah dimulai atau berakhir.');
            }

            /** @var PendaftaranKegiatan|null $existing */
            $existing = PendaftaranKegiatan::query()
                ->where('kegiatan_id', $lockedKegiatan->kegiatan_id)
                ->where('user_id', $user->user_id)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->status_pendaftaran !== PendaftaranKegiatan::STATUS_DIBATALKAN) {
                throw new DomainException('Anda sudah terdaftar pada kegiatan ini.');
            }

            $kuotaTerisi = $this->hitungKuotaTerisi($lockedKegiatan);
            if ($kuotaTerisi >= (int) $lockedKegiatan->kuota) {
                throw new DomainException('Maaf, kuota peserta untuk kegiatan ini sudah penuh.');
            }

            if ($existing) {
                $existing->update([
                    'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
                ]);

                return $existing->fresh();
            }

            return PendaftaranKegiatan::query()->create([
                'kegiatan_id' => $lockedKegiatan->kegiatan_id,
                'user_id' => $user->user_id,
                'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
            ]);
        });
    }

    /**
     * Membatalkan pendaftaran milik pelapor jika berstatus aktif dan kegiatan belum dimulai.
     *
     * @throws DomainException
     */
    public function batalkanPendaftaran(PendaftaranKegiatan $pendaftaran, User $user): PendaftaranKegiatan
    {
        return DB::transaction(function () use ($pendaftaran, $user): PendaftaranKegiatan {
            /** @var PendaftaranKegiatan $lockedPendaftaran */
            $lockedPendaftaran = PendaftaranKegiatan::query()
                ->with('kegiatanEdukasi')
                ->where('pendaftaran_id', $pendaftaran->pendaftaran_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $lockedPendaftaran->user_id !== (int) $user->user_id) {
                throw new DomainException('Anda tidak berhak membatalkan pendaftaran milik peserta lain.');
            }

            if ($lockedPendaftaran->status_pendaftaran !== PendaftaranKegiatan::STATUS_AKTIF) {
                throw new DomainException('Hanya pendaftaran yang masih aktif yang dapat dibatalkan.');
            }

            if ($lockedPendaftaran->kegiatanEdukasi->sudahDimulai()) {
                throw new DomainException('Pendaftaran tidak dapat dibatalkan karena kegiatan sudah dimulai.');
            }

            $lockedPendaftaran->update([
                'status_pendaftaran' => PendaftaranKegiatan::STATUS_DIBATALKAN,
            ]);

            return $lockedPendaftaran->fresh();
        });
    }

    /**
     * Memverifikasi kehadiran peserta ('hadir' atau 'tidak_hadir') oleh Admin Kasus
     * setelah waktu mulai kegiatan berlangsung.
     *
     * @throws DomainException
     */
    public function verifikasiKehadiran(PendaftaranKegiatan $pendaftaran, string $statusKehadiran): PendaftaranKegiatan
    {
        if (! in_array($statusKehadiran, [
            PendaftaranKegiatan::STATUS_HADIR,
            PendaftaranKegiatan::STATUS_TIDAK_HADIR,
        ], true)) {
            throw new DomainException('Status kehadiran hanya boleh diisi "hadir" atau "tidak_hadir".');
        }

        return DB::transaction(function () use ($pendaftaran, $statusKehadiran): PendaftaranKegiatan {
            /** @var PendaftaranKegiatan $lockedPendaftaran */
            $lockedPendaftaran = PendaftaranKegiatan::query()
                ->with('kegiatanEdukasi')
                ->where('pendaftaran_id', $pendaftaran->pendaftaran_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($lockedPendaftaran->kegiatanEdukasi->status, [
                KegiatanEdukasi::STATUS_TAYANG,
                KegiatanEdukasi::STATUS_SELESAI,
            ], true)) {
                throw new DomainException('Verifikasi kehadiran tidak tersedia untuk kegiatan berstatus draft atau dibatalkan.');
            }

            if (! $lockedPendaftaran->kegiatanEdukasi->sudahDimulai()) {
                throw new DomainException('Verifikasi kehadiran hanya dapat dilakukan setelah kegiatan berlangsung.');
            }

            if ($lockedPendaftaran->status_pendaftaran === PendaftaranKegiatan::STATUS_DIBATALKAN) {
                throw new DomainException('Pendaftaran yang sudah dibatalkan oleh peserta tidak dapat diverifikasi kehadirannya.');
            }

            $lockedPendaftaran->update([
                'status_pendaftaran' => $statusKehadiran,
            ]);

            return $lockedPendaftaran->fresh();
        });
    }

    /**
     * Menghapus permanen kegiatan jika belum memiliki pendaftar.
     * Diblokir dengan DomainException apabila sudah memiliki pendaftar.
     *
     * @throws DomainException
     */
    public function hapusPermanen(KegiatanEdukasi $kegiatan): void
    {
        DB::transaction(function () use ($kegiatan): void {
            /** @var KegiatanEdukasi $lockedKegiatan */
            $lockedKegiatan = KegiatanEdukasi::query()
                ->where('kegiatan_id', $kegiatan->kegiatan_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedKegiatan->pendaftaranKegiatan()->exists()) {
                throw new DomainException(
                    "Kegiatan \"{$lockedKegiatan->judul}\" tidak dapat dihapus permanen karena sudah memiliki peserta terdaftar. Gunakan fitur nonaktifkan (status Dibatalkan atau Draft)."
                );
            }

            $lockedKegiatan->delete();
        });
    }

    /**
     * Menonaktifkan kegiatan menjadi status 'dibatalkan' atau 'draft'.
     */
    public function nonaktifkan(KegiatanEdukasi $kegiatan, string $targetStatus = KegiatanEdukasi::STATUS_DIBATALKAN): KegiatanEdukasi
    {
        if (! in_array($targetStatus, [KegiatanEdukasi::STATUS_DIBATALKAN, KegiatanEdukasi::STATUS_DRAFT], true)) {
            $targetStatus = KegiatanEdukasi::STATUS_DIBATALKAN;
        }

        $kegiatan->update(['status' => $targetStatus]);

        return $kegiatan->fresh();
    }
}
