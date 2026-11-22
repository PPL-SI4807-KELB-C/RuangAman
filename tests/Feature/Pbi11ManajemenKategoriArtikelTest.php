<?php

namespace Tests\Feature;

use App\Models\Artikel;
use App\Models\KategoriArtikel;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Pbi11ManajemenKategoriArtikelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $pelapor;

    private User $konselor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Admin Kasus'))->firstOrFail();
        $this->pelapor = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Pelapor'))->firstOrFail();
        $this->konselor = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Konselor'))->firstOrFail();
    }

    private function buatKategori(string $nama = 'Kategori Uji'): KategoriArtikel
    {
        return KategoriArtikel::create(['nama_kategori' => $nama, 'deskripsi' => 'Deskripsi uji']);
    }

    #[Test]
    public function tamu_diarahkan_ke_login(): void
    {
        $this->get(route('admin.kategori-artikel.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function peran_selain_admin_kasus_mendapat_403_di_semua_aksi(): void
    {
        $kategori = $this->buatKategori();

        foreach ([$this->pelapor, $this->konselor] as $user) {
            $this->actingAs($user);
            $this->get(route('admin.kategori-artikel.index'))->assertForbidden();
            $this->get(route('admin.kategori-artikel.create'))->assertForbidden();
            $this->post(route('admin.kategori-artikel.store'), ['nama_kategori' => 'X'])->assertForbidden();
            $this->get(route('admin.kategori-artikel.edit', $kategori))->assertForbidden();
            $this->put(route('admin.kategori-artikel.update', $kategori), ['nama_kategori' => 'Y'])->assertForbidden();
            $this->delete(route('admin.kategori-artikel.destroy', $kategori))->assertForbidden();
        }

        $this->assertDatabaseHas('kategori_artikel', ['nama_kategori' => 'Kategori Uji']);
    }

    #[Test]
    public function admin_melihat_daftar_dan_dapat_mencari(): void
    {
        $this->buatKategori('Zebra Unik');
        $this->actingAs($this->admin);

        $this->get(route('admin.kategori-artikel.index'))->assertOk()->assertSee('Zebra Unik')->assertSee('Umum');
        $this->get(route('admin.kategori-artikel.index', ['q' => 'Zebra']))->assertOk()->assertSee('Zebra Unik');
    }

    #[Test]
    public function admin_dapat_membuat_kategori(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('admin.kategori-artikel.create'))->assertOk();
        $this->post(route('admin.kategori-artikel.store'), [
            'nama_kategori' => 'Kategori Baru',
            'deskripsi' => 'Deskripsi baru',
        ])->assertRedirect(route('admin.kategori-artikel.index'));

        $this->assertDatabaseHas('kategori_artikel', ['nama_kategori' => 'Kategori Baru']);
    }

    #[Test]
    public function validasi_nama_wajib_unik_dan_maksimal_100(): void
    {
        $this->buatKategori('Sudah Ada');
        $this->actingAs($this->admin);

        $this->post(route('admin.kategori-artikel.store'), ['nama_kategori' => ''])->assertSessionHasErrors('nama_kategori');
        $this->post(route('admin.kategori-artikel.store'), ['nama_kategori' => 'Sudah Ada'])->assertSessionHasErrors('nama_kategori');
        $this->post(route('admin.kategori-artikel.store'), ['nama_kategori' => str_repeat('a', 101)])->assertSessionHasErrors('nama_kategori');
    }

    #[Test]
    public function admin_dapat_mengubah_kategori_dan_nama_sendiri_tidak_dianggap_duplikat(): void
    {
        $kategori = $this->buatKategori();
        $this->actingAs($this->admin);

        $this->get(route('admin.kategori-artikel.edit', $kategori))->assertOk();
        $this->put(route('admin.kategori-artikel.update', $kategori), [
            'nama_kategori' => 'Kategori Uji',
            'deskripsi' => 'Diubah',
        ])->assertSessionHasNoErrors();
        $this->put(route('admin.kategori-artikel.update', $kategori), [
            'nama_kategori' => 'Nama Diubah',
        ])->assertRedirect(route('admin.kategori-artikel.index'));

        $this->assertDatabaseHas('kategori_artikel', ['kategori_id' => $kategori->kategori_id, 'nama_kategori' => 'Nama Diubah']);
    }

    #[Test]
    public function kategori_bawaan_umum_tidak_dapat_diganti_namanya_atau_dihapus(): void
    {
        $umum = KategoriArtikel::default();
        $this->actingAs($this->admin);

        $this->put(route('admin.kategori-artikel.update', $umum), ['nama_kategori' => 'Lain'])
            ->assertSessionHasErrors('nama_kategori');
        $this->delete(route('admin.kategori-artikel.destroy', $umum))->assertSessionHas('error');

        $this->assertDatabaseHas('kategori_artikel', ['kategori_id' => $umum->kategori_id, 'nama_kategori' => 'Umum']);
    }

    #[Test]
    public function kategori_yang_dipakai_artikel_tidak_dapat_dihapus(): void
    {
        $kategori = $this->buatKategori();
        Artikel::create([
            'kategori_id' => $kategori->kategori_id,
            'penulis_id' => $this->admin->user_id,
            'judul' => 'Artikel Pemakai',
            'slug' => 'artikel-pemakai',
            'isi' => 'Isi',
            'status_publikasi' => Artikel::STATUS_DRAFT,
        ]);
        $this->actingAs($this->admin);

        $this->delete(route('admin.kategori-artikel.destroy', $kategori))->assertSessionHas('error');
        $this->assertDatabaseHas('kategori_artikel', ['kategori_id' => $kategori->kategori_id]);
    }

    #[Test]
    public function kategori_tanpa_artikel_dapat_dihapus(): void
    {
        $kategori = $this->buatKategori();
        $this->actingAs($this->admin);

        $this->delete(route('admin.kategori-artikel.destroy', $kategori))
            ->assertRedirect(route('admin.kategori-artikel.index'));
        $this->assertDatabaseMissing('kategori_artikel', ['kategori_id' => $kategori->kategori_id]);
    }
}
