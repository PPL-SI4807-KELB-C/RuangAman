<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman artikel (publik & admin) harus tetap ter-render setelah penyeragaman UI.
 */
class ArtikelHalamanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_daftar_artikel_publik_dapat_dibuka_dengan_dan_tanpa_filter(): void
    {
        $this->get(route('artikel.index'))
            ->assertOk()
            ->assertSee('Temukan informasi untuk memahami dan bertindak aman')
            ->assertSee('Semua kategori');

        $this->get(route('artikel.index', ['search' => 'kata-yang-tidak-mungkin-ada-zzz', 'kategori_id' => 999]))
            ->assertOk()
            ->assertSee('Artikel belum ditemukan');
    }

    public function test_halaman_admin_artikel_dapat_dibuka_admin(): void
    {
        $admin = User::where('email', 'admin@ruangaman.test')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.artikel.index'))
            ->assertOk()
            ->assertSee('Manajemen Artikel Edukasi');
    }
}
