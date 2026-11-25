<?php

namespace Tests\Feature;

use App\Models\LaporanKasus;
use App\Models\LogPenanganan;
use App\Models\PenangananKasus;
use App\Models\Permission;
use App\Models\Role;
use App\Models\StatusKasus;
use App\Models\User;
use App\Services\LogPenangananService;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LogPenangananSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Pbi06LogPenangananKasusTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private User $konselorUser;

    private User $konselorLain;

    private User $pelaporUser;

    private StatusKasus $statusPending;

    private StatusKasus $statusProses;

    private StatusKasus $statusSelesai;

    private StatusKasus $statusDitolak;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);

        $this->adminUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Admin Kasus'))->firstOrFail();
        $this->konselorUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Konselor'))->firstOrFail();
        $this->pelaporUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Pelapor'))->firstOrFail();

        $roleKonselor = Role::where('nama_role', 'Konselor')->firstOrFail();
        $this->konselorLain = User::query()->create([
            'role_id' => $roleKonselor->role_id,
            'nama_tampilan' => 'Dr. Maya Konselor',
            'email' => 'konselor2@ruangaman.test',
            'password_hash' => 'password',
            'status' => 'aktif',
        ]);

        $this->statusPending = StatusKasus::where('nama_status', 'Pending')->firstOrFail();
        $this->statusProses = StatusKasus::where('nama_status', 'Proses')->firstOrFail();
        $this->statusSelesai = StatusKasus::where('nama_status', 'Selesai')->firstOrFail();
        $this->statusDitolak = StatusKasus::where('nama_status', 'Ditolak')->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $penangananOverride
     */
    private function buatPenangananKasus(
        ?User $petugas = null,
        ?StatusKasus $status = null,
        string $kodeLaporan = 'RA-2026-TEST01',
        array $penangananOverride = []
    ): PenangananKasus {
        $petugas ??= $this->konselorUser;
        $status ??= $this->statusProses;

        $laporan = LaporanKasus::query()->create([
            'user_id' => $this->pelaporUser->user_id,
            'status_id' => $status->status_id,
            'kode_laporan' => $kodeLaporan,
            'judul' => "Judul Laporan {$kodeLaporan}",
            'kategori_kekerasan' => 'Psikis',
            'lokasi_kejadian' => 'Area Kampus',
            'kronologi' => 'Kronologi rahasia pelapor.',
            'waktu_kejadian' => now()->subDays(2),
            'tingkat_urgensi' => 'sedang',
            'mode_anonim' => true,
        ]);

        return PenangananKasus::query()->create(array_merge([
            'laporan_id' => $laporan->laporan_id,
            'petugas_id' => $petugas->user_id,
            'tanggal_mulai' => now()->subDay()->toDateString(),
            'tanggal_selesai' => null,
            'catatan' => 'Penugasan uji PBI-06.',
        ], $penangananOverride));
    }

    #[Test]
    public function seeder_log_penanganan_menghasilkan_log_sistem_dan_manual(): void
    {
        $this->seed(LogPenangananSeeder::class);

        $this->assertDatabaseHas('log_penanganan', [
            'jenis_log' => LogPenanganan::JENIS_SISTEM,
        ]);
        $this->assertDatabaseHas('log_penanganan', [
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
        ]);
    }

    #[Test]
    public function admin_dapat_membuat_log_manual(): void
    {
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-ADM01');

        $this->actingAs($this->adminUser)->get(route('log-penanganan.create'))->assertOk();

        $response = $this->actingAs($this->adminUser)->post(route('log-penanganan.store'), [
            'penanganan_id' => $penanganan->penanganan_id,
            'aktivitas' => 'Admin meninjau koordinasi rujukan layanan medis.',
            'waktu' => now()->subMinutes(10)->format('Y-m-d\TH:i'),
        ]);

        $log = LogPenanganan::query()->latest('log_id')->firstOrFail();

        $response->assertRedirect(route('log-penanganan.show', $log));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('log_penanganan', [
            'log_id' => $log->log_id,
            'penanganan_id' => $penanganan->penanganan_id,
            'laporan_id' => $penanganan->laporan_id,
            'user_id' => $this->adminUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
            'aktivitas' => 'Admin meninjau koordinasi rujukan layanan medis.',
        ]);
    }

    #[Test]
    public function konselor_dapat_membuat_log_manual_pada_kasus_yang_ditugaskan(): void
    {
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-KNS01');

        $response = $this->actingAs($this->konselorUser)->post(route('log-penanganan.store'), [
            'penanganan_id' => $penanganan->penanganan_id,
            'aktivitas' => 'Melakukan asesmen psikologis awal bersama pelapor melalui sesi daring.',
            'waktu' => now()->subMinutes(5)->format('Y-m-d\TH:i'),
        ]);

        $log = LogPenanganan::query()->latest('log_id')->firstOrFail();

        $response->assertRedirect(route('log-penanganan.show', $log));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('log_penanganan', [
            'log_id' => $log->log_id,
            'penanganan_id' => $penanganan->penanganan_id,
            'user_id' => $this->konselorUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
        ]);
    }

    #[Test]
    public function konselor_tidak_dapat_membuat_log_pada_kasus_yang_tidak_ditugaskan(): void
    {
        $penangananKonselorLain = $this->buatPenangananKasus($this->konselorLain, $this->statusProses, 'RA-2026-KNS02');

        $this->actingAs($this->konselorUser)
            ->get(route('log-penanganan.create', ['penanganan_id' => $penangananKonselorLain->penanganan_id]))
            ->assertForbidden();

        $response = $this->actingAs($this->konselorUser)->post(route('log-penanganan.store'), [
            'penanganan_id' => $penangananKonselorLain->penanganan_id,
            'aktivitas' => 'Mencoba menyelipkan catatan ke kasus konselor lain.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('log_penanganan', [
            'penanganan_id' => $penangananKonselorLain->penanganan_id,
            'user_id' => $this->konselorUser->user_id,
        ]);
    }

    #[Test]
    public function konselor_tidak_dapat_melihat_log_kasus_yang_tidak_ditugaskan(): void
    {
        $penangananSaya = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-SAYA');
        $penangananOrangLain = $this->buatPenangananKasus($this->konselorLain, $this->statusProses, 'RA-2026-LAIN');

        $logSaya = LogPenanganan::query()->create([
            'penanganan_id' => $penangananSaya->penanganan_id,
            'user_id' => $this->konselorUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
            'aktivitas' => 'Catatan milik kasus yang ditugaskan kepada saya.',
            'waktu' => now()->subMinutes(15),
        ]);

        $logOrangLain = LogPenanganan::query()->create([
            'penanganan_id' => $penangananOrangLain->penanganan_id,
            'user_id' => $this->konselorLain->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
            'aktivitas' => 'Catatan rahasia milik kasus konselor lain.',
            'waktu' => now()->subMinutes(10),
        ]);

        $indexResponse = $this->actingAs($this->konselorUser)->get(route('log-penanganan.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee($logSaya->aktivitas);
        $indexResponse->assertDontSee($logOrangLain->aktivitas);

        $this->actingAs($this->konselorUser)->get(route('log-penanganan.show', $logOrangLain))->assertForbidden();
    }

    #[Test]
    public function pelapor_tidak_dapat_mengakses_log_penanganan(): void
    {
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-PLP01');
        $log = LogPenanganan::query()->create([
            'penanganan_id' => $penanganan->penanganan_id,
            'user_id' => $this->konselorUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
            'aktivitas' => 'Catatan internal penanganan.',
            'waktu' => now(),
        ]);

        $this->actingAs($this->pelaporUser)->get(route('log-penanganan.index'))->assertForbidden();
        $this->actingAs($this->pelaporUser)->get(route('log-penanganan.create'))->assertForbidden();
        $this->actingAs($this->pelaporUser)->post(route('log-penanganan.store'), [
            'penanganan_id' => $penanganan->penanganan_id,
            'aktivitas' => 'Akses ilegal oleh pelapor.',
        ])->assertForbidden();
        $this->actingAs($this->pelaporUser)->get(route('log-penanganan.show', $log))->assertForbidden();
        $this->actingAs($this->pelaporUser)->get(route('log-penanganan.edit', $log))->assertForbidden();
        $this->actingAs($this->pelaporUser)->put(route('log-penanganan.update', $log), [
            'aktivitas' => 'Ubah ilegal oleh pelapor.',
        ])->assertForbidden();
        $this->actingAs($this->pelaporUser)->delete(route('log-penanganan.destroy', $log))->assertForbidden();
    }

    #[Test]
    public function tamu_tidak_dapat_mengakses_log_penanganan(): void
    {
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-GST01');
        $log = LogPenanganan::query()->create([
            'penanganan_id' => $penanganan->penanganan_id,
            'user_id' => $this->konselorUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
            'aktivitas' => 'Catatan rahasia tidak boleh diakses guest.',
            'waktu' => now(),
        ]);

        $this->get(route('log-penanganan.index'))->assertRedirect(route('login'));
        $this->get(route('log-penanganan.create'))->assertRedirect(route('login'));
        $this->post(route('log-penanganan.store'), [])->assertRedirect(route('login'));
        $this->get(route('log-penanganan.show', $log))->assertRedirect(route('login'));
        $this->get(route('log-penanganan.edit', $log))->assertRedirect(route('login'));
        $this->put(route('log-penanganan.update', $log), [])->assertRedirect(route('login'));
        $this->delete(route('log-penanganan.destroy', $log))->assertRedirect(route('login'));
    }

    #[Test]
    public function konselor_dapat_mengubah_dan_menghapus_log_manual_miliknya(): void
    {
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-OWN01');
        $log = LogPenanganan::query()->create([
            'penanganan_id' => $penanganan->penanganan_id,
            'user_id' => $this->konselorUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
            'aktivitas' => 'Catatan awal konselor.',
            'waktu' => now()->subHour(),
        ]);

        $this->actingAs($this->konselorUser)->get(route('log-penanganan.edit', $log))->assertOk();

        $updateResponse = $this->actingAs($this->konselorUser)->put(route('log-penanganan.update', $log), [
            'aktivitas' => 'Catatan awal konselor telah diperbarui dengan hasil sesi pertama.',
        ]);

        $updateResponse->assertRedirect(route('log-penanganan.show', $log));
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('log_penanganan', [
            'log_id' => $log->log_id,
            'aktivitas' => 'Catatan awal konselor telah diperbarui dengan hasil sesi pertama.',
        ]);

        $deleteResponse = $this->actingAs($this->konselorUser)->delete(route('log-penanganan.destroy', $log));
        $deleteResponse->assertRedirect(route('log-penanganan.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('log_penanganan', ['log_id' => $log->log_id]);
    }

    #[Test]
    public function konselor_tidak_dapat_mengubah_atau_menghapus_log_manual_milik_pengguna_lain(): void
    {
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-OTR01');
        $logMilikAdmin = LogPenanganan::query()->create([
            'penanganan_id' => $penanganan->penanganan_id,
            'user_id' => $this->adminUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
            'aktivitas' => 'Catatan arahan yang dibuat oleh Admin Kasus.',
            'waktu' => now()->subHour(),
        ]);

        $this->actingAs($this->konselorUser)->get(route('log-penanganan.edit', $logMilikAdmin))->assertForbidden();
        $this->actingAs($this->konselorUser)->put(route('log-penanganan.update', $logMilikAdmin), [
            'aktivitas' => 'Percobaan mengubah log admin.',
        ])->assertForbidden();
        $this->actingAs($this->konselorUser)->delete(route('log-penanganan.destroy', $logMilikAdmin))->assertForbidden();

        $this->assertDatabaseHas('log_penanganan', [
            'log_id' => $logMilikAdmin->log_id,
            'aktivitas' => 'Catatan arahan yang dibuat oleh Admin Kasus.',
        ]);
    }

    #[Test]
    public function admin_dapat_mengubah_dan_menghapus_log_manual_sesuai_permission(): void
    {
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-PRM01');
        $logMilikKonselor = LogPenanganan::query()->create([
            'penanganan_id' => $penanganan->penanganan_id,
            'user_id' => $this->konselorUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
            'aktivitas' => 'Catatan konselor sebelum disunting admin.',
            'waktu' => now()->subHour(),
        ]);

        // Saat permission kelola-log-manual ada pada role Admin Kasus -> berhasil
        $this->actingAs($this->adminUser)->put(route('log-penanganan.update', $logMilikKonselor), [
            'aktivitas' => 'Disunting oleh Admin sesuai izin kelola-log-manual.',
        ])->assertRedirect(route('log-penanganan.show', $logMilikKonselor));

        $this->assertDatabaseHas('log_penanganan', [
            'log_id' => $logMilikKonselor->log_id,
            'aktivitas' => 'Disunting oleh Admin sesuai izin kelola-log-manual.',
        ]);

        // Cabut permission kelola-log-manual dari role Admin Kasus -> harus ditolak 403
        $perm = Permission::where('nama_permission', 'kelola-log-manual')->firstOrFail();
        $this->adminUser->role->permissions()->detach($perm->permission_id);
        $this->adminUser->refresh();

        $this->actingAs($this->adminUser)->get(route('log-penanganan.edit', $logMilikKonselor))->assertForbidden();
        $this->actingAs($this->adminUser)->put(route('log-penanganan.update', $logMilikKonselor), [
            'aktivitas' => 'Tidak boleh lolos tanpa permission.',
        ])->assertForbidden();
        $this->actingAs($this->adminUser)->delete(route('log-penanganan.destroy', $logMilikKonselor))->assertForbidden();

        // Kembalikan permission lalu uji hapus sukses
        $this->adminUser->role->permissions()->attach($perm->permission_id);
        $this->actingAs($this->adminUser)->delete(route('log-penanganan.destroy', $logMilikKonselor))
            ->assertRedirect(route('log-penanganan.index'));
        $this->assertDatabaseMissing('log_penanganan', ['log_id' => $logMilikKonselor->log_id]);
    }

    #[Test]
    public function log_manual_tidak_dapat_diubah_atau_dihapus_jika_kasus_sudah_selesai_atau_ditolak(): void
    {
        $penangananSelesai = $this->buatPenangananKasus($this->konselorUser, $this->statusSelesai, 'RA-2026-CLS01', [
            'tanggal_selesai' => now()->toDateString(),
        ]);

        $logKasusSelesai = LogPenanganan::query()->create([
            'penanganan_id' => $penangananSelesai->penanganan_id,
            'user_id' => $this->konselorUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
            'aktivitas' => 'Catatan penutupan saat kasus selesai.',
            'waktu' => now()->subMinutes(30),
        ]);

        // Baik Konselor pembuat maupun Admin ditolak mengubah/menghapus ketika kasus tidak aktif
        $this->actingAs($this->konselorUser)->get(route('log-penanganan.edit', $logKasusSelesai))->assertForbidden();
        $this->actingAs($this->adminUser)->put(route('log-penanganan.update', $logKasusSelesai), [
            'aktivitas' => 'Edit paksa kasus selesai.',
        ])->assertForbidden();
        $this->actingAs($this->adminUser)->delete(route('log-penanganan.destroy', $logKasusSelesai))->assertForbidden();

        // Menambah log manual ke kasus tidak aktif juga harus gagal dengan error
        $this->actingAs($this->adminUser)->post(route('log-penanganan.store'), [
            'penanganan_id' => $penangananSelesai->penanganan_id,
            'aktivitas' => 'Menambah log baru pada kasus yang sudah selesai.',
        ])->assertSessionHas('error');

        $this->assertDatabaseHas('log_penanganan', ['log_id' => $logKasusSelesai->log_id]);
    }

    #[Test]
    public function log_sistem_tidak_dapat_diedit_melalui_ui_maupun_endpoint(): void
    {
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-SYS01');
        $logSistem = app(LogPenangananService::class)->catatLogSistem(
            $penanganan->penanganan_id,
            $this->adminUser->user_id,
            'Status kasus berubah dari Pending menjadi Proses.'
        );

        // UI index & show menampilkan badge Sistem Otomatis dan tidak merender tautan edit
        $indexResponse = $this->actingAs($this->adminUser)->get(route('log-penanganan.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Sistem Otomatis');
        $indexResponse->assertDontSee(route('log-penanganan.edit', $logSistem));

        $showResponse = $this->actingAs($this->adminUser)->get(route('log-penanganan.show', $logSistem));
        $showResponse->assertOk();
        $showResponse->assertSee('Entri otomatis sistem terkunci (hanya-baca)');
        $showResponse->assertDontSee(route('log-penanganan.edit', $logSistem));

        // Endpoint edit & update wajib 403
        $this->actingAs($this->adminUser)->get(route('log-penanganan.edit', $logSistem))->assertForbidden();
        $this->actingAs($this->adminUser)->put(route('log-penanganan.update', $logSistem), [
            'aktivitas' => 'Manipulasi log sistem.',
        ])->assertForbidden();

        $this->assertDatabaseHas('log_penanganan', [
            'log_id' => $logSistem->log_id,
            'aktivitas' => 'Status kasus berubah dari Pending menjadi Proses.',
            'jenis_log' => LogPenanganan::JENIS_SISTEM,
        ]);
    }

    #[Test]
    public function log_sistem_tidak_dapat_dihapus_melalui_ui_maupun_endpoint(): void
    {
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-SYS02');
        $logSistem = app(LogPenangananService::class)->catatLogSistem(
            $penanganan->penanganan_id,
            $this->adminUser->user_id,
            'Log sistem terkunci permanen.'
        );

        $showResponse = $this->actingAs($this->adminUser)->get(route('log-penanganan.show', $logSistem));
        $showResponse->assertOk();
        $showResponse->assertSee('Entri otomatis sistem terkunci (hanya-baca)');
        $showResponse->assertDontSee('Hapus Catatan');
        $showResponse->assertDontSee('Ya, Hapus Catatan');
        $showResponse->assertDontSee('"method":"DELETE"', false);

        $this->actingAs($this->adminUser)->delete(route('log-penanganan.destroy', $logSistem))->assertForbidden();
        $this->actingAs($this->konselorUser)->delete(route('log-penanganan.destroy', $logSistem))->assertForbidden();

        $this->assertDatabaseHas('log_penanganan', [
            'log_id' => $logSistem->log_id,
            'jenis_log' => LogPenanganan::JENIS_SISTEM,
        ]);
    }

    #[Test]
    public function log_sistem_dibuat_otomatis_saat_transisi_status_dipicu(): void
    {
        $service = app(LogPenangananService::class);
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-FR15');

        // 1. Hook catatLogSistem langsung
        $logLangsung = $service->catatLogSistem(
            $penanganan->penanganan_id,
            $this->adminUser->user_id,
            'Transisi otomatis awal tercatat.'
        );
        $this->assertEquals(LogPenanganan::JENIS_SISTEM, $logLangsung->jenis_log);
        $this->assertEquals($this->adminUser->user_id, $logLangsung->user_id);

        // 2. Hook Pending -> Proses
        $logProses = $service->catatPendingKeProses($penanganan->penanganan_id, $this->adminUser->user_id, $this->konselorUser->nama_tampilan);
        $this->assertEquals(LogPenanganan::JENIS_SISTEM, $logProses->jenis_log);
        $this->assertStringContainsString('Pending menjadi Proses', $logProses->aktivitas);

        // 3. Hook Proses -> Selesai
        $logSelesai = $service->catatProsesKeSelesai($penanganan->penanganan_id, $this->adminUser->user_id, 'Pendampingan tuntas.');
        $this->assertEquals(LogPenanganan::JENIS_SISTEM, $logSelesai->jenis_log);
        $this->assertStringContainsString('Proses menjadi Selesai', $logSelesai->aktivitas);

        // 4. Hook Pending -> Ditolak tanpa penanganan_id
        $laporanDitolak = LaporanKasus::query()->create([
            'user_id' => $this->pelaporUser->user_id,
            'status_id' => $this->statusDitolak->status_id,
            'kode_laporan' => 'RA-2026-TOLAK',
            'judul' => 'Laporan Uji Penolakan FR-15',
            'kategori_kekerasan' => 'Siber',
            'lokasi_kejadian' => 'Daring',
            'kronologi' => 'Data tidak lengkap.',
            'waktu_kejadian' => now(),
            'tingkat_urgensi' => 'rendah',
            'mode_anonim' => true,
        ]);

        $logDitolak = $service->catatPendingKeDitolak(
            $laporanDitolak->laporan_id,
            $this->adminUser->user_id,
            'Laporan duplikat atau tidak valid.'
        );
        $this->assertNull($logDitolak->penanganan_id);
        $this->assertEquals($laporanDitolak->laporan_id, $logDitolak->laporan_id);
        $this->assertEquals(LogPenanganan::JENIS_SISTEM, $logDitolak->jenis_log);

        // 5. Pastikan request manual yang mencoba menyuntikkan jenis_log=sistem tetap menyimpan 'manual'
        $this->actingAs($this->adminUser)->post(route('log-penanganan.store'), [
            'penanganan_id' => $penanganan->penanganan_id,
            'jenis_log' => 'sistem',
            'aktivitas' => 'Percobaan menyuntikkan jenis sistem via form manual.',
        ]);

        $this->assertDatabaseHas('log_penanganan', [
            'aktivitas' => 'Percobaan menyuntikkan jenis sistem via form manual.',
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
        ]);
    }

    #[Test]
    public function filter_dan_pencarian_berfungsi_dengan_benar(): void
    {
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-FLT01');

        LogPenanganan::query()->create([
            'penanganan_id' => $penanganan->penanganan_id,
            'user_id' => $this->konselorUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
            'aktivitas' => 'Konselor menghubungi pendamping hukum kampus.',
            'waktu' => now()->subDays(5),
        ]);

        LogPenanganan::query()->create([
            'penanganan_id' => $penanganan->penanganan_id,
            'user_id' => $this->adminUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_SISTEM,
            'aktivitas' => 'Status kasus berubah dari Pending menjadi Proses.',
            'waktu' => now()->subDay(),
        ]);

        // Filter search aktivitas
        $this->actingAs($this->adminUser)
            ->get(route('log-penanganan.index', ['search' => 'pendamping hukum']))
            ->assertOk()
            ->assertSee('Konselor menghubungi pendamping hukum kampus.')
            ->assertDontSee('Status kasus berubah dari Pending menjadi Proses.');

        // Filter jenis_log = sistem
        $this->actingAs($this->adminUser)
            ->get(route('log-penanganan.index', ['jenis_log' => 'sistem']))
            ->assertOk()
            ->assertSee('Status kasus berubah dari Pending menjadi Proses.')
            ->assertDontSee('Konselor menghubungi pendamping hukum kampus.');

        // Filter pembuat_id = konselorUser
        $this->actingAs($this->adminUser)
            ->get(route('log-penanganan.index', ['pembuat_id' => $this->konselorUser->user_id]))
            ->assertOk()
            ->assertSee('Konselor menghubungi pendamping hukum kampus.')
            ->assertDontSee('Status kasus berubah dari Pending menjadi Proses.');

        // Filter rentang waktu
        $this->actingAs($this->adminUser)
            ->get(route('log-penanganan.index', [
                'dari_tanggal' => now()->subDays(2)->toDateString(),
                'sampai_tanggal' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Status kasus berubah dari Pending menjadi Proses.')
            ->assertDontSee('Konselor menghubungi pendamping hukum kampus.');
    }

    #[Test]
    public function privasi_email_dan_identitas_pelapor_terlindungi_pada_ui_log(): void
    {
        $penanganan = $this->buatPenangananKasus($this->konselorUser, $this->statusProses, 'RA-2026-PRV01');

        $log = LogPenanganan::query()->create([
            'penanganan_id' => $penanganan->penanganan_id,
            'user_id' => $this->konselorUser->user_id,
            'jenis_log' => LogPenanganan::JENIS_MANUAL,
            'aktivitas' => 'Pelapor telah diberikan informasi layanan konseling lanjutan.',
            'waktu' => now()->subMinutes(20),
        ]);

        $indexResponse = $this->actingAs($this->adminUser)->get(route('log-penanganan.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('RA-2026-PRV01');
        $indexResponse->assertSee($this->konselorUser->nama_tampilan);
        $indexResponse->assertDontSee($this->pelaporUser->email);
        $indexResponse->assertDontSee($this->pelaporUser->nama_tampilan);
        $indexResponse->assertDontSee($this->konselorUser->email);
        $indexResponse->assertDontSee($this->adminUser->email);

        $showResponse = $this->actingAs($this->konselorUser)->get(route('log-penanganan.show', $log));
        $showResponse->assertOk();
        $showResponse->assertDontSee($this->pelaporUser->email);
        $showResponse->assertDontSee($this->pelaporUser->nama_tampilan);
        $showResponse->assertDontSee($this->konselorUser->email);
    }
}
