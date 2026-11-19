<?php

namespace App\Services;

use App\Models\Konselor;
use App\Models\User;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class KonselorService
{
    /**
     * Membuat profil konselor baru untuk akun ber-role Konselor.
     * Satu akun hanya boleh memiliki satu profil konselor.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws DomainException
     */
    public function buat(array $data): Konselor
    {
        return DB::transaction(function () use ($data): Konselor {
            /** @var User|null $user */
            $user = User::query()
                ->with('role')
                ->where('user_id', $data['user_id'])
                ->lockForUpdate()
                ->first();

            if (! $user) {
                throw new DomainException('Akun pengguna yang dipilih tidak ditemukan.');
            }

            if (! $user->isKonselor()) {
                throw new DomainException('Akun yang dipilih bukan pengguna dengan role Konselor.');
            }

            if (Konselor::query()->where('user_id', $user->user_id)->exists()) {
                throw new DomainException('Akun tersebut sudah memiliki profil konselor.');
            }

            return Konselor::query()->create([
                'user_id' => $user->user_id,
                'nama' => $data['nama'],
                'spesialisasi' => $data['spesialisasi'],
                'bio' => $data['bio'] ?? null,
                'status' => $data['status'],
            ]);
        });
    }

    /**
     * Memperbarui biodata dan status ketersediaan konselor oleh Admin Kasus.
     *
     * @param  array<string, mixed>  $data
     */
    public function perbarui(Konselor $konselor, array $data): Konselor
    {
        $konselor->update([
            'nama' => $data['nama'],
            'spesialisasi' => $data['spesialisasi'],
            'bio' => $data['bio'] ?? null,
            'status' => $data['status'],
        ]);

        return $konselor->fresh();
    }

    /**
     * Konselor memperbarui biodata profesionalnya sendiri tanpa mengubah
     * status ketersediaan maupun akun pengguna.
     *
     * @param  array<string, mixed>  $data
     */
    public function perbaruiProfil(Konselor $konselor, array $data): Konselor
    {
        $konselor->update([
            'nama' => $data['nama'],
            'spesialisasi' => $data['spesialisasi'],
            'bio' => $data['bio'] ?? null,
        ]);

        return $konselor->fresh();
    }

    /**
     * Menghitung jumlah slot jadwal konseling yang dimiliki konselor.
     * Hanya membaca tabel milik PBI-14 sebagai dasar proteksi penghapusan.
     */
    public function jumlahJadwal(Konselor $konselor): int
    {
        return DB::table('jadwal_konseling')
            ->where('konselor_id', $konselor->konselor_id)
            ->count();
    }

    /**
     * Menghapus profil konselor jika belum memiliki slot jadwal konseling.
     * Profil yang sudah dipakai pada jadwal harus dinonaktifkan, bukan dihapus,
     * agar riwayat layanan konseling tetap utuh.
     *
     * @throws DomainException
     */
    public function hapusPermanen(Konselor $konselor): void
    {
        DB::transaction(function () use ($konselor): void {
            /** @var Konselor $lockedKonselor */
            $lockedKonselor = Konselor::query()
                ->where('konselor_id', $konselor->konselor_id)
                ->lockForUpdate()
                ->firstOrFail();

            $jumlahJadwal = $this->jumlahJadwal($lockedKonselor);

            if ($jumlahJadwal > 0) {
                throw new DomainException(
                    "Profil konselor \"{$lockedKonselor->nama}\" tidak dapat dihapus karena sudah memiliki {$jumlahJadwal} slot jadwal konseling. Ubah status menjadi Nonaktif sebagai gantinya."
                );
            }

            $lockedKonselor->delete();
        });
    }

    /**
     * Daftar akun ber-role Konselor yang belum memiliki profil konselor.
     *
     * @return Collection<int, User>
     */
    public function calonPengguna(): Collection
    {
        return User::query()
            ->whereHas('role', fn ($query) => $query->where('nama_role', 'Konselor'))
            ->whereDoesntHave('konselor')
            ->orderBy('nama_tampilan')
            ->get(['user_id', 'nama_tampilan', 'status']);
    }
}
