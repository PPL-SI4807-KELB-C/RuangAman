<?php

namespace Tests\Feature;

use App\Models\ProfilPelapor;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pbi17ProfilPelaporTest extends TestCase
{
    use RefreshDatabase;

    private User $pelapor;
    private User $konselor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);

        $this->pelapor = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Pelapor'))->firstOrFail();
        $this->konselor = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Konselor'))->firstOrFail();
    }

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('profil-pelapor.edit'))->assertRedirect(route('login'));
    }

    public function test_non_pelapor_dilarang_mengakses_profil_pelapor(): void
    {
        $this->actingAs($this->konselor)->get(route('profil-pelapor.edit'))->assertForbidden();
        $this->actingAs($this->konselor)
            ->put(route('profil-pelapor.update'), ['alias' => 'Coba'])
            ->assertForbidden();
    }

    public function test_pelapor_dapat_membuka_halaman_profil(): void
    {
        $this->actingAs($this->pelapor)
            ->get(route('profil-pelapor.edit'))
            ->assertOk()
            ->assertSee('Bintang Laut');
    }

    public function test_profil_dibuat_otomatis_dengan_mode_anonim_aktif_jika_belum_ada(): void
    {
        ProfilPelapor::where('user_id', $this->pelapor->user_id)->delete();

        $this->actingAs($this->pelapor)->get(route('profil-pelapor.edit'))->assertOk();

        $profil = ProfilPelapor::where('user_id', $this->pelapor->user_id)->firstOrFail();
        $this->assertTrue($profil->preferensi_anonim);
    }

    public function test_pelapor_dapat_memperbarui_alias_kontak_dan_preferensi_anonim(): void
    {
        $response = $this->actingAs($this->pelapor)->put(route('profil-pelapor.update'), [
            'alias' => 'Camar Biru',
            'kontak_opsional' => '@camar_biru',
            'preferensi_anonim' => '1',
        ]);

        $response->assertRedirect(route('profil-pelapor.edit'));
        $response->assertSessionHas('success');

        $profil = ProfilPelapor::where('user_id', $this->pelapor->user_id)->firstOrFail();
        $this->assertSame('Camar Biru', $profil->alias);
        $this->assertSame('@camar_biru', $profil->kontak_opsional);
        $this->assertTrue($profil->preferensi_anonim);

        // Alias menjadi nama tampilan akun.
        $this->assertSame('Camar Biru', $this->pelapor->fresh()->nama_tampilan);
    }

    public function test_checkbox_tidak_dicentang_menonaktifkan_mode_anonim_bawaan(): void
    {
        $this->actingAs($this->pelapor)->put(route('profil-pelapor.update'), [
            'alias' => 'Bintang Laut',
        ]);

        $this->assertFalse(
            ProfilPelapor::where('user_id', $this->pelapor->user_id)->firstOrFail()->preferensi_anonim
        );
    }

    public function test_kontak_opsional_boleh_dikosongkan(): void
    {
        $this->actingAs($this->pelapor)->put(route('profil-pelapor.update'), [
            'alias' => 'Bintang Laut',
            'kontak_opsional' => '',
        ])->assertSessionHasNoErrors();

        $this->assertNull(
            ProfilPelapor::where('user_id', $this->pelapor->user_id)->firstOrFail()->kontak_opsional
        );
    }

    public function test_alias_wajib_diisi_dan_dibatasi_panjangnya(): void
    {
        $this->actingAs($this->pelapor)
            ->put(route('profil-pelapor.update'), ['alias' => ''])
            ->assertSessionHasErrors('alias');

        $this->actingAs($this->pelapor)
            ->put(route('profil-pelapor.update'), ['alias' => str_repeat('a', 101)])
            ->assertSessionHasErrors('alias');
    }

    public function test_kontak_opsional_dibatasi_100_karakter(): void
    {
        $this->actingAs($this->pelapor)
            ->put(route('profil-pelapor.update'), [
                'alias' => 'Bintang Laut',
                'kontak_opsional' => str_repeat('x', 101),
            ])
            ->assertSessionHasErrors('kontak_opsional');
    }
}
