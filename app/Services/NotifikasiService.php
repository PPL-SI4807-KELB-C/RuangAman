<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * PBI-18: Service pembuat notifikasi in-app otomatis untuk kejadian spesifik
 * (perubahan status kasus, penugasan kasus, jadwal konseling, kegiatan).
 *
 * Notifikasi TIDAK digunakan untuk menerbitkan pengumuman umum/broadcast (FR-29, FR-31).
 */
class NotifikasiService
{
    /**
     * Membuat satu notifikasi untuk satu user, dan opsional mengirim salinan email.
     */
    public function kirim(int $userId, string $judul, string $pesan, string $tipe): ?Notifikasi
    {
        $user = User::query()->find($userId);

        if ($user === null || $user->status !== 'aktif') {
            return null;
        }

        $notifikasi = Notifikasi::create([
            'user_id' => $user->user_id,
            'judul' => mb_substr($judul, 0, 150),
            'pesan' => $pesan,
            'tipe' => mb_substr($tipe, 0, 50),
        ]);

        $this->kirimEmail($user, $notifikasi);

        return $notifikasi;
    }

    /**
     * Kejadian: status laporan kasus berubah -> beri tahu pelapor pemilik laporan.
     */
    public function statusKasusBerubah(?int $pelaporId, string $kodeLaporan, string $statusAwal, string $statusTujuan, ?string $catatan = null): ?Notifikasi
    {
        if ($pelaporId === null) {
            return null;
        }

        if ($statusAwal === $statusTujuan) {
            $judul = "Tindak lanjut laporan {$kodeLaporan}";
            $pesan = "Tim penanganan meminta tindak lanjut pada laporan {$kodeLaporan}. Status laporan tetap {$statusTujuan}.";
        } else {
            $judul = "Status laporan {$kodeLaporan} diperbarui";
            $pesan = "Status laporan {$kodeLaporan} berubah dari {$statusAwal} menjadi {$statusTujuan}.";
        }

        if (trim((string) $catatan) !== '') {
            $pesan .= ' Catatan: '.trim((string) $catatan);
        }

        return $this->kirim($pelaporId, $judul, $pesan, Notifikasi::TIPE_STATUS_BERUBAH);
    }

    /**
     * Kejadian: kasus ditugaskan ke petugas/konselor -> beri tahu petugas.
     * Pesan hanya memuat kode laporan agar identitas pelapor tetap terlindungi.
     */
    public function kasusDitugaskan(?int $petugasId, string $kodeLaporan): ?Notifikasi
    {
        if ($petugasId === null) {
            return null;
        }

        return $this->kirim(
            $petugasId,
            "Penugasan kasus {$kodeLaporan}",
            "Anda ditugaskan sebagai petugas/konselor pendamping untuk laporan {$kodeLaporan}. Silakan tinjau detail penanganan kasus.",
            Notifikasi::TIPE_PENUGASAN_KASUS
        );
    }

    /**
     * Kejadian: petugas/konselor dilepas dari penugasan kasus.
     */
    public function penugasanDicabut(?int $petugasId, string $kodeLaporan): ?Notifikasi
    {
        if ($petugasId === null) {
            return null;
        }

        return $this->kirim(
            $petugasId,
            "Penugasan kasus {$kodeLaporan} dicabut",
            "Anda tidak lagi ditugaskan pada laporan {$kodeLaporan}.",
            Notifikasi::TIPE_PENUGASAN_KASUS
        );
    }

    /**
     * Kejadian: laporan pelapor mendapatkan pendamping -> beri tahu pelapor.
     */
    public function pendampingDitetapkan(?int $pelaporId, string $kodeLaporan): ?Notifikasi
    {
        if ($pelaporId === null) {
            return null;
        }

        return $this->kirim(
            $pelaporId,
            "Pendamping laporan {$kodeLaporan} ditetapkan",
            "Laporan {$kodeLaporan} telah mendapatkan petugas/konselor pendamping dan sedang ditindaklanjuti.",
            Notifikasi::TIPE_PENUGASAN_KASUS
        );
    }

    /**
     * Kejadian: jadwal konseling dikonfirmasi atau diubah.
     *
     * @param  string  $aksi  contoh: 'dikonfirmasi', 'diubah', 'dibatalkan'
     */
    public function jadwalKonseling(int $userId, string $aksi, string $tanggal, string $waktu, ?string $keterangan = null): ?Notifikasi
    {
        $pesan = "Jadwal konseling Anda pada {$tanggal} pukul {$waktu} telah {$aksi}.";

        if (trim((string) $keterangan) !== '') {
            $pesan .= ' '.trim((string) $keterangan);
        }

        return $this->kirim($userId, "Jadwal konseling {$aksi}", $pesan, Notifikasi::TIPE_JADWAL_KONSELING);
    }

    /**
     * Kejadian: pendaftaran kegiatan edukasi diterima / kehadiran diverifikasi.
     */
    public function kegiatan(int $userId, string $judulKegiatan, string $pesan): ?Notifikasi
    {
        return $this->kirim($userId, "Kegiatan: {$judulKegiatan}", $pesan, Notifikasi::TIPE_KEGIATAN);
    }

    /**
     * Mengirim email pemberitahuan generik bila diaktifkan. Kegagalan email
     * tidak boleh menggagalkan proses bisnis utama.
     */
    private function kirimEmail(User $user, Notifikasi $notifikasi): void
    {
        if (! config('notifikasi.kirim_email') || empty($user->email)) {
            return;
        }

        try {
            Mail::raw(
                "Halo,\n\nAda pemberitahuan baru di akun RuangAman Anda.\n\n"
                .'Silakan masuk ke RuangAman untuk melihat detailnya: '.route('notifikasi.index')."\n\n"
                ."Demi keamanan, detail kasus tidak dicantumkan pada email ini.\n\n— RuangAman",
                function ($message) use ($user) {
                    $message->to($user->email)->subject('Pemberitahuan baru dari RuangAman');
                }
            );
        } catch (Throwable $e) {
            Log::warning('Gagal mengirim email notifikasi.', [
                'notifikasi_id' => $notifikasi->notifikasi_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
