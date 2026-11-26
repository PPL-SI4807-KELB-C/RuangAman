<?php

namespace Tests\Feature;

use App\Models\Artikel;
use App\Models\FeedbackArtikel;
use App\Models\KategoriArtikel;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Pbi12ManajemenFeedbackArtikelTest extends TestCase
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
            'judul' => 'Artikel Uji Feedback',
            'slug' => 'artikel-uji-feedback',
            'isi' => 'Isi artikel edukasi untuk pengujian feedback.',
            'status_publikasi' => Artikel::STATUS_PUBLISHED,
        ], $override));
    }

    private function buatFeedback(
        ?User $user = null,
        string $status = FeedbackArtikel::STATUS_PENDING,
        string $isi = 'Artikel ini sangat membantu saya.',
        ?Artikel $artikel = null,
    ): FeedbackArtikel {
        return FeedbackArtikel::create([
            'artikel_id' => ($artikel ?? $this->buatArtikel())->artikel_id,
            'user_id' => ($user ?? $this->pelapor)->user_id,
            'isi_feedback' => $isi,
            'status_moderasi' => $status,
        ]);
    }

    private function pelaporLain(): User
    {
        return User::factory()->create([
            'role_id' => $this->pelapor->role_id,
            'nama_tampilan' => 'Alias Lain',
            'email' => 'pelapor-lain@ruangaman.test',
        ]);
    }

    #[Test]
    public function tamu_diarahkan_ke_login_untuk_fitur_pelapor_dan_admin(): void
    {
        $artikel = $this->buatArtikel();

        $this->get(route('feedback-saya.index'))->assertRedirect(route('login'));
        $this->post(route('feedback.store', $artikel), ['isi_feedback' => 'Feedback pengunjung.'])
            ->assertRedirect(route('login'));
        $this->get(route('admin.feedback-artikel.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function peran_yang_tidak_berwenang_mendapat_403(): void
    {
        $artikel = $this->buatArtikel();
        $feedback = $this->buatFeedback();

        foreach ([$this->konselor, $this->admin] as $user) {
            $this->actingAs($user);
            $this->get(route('feedback-saya.index'))->assertForbidden();
            $this->post(route('feedback.store', $artikel), ['isi_feedback' => 'Tidak diizinkan.'])->assertForbidden();
        }

        $this->actingAs($this->pelapor);
        $this->get(route('admin.feedback-artikel.index'))->assertForbidden();
        $this->patch(route('admin.feedback-artikel.moderasi', $feedback), [
            'status_moderasi' => FeedbackArtikel::STATUS_DISETUJUI,
        ])->assertForbidden();

        $this->assertDatabaseHas('feedback_artikel', [
            'feedback_id' => $feedback->feedback_id,
            'status_moderasi' => FeedbackArtikel::STATUS_PENDING,
        ]);
    }

    #[Test]
    public function pelapor_dapat_mengirim_feedback_yang_masuk_ke_antrean_moderasi(): void
    {
        $artikel = $this->buatArtikel();
        $this->actingAs($this->pelapor);

        $this->get(route('artikel.show', $artikel))->assertOk()->assertSee('Kirim Feedback');

        $this->post(route('feedback.store', $artikel), [
            'isi_feedback' => '  Materi ini jelas dan mudah dipahami.  ',
            'status_moderasi' => FeedbackArtikel::STATUS_DISETUJUI,
            'user_id' => $this->admin->user_id,
        ])->assertRedirect(route('artikel.show', $artikel).'#feedback')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('feedback_artikel', [
            'artikel_id' => $artikel->artikel_id,
            'user_id' => $this->pelapor->user_id,
            'isi_feedback' => 'Materi ini jelas dan mudah dipahami.',
            'status_moderasi' => FeedbackArtikel::STATUS_PENDING,
        ]);
    }

    #[Test]
    public function validasi_feedback_menolak_data_kosong_terlalu_pendek_dan_terlalu_panjang(): void
    {
        $artikel = $this->buatArtikel();
        $this->actingAs($this->pelapor);

        $this->post(route('feedback.store', $artikel), ['isi_feedback' => ''])
            ->assertSessionHasErrors('isi_feedback');
        $this->post(route('feedback.store', $artikel), ['isi_feedback' => '     '])
            ->assertSessionHasErrors('isi_feedback');
        $this->post(route('feedback.store', $artikel), ['isi_feedback' => 'Abcd'])
            ->assertSessionHasErrors('isi_feedback');
        $this->post(route('feedback.store', $artikel), ['isi_feedback' => str_repeat('a', 2001)])
            ->assertSessionHasErrors('isi_feedback');

        $this->assertDatabaseCount('feedback_artikel', 0);
    }

    #[Test]
    public function feedback_hanya_dapat_dikirim_untuk_artikel_terpublikasi(): void
    {
        $artikel = $this->buatArtikel([
            'slug' => 'artikel-draf-feedback',
            'status_publikasi' => Artikel::STATUS_DRAFT,
        ]);
        $this->actingAs($this->pelapor);

        $this->get(route('artikel.show', $artikel))->assertOk()->assertDontSee('Kirim Feedback');
        $this->post(route('feedback.store', $artikel), ['isi_feedback' => 'Feedback untuk draf.'])
            ->assertForbidden();

        $this->assertDatabaseCount('feedback_artikel', 0);
    }

    #[Test]
    public function halaman_publik_hanya_menampilkan_feedback_disetujui_dan_tanpa_email(): void
    {
        $artikel = $this->buatArtikel();
        $this->buatFeedback(status: FeedbackArtikel::STATUS_DISETUJUI, isi: 'Feedback disetujui tampil publik.', artikel: $artikel);
        $this->buatFeedback(status: FeedbackArtikel::STATUS_PENDING, isi: 'Feedback pending tetap rahasia.', artikel: $artikel);
        $this->buatFeedback(status: FeedbackArtikel::STATUS_DITOLAK, isi: 'Feedback ditolak tetap rahasia.', artikel: $artikel);

        $response = $this->get(route('artikel.show', $artikel));

        $response->assertOk()
            ->assertSee('Feedback disetujui tampil publik.')
            ->assertSee('Bintang Laut')
            ->assertDontSee('Feedback pending tetap rahasia.')
            ->assertDontSee('Feedback ditolak tetap rahasia.')
            ->assertDontSee($this->pelapor->email)
            ->assertDontSee('Kirim Feedback');
    }

    #[Test]
    public function output_feedback_di_escape_untuk_mencegah_xss(): void
    {
        $artikel = $this->buatArtikel();
        $this->buatFeedback(
            status: FeedbackArtikel::STATUS_DISETUJUI,
            isi: '<script>alert("uji")</script>',
            artikel: $artikel,
        );

        $this->get(route('artikel.show', $artikel))
            ->assertOk()
            ->assertDontSee('<script>alert("uji")</script>', false)
            ->assertSee('&lt;script&gt;');
    }

    #[Test]
    public function pelapor_hanya_melihat_feedback_miliknya_sendiri(): void
    {
        $feedbackSendiri = $this->buatFeedback(isi: 'Feedback milik saya.');
        $this->buatFeedback(user: $this->pelaporLain(), isi: 'Feedback milik pengguna lain.');
        $this->actingAs($this->pelapor);

        $this->get(route('feedback-saya.index'))
            ->assertOk()
            ->assertSee('Feedback milik saya.')
            ->assertDontSee('Feedback milik pengguna lain.');

        $this->get(route('feedback-saya.index', ['status' => FeedbackArtikel::STATUS_DITOLAK]))
            ->assertOk()
            ->assertDontSee('Feedback milik saya.');
    }

    #[Test]
    public function pelapor_lain_tidak_dapat_mengubah_atau_menghapus_feedback(): void
    {
        $feedback = $this->buatFeedback();
        $this->actingAs($this->pelaporLain());

        $this->get(route('feedback-saya.edit', $feedback))->assertForbidden();
        $this->put(route('feedback-saya.update', $feedback), ['isi_feedback' => 'Ubah ilegal.'])->assertForbidden();
        $this->delete(route('feedback-saya.destroy', $feedback))->assertForbidden();

        $this->assertDatabaseHas('feedback_artikel', [
            'feedback_id' => $feedback->feedback_id,
            'isi_feedback' => 'Artikel ini sangat membantu saya.',
        ]);
    }

    #[Test]
    public function pelapor_dapat_mengubah_feedback_dan_status_kembali_ke_pending(): void
    {
        $feedback = $this->buatFeedback(status: FeedbackArtikel::STATUS_DISETUJUI);
        $this->actingAs($this->pelapor);

        $this->get(route('feedback-saya.edit', $feedback))->assertOk();
        $this->put(route('feedback-saya.update', $feedback), [
            'isi_feedback' => 'Feedback yang sudah diperbarui.',
            'status_moderasi' => FeedbackArtikel::STATUS_DISETUJUI,
        ])->assertRedirect(route('feedback-saya.index'))->assertSessionHasNoErrors();

        $feedback->refresh();
        $this->assertSame('Feedback yang sudah diperbarui.', $feedback->isi_feedback);
        $this->assertSame(FeedbackArtikel::STATUS_PENDING, $feedback->status_moderasi);
    }

    #[Test]
    public function pelapor_dapat_menghapus_feedback_miliknya(): void
    {
        $feedback = $this->buatFeedback();
        $this->actingAs($this->pelapor);

        $this->delete(route('feedback-saya.destroy', $feedback))
            ->assertRedirect(route('feedback-saya.index'));

        $this->assertDatabaseMissing('feedback_artikel', ['feedback_id' => $feedback->feedback_id]);
    }

    #[Test]
    public function admin_dapat_melihat_mencari_dan_memfilter_feedback(): void
    {
        $artikel = $this->buatArtikel();
        $pending = $this->buatFeedback(isi: 'Feedback pending unik.', artikel: $artikel);
        $approved = $this->buatFeedback(status: FeedbackArtikel::STATUS_DISETUJUI, isi: 'Feedback disetujui unik.', artikel: $artikel);
        $this->actingAs($this->admin);

        $this->get(route('admin.feedback-artikel.index'))
            ->assertOk()
            ->assertSee('Feedback pending unik.')
            ->assertSee('Feedback disetujui unik.');

        $this->get(route('admin.feedback-artikel.index', ['status' => FeedbackArtikel::STATUS_PENDING]))
            ->assertOk()
            ->assertSee('Feedback pending unik.')
            ->assertDontSee('Feedback disetujui unik.');

        $this->get(route('admin.feedback-artikel.index', ['search' => 'pending unik']))
            ->assertOk()
            ->assertSee('Feedback pending unik.')
            ->assertDontSee('Feedback disetujui unik.');

        $this->get(route('admin.feedback-artikel.show', $pending))->assertOk()->assertSee('Bintang Laut');
        $this->get(route('admin.feedback-artikel.show', $approved))->assertOk();
    }

    #[Test]
    public function admin_dapat_memoderasi_feedback_dan_feedback_disetujui_tampil_publik(): void
    {
        $artikel = $this->buatArtikel();
        $feedback = $this->buatFeedback(isi: 'Feedback siap ditampilkan.', artikel: $artikel);
        $this->actingAs($this->admin);

        $this->patch(route('admin.feedback-artikel.moderasi', $feedback), ['status_moderasi' => 'tidak-valid'])
            ->assertSessionHasErrors('status_moderasi');
        $this->assertSame(FeedbackArtikel::STATUS_PENDING, $feedback->refresh()->status_moderasi);

        $this->patch(route('admin.feedback-artikel.moderasi', $feedback), [
            'status_moderasi' => FeedbackArtikel::STATUS_DISETUJUI,
        ])->assertRedirect(route('admin.feedback-artikel.show', $feedback))->assertSessionHasNoErrors();

        $this->assertSame(FeedbackArtikel::STATUS_DISETUJUI, $feedback->refresh()->status_moderasi);
        $this->get(route('artikel.show', $artikel))->assertSee('Feedback siap ditampilkan.');
    }
}
