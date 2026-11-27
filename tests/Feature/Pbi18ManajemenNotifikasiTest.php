<?php

namespace Tests\Feature;

use App\Models\KegiatanEdukasi;
use App\Models\LaporanKasus;
use App\Models\Notifikasi;
use App\Models\PenangananKasus;
use App\Models\PendaftaranKegiatan;
use App\Models\StatusKasus;
use App\Models\User;
use App\Services\NotifikasiService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Pbi18ManajemenNotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $konselor;
    private User $pelapor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@ruangaman.test')->firstOrFail();
        $this->konselor = User::where('email', 'konselor@ruangaman.test')->firstOrFail();
        $this->pelapor = User::where('email', 'pelapor@ruangaman.test')->firstOrFail();

        // Mulai dari kondisi bersih (abaikan data contoh seeder).
        Notifikasi::query()->delete();
    }

    private function buatNotifikasi(User $user, array $atribut = []): Notifikasi
    {
        return Notifikasi::create(array_merge([
            'user_id' => $user->user_id,
            'judul' => 'Status laporan RA-TEST diperbarui',
            'pesan' => 'Status laporan RA-TEST berubah dari Pending menjadi Proses.',
            'tipe' => Notifikasi::TIPE_STATUS_BERUBAH,
        ], $atribut));
    }

    private function buatLaporan(string $kode = 'RA-TEST-0001'): int
    {
        return (int) DB::table('laporan_kasus')->insertGetId([
            'user_id' => $this->pelapor->user_id,
            'status_id' => StatusKasus::where('nama_status', 'Pending')->value('status_id'),
            'kode_laporan' => $kode,
            'judul' => 'Laporan uji',
            'kategori_kekerasan' => 'Psikis',
            'lokasi_kejadian' => 'Kampus',
            'kronologi' => 'Kronologi uji.',
            'waktu_kejadian' => now()->subDay(),
            'tingkat_urgensi' => 'sedang',
            'mode_anonim' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // ---------------------------------------------------------------
    // Akses & Read
    // ---------------------------------------------------------------

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get(route('notifikasi.index'))->assertRedirect(route('login'));
    }

    public function test_semua_peran_dapat_membuka_daftar_notifikasi(): void
    {
        foreach ([$this->admin, $this->konselor, $this->pelapor] as $user) {
            $this->actingAs($user)->get(route('notifikasi.index'))->assertOk()->assertSee('Notifikasi Saya');
        }
    }

    public function test_daftar_hanya_menampilkan_notifikasi_milik_sendiri(): void
    {
        $this->buatNotifikasi($this->pelapor, ['judul' => 'Milik Pelapor']);
        $this->buatNotifikasi($this->konselor, ['judul' => 'Milik Konselor']);

        $this->actingAs($this->pelapor)
            ->get(route('notifikasi.index'))
            ->assertOk()
            ->assertSee('Milik Pelapor')
            ->assertDontSee('Milik Konselor');
    }

    public function test_filter_belum_dibaca_dan_sudah_dibaca(): void
    {
        $this->buatNotifikasi($this->pelapor, ['judul' => 'Notif Baru']);
        $this->buatNotifikasi($this->pelapor, ['judul' => 'Notif Lama', 'read_at' => now()]);

        $this->actingAs($this->pelapor)
            ->get(route('notifikasi.index', ['status' => 'belum-dibaca']))
            ->assertSee('Notif Baru')
            ->assertDontSee('Notif Lama');

        $this->actingAs($this->pelapor)
            ->get(route('notifikasi.index', ['status' => 'dibaca']))
            ->assertSee('Notif Lama')
            ->assertDontSee('Notif Baru');
    }

    public function test_membuka_detail_menandai_notifikasi_dibaca(): void
    {
        $notifikasi = $this->buatNotifikasi($this->pelapor);

        $this->actingAs($this->pelapor)
            ->get(route('notifikasi.show', $notifikasi))
            ->assertOk()
            ->assertSee($notifikasi->judul);

        $this->assertNotNull($notifikasi->fresh()->read_at);
    }

    public function test_navbar_menampilkan_jumlah_belum_dibaca(): void
    {
        $this->buatNotifikasi($this->pelapor);
        $this->buatNotifikasi($this->pelapor);

        $this->actingAs($this->pelapor)
            ->get(route('notifikasi.index'))
            ->assertSee('Notifikasi (2 belum dibaca)', false);
    }

    // ---------------------------------------------------------------
    // Update: tandai dibaca / belum dibaca
    // ---------------------------------------------------------------

    public function test_pengguna_dapat_menandai_dibaca_dan_belum_dibaca(): void
    {
        $notifikasi = $this->buatNotifikasi($this->pelapor);

        $this->actingAs($this->pelapor)
            ->patch(route('notifikasi.baca', $notifikasi))
            ->assertRedirect()
            ->assertSessionHas('success');
        $this->assertNotNull($notifikasi->fresh()->read_at);

        $this->actingAs($this->pelapor)
            ->patch(route('notifikasi.belum-dibaca', $notifikasi))
            ->assertRedirect(route('notifikasi.index'));
        $this->assertNull($notifikasi->fresh()->read_at);
    }

    public function test_tandai_semua_dibaca_hanya_milik_sendiri(): void
    {
        $this->buatNotifikasi($this->pelapor);
        $this->buatNotifikasi($this->pelapor);
        $lain = $this->buatNotifikasi($this->konselor);

        $this->actingAs($this->pelapor)->patch(route('notifikasi.baca-semua'))->assertRedirect();

        $this->assertSame(0, Notifikasi::where('user_id', $this->pelapor->user_id)->whereNull('read_at')->count());
        $this->assertNull($lain->fresh()->read_at);
    }

    // ---------------------------------------------------------------
    // Delete
    // ---------------------------------------------------------------

    public function test_pengguna_dapat_menghapus_notifikasi_miliknya(): void
    {
        $notifikasi = $this->buatNotifikasi($this->pelapor);

        $this->actingAs($this->pelapor)
            ->delete(route('notifikasi.destroy', $notifikasi))
            ->assertRedirect(route('notifikasi.index'));

        $this->assertDatabaseMissing('notifikasi', ['notifikasi_id' => $notifikasi->notifikasi_id]);
    }

    public function test_hapus_sudah_dibaca_tidak_menghapus_yang_belum_dibaca_atau_milik_orang_lain(): void
    {
        $dibaca = $this->buatNotifikasi($this->pelapor, ['read_at' => now()]);
        $belum = $this->buatNotifikasi($this->pelapor);
        $lain = $this->buatNotifikasi($this->konselor, ['read_at' => now()]);

        $this->actingAs($this->pelapor)->delete(route('notifikasi.hapus-dibaca'))->assertRedirect(route('notifikasi.index'));

        $this->assertDatabaseMissing('notifikasi', ['notifikasi_id' => $dibaca->notifikasi_id]);
        $this->assertDatabaseHas('notifikasi', ['notifikasi_id' => $belum->notifikasi_id]);
        $this->assertDatabaseHas('notifikasi', ['notifikasi_id' => $lain->notifikasi_id]);
    }

    // ---------------------------------------------------------------
    // Privasi: tidak bocor ke user lain
    // ---------------------------------------------------------------

    public function test_pengguna_tidak_dapat_mengakses_notifikasi_milik_orang_lain(): void
    {
        $milikKonselor = $this->buatNotifikasi($this->konselor);

        $this->actingAs($this->pelapor)->get(route('notifikasi.show', $milikKonselor))->assertForbidden();
        $this->actingAs($this->pelapor)->patch(route('notifikasi.baca', $milikKonselor))->assertForbidden();
        $this->actingAs($this->pelapor)->patch(route('notifikasi.belum-dibaca', $milikKonselor))->assertForbidden();
        $this->actingAs($this->pelapor)->delete(route('notifikasi.destroy', $milikKonselor))->assertForbidden();

        $this->assertDatabaseHas('notifikasi', ['notifikasi_id' => $milikKonselor->notifikasi_id, 'read_at' => null]);
    }

    // ---------------------------------------------------------------
    // Create otomatis karena kejadian spesifik
    // ---------------------------------------------------------------

    public function test_perubahan_status_kasus_membuat_notifikasi_untuk_pelapor(): void
    {
        $laporan = LaporanKasus::findOrFail($this->buatLaporan('RA-TEST-0101'));

        $laporan->update(['status_id' => StatusKasus::where('nama_status', 'Proses')->value('status_id')]);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->pelapor->user_id,
            'tipe' => Notifikasi::TIPE_STATUS_BERUBAH,
            'judul' => 'Status laporan RA-TEST-0101 diperbarui',
            'pesan' => 'Status laporan RA-TEST-0101 berubah dari Pending menjadi Proses.',
        ]);
    }

    public function test_perubahan_kolom_lain_laporan_tidak_membuat_notifikasi(): void
    {
        LaporanKasus::findOrFail($this->buatLaporan('RA-TEST-0102'))->update(['judul' => 'Judul baru']);

        $this->assertSame(0, Notifikasi::count());
    }

    public function test_penugasan_kasus_oleh_admin_membuat_notifikasi(): void
    {
        $laporanId = $this->buatLaporan('RA-TEST-0202');

        $this->actingAs($this->admin)->post(route('admin.penanganan-kasus.store'), [
            'laporan_id' => $laporanId,
            'petugas_id' => $this->konselor->user_id,
            'tanggal_mulai' => now()->toDateString(),
            'catatan' => 'Penugasan awal',
        ])->assertRedirect();

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->konselor->user_id,
            'tipe' => Notifikasi::TIPE_PENUGASAN_KASUS,
            'judul' => 'Penugasan kasus RA-TEST-0202',
        ]);
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->pelapor->user_id,
            'tipe' => Notifikasi::TIPE_PENUGASAN_KASUS,
        ]);
    }

    public function test_pergantian_dan_penghapusan_petugas_membuat_notifikasi(): void
    {
        $laporanId = $this->buatLaporan('RA-TEST-0203');
        $penanganan = PenangananKasus::create([
            'laporan_id' => $laporanId,
            'petugas_id' => $this->konselor->user_id,
            'tanggal_mulai' => now()->toDateString(),
        ]);
        Notifikasi::query()->delete();

        $penanganan->update(['petugas_id' => $this->admin->user_id]);

        $this->assertDatabaseHas('notifikasi', ['user_id' => $this->admin->user_id, 'judul' => 'Penugasan kasus RA-TEST-0203']);
        $this->assertDatabaseHas('notifikasi', ['user_id' => $this->konselor->user_id, 'judul' => 'Penugasan kasus RA-TEST-0203 dicabut']);

        $penanganan->delete();

        $this->assertDatabaseHas('notifikasi', ['user_id' => $this->admin->user_id, 'judul' => 'Penugasan kasus RA-TEST-0203 dicabut']);
    }

    public function test_pendaftaran_dan_verifikasi_kegiatan_membuat_notifikasi(): void
    {
        $kegiatan = KegiatanEdukasi::create([
            'judul' => 'Webinar Uji Notifikasi',
            'deskripsi' => 'Deskripsi',
            'narasumber' => 'Narasumber',
            'tanggal_mulai' => now()->addWeek()->toDateString(),
            'waktu_mulai' => '09:00',
            'tanggal_selesai' => now()->addWeek()->toDateString(),
            'waktu_selesai' => '11:00',
            'metode' => 'online',
            'lokasi_atau_tautan' => 'https://contoh.test/webinar',
            'kuota' => 10,
            'status' => KegiatanEdukasi::STATUS_TAYANG,
        ]);

        $pendaftaran = PendaftaranKegiatan::create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelapor->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->pelapor->user_id,
            'tipe' => Notifikasi::TIPE_KEGIATAN,
            'judul' => 'Kegiatan: Webinar Uji Notifikasi',
        ]);

        $pendaftaran->update(['status_pendaftaran' => PendaftaranKegiatan::STATUS_HADIR]);
        $this->assertSame(2, Notifikasi::where('user_id', $this->pelapor->user_id)->count());

        // Pembatalan oleh pelapor sendiri tidak membuat notifikasi.
        $pendaftaran->update(['status_pendaftaran' => PendaftaranKegiatan::STATUS_DIBATALKAN]);
        $this->assertSame(2, Notifikasi::where('user_id', $this->pelapor->user_id)->count());
    }

    public function test_pesan_notifikasi_petugas_tidak_memuat_identitas_pelapor(): void
    {
        $notifikasi = app(NotifikasiService::class)->kasusDitugaskan((int) $this->konselor->user_id, 'RA-TEST-0303');

        $this->assertStringNotContainsString($this->pelapor->nama_tampilan, $notifikasi->pesan);
        $this->assertStringNotContainsString($this->pelapor->email, $notifikasi->pesan);
    }

    public function test_notifikasi_jadwal_konseling_dapat_dibuat(): void
    {
        $notifikasi = app(NotifikasiService::class)->jadwalKonseling((int) $this->pelapor->user_id, 'dikonfirmasi', '01 Des 2026', '10:00');

        $this->assertSame(Notifikasi::TIPE_JADWAL_KONSELING, $notifikasi->tipe);
        $this->assertNull($notifikasi->read_at);
    }

    public function test_email_dikirim_hanya_bila_diaktifkan(): void
    {
        // MAIL_MAILER=array pada phpunit.xml: email tersimpan di transport array.
        $transport = Mail::mailer()->getSymfonyTransport();

        config(['notifikasi.kirim_email' => false]);
        app(NotifikasiService::class)->kasusDitugaskan((int) $this->konselor->user_id, 'RA-TEST-0404');
        $this->assertCount(0, $transport->messages());

        config(['notifikasi.kirim_email' => true]);
        app(NotifikasiService::class)->kasusDitugaskan((int) $this->konselor->user_id, 'RA-TEST-0405');
        $this->assertCount(1, $transport->messages());

        $email = $transport->messages()->first()->getOriginalMessage();
        $this->assertSame($this->konselor->email, $email->getTo()[0]->getAddress());
        // Isi email generik: tidak memuat kode/detail kasus.
        $this->assertStringNotContainsString('RA-TEST-0405', $email->getTextBody());
    }

    public function test_tidak_ada_rute_untuk_membuat_notifikasi_secara_manual(): void
    {
        $this->actingAs($this->admin)
            ->post('/notifikasi', ['user_id' => $this->pelapor->user_id, 'judul' => 'Broadcast', 'pesan' => 'x', 'tipe' => 'pengumuman'])
            ->assertStatus(405);

        $this->assertDatabaseMissing('notifikasi', ['judul' => 'Broadcast']);
    }
}
