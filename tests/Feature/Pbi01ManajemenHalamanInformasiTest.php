<?php

namespace Tests\Feature;

use App\Models\HalamanInformasi;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Pbi01ManajemenHalamanInformasiTest extends TestCase
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

    /**
     * @param  array<string, mixed>  $override
     */
    private function buatHalaman(array $override = []): HalamanInformasi
    {
        return HalamanInformasi::query()->create(array_merge([
            'judul' => 'Halaman Contoh',
            'slug' => 'halaman-contoh',
            'isi' => 'Isi halaman contoh yang cukup panjang untuk lolos validasi minimal sepuluh karakter.',
            'status_publikasi' => HalamanInformasi::STATUS_PUBLISHED,
        ], $override));
    }

    #[Test]
    public function guest_cannot_access_admin_halaman_index(): void
    {
        $this->get(route('admin.halaman-informasi.index'))->assertRedirect(route('login'));

        $this->post(route('admin.halaman-informasi.store'), [
            'judul' => 'Percobaan Guest',
            'isi' => 'Isi yang cukup panjang untuk diuji.',
            'status_publikasi' => HalamanInformasi::STATUS_PUBLISHED,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('halaman_informasi', ['judul' => 'Percobaan Guest']);
    }

    #[Test]
    public function pelapor_dan_konselor_cannot_access_admin_halaman_index(): void
    {
        $this->actingAs($this->pelaporUser)
            ->get(route('admin.halaman-informasi.index'))
            ->assertForbidden();

        $this->actingAs($this->konselorUser)
            ->get(route('admin.halaman-informasi.index'))
            ->assertForbidden();
    }

    #[Test]
    public function admin_kasus_can_view_index_dan_mencari_judul(): void
    {
        $this->buatHalaman(['judul' => 'Panduan Keselamatan Berkendara', 'slug' => 'panduan-keselamatan']);
        $this->buatHalaman(['judul' => 'Kebijakan Data Pribadi', 'slug' => 'kebijakan-data']);

        $this->actingAs($this->adminUser)
            ->get(route('admin.halaman-informasi.index'))
            ->assertOk()
            ->assertSee('Manajemen Halaman Informasi')
            ->assertSee('Panduan Keselamatan Berkendara')
            ->assertSee('Kebijakan Data Pribadi');

        $this->actingAs($this->adminUser)
            ->get(route('admin.halaman-informasi.index', ['search' => 'Kebijakan']))
            ->assertOk()
            ->assertSee('Kebijakan Data Pribadi')
            ->assertDontSee('Panduan Keselamatan Berkendara');
    }

    #[Test]
    public function admin_kasus_can_create_halaman_dengan_slug_otomatis(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.halaman-informasi.store'), [
            'judul' => 'Halaman Uji Baru',
            'isi' => 'Konten halaman uji baru yang dibuat melalui formulir admin kasus.',
            'status_publikasi' => HalamanInformasi::STATUS_DRAFT,
        ]);

        $halaman = HalamanInformasi::where('judul', 'Halaman Uji Baru')->firstOrFail();

        $response->assertRedirect(route('admin.halaman-informasi.show', $halaman));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('halaman_informasi', [
            'halaman_id' => $halaman->halaman_id,
            'slug' => 'halaman-uji-baru',
            'status_publikasi' => HalamanInformasi::STATUS_DRAFT,
        ]);
    }

    #[Test]
    public function slug_tetap_unik_saat_judul_duplikat(): void
    {
        $this->buatHalaman(['judul' => 'Tentang Kami', 'slug' => 'tentang-kami']);

        $this->actingAs($this->adminUser)->post(route('admin.halaman-informasi.store'), [
            'judul' => 'Tentang Kami',
            'isi' => 'Versi kedua dari halaman tentang kami untuk menguji pembuatan slug unik.',
            'status_publikasi' => HalamanInformasi::STATUS_DRAFT,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('halaman_informasi', ['judul' => 'Tentang Kami', 'slug' => 'tentang-kami']);
        $this->assertDatabaseHas('halaman_informasi', ['judul' => 'Tentang Kami', 'slug' => 'tentang-kami-2']);
    }

    #[Test]
    public function admin_kasus_can_update_halaman_dan_slug_dihasilkan_ulang_saat_judul_berubah(): void
    {
        $halaman = $this->buatHalaman(['judul' => 'Judul Lama', 'slug' => 'judul-lama']);

        $response = $this->actingAs($this->adminUser)->put(route('admin.halaman-informasi.update', $halaman), [
            'judul' => 'Judul Baru Sesudah Ubah',
            'isi' => $halaman->isi,
            'status_publikasi' => HalamanInformasi::STATUS_PUBLISHED,
        ]);

        $halaman->refresh();

        $response->assertRedirect(route('admin.halaman-informasi.show', $halaman));
        $response->assertSessionHas('success');
        $this->assertSame('judul-baru-sesudah-ubah', $halaman->slug);
    }

    #[Test]
    public function slug_pertama_saat_update_tidak_diubah_kalau_judul_sama(): void
    {
        $halaman = $this->buatHalaman(['judul' => 'Visi Misi', 'slug' => 'visi-misi']);

        $this->actingAs($this->adminUser)
            ->put(route('admin.halaman-informasi.update', $halaman), [
                'judul' => 'Visi Misi',
                'isi' => 'Isi baru yang sudah diperbarui dan tetap melewati aturan panjang minimal.',
                'status_publikasi' => HalamanInformasi::STATUS_PUBLISHED,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.halaman-informasi.show', $halaman));

        $halaman->refresh();
        $this->assertSame('visi-misi', $halaman->slug);
        $this->assertStringContainsString('Isi baru yang sudah diperbarui', $halaman->isi);
    }

    #[Test]
    public function update_menolak_isi_yang_terlalu_singkat(): void
    {
        $halaman = $this->buatHalaman();

        $this->actingAs($this->adminUser)
            ->put(route('admin.halaman-informasi.update', $halaman), [
                'judul' => 'Judul Cukup',
                'isi' => 'pendek',
                'status_publikasi' => HalamanInformasi::STATUS_PUBLISHED,
            ])
            ->assertSessionHasErrors('isi');
    }

    #[Test]
    public function halaman_published_tampil_untuk_guest(): void
    {
        $halaman = $this->buatHalaman([
            'judul' => 'Kebijakan Privasi RuangAman',
            'slug' => 'kebijakan-privasi-ruangaman',
            'isi' => 'Kami membatasi akses data laporan hanya pada petugas yang ditugaskan.',
        ]);

        $this->get(route('halaman.show', $halaman->slug))
            ->assertOk()
            ->assertSee('Kebijakan Privasi RuangAman')
            ->assertSee('petugas yang ditugaskan');
    }

    #[Test]
    public function halaman_draft_tidak_tampil_untuk_guest_tetapi_bisa_dilihat_admin(): void
    {
        $halaman = $this->buatHalaman([
            'judul' => 'Rencana Fitur Konseling',
            'slug' => 'rencana-fitur-konseling',
            'status_publikasi' => HalamanInformasi::STATUS_DRAFT,
        ]);

        $this->get(route('halaman.show', $halaman->slug))->assertForbidden();

        $this->actingAs($this->adminUser)
            ->get(route('halaman.show', $halaman->slug))
            ->assertOk()
            ->assertSee('Rencana Fitur Konseling');
    }

    #[Test]
    public function admin_kasus_can_delete_halaman(): void
    {
        $halaman = $this->buatHalaman(['judul' => 'Halaman Akan Dihapus', 'slug' => 'akan-dihapus']);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.halaman-informasi.destroy', $halaman));

        $response->assertRedirect(route('admin.halaman-informasi.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('halaman_informasi', ['halaman_id' => $halaman->halaman_id]);
    }

    #[Test]
    public function pelapor_cannot_delete_halaman(): void
    {
        $halaman = $this->buatHalaman(['judul' => 'Halaman Aman', 'slug' => 'halaman-aman']);

        $this->actingAs($this->pelaporUser)
            ->delete(route('admin.halaman-informasi.destroy', $halaman))
            ->assertForbidden();

        $this->assertDatabaseHas('halaman_informasi', ['halaman_id' => $halaman->halaman_id]);
    }
}
