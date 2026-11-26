<?php

namespace Tests\Feature;

use App\Models\InstansiBantuan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Pbi07ManajemenInstansiBantuanTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private User $pelaporUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);

        $this->adminUser = User::whereHas('role', fn ($query) => $query->where('nama_role', 'Admin Kasus'))->firstOrFail();
        $this->pelaporUser = User::whereHas('role', fn ($query) => $query->where('nama_role', 'Pelapor'))->firstOrFail();
    }

    #[Test]
    public function halaman_index_manajemen_instansi_dapat_diakses_oleh_admin_kasus(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('instansi.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen instansi bantuan');
        $response->assertSee('Daftar instansi');
        $response->assertSee('+ &nbsp;Tambah instansi', false);
    }

    #[Test]
    public function halaman_index_instansi_tidak_dapat_diakses_oleh_tamu(): void
    {
        $response = $this->get(route('instansi.index'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function pelapor_dan_konselor_tidak_dapat_mengelola_instansi(): void
    {
        $konselorUser = User::whereHas('role', fn ($query) => $query->where('nama_role', 'Konselor'))->firstOrFail();
        $instansi = InstansiBantuan::query()->firstOrFail();
        $jumlahAwal = InstansiBantuan::count();

        foreach ([$this->pelaporUser, $konselorUser] as $user) {
            $this->actingAs($user)->get(route('instansi.index'))->assertForbidden();
            $this->actingAs($user)->get(route('instansi.create'))->assertForbidden();
            $this->actingAs($user)->post(route('instansi.store'), ['nama_instansi' => 'Instansi Palsu'])->assertForbidden();
            $this->actingAs($user)->put(route('instansi.update', $instansi), ['nama_instansi' => 'Diubah'])->assertForbidden();
            $this->actingAs($user)->delete(route('instansi.destroy', $instansi))->assertForbidden();
            $this->actingAs($user)->delete(route('admin.instansi.destroy', $instansi))->assertForbidden();
        }

        $this->assertSame($jumlahAwal, InstansiBantuan::count());
        $this->assertNotSame('Diubah', $instansi->fresh()->nama_instansi);
    }

    #[Test]
    public function halaman_direktori_publik_dapat_diakses_oleh_siapa_saja_tanpa_login(): void
    {
        $response = $this->get(route('instansi.publik'));

        $response->assertStatus(200);
        $response->assertSee('Temukan pilihan bantuan Anda.');
        $response->assertSee('Menampilkan instansi aktif');
    }

    #[Test]
    public function halaman_detail_direktori_publik_dapat_diakses_untuk_instansi_aktif(): void
    {
        $instansiAktif = InstansiBantuan::where('status', 'aktif')->firstOrFail();

        $response = $this->get(route('instansi.publik.show', $instansiAktif));

        $response->assertStatus(200);
        $response->assertSee($instansiAktif->nama_instansi);
        $response->assertSee('ALAMAT INSTANSI');
        $response->assertSee('Layanan yang tersedia');
        $response->assertSee('Hubungi instansi');
        $response->assertSee('Utamakan kenyamanan dan keselamatan Anda.');
    }

    #[Test]
    public function halaman_detail_direktori_publik_mengembalikan_404_untuk_instansi_nonaktif(): void
    {
        $instansiNonaktif = InstansiBantuan::create([
            'nama_instansi' => 'Instansi Nonaktif Publik',
            'alamat' => 'Jl. Tertutup No. 1',
            'deskripsi' => 'Tidak boleh tampil di publik.',
            'status' => 'nonaktif',
        ]);

        $this->get(route('instansi.publik.show', $instansiNonaktif))
            ->assertNotFound();
    }

    #[Test]
    public function halaman_tambah_instansi_dapat_diakses(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('instansi.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah instansi bantuan');
        $response->assertSee('Simpan instansi');
    }

    #[Test]
    public function dapat_menambahkan_instansi_bantuan_valid(): void
    {
        $payload = [
            'nama_instansi' => 'Pusat Pemulihan Damai',
            'alamat' => 'Jl. Merdeka No. 45, Semarang',
            'deskripsi' => 'Layanan konseling krisis dan pendampingan korban.',
            'website' => 'https://damai.example.org',
            'status' => 'aktif',
        ];

        $response = $this->actingAs($this->adminUser)->post(route('instansi.store'), $payload);

        $response->assertRedirect(route('instansi.index'));
        $response->assertSessionHas('success', 'Instansi bantuan berhasil ditambahkan.');

        $this->assertDatabaseHas('instansi_bantuan', [
            'nama_instansi' => 'Pusat Pemulihan Damai',
            'alamat' => 'Jl. Merdeka No. 45, Semarang',
            'status' => 'aktif',
        ]);
    }

    #[Test]
    public function validasi_input_nama_alamat_dan_deskripsi_wajib_diisi(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('instansi.store'), [
            'nama_instansi' => '',
            'alamat' => '',
            'deskripsi' => '',
            'website' => '',
            'status' => '',
        ]);

        $response->assertSessionHasErrors(['nama_instansi', 'alamat', 'deskripsi', 'status']);
    }

    #[Test]
    public function validasi_format_website_harus_url_valid(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('instansi.store'), [
            'nama_instansi' => 'Instansi Tes',
            'alamat' => 'Alamat Tes',
            'deskripsi' => 'Deskripsi Tes',
            'website' => 'bukan-url-valid',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors(['website']);
    }

    #[Test]
    public function halaman_detail_instansi_dapat_diakses(): void
    {
        $instansi = InstansiBantuan::firstOrFail();

        $response = $this->actingAs($this->adminUser)->get(route('instansi.show', $instansi));

        $response->assertStatus(200);
        $response->assertSee($instansi->nama_instansi);
        $response->assertSee('Detail instansi bantuan');
        $response->assertSee('Ubah instansi');
    }

    #[Test]
    public function halaman_ubah_instansi_dapat_diakses(): void
    {
        $instansi = InstansiBantuan::firstOrFail();

        $response = $this->actingAs($this->adminUser)->get(route('instansi.edit', $instansi));

        $response->assertStatus(200);
        $response->assertSee('Ubah instansi bantuan');
        $response->assertSee($instansi->nama_instansi);
        $response->assertSee('Simpan perubahan');
    }

    #[Test]
    public function dapat_memperbarui_data_instansi(): void
    {
        $instansi = InstansiBantuan::firstOrFail();

        $payload = [
            'nama_instansi' => 'Nama Instansi Terupdate',
            'alamat' => 'Alamat Terupdate No. 99',
            'deskripsi' => 'Deskripsi layanan baru.',
            'website' => 'https://updated.example.org',
            'status' => 'nonaktif',
        ];

        $response = $this->actingAs($this->adminUser)->put(route('instansi.update', $instansi), $payload);

        $response->assertRedirect(route('instansi.index'));
        $response->assertSessionHas('success', 'Instansi bantuan berhasil diperbarui.');

        $this->assertDatabaseHas('instansi_bantuan', [
            'instansi_id' => $instansi->instansi_id,
            'nama_instansi' => 'Nama Instansi Terupdate',
            'status' => 'nonaktif',
        ]);
    }

    #[Test]
    public function dapat_menghapus_instansi_bantuan(): void
    {
        $instansi = InstansiBantuan::create([
            'nama_instansi' => 'Instansi Untuk Dihapus',
            'alamat' => 'Jl. Hapus No. 1',
            'deskripsi' => 'Akan segera dihapus',
            'website' => null,
            'status' => 'nonaktif',
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('instansi.destroy', $instansi));

        $response->assertRedirect(route('instansi.index'));
        $response->assertSessionHas('success', 'Instansi bantuan berhasil dihapus.');

        $this->assertDatabaseMissing('instansi_bantuan', [
            'instansi_id' => $instansi->instansi_id,
        ]);
    }

    #[Test]
    public function pencarian_dan_filter_di_halaman_index_berfungsi(): void
    {
        $instansi = InstansiBantuan::firstOrFail();

        $responseSearch = $this->actingAs($this->adminUser)->get(route('instansi.index', [
            'search' => $instansi->nama_instansi,
        ]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee($instansi->nama_instansi);

        $responseFilter = $this->actingAs($this->adminUser)->get(route('instansi.index', [
            'status' => 'aktif',
        ]));
        $responseFilter->assertStatus(200);
    }
}
