<?php

namespace Tests\Feature;

use App\Models\KegiatanEdukasi;
use App\Models\PendaftaranKegiatan;
use App\Models\Role;
use App\Models\User;
use App\Services\KegiatanEdukasiService;
use Database\Seeders\DatabaseSeeder;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Pbi05ManajemenKegiatanEdukasiTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private User $pelaporUser;

    private User $pelaporLain;

    private User $konselorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);

        $this->adminUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Admin Kasus'))->firstOrFail();
        $this->pelaporUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Pelapor'))->firstOrFail();
        $this->konselorUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Konselor'))->firstOrFail();

        $rolePelapor = Role::where('nama_role', 'Pelapor')->firstOrFail();
        $this->pelaporLain = User::query()->create([
            'role_id' => $rolePelapor->role_id,
            'nama_tampilan' => 'Anggrek Malam',
            'email' => 'pelapor2@ruangaman.test',
            'password_hash' => 'password',
            'status' => 'aktif',
        ]);
    }

    /**
     * @param  array<string, mixed>  $override
     */
    private function buatKegiatan(array $override = []): KegiatanEdukasi
    {
        return KegiatanEdukasi::query()->create(array_merge([
            'judul' => 'Webinar Edukasi Pencegahan KBG',
            'deskripsi' => 'Sesi edukasi daring mengenai pencegahan dan pelaporan aman.',
            'narasumber' => 'Dr. Siti Rahmawati',
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'waktu_mulai' => '10:00:00',
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'waktu_selesai' => '12:00:00',
            'metode' => KegiatanEdukasi::METODE_ONLINE,
            'lokasi_atau_tautan' => 'https://meet.ruangaman.test/rahasia-webinar',
            'kuota' => 10,
            'status' => KegiatanEdukasi::STATUS_TAYANG,
        ], $override));
    }

    #[Test]
    public function guest_cannot_access_admin_kegiatan_index(): void
    {
        $response = $this->get(route('admin.kegiatan-edukasi.index'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_create_kegiatan(): void
    {
        $this->get(route('admin.kegiatan-edukasi.create'))->assertRedirect(route('login'));

        $this->post(route('admin.kegiatan-edukasi.store'), [
            'judul' => 'Percobaan Guest',
            'tanggal_mulai' => now()->addDays(2)->toDateString(),
            'waktu_mulai' => '09:00',
            'metode' => KegiatanEdukasi::METODE_ONLINE,
            'kuota' => 10,
            'status' => KegiatanEdukasi::STATUS_TAYANG,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('kegiatan_edukasi', ['judul' => 'Percobaan Guest']);
    }

    #[Test]
    public function pelapor_cannot_access_admin_kegiatan_index(): void
    {
        $this->actingAs($this->pelaporUser)
            ->get(route('admin.kegiatan-edukasi.index'))
            ->assertForbidden();

        $this->actingAs($this->konselorUser)
            ->get(route('admin.kegiatan-edukasi.index'))
            ->assertForbidden();
    }

    #[Test]
    public function pelapor_cannot_create_kegiatan(): void
    {
        $this->actingAs($this->pelaporUser)
            ->get(route('admin.kegiatan-edukasi.create'))
            ->assertForbidden();

        $this->actingAs($this->pelaporUser)
            ->post(route('admin.kegiatan-edukasi.store'), [
                'judul' => 'Percobaan Pelapor',
                'tanggal_mulai' => now()->addDays(2)->toDateString(),
                'waktu_mulai' => '09:00',
                'metode' => KegiatanEdukasi::METODE_ONLINE,
                'kuota' => 10,
                'status' => KegiatanEdukasi::STATUS_TAYANG,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('kegiatan_edukasi', ['judul' => 'Percobaan Pelapor']);
    }

    #[Test]
    public function admin_kasus_can_view_kegiatan_index(): void
    {
        $tayang = $this->buatKegiatan(['judul' => 'Kegiatan Tayang Index', 'status' => KegiatanEdukasi::STATUS_TAYANG]);
        $draft = $this->buatKegiatan(['judul' => 'Kegiatan Draft Index', 'status' => KegiatanEdukasi::STATUS_DRAFT]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.kegiatan-edukasi.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Kegiatan');
        $response->assertSee($tayang->judul);
        $response->assertSee($draft->judul);
    }

    #[Test]
    public function admin_kasus_can_create_kegiatan(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.kegiatan-edukasi.store'), [
            'judul' => 'Workshop Hukum UU TPKS Terbaru',
            'deskripsi' => 'Pembahasan teknis pendampingan hukum.',
            'narasumber' => 'Adv. Andi Wijaya',
            'tanggal_mulai' => now()->addDays(10)->toDateString(),
            'waktu_mulai' => '09:00',
            'tanggal_selesai' => now()->addDays(10)->toDateString(),
            'waktu_selesai' => '11:30',
            'metode' => KegiatanEdukasi::METODE_OFFLINE,
            'lokasi_atau_tautan' => 'Ruang Auditorium Utama',
            'kuota' => 25,
            'status' => KegiatanEdukasi::STATUS_TAYANG,
        ]);

        $kegiatan = KegiatanEdukasi::where('judul', 'Workshop Hukum UU TPKS Terbaru')->firstOrFail();

        $response->assertRedirect(route('admin.kegiatan-edukasi.show', $kegiatan));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('kegiatan_edukasi', [
            'judul' => 'Workshop Hukum UU TPKS Terbaru',
            'metode' => KegiatanEdukasi::METODE_OFFLINE,
            'kuota' => 25,
            'status' => KegiatanEdukasi::STATUS_TAYANG,
        ]);
    }

    #[Test]
    public function admin_kasus_can_update_kegiatan(): void
    {
        $kegiatan = $this->buatKegiatan(['judul' => 'Judul Sebelum Update']);

        $response = $this->actingAs($this->adminUser)->put(route('admin.kegiatan-edukasi.update', $kegiatan), [
            'judul' => 'Judul Sesudah Update',
            'deskripsi' => 'Deskripsi diperbarui.',
            'narasumber' => 'Dr. Sari Dewi',
            'tanggal_mulai' => now()->addDays(8)->toDateString(),
            'waktu_mulai' => '14:00',
            'tanggal_selesai' => now()->addDays(8)->toDateString(),
            'waktu_selesai' => '16:00',
            'metode' => KegiatanEdukasi::METODE_ONLINE,
            'lokasi_atau_tautan' => 'https://meet.ruangaman.test/updated',
            'kuota' => 45,
            'status' => KegiatanEdukasi::STATUS_TAYANG,
        ]);

        $response->assertRedirect(route('admin.kegiatan-edukasi.show', $kegiatan));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('kegiatan_edukasi', [
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'judul' => 'Judul Sesudah Update',
            'kuota' => 45,
        ]);
    }

    #[Test]
    public function admin_kasus_cannot_set_kuota_below_active_participants(): void
    {
        $kegiatan = $this->buatKegiatan(['kuota' => 5]);

        PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);
        PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporLain->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('admin.kegiatan-edukasi.update', $kegiatan), [
            'judul' => $kegiatan->judul,
            'tanggal_mulai' => $kegiatan->tanggal_mulai->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'metode' => $kegiatan->metode,
            'kuota' => 1,
            'status' => $kegiatan->status,
        ]);

        $response->assertSessionHasErrors('kuota');
        $this->assertDatabaseHas('kegiatan_edukasi', [
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'kuota' => 5,
        ]);
    }

    #[Test]
    public function admin_kasus_cannot_delete_kegiatan_with_participants(): void
    {
        $kegiatan = $this->buatKegiatan(['judul' => 'Kegiatan Terdaftar Tidak Boleh Dihapus']);

        PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.kegiatan-edukasi.destroy', $kegiatan));

        $response->assertRedirect(route('admin.kegiatan-edukasi.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('kegiatan_edukasi', [
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'status' => KegiatanEdukasi::STATUS_TAYANG,
        ]);
    }

    #[Test]
    public function admin_kasus_can_delete_unused_kegiatan(): void
    {
        $kegiatan = $this->buatKegiatan(['judul' => 'Kegiatan Kosong Siap Hapus']);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.kegiatan-edukasi.destroy', $kegiatan));

        $response->assertRedirect(route('admin.kegiatan-edukasi.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kegiatan_edukasi', ['kegiatan_id' => $kegiatan->kegiatan_id]);
    }

    #[Test]
    public function pelapor_can_view_tayang_kegiatan_only(): void
    {
        $tayang = $this->buatKegiatan(['judul' => 'Webinar Publik Tayang', 'status' => KegiatanEdukasi::STATUS_TAYANG]);
        $draft = $this->buatKegiatan(['judul' => 'Webinar Tersembunyi Draft', 'status' => KegiatanEdukasi::STATUS_DRAFT]);
        $batal = $this->buatKegiatan(['judul' => 'Webinar Tersembunyi Batal', 'status' => KegiatanEdukasi::STATUS_DIBATALKAN]);

        $response = $this->actingAs($this->pelaporUser)->get(route('kegiatan.index'));

        $response->assertOk();
        $response->assertSee($tayang->judul);
        $response->assertDontSee($draft->judul);
        $response->assertDontSee($batal->judul);
    }

    #[Test]
    public function pelapor_cannot_view_draft_kegiatan(): void
    {
        $draft = $this->buatKegiatan(['judul' => 'Kegiatan Internal Draft', 'status' => KegiatanEdukasi::STATUS_DRAFT]);

        $this->actingAs($this->pelaporUser)
            ->get(route('kegiatan.show', $draft))
            ->assertForbidden();
    }

    #[Test]
    public function pelapor_can_register_for_kegiatan(): void
    {
        $kegiatan = $this->buatKegiatan();

        // Admin Kasus & Konselor dilarang mendaftar sebagai pelapor
        $this->actingAs($this->adminUser)->post(route('kegiatan.daftar', $kegiatan))->assertForbidden();
        $this->actingAs($this->konselorUser)->post(route('kegiatan.daftar', $kegiatan))->assertForbidden();

        $response = $this->actingAs($this->pelaporUser)->post(route('kegiatan.daftar', $kegiatan));

        $response->assertRedirect(route('kegiatan.show', $kegiatan));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pendaftaran_kegiatan', [
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);
    }

    #[Test]
    public function pelapor_cannot_register_twice_for_same_kegiatan(): void
    {
        $kegiatan = $this->buatKegiatan();

        $this->actingAs($this->pelaporUser)->post(route('kegiatan.daftar', $kegiatan))->assertSessionHas('success');
        $second = $this->actingAs($this->pelaporUser)->post(route('kegiatan.daftar', $kegiatan));

        $second->assertRedirect(route('kegiatan.show', $kegiatan));
        $second->assertSessionHas('error');
        $this->assertEquals(1, PendaftaranKegiatan::where('kegiatan_id', $kegiatan->kegiatan_id)->count());
    }

    #[Test]
    public function pelapor_cannot_register_when_quota_full(): void
    {
        $kegiatan = $this->buatKegiatan(['kuota' => 1]);

        $this->actingAs($this->pelaporUser)->post(route('kegiatan.daftar', $kegiatan))->assertSessionHas('success');

        $fullResponse = $this->actingAs($this->pelaporLain)->post(route('kegiatan.daftar', $kegiatan));

        $fullResponse->assertRedirect(route('kegiatan.show', $kegiatan));
        $fullResponse->assertSessionHas('error');
        $this->assertDatabaseMissing('pendaftaran_kegiatan', [
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporLain->user_id,
        ]);
    }

    #[Test]
    public function pelapor_cannot_register_for_non_tayang_kegiatan(): void
    {
        $draft = $this->buatKegiatan(['status' => KegiatanEdukasi::STATUS_DRAFT]);
        $dibatalkan = $this->buatKegiatan(['status' => KegiatanEdukasi::STATUS_DIBATALKAN]);

        $this->actingAs($this->pelaporUser)
            ->post(route('kegiatan.daftar', $draft))
            ->assertRedirect(route('kegiatan.show', $draft))
            ->assertSessionHas('error');

        $this->actingAs($this->pelaporUser)
            ->post(route('kegiatan.daftar', $dibatalkan))
            ->assertRedirect(route('kegiatan.show', $dibatalkan))
            ->assertSessionHas('error');

        $this->assertEquals(0, PendaftaranKegiatan::count());
    }

    #[Test]
    public function pelapor_cannot_register_for_finished_kegiatan(): void
    {
        $selesaiStatus = $this->buatKegiatan(['status' => KegiatanEdukasi::STATUS_SELESAI]);
        $lewatWaktu = $this->buatKegiatan([
            'status' => KegiatanEdukasi::STATUS_TAYANG,
            'tanggal_mulai' => now()->subDay()->toDateString(),
            'waktu_mulai' => '09:00:00',
        ]);

        $this->actingAs($this->pelaporUser)
            ->post(route('kegiatan.daftar', $selesaiStatus))
            ->assertSessionHas('error');

        $this->actingAs($this->pelaporUser)
            ->post(route('kegiatan.daftar', $lewatWaktu))
            ->assertSessionHas('error');

        $this->assertEquals(0, PendaftaranKegiatan::count());
    }

    #[Test]
    public function pelapor_can_view_own_pendaftaran_only(): void
    {
        $kegiatanMilikSaya = $this->buatKegiatan(['judul' => 'Webinar Pribadi Pelapor 1']);
        $kegiatanMilikLain = $this->buatKegiatan(['judul' => 'Webinar Eksklusif Pelapor 2']);

        PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatanMilikSaya->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatanMilikLain->kegiatan_id,
            'user_id' => $this->pelaporLain->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        $response = $this->actingAs($this->pelaporUser)->get(route('pendaftaran-kegiatan.index'));

        $response->assertOk();
        $response->assertSee($kegiatanMilikSaya->judul);
        $response->assertDontSee($kegiatanMilikLain->judul);
    }

    #[Test]
    public function pelapor_cannot_view_other_users_pendaftaran(): void
    {
        $kegiatan = $this->buatKegiatan(['judul' => 'Webinar Peserta Lain']);

        PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporLain->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        // Pada Pendaftaran Saya milik pelaporUser, tidak boleh ada data milik pelaporLain
        $ownPage = $this->actingAs($this->pelaporUser)->get(route('pendaftaran-kegiatan.index'));
        $ownPage->assertOk();
        $ownPage->assertDontSee($kegiatan->judul);
        $ownPage->assertDontSee($this->pelaporLain->nama_tampilan);

        // Pada detail kegiatan yang dibuka pelaporUser, identitas/status pendaftaran pelaporLain tidak ditampilkan
        $detailPage = $this->actingAs($this->pelaporUser)->get(route('kegiatan.show', $kegiatan));
        $detailPage->assertOk();
        $detailPage->assertDontSee($this->pelaporLain->nama_tampilan);
        $detailPage->assertDontSee($this->pelaporLain->email);
    }

    #[Test]
    public function pelapor_can_cancel_active_pendaftaran_before_start(): void
    {
        $kegiatan = $this->buatKegiatan(['kuota' => 1]);

        $this->actingAs($this->pelaporUser)->post(route('kegiatan.daftar', $kegiatan));
        $pendaftaran = PendaftaranKegiatan::where('kegiatan_id', $kegiatan->kegiatan_id)
            ->where('user_id', $this->pelaporUser->user_id)
            ->firstOrFail();

        $response = $this->actingAs($this->pelaporUser)
            ->from(route('pendaftaran-kegiatan.index'))
            ->patch(route('pendaftaran-kegiatan.cancel', $pendaftaran));

        $response->assertRedirect(route('pendaftaran-kegiatan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pendaftaran_kegiatan', [
            'pendaftaran_id' => $pendaftaran->pendaftaran_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_DIBATALKAN,
        ]);
    }

    #[Test]
    public function pelapor_cannot_cancel_pendaftaran_after_start(): void
    {
        // Uji termasuk kegiatan multi-hari: tanggal_mulai sudah lewat meski tanggal_selesai masih besok
        $kegiatanMultiHari = $this->buatKegiatan([
            'tanggal_mulai' => now()->subHours(3)->toDateString(),
            'waktu_mulai' => now()->subHours(3)->format('H:i:s'),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'waktu_selesai' => '17:00:00',
        ]);

        $pendaftaran = PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatanMultiHari->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        $response = $this->actingAs($this->pelaporUser)
            ->from(route('pendaftaran-kegiatan.index'))
            ->patch(route('pendaftaran-kegiatan.cancel', $pendaftaran));

        $response->assertRedirect(route('pendaftaran-kegiatan.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('pendaftaran_kegiatan', [
            'pendaftaran_id' => $pendaftaran->pendaftaran_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);
    }

    #[Test]
    public function pelapor_cannot_cancel_other_users_pendaftaran(): void
    {
        $kegiatan = $this->buatKegiatan();

        $pendaftaranMilikLain = PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporLain->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        $this->actingAs($this->pelaporUser)
            ->patch(route('pendaftaran-kegiatan.cancel', $pendaftaranMilikLain))
            ->assertForbidden();

        $this->assertDatabaseHas('pendaftaran_kegiatan', [
            'pendaftaran_id' => $pendaftaranMilikLain->pendaftaran_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);
    }

    #[Test]
    public function admin_kasus_can_mark_hadir(): void
    {
        $kegiatan = $this->buatKegiatan([
            'tanggal_mulai' => now()->subHour()->toDateString(),
            'waktu_mulai' => now()->subHour()->format('H:i:s'),
        ]);

        $pendaftaran = PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        $response = $this->actingAs($this->adminUser)->patch(
            route('admin.kegiatan-edukasi.verifikasi-kehadiran', [$kegiatan, $pendaftaran]),
            ['status_pendaftaran' => PendaftaranKegiatan::STATUS_HADIR]
        );

        $response->assertRedirect(route('admin.kegiatan-edukasi.show', $kegiatan));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pendaftaran_kegiatan', [
            'pendaftaran_id' => $pendaftaran->pendaftaran_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_HADIR,
        ]);
    }

    #[Test]
    public function admin_kasus_can_mark_tidak_hadir(): void
    {
        $kegiatan = $this->buatKegiatan([
            'tanggal_mulai' => now()->subHour()->toDateString(),
            'waktu_mulai' => now()->subHour()->format('H:i:s'),
        ]);

        $pendaftaran = PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        $response = $this->actingAs($this->adminUser)->patch(
            route('admin.kegiatan-edukasi.verifikasi-kehadiran', [$kegiatan, $pendaftaran]),
            ['status_pendaftaran' => PendaftaranKegiatan::STATUS_TIDAK_HADIR]
        );

        $response->assertRedirect(route('admin.kegiatan-edukasi.show', $kegiatan));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pendaftaran_kegiatan', [
            'pendaftaran_id' => $pendaftaran->pendaftaran_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_TIDAK_HADIR,
        ]);
    }

    #[Test]
    public function pelapor_cannot_verify_attendance(): void
    {
        $kegiatan = $this->buatKegiatan([
            'tanggal_mulai' => now()->subHour()->toDateString(),
            'waktu_mulai' => now()->subHour()->format('H:i:s'),
        ]);

        $pendaftaran = PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        $this->actingAs($this->pelaporUser)->patch(
            route('admin.kegiatan-edukasi.verifikasi-kehadiran', [$kegiatan, $pendaftaran]),
            ['status_pendaftaran' => PendaftaranKegiatan::STATUS_HADIR]
        )->assertForbidden();

        $this->assertDatabaseHas('pendaftaran_kegiatan', [
            'pendaftaran_id' => $pendaftaran->pendaftaran_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);
    }

    #[Test]
    public function online_meeting_link_hidden_before_registration(): void
    {
        $tautanRahasia = 'https://meet.ruangaman.test/rahasia-link-vip-99';
        $kegiatan = $this->buatKegiatan([
            'metode' => KegiatanEdukasi::METODE_ONLINE,
            'lokasi_atau_tautan' => $tautanRahasia,
        ]);

        // Guest tidak boleh melihat tautan meeting
        $this->get(route('kegiatan.show', $kegiatan))
            ->assertOk()
            ->assertDontSee($tautanRahasia);

        // Pelapor yang belum mendaftar tidak boleh melihat tautan meeting
        $this->actingAs($this->pelaporUser)
            ->get(route('kegiatan.show', $kegiatan))
            ->assertOk()
            ->assertDontSee($tautanRahasia);
    }

    #[Test]
    public function online_meeting_link_visible_for_active_participant(): void
    {
        $tautanRahasia = 'https://meet.ruangaman.test/rahasia-link-vip-99';
        $kegiatan = $this->buatKegiatan([
            'metode' => KegiatanEdukasi::METODE_ONLINE,
            'lokasi_atau_tautan' => $tautanRahasia,
        ]);

        $pendaftaran = PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);

        $this->actingAs($this->pelaporUser)
            ->get(route('kegiatan.show', $kegiatan))
            ->assertOk()
            ->assertSee($tautanRahasia);

        $this->actingAs($this->pelaporUser)
            ->get(route('pendaftaran-kegiatan.index'))
            ->assertOk()
            ->assertSee($tautanRahasia);

        // Ketika status sudah ditandai hadir, link tetap terlihat
        $pendaftaran->update(['status_pendaftaran' => PendaftaranKegiatan::STATUS_HADIR]);

        $this->actingAs($this->pelaporUser)
            ->get(route('kegiatan.show', $kegiatan))
            ->assertOk()
            ->assertSee($tautanRahasia);

        $this->actingAs($this->pelaporUser)
            ->get(route('pendaftaran-kegiatan.index'))
            ->assertOk()
            ->assertSee($tautanRahasia);
    }

    #[Test]
    public function online_meeting_link_hidden_for_non_participant(): void
    {
        $tautanRahasia = 'https://meet.ruangaman.test/rahasia-link-vip-99';
        $kegiatan = $this->buatKegiatan([
            'metode' => KegiatanEdukasi::METODE_ONLINE,
            'lokasi_atau_tautan' => $tautanRahasia,
        ]);

        // Pelapor yang membatalkan pendaftaran tidak boleh melihat tautan lagi
        $pendaftaran = PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_DIBATALKAN,
        ]);

        $this->actingAs($this->pelaporUser)
            ->get(route('kegiatan.show', $kegiatan))
            ->assertOk()
            ->assertDontSee($tautanRahasia);

        $this->actingAs($this->pelaporUser)
            ->get(route('pendaftaran-kegiatan.index'))
            ->assertOk()
            ->assertDontSee($tautanRahasia);

        // Pelapor lain yang sama sekali bukan peserta tidak boleh melihat tautan
        $this->actingAs($this->pelaporLain)
            ->get(route('kegiatan.show', $kegiatan))
            ->assertOk()
            ->assertDontSee($tautanRahasia);

        // Status tidak_hadir juga tidak mendapat tautan
        $pendaftaran->update(['status_pendaftaran' => PendaftaranKegiatan::STATUS_TIDAK_HADIR]);

        $this->actingAs($this->pelaporUser)
            ->get(route('kegiatan.show', $kegiatan))
            ->assertOk()
            ->assertDontSee($tautanRahasia);
    }

    #[Test]
    public function email_is_never_exposed_in_admin_or_public_view(): void
    {
        $kegiatan = $this->buatKegiatan();

        PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_AKTIF,
        ]);
        PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporLain->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_HADIR,
        ]);

        // Admin view menampilkan alias, tetapi tidak boleh pernah membocorkan email
        $adminShow = $this->actingAs($this->adminUser)->get(route('admin.kegiatan-edukasi.show', $kegiatan));
        $adminShow->assertOk();
        $adminShow->assertSee('Bintang Laut');
        $adminShow->assertSee('Anggrek Malam');
        $adminShow->assertDontSee($this->pelaporUser->email);
        $adminShow->assertDontSee($this->pelaporLain->email);
        $adminShow->assertDontSee($this->adminUser->email);

        // Public index & show tidak membocorkan email ataupun identitas peserta lain
        $publicIndex = $this->get(route('kegiatan.index'));
        $publicIndex->assertOk();
        $publicIndex->assertDontSee($this->pelaporUser->email);
        $publicIndex->assertDontSee($this->pelaporLain->email);

        $publicShow = $this->get(route('kegiatan.show', $kegiatan));
        $publicShow->assertOk();
        $publicShow->assertDontSee($this->pelaporUser->email);
        $publicShow->assertDontSee($this->pelaporLain->email);
        $publicShow->assertDontSee('Anggrek Malam');
    }

    #[Test]
    public function quota_counts_active_and_hadir_only(): void
    {
        $rolePelapor = Role::where('nama_role', 'Pelapor')->firstOrFail();

        $pelapor3 = User::query()->create([
            'role_id' => $rolePelapor->role_id,
            'nama_tampilan' => 'Peserta Tiga',
            'email' => 'pelapor3@ruangaman.test',
            'password_hash' => 'password',
            'status' => 'aktif',
        ]);
        $pelapor4 = User::query()->create([
            'role_id' => $rolePelapor->role_id,
            'nama_tampilan' => 'Peserta Empat',
            'email' => 'pelapor4@ruangaman.test',
            'password_hash' => 'password',
            'status' => 'aktif',
        ]);
        $pelapor5 = User::query()->create([
            'role_id' => $rolePelapor->role_id,
            'nama_tampilan' => 'Peserta Lima',
            'email' => 'pelapor5@ruangaman.test',
            'password_hash' => 'password',
            'status' => 'aktif',
        ]);

        // Kuota 2: 1 dibatalkan (tidak dihitung), 1 tidak_hadir (tidak dihitung), 1 hadir (dihitung) -> tersisa 1 kursi
        $kegiatan = $this->buatKegiatan(['kuota' => 2]);

        PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporUser->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_DIBATALKAN,
        ]);
        PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $this->pelaporLain->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_TIDAK_HADIR,
        ]);
        PendaftaranKegiatan::query()->create([
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $pelapor3->user_id,
            'status_pendaftaran' => PendaftaranKegiatan::STATUS_HADIR,
        ]);

        $service = app(KegiatanEdukasiService::class);
        $this->assertEquals(1, $service->hitungKuotaTerisi($kegiatan));
        $this->assertEquals(1, $service->sisaKuota($kegiatan));

        // Pelapor 4 bisa mengambil 1 kursi tersisa
        $this->actingAs($pelapor4)->post(route('kegiatan.daftar', $kegiatan))->assertSessionHas('success');

        $this->assertEquals(2, $service->hitungKuotaTerisi($kegiatan));
        $this->assertEquals(0, $service->sisaKuota($kegiatan));

        // Pelapor 5 gagal karena kuota sekarang benar-benar penuh (1 hadir + 1 aktif = 2)
        $this->actingAs($pelapor5)->post(route('kegiatan.daftar', $kegiatan))->assertSessionHas('error');
        $this->assertDatabaseMissing('pendaftaran_kegiatan', [
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'user_id' => $pelapor5->user_id,
        ]);
    }

    #[Test]
    public function admin_kasus_can_deactivate_kegiatan_to_dibatalkan_or_draft(): void
    {
        $kegiatan = $this->buatKegiatan(['status' => KegiatanEdukasi::STATUS_TAYANG]);

        $response = $this->actingAs($this->adminUser)
            ->from(route('admin.kegiatan-edukasi.show', $kegiatan))
            ->patch(route('admin.kegiatan-edukasi.nonaktifkan', $kegiatan), [
                'target_status' => KegiatanEdukasi::STATUS_DIBATALKAN,
            ]);

        $response->assertRedirect(route('admin.kegiatan-edukasi.show', $kegiatan));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('kegiatan_edukasi', [
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'status' => KegiatanEdukasi::STATUS_DIBATALKAN,
        ]);
    }

    #[Test]
    public function service_daftar_enforces_transactional_quota_limit_without_overbooking(): void
    {
        $kegiatan = $this->buatKegiatan(['kuota' => 1]);
        $service = app(KegiatanEdukasiService::class);

        $first = $service->daftar($kegiatan, $this->pelaporUser);
        $this->assertEquals(PendaftaranKegiatan::STATUS_AKTIF, $first->status_pendaftaran);

        $this->expectException(DomainException::class);
        $service->daftar($kegiatan, $this->pelaporLain);
    }
}
