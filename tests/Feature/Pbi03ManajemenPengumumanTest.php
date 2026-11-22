<?php

namespace Tests\Feature;

use App\Models\Pengumuman;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Pbi03ManajemenPengumumanTest extends TestCase
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
    private function buatPengumuman(array $override = []): Pengumuman
    {
        return Pengumuman::query()->create(array_merge([
            'dibuat_oleh' => $this->adminUser->user_id,
            'judul' => 'Pengumuman Contoh',
            'isi' => 'Isi pengumuman contoh untuk keperluan pengujian periode tayang layanan.',
            'mulai_tayang' => now()->subDays(2)->toDateString(),
            'akhir_tayang' => now()->addDays(14)->toDateString(),
            'status' => Pengumuman::STATUS_AKTIF,
        ], $override));
    }

    #[Test]
    public function guest_dan_non_admin_cannot_access_admin_pengumuman(): void
    {
        $this->get(route('admin.pengumuman.index'))->assertRedirect(route('login'));

        $this->actingAs($this->pelaporUser)
            ->get(route('admin.pengumuman.index'))
            ->assertForbidden();

        $this->actingAs($this->konselorUser)
            ->post(route('admin.pengumuman.store'), [
                'judul' => 'Percobaan Konselor',
                'isi' => 'Isi yang cukup panjang untuk melewati aturan minimal karakter.',
                'mulai_tayang' => now()->toDateString(),
                'status' => Pengumuman::STATUS_AKTIF,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('pengumuman', ['judul' => 'Percobaan Konselor']);
    }

    #[Test]
    public function admin_kasus_can_view_index_dan_mencari_judul(): void
    {
        $this->buatPengumuman(['judul' => 'Jadwal Libur Layanan']);
        $this->buatPengumuman(['judul' => 'Webinar Bulan Ini']);

        $this->actingAs($this->adminUser)
            ->get(route('admin.pengumuman.index'))
            ->assertOk()
            ->assertSee('Manajemen Pengumuman')
            ->assertSee('Jadwal Libur Layanan')
            ->assertSee('Webinar Bulan Ini');

        $this->actingAs($this->adminUser)
            ->get(route('admin.pengumuman.index', ['search' => 'Libur']))
            ->assertOk()
            ->assertSee('Jadwal Libur Layanan')
            ->assertDontSee('Webinar Bulan Ini');
    }

    #[Test]
    public function admin_kasus_can_create_pengumuman_dengan_pencatatan_pembuat(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.pengumuman.store'), [
            'judul' => 'Pembukaan Pendaftaran Relawan',
            'isi' => 'Pendaftaran relawan pendamping dibuka untuk periode berikut.',
            'mulai_tayang' => now()->toDateString(),
            'akhir_tayang' => now()->addDays(7)->toDateString(),
            'status' => Pengumuman::STATUS_AKTIF,
        ]);

        $pengumuman = Pengumuman::where('judul', 'Pembukaan Pendaftaran Relawan')->firstOrFail();

        $response->assertRedirect(route('admin.pengumuman.show', $pengumuman));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pengumuman', [
            'pengumuman_id' => $pengumuman->pengumuman_id,
            'dibuat_oleh' => $this->adminUser->user_id,
            'status' => Pengumuman::STATUS_AKTIF,
        ]);
    }

    #[Test]
    public function pengumuman_tidak_mengirim_notifikasi_otomatis(): void
    {
        $pengumuman = $this->buatPengumuman(['judul' => 'Pengumuman Tanpa Notifikasi']);

        $this->actingAs($this->adminUser)->post(route('admin.pengumuman.store'), [
            'judul' => 'Penambahan Tanpa Notifikasi',
            'isi' => 'Konten ini dibuat untuk memastikan tidak ada notifikasi otomatis terkirim.',
            'mulai_tayang' => now()->toDateString(),
            'status' => Pengumuman::STATUS_AKTIF,
        ])->assertSessionHasNoErrors();

        $this->actingAs($this->adminUser)->put(route('admin.pengumuman.update', $pengumuman), [
            'judul' => 'Pengumuman Tanpa Notifikasi',
            'isi' => 'Konten sudah diperbarui dan tetap tidak memicu notifikasi apa pun.',
            'mulai_tayang' => $pengumuman->mulai_tayang->toDateString(),
            'status' => Pengumuman::STATUS_AKTIF,
        ])->assertSessionHasNoErrors();

        $this->get(route('pengumuman.index'))->assertOk();
        $this->assertDatabaseCount('notifikasi', 0);
    }

    #[Test]
    public function akhir_tayang_yang_lebih_awal_dari_mulai_tayang_ditolak(): void
    {
        $this->actingAs($this->adminUser)
            ->post(route('admin.pengumuman.store'), [
                'judul' => 'Periode Tayang Tidak Valid',
                'isi' => 'Pengumuman dengan akhir periode lebih awal daripada tanggal mulai tayang.',
                'mulai_tayang' => now()->toDateString(),
                'akhir_tayang' => now()->subDays(3)->toDateString(),
                'status' => Pengumuman::STATUS_AKTIF,
            ])
            ->assertSessionHasErrors('akhir_tayang');

        $this->assertDatabaseMissing('pengumuman', ['judul' => 'Periode Tayang Tidak Valid']);
    }

    #[Test]
    public function daftar_publik_hanya_memuat_pengumuman_dalam_periode_tayang(): void
    {
        $this->buatPengumuman([
            'judul' => 'Pengumuman Sedang Tayang',
            'mulai_tayang' => now()->subDays(3)->toDateString(),
            'akhir_tayang' => now()->addDays(3)->toDateString(),
        ]);
        $this->buatPengumuman([
            'judul' => 'Pengumuman Tayang Mendatang',
            'mulai_tayang' => now()->addDays(5)->toDateString(),
            'akhir_tayang' => now()->addDays(10)->toDateString(),
        ]);
        $this->buatPengumuman([
            'judul' => 'Pengumuman Periode Habis',
            'mulai_tayang' => now()->subDays(20)->toDateString(),
            'akhir_tayang' => now()->subDays(2)->toDateString(),
        ]);
        $this->buatPengumuman([
            'judul' => 'Pengumuman Sudah Diarsipkan',
            'status' => Pengumuman::STATUS_ARSIP,
        ]);
        $this->buatPengumuman([
            'judul' => 'Pengumuman Tanpa Batas Akhir',
            'akhir_tayang' => null,
        ]);

        $this->get(route('pengumuman.index'))
            ->assertOk()
            ->assertSee('Pengumuman Sedang Tayang')
            ->assertSee('Pengumuman Tanpa Batas Akhir')
            ->assertDontSee('Pengumuman Tayang Mendatang')
            ->assertDontSee('Pengumuman Periode Habis')
            ->assertDontSee('Pengumuman Sudah Diarsipkan');
    }

    #[Test]
    public function detail_publik_hanya_bisa_dibuka_selama_periode_tayang(): void
    {
        $sedangTayang = $this->buatPengumuman([
            'judul' => 'Detail Yang Bisa Dibuka',
            'isi' => 'Detail ini dapat diakses publik karena berada dalam periode tayang.',
        ]);
        $sudahLewat = $this->buatPengumuman([
            'judul' => 'Detail Yang Sudah Lewat',
            'isi' => 'Detail ini tidak boleh dibuka publik karena periodenya telah berakhir.',
            'mulai_tayang' => now()->subDays(15)->toDateString(),
            'akhir_tayang' => now()->subDays(5)->toDateString(),
        ]);

        $this->get(route('pengumuman.show', $sedangTayang))
            ->assertOk()
            ->assertSee('Detail Yang Bisa Dibuka');

        $this->get(route('pengumuman.show', $sudahLewat))->assertForbidden();
        $this->actingAs($this->pelaporUser)->get(route('pengumuman.show', $sudahLewat))->assertForbidden();

        $this->actingAs($this->adminUser)
            ->get(route('pengumuman.show', $sudahLewat))
            ->assertOk()
            ->assertSee('Detail Yang Sudah Lewat');
    }

    #[Test]
    public function admin_kasus_can_update_dan_delete_pengumuman(): void
    {
        $pengumuman = $this->buatPengumuman(['judul' => 'Judul Sebelum Ubah']);

        $this->actingAs($this->adminUser)
            ->put(route('admin.pengumuman.update', $pengumuman), [
                'judul' => 'Judul Sesudah Ubah',
                'isi' => $pengumuman->isi,
                'mulai_tayang' => $pengumuman->mulai_tayang->toDateString(),
                'akhir_tayang' => null,
                'status' => Pengumuman::STATUS_ARSIP,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pengumuman', [
            'pengumuman_id' => $pengumuman->pengumuman_id,
            'judul' => 'Judul Sesudah Ubah',
            'status' => Pengumuman::STATUS_ARSIP,
            'akhir_tayang' => null,
        ]);

        $this->actingAs($this->adminUser)
            ->delete(route('admin.pengumuman.destroy', $pengumuman))
            ->assertRedirect(route('admin.pengumuman.index'));

        $this->assertDatabaseMissing('pengumuman', ['pengumuman_id' => $pengumuman->pengumuman_id]);
    }

    #[Test]
    public function pengumuman_diarsipkan_hilang_dari_daftar_publik_setelah_update(): void
    {
        $pengumuman = $this->buatPengumuman(['judul' => 'Diarsipkan Lewat Update']);

        $this->get(route('pengumuman.index'))->assertOk()->assertSee('Diarsipkan Lewat Update');

        $this->actingAs($this->adminUser)
            ->followingRedirects()
            ->put(route('admin.pengumuman.update', $pengumuman), [
                'judul' => 'Diarsipkan Lewat Update',
                'isi' => $pengumuman->isi,
                'mulai_tayang' => $pengumuman->mulai_tayang->toDateString(),
                'status' => Pengumuman::STATUS_ARSIP,
            ])
            ->assertOk()
            ->assertSessionHasNoErrors();

        $this->get(route('pengumuman.index'))->assertOk()->assertDontSee('Diarsipkan Lewat Update');
    }

    #[Test]
    public function beranda_tampil_tiga_pengumuman_tayang_terbaru(): void
    {
        Pengumuman::query()->delete();

        $terbaru = $this->buatPengumuman(['judul' => 'Pengumuman Beranda Terbaru', 'mulai_tayang' => now()->toDateString()]);
        $this->buatPengumuman(['judul' => 'Pengumuman Beranda Kedua', 'mulai_tayang' => now()->subDays(3)->toDateString()]);
        $this->buatPengumuman(['judul' => 'Pengumuman Beranda Ketiga', 'mulai_tayang' => now()->subDays(5)->toDateString()]);
        $this->buatPengumuman(['judul' => 'Pengumuman Beranda Keempat', 'mulai_tayang' => now()->subDays(7)->toDateString()]);
        $this->buatPengumuman(['judul' => 'Pengumuman Beranda Belum Tayang', 'mulai_tayang' => now()->addDays(4)->toDateString(), 'akhir_tayang' => null]);
        $this->buatPengumuman(['judul' => 'Pengumuman Beranda Lewat Periode', 'mulai_tayang' => now()->subDays(30)->toDateString(), 'akhir_tayang' => now()->subDays(10)->toDateString()]);
        $this->buatPengumuman(['judul' => 'Pengumuman Beranda Arsip', 'status' => Pengumuman::STATUS_ARSIP]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Pengumuman terbaru')
            ->assertSee('Pengumuman Beranda Terbaru')
            ->assertSee('Pengumuman Beranda Kedua')
            ->assertSee('Pengumuman Beranda Ketiga')
            ->assertDontSee('Pengumuman Beranda Keempat')
            ->assertDontSee('Pengumuman Beranda Belum Tayang')
            ->assertDontSee('Pengumuman Beranda Lewat Periode')
            ->assertDontSee('Pengumuman Beranda Arsip')
            ->assertSee(route('pengumuman.show', $terbaru), false);
    }

    #[Test]
    public function beranda_tanpa_pengumuman_tayang_tidak_memuat_blok_pengumuman(): void
    {
        Pengumuman::query()->delete();

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Pengumuman terbaru');
    }
}
