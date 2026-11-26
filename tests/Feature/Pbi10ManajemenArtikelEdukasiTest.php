<?php

namespace Tests\Feature;

use App\Models\Artikel;
use App\Models\KategoriArtikel;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Pbi10ManajemenArtikelEdukasiTest extends TestCase
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

    private function buatArtikel(array $override = []): Artikel
    {
        return Artikel::create(array_merge([
            'kategori_id' => KategoriArtikel::default()->kategori_id,
            'penulis_id' => $this->admin->user_id,
            'judul' => 'Artikel Dasar',
            'slug' => 'artikel-dasar',
            'isi' => 'Isi artikel dasar.',
            'status_publikasi' => Artikel::STATUS_DRAFT,
        ], $override));
    }

    #[Test]
    public function tamu_diarahkan_ke_login(): void
    {
        $this->get(route('admin.artikel.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function peran_selain_admin_kasus_mendapat_403(): void
    {
        $artikel = $this->buatArtikel();

        foreach ([$this->pelapor, $this->konselor] as $user) {
            $this->actingAs($user);
            $this->get(route('admin.artikel.index'))->assertForbidden();
            $this->get(route('admin.artikel.create'))->assertForbidden();
            $this->post(route('admin.artikel.store'), [])->assertForbidden();
            $this->put(route('admin.artikel.update', $artikel), [])->assertForbidden();
            $this->delete(route('admin.artikel.destroy', $artikel))->assertForbidden();
        }

        $this->assertDatabaseHas('artikel', ['slug' => 'artikel-dasar']);
    }

    #[Test]
    public function admin_melihat_daftar_artikel(): void
    {
        $this->buatArtikel(['judul' => 'Judul Terlihat', 'slug' => 'judul-terlihat']);
        $this->actingAs($this->admin);

        $this->get(route('admin.artikel.index'))->assertOk()->assertSee('Judul Terlihat');
    }

    #[Test]
    public function artikel_dapat_dibuat_tanpa_memilih_kategori_dan_masuk_ke_umum(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('admin.artikel.create'))->assertOk();
        $this->post(route('admin.artikel.store'), [
            'judul' => 'Artikel Tanpa Kategori',
            'isi' => 'Isi.',
            'status_publikasi' => Artikel::STATUS_DRAFT,
        ])->assertSessionHasNoErrors();

        $artikel = Artikel::where('judul', 'Artikel Tanpa Kategori')->firstOrFail();
        $this->assertSame(KategoriArtikel::default()->kategori_id, $artikel->kategori_id);
        $this->assertSame($this->admin->user_id, $artikel->penulis_id);
        $this->assertSame('artikel-tanpa-kategori', $artikel->slug);
    }

    #[Test]
    public function artikel_dapat_dibuat_dengan_kategori_pilihan(): void
    {
        $kategori = KategoriArtikel::create(['nama_kategori' => 'Pilihan']);
        $this->actingAs($this->admin);

        $this->post(route('admin.artikel.store'), [
            'kategori_id' => $kategori->kategori_id,
            'judul' => 'Artikel Berkategori',
            'isi' => 'Isi.',
            'status_publikasi' => Artikel::STATUS_PUBLISHED,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('artikel', ['judul' => 'Artikel Berkategori', 'kategori_id' => $kategori->kategori_id]);
    }

    #[Test]
    public function validasi_menolak_input_tidak_valid(): void
    {
        $this->actingAs($this->admin);

        $this->post(route('admin.artikel.store'), [])->assertSessionHasErrors(['judul', 'isi', 'status_publikasi']);
        $this->post(route('admin.artikel.store'), [
            'kategori_id' => 999999,
            'judul' => 'X',
            'isi' => 'Y',
            'status_publikasi' => 'tidak-valid',
        ])->assertSessionHasErrors(['kategori_id', 'status_publikasi']);
    }

    #[Test]
    public function admin_dapat_mengubah_artikel_dan_slug_mengikuti_judul(): void
    {
        $artikel = $this->buatArtikel();
        $this->actingAs($this->admin);

        $this->get(route('admin.artikel.edit', $artikel))->assertOk();
        $this->put(route('admin.artikel.update', $artikel), [
            'judul' => 'Judul Baru Artikel',
            'isi' => 'Isi baru.',
            'status_publikasi' => Artikel::STATUS_PUBLISHED,
        ])->assertSessionHasNoErrors();

        $artikel->refresh();
        $this->assertSame('Judul Baru Artikel', $artikel->judul);
        $this->assertSame('judul-baru-artikel', $artikel->slug);
        $this->assertSame(Artikel::STATUS_PUBLISHED, $artikel->status_publikasi);
    }

    #[Test]
    public function admin_dapat_menghapus_artikel(): void
    {
        $artikel = $this->buatArtikel();
        $this->actingAs($this->admin);

        $this->delete(route('admin.artikel.destroy', $artikel))->assertRedirect();
        $this->assertDatabaseMissing('artikel', ['artikel_id' => $artikel->artikel_id]);
    }
}
