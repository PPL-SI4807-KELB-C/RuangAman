<?php

namespace Tests\Feature;

use App\Models\InstansiBantuan;
use App\Models\KontakBantuan;
use App\Models\LayananBantuan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PBI-08: Layanan Bantuan & Kontak Bantuan — Feature Tests
 *
 * Menguji seluruh fungsionalitas CRUD untuk LayananBantuan dan KontakBantuan
 * yang berada di bawah (nested) InstansiBantuan, serta pembatasan akses hak admin.
 */
class Pbi08LayananKontakBantuanTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private User $pelaporUser;

    private User $konselorUser;

    private InstansiBantuan $instansi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);

        $this->adminUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Admin Kasus'))->firstOrFail();
        $this->pelaporUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Pelapor'))->firstOrFail();
        $this->konselorUser = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Konselor'))->firstOrFail();

        $this->instansi = InstansiBantuan::firstOrFail();
    }

    // =========================================================================
    // LAYANAN BANTUAN
    // =========================================================================

    #[Test]
    public function admin_dapat_melihat_daftar_layanan(): void
    {
        $layanan = LayananBantuan::create([
            'instansi_id'  => $this->instansi->instansi_id,
            'nama_layanan' => 'Konseling Pengujian',
            'deskripsi'    => 'Deskripsi uji coba layanan.',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('instansi.layanan.index', $this->instansi));

        $response->assertStatus(200);
        $response->assertSee('Layanan Bantuan');
        $response->assertSee('Konseling Pengujian');
    }

    #[Test]
    public function admin_dapat_melihat_form_tambah_layanan(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('instansi.layanan.create', $this->instansi));

        $response->assertStatus(200);
        $response->assertSee('Tambah Layanan Bantuan');
        $response->assertSee('Simpan Layanan');
    }

    #[Test]
    public function admin_dapat_menyimpan_layanan_baru(): void
    {
        $payload = [
            'nama_layanan' => 'Pendampingan Medis',
            'deskripsi'    => 'Bantuan rujukan medis dan visum.',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('instansi.layanan.store', $this->instansi), $payload);

        $response->assertRedirect(route('instansi.layanan.index', $this->instansi));
        $response->assertSessionHas('success', 'Layanan bantuan berhasil ditambahkan.');

        $this->assertDatabaseHas('layanan_bantuan', [
            'instansi_id'  => $this->instansi->instansi_id,
            'nama_layanan' => 'Pendampingan Medis',
        ]);
    }

    #[Test]
    public function validasi_gagal_jika_nama_layanan_kosong(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('instansi.layanan.store', $this->instansi), [
                'nama_layanan' => '',
            ]);

        $response->assertSessionHasErrors('nama_layanan');
    }

    #[Test]
    public function admin_dapat_melihat_detail_layanan(): void
    {
        $layanan = LayananBantuan::create([
            'instansi_id'  => $this->instansi->instansi_id,
            'nama_layanan' => 'Layanan Detail Tes',
            'deskripsi'    => 'Keterangan detail.',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('instansi.layanan.show', [$this->instansi, $layanan]));

        $response->assertStatus(200);
        $response->assertSee('Layanan Detail Tes');
        $response->assertSee('Keterangan detail.');
    }

    #[Test]
    public function admin_dapat_melihat_form_edit_layanan(): void
    {
        $layanan = LayananBantuan::create([
            'instansi_id'  => $this->instansi->instansi_id,
            'nama_layanan' => 'Layanan Edit Tes',
            'deskripsi'    => null,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('instansi.layanan.edit', [$this->instansi, $layanan]));

        $response->assertStatus(200);
        $response->assertSee('Ubah Layanan Bantuan');
        $response->assertSee('Layanan Edit Tes');
    }

    #[Test]
    public function admin_dapat_memperbarui_layanan(): void
    {
        $layanan = LayananBantuan::create([
            'instansi_id'  => $this->instansi->instansi_id,
            'nama_layanan' => 'Layanan Sebelum Update',
            'deskripsi'    => null,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('instansi.layanan.update', [$this->instansi, $layanan]), [
                'nama_layanan' => 'Layanan Sesudah Update',
                'deskripsi'    => 'Deskripsi berhasil diperbarui.',
            ]);

        $response->assertRedirect(route('instansi.layanan.index', $this->instansi));
        $response->assertSessionHas('success', 'Layanan bantuan berhasil diperbarui.');

        $this->assertDatabaseHas('layanan_bantuan', [
            'layanan_id'   => $layanan->layanan_id,
            'nama_layanan' => 'Layanan Sesudah Update',
        ]);
    }

    #[Test]
    public function admin_dapat_menghapus_layanan(): void
    {
        $layanan = LayananBantuan::create([
            'instansi_id'  => $this->instansi->instansi_id,
            'nama_layanan' => 'Layanan Akan Dihapus',
            'deskripsi'    => null,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('instansi.layanan.destroy', [$this->instansi, $layanan]));

        $response->assertRedirect(route('instansi.layanan.index', $this->instansi));
        $response->assertSessionHas('success', 'Layanan bantuan berhasil dihapus.');

        $this->assertDatabaseMissing('layanan_bantuan', [
            'layanan_id' => $layanan->layanan_id,
        ]);
    }

    #[Test]
    public function layanan_dari_instansi_lain_mengembalikan_404(): void
    {
        $instansiLain = InstansiBantuan::create([
            'nama_instansi' => 'Instansi Pihak Ketiga',
            'alamat'        => 'Jl. Lain No. 99',
            'deskripsi'     => 'Deskripsi lain.',
            'status'        => 'aktif',
        ]);

        $layananLain = LayananBantuan::create([
            'instansi_id'  => $instansiLain->instansi_id,
            'nama_layanan' => 'Layanan Instansi Lain',
            'deskripsi'    => null,
        ]);

        $this->actingAs($this->adminUser)
            ->get(route('instansi.layanan.show', [$this->instansi, $layananLain]))
            ->assertNotFound();
    }

    // =========================================================================
    // KONTAK BANTUAN
    // =========================================================================

    #[Test]
    public function admin_dapat_melihat_daftar_kontak(): void
    {
        $kontak = KontakBantuan::create([
            'instansi_id'  => $this->instansi->instansi_id,
            'jenis_kontak' => 'telepon',
            'nilai_kontak' => '021-9999-8888',
            'keterangan'   => 'Senin-Jumat',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('instansi.kontak.index', $this->instansi));

        $response->assertStatus(200);
        $response->assertSee('Kontak Bantuan');
        $response->assertSee('021-9999-8888');
    }

    #[Test]
    public function admin_dapat_melihat_form_tambah_kontak(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('instansi.kontak.create', $this->instansi));

        $response->assertStatus(200);
        $response->assertSee('Tambah Kontak Bantuan');
        $response->assertSee('Simpan Kontak');
    }

    #[Test]
    public function admin_dapat_menyimpan_kontak_baru(): void
    {
        $payload = [
            'jenis_kontak' => 'whatsapp',
            'nilai_kontak' => '0812-3456-7890',
            'keterangan'   => 'Layanan pesan 24 jam',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('instansi.kontak.store', $this->instansi), $payload);

        $response->assertRedirect(route('instansi.kontak.index', $this->instansi));
        $response->assertSessionHas('success', 'Kontak bantuan berhasil ditambahkan.');

        $this->assertDatabaseHas('kontak_bantuan', [
            'instansi_id'  => $this->instansi->instansi_id,
            'jenis_kontak' => 'whatsapp',
            'nilai_kontak' => '0812-3456-7890',
        ]);
    }

    #[Test]
    public function validasi_gagal_jika_jenis_kontak_tidak_valid(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('instansi.kontak.store', $this->instansi), [
                'jenis_kontak' => 'telegram', // Tidak ada dalam enum telepon, whatsapp, email, hotline
                'nilai_kontak' => '0812-0000-0000',
            ]);

        $response->assertSessionHasErrors('jenis_kontak');
    }

    #[Test]
    public function admin_dapat_melihat_detail_kontak(): void
    {
        $kontak = KontakBantuan::create([
            'instansi_id'  => $this->instansi->instansi_id,
            'jenis_kontak' => 'email',
            'nilai_kontak' => 'kontak@bantuan.test',
            'keterangan'   => 'Alamat surel resmi',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('instansi.kontak.show', [$this->instansi, $kontak]));

        $response->assertStatus(200);
        $response->assertSee('kontak@bantuan.test');
        $response->assertSee('Alamat surel resmi');
    }

    #[Test]
    public function admin_dapat_melihat_form_edit_kontak(): void
    {
        $kontak = KontakBantuan::create([
            'instansi_id'  => $this->instansi->instansi_id,
            'jenis_kontak' => 'hotline',
            'nilai_kontak' => '1500-111',
            'keterangan'   => null,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('instansi.kontak.edit', [$this->instansi, $kontak]));

        $response->assertStatus(200);
        $response->assertSee('Ubah Kontak Bantuan');
        $response->assertSee('1500-111');
    }

    #[Test]
    public function admin_dapat_memperbarui_kontak(): void
    {
        $kontak = KontakBantuan::create([
            'instansi_id'  => $this->instansi->instansi_id,
            'jenis_kontak' => 'telepon',
            'nilai_kontak' => '021-1111-2222',
            'keterangan'   => null,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('instansi.kontak.update', [$this->instansi, $kontak]), [
                'jenis_kontak' => 'whatsapp',
                'nilai_kontak' => '0899-8888-7777',
                'keterangan'   => 'Nomor baru WhatsApp',
            ]);

        $response->assertRedirect(route('instansi.kontak.index', $this->instansi));
        $response->assertSessionHas('success', 'Kontak bantuan berhasil diperbarui.');

        $this->assertDatabaseHas('kontak_bantuan', [
            'kontak_id'    => $kontak->kontak_id,
            'jenis_kontak' => 'whatsapp',
            'nilai_kontak' => '0899-8888-7777',
        ]);
    }

    #[Test]
    public function admin_dapat_menghapus_kontak(): void
    {
        $kontak = KontakBantuan::create([
            'instansi_id'  => $this->instansi->instansi_id,
            'jenis_kontak' => 'email',
            'nilai_kontak' => 'hapus@uji.test',
            'keterangan'   => null,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('instansi.kontak.destroy', [$this->instansi, $kontak]));

        $response->assertRedirect(route('instansi.kontak.index', $this->instansi));
        $response->assertSessionHas('success', 'Kontak bantuan berhasil dihapus.');

        $this->assertDatabaseMissing('kontak_bantuan', [
            'kontak_id' => $kontak->kontak_id,
        ]);
    }

    #[Test]
    public function kontak_dari_instansi_lain_mengembalikan_404(): void
    {
        $instansiLain = InstansiBantuan::create([
            'nama_instansi' => 'Instansi Lain Kontak',
            'alamat'        => 'Jl. Kontak No. 10',
            'deskripsi'     => 'Deskripsi.',
            'status'        => 'aktif',
        ]);

        $kontakLain = KontakBantuan::create([
            'instansi_id'  => $instansiLain->instansi_id,
            'jenis_kontak' => 'telepon',
            'nilai_kontak' => '021-3333-4444',
            'keterangan'   => null,
        ]);

        $this->actingAs($this->adminUser)
            ->get(route('instansi.kontak.show', [$this->instansi, $kontakLain]))
            ->assertNotFound();
    }

    // =========================================================================
    // OTORISASI & KEAMANAN
    // =========================================================================

    #[Test]
    public function tamu_tidak_dapat_mengakses_manajemen_layanan_dan_kontak(): void
    {
        $this->get(route('instansi.layanan.index', $this->instansi))
            ->assertRedirect(route('login'));

        $this->get(route('instansi.kontak.index', $this->instansi))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function pelapor_dan_konselor_tidak_dapat_mengelola_layanan_dan_kontak(): void
    {
        foreach ([$this->pelaporUser, $this->konselorUser] as $user) {
            $this->actingAs($user)
                ->get(route('instansi.layanan.index', $this->instansi))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('instansi.layanan.create', $this->instansi))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('instansi.layanan.store', $this->instansi), ['nama_layanan' => 'Ilegal'])
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('instansi.kontak.index', $this->instansi))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('instansi.kontak.create', $this->instansi))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('instansi.kontak.store', $this->instansi), ['jenis_kontak' => 'telepon', 'nilai_kontak' => '021-000'])
                ->assertForbidden();
        }
    }
}
