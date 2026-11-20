<?php

namespace Tests\Feature;

use App\Models\Konselor;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Pbi13ManajemenDataKonselorTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private User $pelaporUser;

    private User $konselorUser;

    private User $calonKonselorUser;

    private User $konselorLainUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);

        $this->adminUser = User::whereHas('role', fn ($query) => $query->where('nama_role', 'Admin Kasus'))->firstOrFail();
        $this->pelaporUser = User::whereHas('role', fn ($query) => $query->where('nama_role', 'Pelapor'))->firstOrFail();
        $this->konselorUser = User::whereHas('role', fn ($query) => $query->where('nama_role', 'Konselor'))->firstOrFail();

        $roleKonselor = Role::where('nama_role', 'Konselor')->firstOrFail();

        // Akun ber-role Konselor yang belum memiliki profil (untuk uji penambahan data).
        $this->calonKonselorUser = User::query()->create([
            'role_id' => $roleKonselor->role_id,
            'nama_tampilan' => 'Konselor Baru',
            'email' => 'konselor.baru@ruangaman.test',
            'password_hash' => 'password',
            'status' => 'aktif',
        ]);

        // Akun konselor lain yang sudah memiliki profil.
        $this->konselorLainUser = User::query()->create([
            'role_id' => $roleKonselor->role_id,
            'nama_tampilan' => 'Konselor Lain',
            'email' => 'konselor.lain@ruangaman.test',
            'password_hash' => 'password',
            'status' => 'aktif',
        ]);

        Konselor::query()->create([
            'user_id' => $this->konselorLainUser->user_id,
            'nama' => 'Dewi Anggraini, M.Psi.',
            'spesialisasi' => 'Konseling Keluarga',
            'bio' => 'Pendamping keluarga dan anak.',
            'status' => Konselor::STATUS_AKTIF,
        ]);
    }

    private function konselorMilikSaya(): Konselor
    {
        return Konselor::query()->where('user_id', $this->konselorUser->user_id)->firstOrFail();
    }

    private function buatJadwal(Konselor $konselor): void
    {
        DB::table('jadwal_konseling')->insert([
            'konselor_id' => $konselor->konselor_id,
            'user_id' => null,
            'tanggal' => now()->addDays(3)->toDateString(),
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '10:00:00',
            'metode' => 'online',
            'lokasi_atau_tautan' => 'https://meet.ruangaman.test/konseling-uji',
            'status' => 'tersedia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    #[Test]
    public function guest_cannot_access_admin_konselor_pages(): void
    {
        $this->get(route('admin.konselor.index'))->assertRedirect(route('login'));
        $this->get(route('admin.konselor.create'))->assertRedirect(route('login'));

        $this->post(route('admin.konselor.store'), [
            'user_id' => $this->calonKonselorUser->user_id,
            'nama' => 'Percobaan Guest',
            'spesialisasi' => 'Umum',
            'status' => Konselor::STATUS_AKTIF,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('konselor', ['nama' => 'Percobaan Guest']);
    }

    #[Test]
    public function pelapor_and_konselor_cannot_access_admin_konselor_index(): void
    {
        $this->actingAs($this->pelaporUser)
            ->get(route('admin.konselor.index'))
            ->assertForbidden();

        $this->actingAs($this->konselorUser)
            ->get(route('admin.konselor.index'))
            ->assertForbidden();

        $this->actingAs($this->konselorUser)
            ->get(route('admin.konselor.create'))
            ->assertForbidden();
    }

    #[Test]
    public function admin_kasus_can_view_konselor_index(): void
    {
        $konselor = $this->konselorMilikSaya();

        $response = $this->actingAs($this->adminUser)->get(route('admin.konselor.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Data Konselor');
        $response->assertSee($konselor->nama);
        $response->assertSee('Dewi Anggraini, M.Psi.');
    }

    #[Test]
    public function admin_kasus_can_filter_konselor_by_status(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.konselor.index', ['status' => Konselor::STATUS_CUTI]));

        $response->assertOk();
        $response->assertSee('Maya Larasati, M.Psi.');
        $response->assertDontSee('Bagas Prasetya, S.Psi.');
    }

    #[Test]
    public function admin_kasus_can_create_konselor_profile_for_eligible_user(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.konselor.store'), [
            'user_id' => $this->calonKonselorUser->user_id,
            'nama' => 'Sari Puspita, M.Psi.',
            'spesialisasi' => 'Psikologi Klinis Anak',
            'bio' => 'Berpengalaman mendampingi anak dan remaja penyintas kekerasan.',
            'status' => Konselor::STATUS_AKTIF,
        ]);

        $konselor = Konselor::query()->where('user_id', $this->calonKonselorUser->user_id)->firstOrFail();

        $response->assertRedirect(route('admin.konselor.show', $konselor));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('konselor', [
            'konselor_id' => $konselor->konselor_id,
            'user_id' => $this->calonKonselorUser->user_id,
            'nama' => 'Sari Puspita, M.Psi.',
            'spesialisasi' => 'Psikologi Klinis Anak',
            'status' => Konselor::STATUS_AKTIF,
        ]);
    }

    #[Test]
    public function admin_kasus_cannot_create_duplicate_profile_for_same_user(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.konselor.store'), [
            'user_id' => $this->konselorUser->user_id,
            'nama' => 'Duplikat Profil',
            'spesialisasi' => 'Umum',
            'status' => Konselor::STATUS_AKTIF,
        ]);

        $response->assertSessionHasErrors('user_id');
        $this->assertEquals(
            1,
            Konselor::query()->where('user_id', $this->konselorUser->user_id)->count()
        );
    }

    #[Test]
    public function admin_kasus_cannot_create_profile_for_non_konselor_user(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.konselor.store'), [
            'user_id' => $this->pelaporUser->user_id,
            'nama' => 'Salah Role',
            'spesialisasi' => 'Umum',
            'status' => Konselor::STATUS_AKTIF,
        ]);

        $response->assertSessionHasErrors('user_id');
        $this->assertDatabaseMissing('konselor', [
            'user_id' => $this->pelaporUser->user_id,
        ]);
    }

    #[Test]
    public function konselor_creation_requires_mandatory_fields_and_valid_status(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.konselor.store'), [
            'user_id' => $this->calonKonselorUser->user_id,
            'nama' => '',
            'spesialisasi' => '',
            'status' => 'libur-panjang',
        ]);

        $response->assertSessionHasErrors(['nama', 'spesialisasi', 'status']);
        $this->assertDatabaseMissing('konselor', ['user_id' => $this->calonKonselorUser->user_id]);
    }

    #[Test]
    public function admin_kasus_can_update_konselor_without_changing_owner_account(): void
    {
        $konselor = $this->konselorMilikSaya();

        $response = $this->actingAs($this->adminUser)->put(route('admin.konselor.update', $konselor), [
            'user_id' => $this->pelaporUser->user_id,
            'nama' => 'Rina Kusuma, M.Psi. (Diperbarui)',
            'spesialisasi' => 'Psikologi Klinis Dewasa & Trauma',
            'bio' => 'Bio diperbarui oleh Admin Kasus.',
            'status' => Konselor::STATUS_CUTI,
        ]);

        $response->assertRedirect(route('admin.konselor.show', $konselor));
        $response->assertSessionHas('success');

        $konselor->refresh();
        $this->assertSame('Rina Kusuma, M.Psi. (Diperbarui)', $konselor->nama);
        $this->assertSame('Psikologi Klinis Dewasa & Trauma', $konselor->spesialisasi);
        $this->assertSame(Konselor::STATUS_CUTI, $konselor->status);
        $this->assertSame((int) $this->konselorUser->user_id, (int) $konselor->user_id);
    }

    #[Test]
    public function admin_kasus_cannot_delete_konselor_with_schedule_slots(): void
    {
        $konselor = $this->konselorMilikSaya();
        $this->buatJadwal($konselor);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.konselor.destroy', $konselor));

        $response->assertRedirect(route('admin.konselor.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('konselor', ['konselor_id' => $konselor->konselor_id]);
        $this->assertDatabaseCount('jadwal_konseling', 1);
    }

    #[Test]
    public function admin_kasus_can_delete_unused_konselor_profile(): void
    {
        $konselor = Konselor::query()->create([
            'user_id' => $this->calonKonselorUser->user_id,
            'nama' => 'Profil Tanpa Jadwal',
            'spesialisasi' => 'Konseling Singkat',
            'bio' => null,
            'status' => Konselor::STATUS_NONAKTIF,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.konselor.destroy', $konselor));

        $response->assertRedirect(route('admin.konselor.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('konselor', ['konselor_id' => $konselor->konselor_id]);
        $this->assertDatabaseHas('users', ['user_id' => $this->calonKonselorUser->user_id]);
    }

    #[Test]
    public function konselor_can_view_own_profile_page(): void
    {
        $konselor = $this->konselorMilikSaya();

        $response = $this->actingAs($this->konselorUser)->get(route('konselor.profil.edit'));

        $response->assertOk();
        $response->assertSee('Profil Konselor Saya');
        $response->assertSee($konselor->nama);
    }

    #[Test]
    public function konselor_can_update_own_biodata(): void
    {
        $konselor = $this->konselorMilikSaya();

        $response = $this->actingAs($this->konselorUser)->put(route('konselor.profil.update'), [
            'nama' => 'Rina Kusuma, M.Psi., Psikolog',
            'spesialisasi' => 'Psikologi Klinis & Pendampingan Trauma',
            'bio' => 'Pendamping psikososial penyintas KBG dengan pendekatan trauma-informed.',
        ]);

        $response->assertRedirect(route('konselor.profil.edit'));
        $response->assertSessionHas('success');

        $konselor->refresh();
        $this->assertSame('Rina Kusuma, M.Psi., Psikolog', $konselor->nama);
        $this->assertSame('Psikologi Klinis & Pendampingan Trauma', $konselor->spesialisasi);
    }

    #[Test]
    public function konselor_cannot_change_own_availability_status(): void
    {
        $konselor = $this->konselorMilikSaya();

        $this->actingAs($this->konselorUser)->put(route('konselor.profil.update'), [
            'nama' => $konselor->nama,
            'spesialisasi' => $konselor->spesialisasi,
            'bio' => $konselor->bio,
            'status' => Konselor::STATUS_NONAKTIF,
        ])->assertSessionHas('success');

        $this->assertSame(Konselor::STATUS_AKTIF, $konselor->refresh()->status);
    }

    #[Test]
    public function konselor_cannot_access_admin_konselor_detail_of_other_counselor(): void
    {
        $konselorLain = Konselor::query()->where('user_id', $this->konselorLainUser->user_id)->firstOrFail();

        $this->actingAs($this->konselorUser)
            ->get(route('admin.konselor.show', $konselorLain))
            ->assertForbidden();

        $this->actingAs($this->konselorUser)
            ->get(route('admin.konselor.edit', $konselorLain))
            ->assertForbidden();

        $this->actingAs($this->konselorUser)
            ->put(route('admin.konselor.update', $konselorLain), [
                'nama' => 'Percobaan Ubah Profil Lain',
                'spesialisasi' => 'Umum',
                'status' => Konselor::STATUS_AKTIF,
            ])
            ->assertForbidden();

        $this->assertSame('Dewi Anggraini, M.Psi.', $konselorLain->refresh()->nama);
    }

    #[Test]
    public function pelapor_and_admin_cannot_access_counselor_profile_page(): void
    {
        $this->actingAs($this->pelaporUser)
            ->get(route('konselor.profil.edit'))
            ->assertForbidden();

        $this->actingAs($this->adminUser)
            ->get(route('konselor.profil.edit'))
            ->assertForbidden();
    }

    #[Test]
    public function admin_konselor_pages_never_expose_account_email(): void
    {
        $konselor = $this->konselorMilikSaya();

        $index = $this->actingAs($this->adminUser)->get(route('admin.konselor.index'));
        $index->assertOk();
        $index->assertDontSee($this->konselorUser->email);

        $show = $this->actingAs($this->adminUser)->get(route('admin.konselor.show', $konselor));
        $show->assertOk();
        $show->assertDontSee($this->konselorUser->email);
        $show->assertDontSee($this->konselorLainUser->email);

        $edit = $this->actingAs($this->adminUser)->get(route('admin.konselor.edit', $konselor));
        $edit->assertOk();
        $edit->assertDontSee($this->konselorUser->email);
    }
}
