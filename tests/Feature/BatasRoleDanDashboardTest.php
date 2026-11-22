<?php

namespace Tests\Feature;

use App\Models\LaporanKasus;
use App\Models\Role;
use App\Models\StatusKasus;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Batas peran (Tabel 3.1.2) dan dashboard per peran (FR-02, FR-04, FR-05, FR-06).
 */
class BatasRoleDanDashboardTest extends TestCase
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

        $this->adminUser = User::where('email', 'admin@ruangaman.test')->firstOrFail();
        $this->pelaporUser = User::where('email', 'pelapor@ruangaman.test')->firstOrFail();
        $this->konselorUser = User::where('email', 'konselor@ruangaman.test')->firstOrFail();
    }

    public function test_hanya_pelapor_yang_dapat_membuka_formulir_laporan(): void
    {
        $this->get(route('laporan.create'))->assertRedirect(route('login'));

        $this->actingAs($this->adminUser)->get(route('laporan.create'))->assertForbidden();
        $this->actingAs($this->konselorUser)->get(route('laporan.create'))->assertForbidden();

        $this->actingAs($this->pelaporUser)->get(route('laporan.create'))->assertOk();
    }

    public function test_admin_dan_konselor_tidak_dapat_menyimpan_laporan(): void
    {
        $data = [
            'kategori_kekerasan' => 'Siber',
            'judul' => 'Percobaan laporan oleh staf',
            'lokasi_kejadian' => 'Daring',
            'kronologi' => 'Tidak boleh tersimpan karena peran staf tidak membuat laporan.',
            'waktu_kejadian' => now()->format('Y-m-d\TH:i'),
            'tingkat_urgensi' => 'rendah',
        ];

        $before = LaporanKasus::count();

        $this->actingAs($this->adminUser)->post(route('laporan.store'), $data)->assertForbidden();
        $this->actingAs($this->konselorUser)->post(route('laporan.store'), $data)->assertForbidden();

        $this->assertSame($before, LaporanKasus::count());
    }

    public function test_laporan_milik_pelapor_lain_tidak_boleh_diklaim(): void
    {
        $laporan = $this->buatLaporan($this->pelaporUser, 'KODEMILIKPELAPOR');

        $penyusup = $this->buatAkun('Pelapor Lain', 'pelapor-lain@ruangaman.test');

        $this->actingAs($penyusup)
            ->post(route('lacak.tautkan'), ['kode_laporan' => $laporan->kode_laporan])
            ->assertSessionHas('error');

        $this->assertSame($this->pelaporUser->user_id, $laporan->fresh()->user_id);
    }

    public function test_staf_tidak_boleh_mengklaim_lewat_kode_lacak(): void
    {
        $laporan = $this->buatLaporan($this->buatAkunAnonim(), 'KODETAMUSTAF');

        $this->actingAs($this->adminUser)
            ->post(route('lacak.tautkan'), ['kode_laporan' => $laporan->kode_laporan])
            ->assertForbidden();

        $this->assertNotSame($this->adminUser->user_id, $laporan->fresh()->user_id);
    }

    public function test_laporan_anonim_bersama_hanya_diklaim_oleh_pelapor(): void
    {
        $laporan = $this->buatLaporan($this->buatAkunAnonim(), 'KODETAMU01');

        $peminta = $this->buatPelapor('Ali Alias', 'ali@ruangaman.test');

        $this->actingAs($peminta)
            ->post(route('lacak.tautkan'), ['kode_laporan' => $laporan->kode_laporan])
            ->assertRedirect(route('lacak.hasil'))
            ->assertSessionHas('success');

        $this->assertSame($peminta->user_id, $laporan->fresh()->user_id);
    }

    public function test_setiap_role_diarahkan_ke_dashboard_sesudah_masuk(): void
    {
        foreach (['admin@ruangaman.test', 'pelapor@ruangaman.test', 'konselor@ruangaman.test'] as $email) {
            $this->post('/login', ['email' => $email, 'password' => 'password'])
                ->assertRedirect(route('dashboard'));
        }
    }

    public function test_dashboard_admin_kasus_memuat_ringkasan_tanpa_cta_pembuatan_laporan(): void
    {
        $this->buatLaporan($this->pelaporUser, 'KODEADMINDASH');

        $response = $this->actingAs($this->adminUser)->get(route('dashboard'))->assertOk();
        $response->assertSee('masuk dari Pelapor', false);
        $response->assertSee('KODEADMINDASH');
        $this->assertStringNotContainsString('Buat laporan', $response->getContent());
    }

    public function test_dashboard_konselor_memuat_kasus_yang_ditugaskan(): void
    {
        $this->actingAs($this->konselorUser)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Konselor');
    }

    public function test_dashboard_pelapor_hanya_memuat_laporan_milik_sendiri(): void
    {
        $punya = $this->buatLaporan($this->pelaporUser, 'KODEPUNYAPELAPOR');
        $bukanPunya = $this->buatLaporan($this->adminUser, 'KODEBUKANPUNYA');

        $response = $this->actingAs($this->pelaporUser)->get(route('dashboard'))->assertOk();
        $response->assertSee('Dashboard Pelapor');
        $response->assertSee($punya->kode_laporan);
        $this->assertStringNotContainsString($bukanPunya->kode_laporan, $response->getContent());
    }

    public function test_dashboard_memerlukan_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_halaman_lacak_tidak_mempublikasikan_isi_laporan_terbaru(): void
    {
        $rahasia = $this->buatLaporan($this->pelaporUser, 'KODERAHASIAPUBLIK');

        $form = $this->get(route('lacak.form'))->assertOk();
        $this->assertStringNotContainsString($rahasia->kronologi, $form->getContent());

        $this->post(route('lacak.cari'), ['kode_laporan' => 'KODETIDAKADA'])
            ->assertRedirect(route('lacak.hasil'));
        $hasil = $this->get(route('lacak.hasil'))->assertOk();
        $hasil->assertSee('Periksa kembali huruf dan angkanya', false);
        $this->assertStringNotContainsString($rahasia->kronologi, $hasil->getContent());

        $this->flushSession();
        $this->get(route('lacak.hasil'))->assertSessionHasErrors('kode_laporan');
    }

    public function test_lacak_memakai_post_dan_kode_tidak_muncul_di_url(): void
    {
        $laporan = $this->buatLaporan($this->pelaporUser, 'KODEURLAMAN');

        $response = $this->post(route('lacak.cari'), ['kode_laporan' => $laporan->kode_laporan]);
        $response->assertRedirect(route('lacak.hasil'));
        $this->assertStringNotContainsString('KODEURLAMAN', $response->headers->get('Location'));

        $this->get(route('lacak.hasil'))->assertOk()->assertSee($laporan->kronologi);
    }

    public function test_lacak_dibatasi_rate_limit(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('lacak.cari'), ['kode_laporan' => 'TEBAK' . $i])->assertRedirect();
        }

        $this->post(route('lacak.cari'), ['kode_laporan' => 'TEBAK99'])->assertStatus(429);
    }

    public function test_draf_beranda_dikirim_lewat_post_dan_tidak_masuk_url(): void
    {
        $kronologi = 'Isi kronologi rahasia dari beranda';

        $response = $this->actingAs($this->pelaporUser)
            ->post(route('laporan.draf'), ['kategori' => 'Psikis', 'kronologi' => $kronologi]);

        $response->assertRedirect(route('laporan.create'));
        $this->assertStringNotContainsString('kronologi', $response->headers->get('Location'));

        $this->actingAs($this->pelaporUser)->get(route('laporan.create'))
            ->assertOk()
            ->assertSee($kronologi);
    }

    public function test_tamu_yang_mengisi_draf_diarahkan_ke_registrasi(): void
    {
        $this->post(route('laporan.draf'), ['kronologi' => 'Draf tamu'])
            ->assertRedirect(route('register'))
            ->assertSessionHas('draf_laporan.kronologi', 'Draf tamu');
    }

    public function test_formulir_laporan_tidak_berisi_teks_contoh(): void
    {
        $response = $this->actingAs($this->pelaporUser)->get(route('laporan.create'))->assertOk();

        $this->assertStringNotContainsString('Isian ini menggambarkan laporan fiktif', $response->getContent());
        $this->assertMatchesRegularExpression('/<textarea\s+name="kronologi"[^>]*>\s*<\/textarea>/', $response->getContent());
    }

    private function buatLaporan(User $pemilik, string $kode): LaporanKasus
    {
        return LaporanKasus::create([
            'user_id' => $pemilik->user_id,
            'status_id' => StatusKasus::where('nama_status', 'Pending')->firstOrFail()->status_id,
            'kode_laporan' => $kode,
            'judul' => 'Laporan uji batas peran',
            'kategori_kekerasan' => 'Siber',
            'lokasi_kejadian' => 'Daring',
            'kronologi' => 'Kronologi rahasia milik '.$pemilik->email,
            'waktu_kejadian' => now(),
            'tingkat_urgensi' => 'sedang',
            'mode_anonim' => true,
        ]);
    }

    private function buatPelapor(string $nama, string $email): User
    {
        return $this->buatAkun($nama, $email);
    }

    private function buatAkunAnonim(): User
    {
        return $this->buatAkun('Akun Anonim Bersama', LaporanKasus::AKUN_ANONIM_BERSAMA);
    }

    private function buatAkun(string $nama, string $email): User
    {
        return User::create([
            'nama_tampilan' => $nama,
            'email' => $email,
            'password_hash' => 'password',
            'role_id' => Role::where('nama_role', 'Pelapor')->firstOrFail()->role_id,
            'status' => 'aktif',
        ]);
    }
}
