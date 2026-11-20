<?php

namespace Tests\Feature;

use App\Models\GlosariumKbg;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Pbi02ManajemenGlosariumKbgTest extends TestCase
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
    private function buatIstilah(array $override = []): GlosariumKbg
    {
        return GlosariumKbg::query()->create(array_merge([
            'istilah' => 'Istilah Dasar Pengujian',
            'definisi' => 'Definisi dasar yang dipakai sebagai titik awal pengujian modul glosarium.',
            'kategori' => 'Umum',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ], $override));
    }

    #[Test]
    public function guest_dan_pelapor_dan_konselor_cannot_access_admin_glosarium(): void
    {
        $this->get(route('admin.glosarium.index'))->assertRedirect(route('login'));
        $this->get(route('admin.glosarium.create'))->assertRedirect(route('login'));

        $this->actingAs($this->pelaporUser)
            ->get(route('admin.glosarium.index'))
            ->assertForbidden();

        $this->actingAs($this->konselorUser)
            ->post(route('admin.glosarium.store'), [
                'istilah' => 'Percobaan Konselor',
                'definisi' => 'Definisi percobaan yang melewati batas minimal karakter.',
                'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('glosarium_kbg', ['istilah' => 'Percobaan Konselor']);
    }

    #[Test]
    public function admin_kasus_can_view_index_dan_mencari_istilah(): void
    {
        $this->buatIstilah(['istilah' => 'Marker Alfa Uji', 'definisi' => 'Definisi marker alfa untuk keperluan penyaringan daftar admin.']);
        $this->buatIstilah(['istilah' => 'Marker Beta Uji', 'definisi' => 'Definisi marker beta untuk keperluan penyaringan daftar admin.', 'kategori' => 'Digital']);

        $this->actingAs($this->adminUser)
            ->get(route('admin.glosarium.index'))
            ->assertOk()
            ->assertSee('Manajemen Glosarium');

        $this->actingAs($this->adminUser)
            ->get(route('admin.glosarium.index', ['search' => 'Marker']))
            ->assertOk()
            ->assertSee('Marker Alfa Uji')
            ->assertSee('Marker Beta Uji');

        $this->actingAs($this->adminUser)
            ->get(route('admin.glosarium.index', ['search' => 'Marker Beta']))
            ->assertOk()
            ->assertSee('Marker Beta Uji')
            ->assertDontSee('Marker Alfa Uji');
    }

    #[Test]
    public function admin_kasus_can_create_istilah(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.glosarium.store'), [
            'istilah' => 'Istilah Baru Hasil Uji',
            'definisi' => 'Definisi istilah baru yang dibuat melalui formulir admin kasus.',
            'kategori' => 'Layanan',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ]);

        $response->assertRedirect(route('admin.glosarium.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('glosarium_kbg', [
            'istilah' => 'Istilah Baru Hasil Uji',
            'kategori' => 'Layanan',
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ]);
    }

    #[Test]
    public function kategori_boleh_kosong_dan_definisi_terlalu_singkat_ditolak(): void
    {
        $this->actingAs($this->adminUser)->post(route('admin.glosarium.store'), [
            'istilah' => 'Istilah Tanpa Kategori',
            'definisi' => 'Pendampingan proses hukum oleh pihak berkompeten.',
            'kategori' => null,
            'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('glosarium_kbg', ['istilah' => 'Istilah Tanpa Kategori', 'kategori' => null]);

        $this->actingAs($this->adminUser)
            ->post(route('admin.glosarium.store'), [
                'istilah' => 'Istilah Tanpa Definisi',
                'definisi' => 'pendek',
                'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
            ])
            ->assertSessionHasErrors('definisi');
    }

    #[Test]
    public function istilah_duplikat_ditolak(): void
    {
        $this->buatIstilah(['istilah' => 'Istilah Kembar Uji']);

        $this->actingAs($this->adminUser)
            ->post(route('admin.glosarium.store'), [
                'istilah' => 'Istilah Kembar Uji',
                'definisi' => 'Definisi kedua yang seharusnya ditolak karena istilah sudah terdaftar.',
                'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
            ])
            ->assertSessionHasErrors('istilah');

        $this->assertSame(1, GlosariumKbg::where('istilah', 'Istilah Kembar Uji')->count());
    }

    #[Test]
    public function admin_kasus_can_update_dan_delete_istilah(): void
    {
        $glosarium = $this->buatIstilah([
            'istilah' => 'Istilah Rutin Uji',
            'definisi' => 'Definisi awal sebelum diperbarui oleh admin kasus.',
        ]);

        $this->actingAs($this->adminUser)
            ->put(route('admin.glosarium.update', $glosarium), [
                'istilah' => 'Istilah Rutin Uji',
                'definisi' => 'Definisi yang sudah diperbarui oleh admin kasus lewat formulir ubah.',
                'kategori' => 'Psikologi',
                'status_publikasi' => GlosariumKbg::STATUS_PUBLISHED,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.glosarium.index'));

        $this->assertDatabaseHas('glosarium_kbg', [
            'glosarium_id' => $glosarium->glosarium_id,
            'kategori' => 'Psikologi',
        ]);

        $this->actingAs($this->adminUser)
            ->delete(route('admin.glosarium.destroy', $glosarium))
            ->assertRedirect(route('admin.glosarium.index'));

        $this->assertDatabaseMissing('glosarium_kbg', ['glosarium_id' => $glosarium->glosarium_id]);
    }

    #[Test]
    public function glosarium_publik_hanya_tampil_istilah_published(): void
    {
        $this->buatIstilah(['istilah' => 'Marker Publik Tampil', 'definisi' => 'Definisi marker yang boleh dilihat pengunjung umum.']);
        $this->buatIstilah([
            'istilah' => 'Marker Publik Draft',
            'definisi' => 'Definisi marker yang belum disetujui tim redaksi untuk konsumsi publik.',
            'status_publikasi' => GlosariumKbg::STATUS_DRAFT,
        ]);

        $this->get(route('glosarium.index'))
            ->assertOk()
            ->assertSee('Glosarium')
            ->assertSee('Marker Publik Tampil')
            ->assertDontSee('Marker Publik Draft');
    }

    #[Test]
    public function glosarium_publik_dapat_dicari_lewat_query_string(): void
    {
        $this->buatIstilah(['istilah' => 'Marker Cari Ketemu', 'definisi' => 'Definisi mengandung kata kunci zebra-zebra untuk pengujian pencarian.']);
        $this->buatIstilah(['istilah' => 'Marker Cari Tidak', 'definisi' => 'Definisi lain yang tidak memuat kata kunci pencarian yang dipakai uji.']);

        $this->get(route('glosarium.index', ['search' => 'zebra-zebra']))
            ->assertOk()
            ->assertSee('Marker Cari Ketemu')
            ->assertDontSee('Marker Cari Tidak');
    }
}
