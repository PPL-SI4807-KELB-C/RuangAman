<?php

namespace Tests\Feature;

use App\Models\LaporanKasus;
use App\Models\PenangananKasus;
use App\Models\Role;
use App\Models\StatusKasus;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\StatusKasusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pbi04ManajemenStatusDanPenugasanKasusTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $pelaporUser;
    private User $konselorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->seed(DatabaseSeeder::class);

        $this->adminUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Admin Kasus'))->firstOrFail();
        $this->pelaporUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Pelapor'))->firstOrFail();
        $this->konselorUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Konselor'))->firstOrFail();
    }

    public function test_seeder_status_kasus_menghasilkan_empat_status(): void
    {
        $this->assertDatabaseHas('status_kasus', ['nama_status' => 'Pending']);
        $this->assertDatabaseHas('status_kasus', ['nama_status' => 'Proses']);
        $this->assertDatabaseHas('status_kasus', ['nama_status' => 'Selesai']);
        $this->assertDatabaseHas('status_kasus', ['nama_status' => 'Ditolak']);
        $this->assertCount(4, StatusKasus::all());
    }

    public function test_pengguna_non_admin_dilarang_mengakses_status_kasus(): void
    {
        $response = $this->actingAs($this->pelaporUser)->get(route('admin.status-kasus.index'));
        $response->assertForbidden();
    }

    public function test_admin_dapat_melihat_daftar_status_kasus(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.status-kasus.index'));
        $response->assertOk();
        $response->assertSee('Manajemen Status Kasus');
        $response->assertSee('Pending');
    }

    public function test_admin_dapat_menambah_status_kasus_baru(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.status-kasus.store'), [
            'nama_status' => 'Mediasi Berjalan',
            'deskripsi' => 'Kasus sedang dalam proses mediasi keluarga.',
        ]);

        $response->assertRedirect(route('admin.status-kasus.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('status_kasus', ['nama_status' => 'Mediasi Berjalan']);
    }

    public function test_validasi_nama_status_harus_unik(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.status-kasus.store'), [
            'nama_status' => 'Pending',
            'deskripsi' => 'Duplikat status.',
        ]);

        $response->assertSessionHasErrors('nama_status');
    }

    public function test_status_kasus_yang_digunakan_laporan_tidak_boleh_dihapus(): void
    {
        $statusPending = StatusKasus::where('nama_status', 'Pending')->firstOrFail();

        // Buat satu laporan yang menggunakan status Pending
        LaporanKasus::create([
            'user_id' => $this->pelaporUser->user_id,
            'status_id' => $statusPending->status_id,
            'kode_laporan' => 'LAP-TEST-001',
            'judul' => 'Uji Coba Laporan Pelecehan',
            'kategori_kekerasan' => 'Siber',
            'lokasi_kejadian' => 'Daring',
            'kronologi' => 'Kronologi uji coba untuk validasi proteksi hapus status.',
            'waktu_kejadian' => now(),
            'tingkat_urgensi' => 'sedang',
            'mode_anonim' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.status-kasus.destroy', $statusPending));

        $response->assertRedirect(route('admin.status-kasus.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('status_kasus', ['status_id' => $statusPending->status_id]);
    }

    public function test_status_kasus_yang_tidak_digunakan_dapat_dihapus(): void
    {
        $statusBaru = StatusKasus::create([
            'nama_status' => 'Status Sementara',
            'deskripsi' => 'Akan segera dihapus.',
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.status-kasus.destroy', $statusBaru));

        $response->assertRedirect(route('admin.status-kasus.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('status_kasus', ['status_id' => $statusBaru->status_id]);
    }

    public function test_admin_dapat_menugaskan_petugas_pada_laporan_kasus(): void
    {
        $statusProses = StatusKasus::where('nama_status', 'Proses')->firstOrFail();

        $laporan = LaporanKasus::create([
            'user_id' => $this->pelaporUser->user_id,
            'status_id' => $statusProses->status_id,
            'kode_laporan' => 'LAP-TUGAS-001',
            'judul' => 'Laporan Penugasan Konselor',
            'kategori_kekerasan' => 'Psikis',
            'lokasi_kejadian' => 'Kampus',
            'kronologi' => 'Kronologi kasus untuk penugasan.',
            'waktu_kejadian' => now(),
            'tingkat_urgensi' => 'tinggi',
            'mode_anonim' => false,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.penanganan-kasus.store'), [
            'laporan_id' => $laporan->laporan_id,
            'petugas_id' => $this->konselorUser->user_id,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'catatan' => 'Harap segera hubungi pelapor untuk asesmen awal.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('penanganan_kasus', [
            'laporan_id' => $laporan->laporan_id,
            'petugas_id' => $this->konselorUser->user_id,
        ]);
    }

    public function test_satu_laporan_hanya_boleh_memiliki_satu_penugasan(): void
    {
        $statusProses = StatusKasus::where('nama_status', 'Proses')->firstOrFail();

        $laporan = LaporanKasus::create([
            'user_id' => $this->pelaporUser->user_id,
            'status_id' => $statusProses->status_id,
            'kode_laporan' => 'LAP-TUGAS-002',
            'judul' => 'Laporan Uji Unique Penugasan',
            'kategori_kekerasan' => 'Psikis',
            'lokasi_kejadian' => 'Kampus',
            'kronologi' => 'Kronologi kasus.',
            'waktu_kejadian' => now(),
            'tingkat_urgensi' => 'sedang',
            'mode_anonim' => false,
        ]);

        PenangananKasus::create([
            'laporan_id' => $laporan->laporan_id,
            'petugas_id' => $this->konselorUser->user_id,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'catatan' => 'Penugasan pertama.',
        ]);

        // Coba tugaskan lagi pada laporan yang sama
        $response = $this->actingAs($this->adminUser)->post(route('admin.penanganan-kasus.store'), [
            'laporan_id' => $laporan->laporan_id,
            'petugas_id' => $this->adminUser->user_id,
            'tanggal_mulai' => now()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('laporan_id');
    }

    public function test_validasi_tanggal_selesai_harus_setelah_atau_sama_dengan_tanggal_mulai(): void
    {
        $statusProses = StatusKasus::where('nama_status', 'Proses')->firstOrFail();

        $laporan = LaporanKasus::create([
            'user_id' => $this->pelaporUser->user_id,
            'status_id' => $statusProses->status_id,
            'kode_laporan' => 'LAP-TUGAS-003',
            'judul' => 'Laporan Uji Tanggal',
            'kategori_kekerasan' => 'Psikis',
            'lokasi_kejadian' => 'Kampus',
            'kronologi' => 'Kronologi kasus.',
            'waktu_kejadian' => now(),
            'tingkat_urgensi' => 'sedang',
            'mode_anonim' => false,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.penanganan-kasus.store'), [
            'laporan_id' => $laporan->laporan_id,
            'petugas_id' => $this->konselorUser->user_id,
            'tanggal_mulai' => '2026-11-10',
            'tanggal_selesai' => '2026-11-05', // Lebih awal dari tanggal_mulai
        ]);

        $response->assertSessionHasErrors('tanggal_selesai');
    }

    public function test_admin_dapat_melihat_detail_penugasan_dan_membatalkannya(): void
    {
        $statusProses = StatusKasus::where('nama_status', 'Proses')->firstOrFail();

        $laporan = LaporanKasus::create([
            'user_id' => $this->pelaporUser->user_id,
            'status_id' => $statusProses->status_id,
            'kode_laporan' => 'LAP-TUGAS-004',
            'judul' => 'Laporan Uji Detail dan Batal',
            'kategori_kekerasan' => 'Fisik',
            'lokasi_kejadian' => 'Asrama',
            'kronologi' => 'Kronologi detail kasus.',
            'waktu_kejadian' => now(),
            'tingkat_urgensi' => 'darurat',
            'mode_anonim' => true,
        ]);

        $penanganan = PenangananKasus::create([
            'laporan_id' => $laporan->laporan_id,
            'petugas_id' => $this->konselorUser->user_id,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'catatan' => 'Perlu pendampingan segera.',
        ]);

        // Detail
        $showResponse = $this->actingAs($this->adminUser)->get(route('admin.penanganan-kasus.show', $penanganan));
        $showResponse->assertOk();
        $showResponse->assertSee($laporan->kode_laporan);
        $showResponse->assertSee('PBI-06 Integration');

        // Batal / Hapus
        $deleteResponse = $this->actingAs($this->adminUser)->delete(route('admin.penanganan-kasus.destroy', $penanganan));
        $deleteResponse->assertRedirect(route('admin.penanganan-kasus.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('penanganan_kasus', ['penanganan_id' => $penanganan->penanganan_id]);
    }

    public function test_admin_dapat_melihat_daftar_penugasan_kasus_berisi_data_aktif_dan_selesai(): void
    {
        $statusProses = StatusKasus::where('nama_status', 'Proses')->firstOrFail();

        $laporanAktif = LaporanKasus::create([
            'user_id' => $this->pelaporUser->user_id,
            'status_id' => $statusProses->status_id,
            'kode_laporan' => 'LAP-INDEX-001',
            'judul' => 'Kasus Penugasan Aktif',
            'kategori_kekerasan' => 'Psikis',
            'lokasi_kejadian' => 'Bandung',
            'kronologi' => 'Kronologi kasus aktif untuk uji render tabel index.',
            'waktu_kejadian' => now(),
            'tingkat_urgensi' => 'sedang',
            'mode_anonim' => true,
        ]);

        $laporanSelesai = LaporanKasus::create([
            'user_id' => $this->pelaporUser->user_id,
            'status_id' => $statusProses->status_id,
            'kode_laporan' => 'LAP-INDEX-002',
            'judul' => 'Kasus Penugasan Selesai',
            'kategori_kekerasan' => 'Fisik',
            'lokasi_kejadian' => 'Jakarta',
            'kronologi' => 'Kronologi kasus selesai untuk uji render tabel index.',
            'waktu_kejadian' => now(),
            'tingkat_urgensi' => 'tinggi',
            'mode_anonim' => false,
        ]);

        PenangananKasus::create([
            'laporan_id' => $laporanAktif->laporan_id,
            'petugas_id' => $this->konselorUser->user_id,
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_selesai' => null,
            'catatan' => 'Pendampingan masih berlangsung.',
        ]);

        PenangananKasus::create([
            'laporan_id' => $laporanSelesai->laporan_id,
            'petugas_id' => $this->konselorUser->user_id,
            'tanggal_mulai' => now()->subDay()->format('Y-m-d'),
            'tanggal_selesai' => now()->format('Y-m-d'),
            'catatan' => 'Pendampingan selesai.',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.penanganan-kasus.index'));

        $response->assertOk();
        $response->assertSee('LAP-INDEX-001');
        $response->assertSee('LAP-INDEX-002');
        $response->assertSee('Sedang Ditangani');
        $response->assertSee('Selesai');
    }
}
