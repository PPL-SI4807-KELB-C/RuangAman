<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Pbi09ManajemenFaqTest extends TestCase
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

    #[Test]
    public function tamu_tidak_dapat_mengakses_manajemen_faq(): void
    {
        $faq = Faq::firstOrFail();

        $this->get(route('admin.faq.index'))->assertRedirect(route('login'));
        $this->get(route('admin.faq.create'))->assertRedirect(route('login'));
        $this->post(route('admin.faq.store'), [])->assertRedirect(route('login'));
        $this->get(route('admin.faq.show', $faq))->assertRedirect(route('login'));
        $this->get(route('admin.faq.edit', $faq))->assertRedirect(route('login'));
        $this->put(route('admin.faq.update', $faq), [])->assertRedirect(route('login'));
        $this->delete(route('admin.faq.destroy', $faq))->assertRedirect(route('login'));
    }

    #[Test]
    public function pelapor_dan_konselor_tidak_dapat_mengelola_faq(): void
    {
        $faq = Faq::firstOrFail();
        $jumlahAwal = Faq::count();

        foreach ([$this->pelaporUser, $this->konselorUser] as $user) {
            $this->actingAs($user)->get(route('admin.faq.index'))->assertForbidden();
            $this->actingAs($user)->get(route('admin.faq.create'))->assertForbidden();
            $this->actingAs($user)->post(route('admin.faq.store'), [
                'pertanyaan' => 'Pertanyaan ilegal',
                'jawaban' => 'Jawaban ilegal',
                'urutan' => 1,
                'status_publikasi' => Faq::STATUS_PUBLISHED,
            ])->assertForbidden();
            $this->actingAs($user)->put(route('admin.faq.update', $faq), [
                'pertanyaan' => 'Diubah ilegal',
                'jawaban' => 'Jawaban',
                'urutan' => 1,
                'status_publikasi' => Faq::STATUS_DRAFT,
            ])->assertForbidden();
            $this->actingAs($user)->delete(route('admin.faq.destroy', $faq))->assertForbidden();
        }

        $this->assertSame($jumlahAwal, Faq::count());
    }

    #[Test]
    public function admin_kasus_dapat_melihat_daftar_faq_beserta_pencarian_dan_filter_status(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.faq.index'));

        $response->assertOk()
            ->assertSee('Manajemen FAQ / Pusat Bantuan')
            ->assertSee('Daftar FAQ')
            ->assertSee('Apakah saya harus menggunakan nama asli?')
            ->assertSee('Bagaimana memilih instansi bantuan?');

        $searchResponse = $this->actingAs($this->adminUser)->get(route('admin.faq.index', [
            'search' => 'Keluar Cepat',
        ]));

        $searchResponse->assertOk()
            ->assertSee('Apa yang dilakukan tombol Keluar Cepat?')
            ->assertDontSee('Apakah saya harus menggunakan nama asli?');

        $draftResponse = $this->actingAs($this->adminUser)->get(route('admin.faq.index', [
            'status' => Faq::STATUS_DRAFT,
        ]));

        $draftResponse->assertOk()
            ->assertSee('Bagaimana memilih instansi bantuan?')
            ->assertDontSee('Apakah saya harus menggunakan nama asli?');
    }

    #[Test]
    public function admin_kasus_dapat_membuka_form_tambah_dan_menyimpan_faq_baru(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('admin.faq.create'))
            ->assertOk()
            ->assertSee('Tambah FAQ')
            ->assertSee('Simpan FAQ');

        $response = $this->actingAs($this->adminUser)->post(route('admin.faq.store'), [
            'pertanyaan' => 'Apakah layanan konseling berbayar?',
            'jawaban' => 'Tidak. Seluruh layanan pendampingan awal bersifat gratis.',
            'urutan' => 7,
            'status_publikasi' => Faq::STATUS_PUBLISHED,
        ]);

        $response->assertRedirect(route('admin.faq.index'))
            ->assertSessionHas('success', 'FAQ berhasil ditambahkan.');

        $this->assertDatabaseHas('faq', [
            'pertanyaan' => 'Apakah layanan konseling berbayar?',
            'urutan' => 7,
            'status_publikasi' => Faq::STATUS_PUBLISHED,
        ]);
    }

    #[Test]
    public function validasi_input_faq_wajib_dan_batas_urutan_diterapkan(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.faq.store'), [
            'pertanyaan' => '',
            'jawaban' => '',
            'urutan' => 0,
            'status_publikasi' => 'tidak-valid',
        ]);

        $response->assertSessionHasErrors([
            'pertanyaan',
            'jawaban',
            'urutan',
            'status_publikasi',
        ]);
    }

    #[Test]
    public function admin_kasus_dapat_melihat_detail_faq(): void
    {
        $faq = Faq::where('status_publikasi', Faq::STATUS_PUBLISHED)->ordered()->firstOrFail();

        $this->actingAs($this->adminUser)
            ->get(route('admin.faq.show', $faq))
            ->assertOk()
            ->assertSee('Detail FAQ')
            ->assertSee($faq->pertanyaan)
            ->assertSee($faq->jawaban)
            ->assertSee('Publikasi &amp; urutan', false);
    }

    #[Test]
    public function admin_kasus_dapat_mengubah_faq(): void
    {
        $faq = Faq::firstOrFail();

        $this->actingAs($this->adminUser)
            ->get(route('admin.faq.edit', $faq))
            ->assertOk()
            ->assertSee('Ubah FAQ')
            ->assertSee('Simpan perubahan');

        $response = $this->actingAs($this->adminUser)->put(route('admin.faq.update', $faq), [
            'pertanyaan' => 'Pertanyaan yang sudah diperbarui?',
            'jawaban' => 'Jawaban yang diperbarui dengan jelas.',
            'urutan' => 10,
            'status_publikasi' => Faq::STATUS_DRAFT,
        ]);

        $response->assertRedirect(route('admin.faq.index'))
            ->assertSessionHas('success', 'FAQ berhasil diperbarui.');

        $this->assertDatabaseHas('faq', [
            'faq_id' => $faq->faq_id,
            'pertanyaan' => 'Pertanyaan yang sudah diperbarui?',
            'urutan' => 10,
            'status_publikasi' => Faq::STATUS_DRAFT,
        ]);
    }

    #[Test]
    public function admin_kasus_dapat_menghapus_faq(): void
    {
        $faq = Faq::create([
            'pertanyaan' => 'Pertanyaan sementara?',
            'jawaban' => 'Akan dihapus.',
            'urutan' => 99,
            'status_publikasi' => Faq::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.faq.destroy', $faq));

        $response->assertRedirect(route('admin.faq.index'))
            ->assertSessionHas('success', 'FAQ berhasil dihapus.');

        $this->assertDatabaseMissing('faq', [
            'faq_id' => $faq->faq_id,
        ]);
    }

    #[Test]
    public function publik_dapat_mengakses_pusat_bantuan_hanya_menampilkan_faq_dipublikasikan_sesuai_urutan(): void
    {
        $response = $this->get(route('faq.index'));

        $response->assertOk()
            ->assertSee('Ada yang ingin Anda ketahui?')
            ->assertSee('Apakah saya harus menggunakan nama asli?')
            ->assertSee('Apa yang dilakukan tombol Keluar Cepat?')
            ->assertDontSee('Bagaimana memilih instansi bantuan?');

        $searchResponse = $this->get(route('faq.index', [
            'search' => 'melacak',
        ]));

        $searchResponse->assertOk()
            ->assertSee('Bagaimana cara melacak laporan saya?')
            ->assertDontSee('Apakah saya harus menyertakan bukti?');
    }
}
