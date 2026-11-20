<?php

namespace Tests\Feature;

use App\Models\LampiranLaporan;
use App\Models\LaporanKasus;
use App\Models\Role;
use App\Models\StatusKasus;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Pbi16ManajemenLaporanKasusTest extends TestCase
{
    use RefreshDatabase;

    private User $pelapor;
    private User $pelaporLain;
    private User $konselor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('local');

        $this->seed(DatabaseSeeder::class);

        $this->pelapor = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Pelapor'))->firstOrFail();
        $this->konselor = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Konselor'))->firstOrFail();

        $this->pelaporLain = User::create([
            'role_id' => Role::where('nama_role', 'Pelapor')->value('role_id'),
            'nama_tampilan' => 'Pelapor Lain',
            'email' => 'pelapor2@ruangaman.test',
            'password_hash' => 'password',
            'status' => 'aktif',
        ]);
    }

    private function buatLaporan(User $user, string $status = 'Pending', array $override = []): LaporanKasus
    {
        return LaporanKasus::create(array_merge([
            'user_id' => $user->user_id,
            'status_id' => StatusKasus::where('nama_status', $status)->value('status_id'),
            'kode_laporan' => 'RA-TEST-' . strtoupper(substr(md5(uniqid('', true)), 0, 6)),
            'judul' => 'Laporan uji',
            'kategori_kekerasan' => 'Psikis',
            'lokasi_kejadian' => 'Kampus',
            'kronologi' => 'Kronologi kejadian untuk keperluan pengujian.',
            'waktu_kejadian' => now()->subDay(),
            'tingkat_urgensi' => 'sedang',
            'mode_anonim' => true,
        ], $override));
    }

    private function buatLampiran(LaporanKasus $laporan, string $nama = 'bukti.pdf'): LampiranLaporan
    {
        $path = "private/laporan/{$laporan->laporan_id}/{$nama}";
        Storage::disk('local')->put($path, 'isi-bukti');

        return LampiranLaporan::create([
            'laporan_id' => $laporan->laporan_id,
            'nama_file' => $nama,
            'path_privat' => $path,
            'tipe_file' => 'application/pdf',
            'ukuran_file' => 9,
            'created_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $override
     * @return array<string, mixed>
     */
    private function payload(array $override = []): array
    {
        return array_merge([
            'judul' => 'Judul baru',
            'kategori_kekerasan' => 'Seksual',
            'lokasi_kejadian' => 'Gedung A lantai 2',
            'kronologi' => 'Kronologi yang diperbarui dengan detail memadai.',
            'waktu_kejadian' => now()->subHours(3)->format('Y-m-d\TH:i'),
            'tingkat_urgensi' => 'tinggi',
            'mode_anonim' => '1',
        ], $override);
    }

    // ---------------------------------------------------------------- akses

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get(route('laporan.index'))->assertRedirect(route('login'));
    }

    public function test_konselor_tidak_dapat_mengakses_daftar_laporan_pelapor(): void
    {
        $this->actingAs($this->konselor)->get(route('laporan.index'))->assertForbidden();
    }

    // ---------------------------------------------------------------- create

    public function test_pelapor_dapat_membuat_laporan_dengan_lampiran(): void
    {
        $file = UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->pelapor)->post(route('laporan.store'), $this->payload([
            'persetujuan' => '1',
            'lampiran' => [$file],
        ]));

        $response->assertRedirect(route('laporan.success'));
        $response->assertSessionHas('kode_laporan');

        $laporan = LaporanKasus::where('user_id', $this->pelapor->user_id)->latest('laporan_id')->firstOrFail();

        $this->assertSame('Pending', $laporan->status->nama_status);
        $this->assertMatchesRegularExpression('/^RA-\d{4}-[A-Z0-9]{6}$/', $laporan->kode_laporan);
        $this->assertCount(1, $laporan->lampirans);
        Storage::disk('local')->assertExists($laporan->lampirans->first()->path_privat);
    }

    public function test_validasi_pembuatan_laporan_menolak_data_kosong(): void
    {
        $this->actingAs($this->pelapor)
            ->post(route('laporan.store'), [])
            ->assertSessionHasErrors(['judul', 'kategori_kekerasan', 'kronologi', 'persetujuan']);

        $this->assertDatabaseCount('laporan_kasus', 0);
    }

    public function test_form_laporan_memakai_preferensi_anonim_dari_profil(): void
    {
        $this->pelapor->profilPelapor()->update(['preferensi_anonim' => false]);

        $this->actingAs($this->pelapor)
            ->get(route('laporan.create'))
            ->assertOk()
            ->assertSee('mode_anonim: false,', false);
    }

    // ---------------------------------------------------------------- read

    public function test_daftar_hanya_menampilkan_laporan_milik_sendiri(): void
    {
        $milikSendiri = $this->buatLaporan($this->pelapor);
        $milikLain = $this->buatLaporan($this->pelaporLain);

        $this->actingAs($this->pelapor)
            ->get(route('laporan.index'))
            ->assertOk()
            ->assertSee($milikSendiri->kode_laporan)
            ->assertDontSee($milikLain->kode_laporan);
    }

    public function test_pelapor_dapat_melihat_detail_laporannya_sendiri(): void
    {
        $laporan = $this->buatLaporan($this->pelapor);
        $this->buatLampiran($laporan, 'foto-bukti.pdf');

        $this->actingAs($this->pelapor)
            ->get(route('laporan.show', $laporan))
            ->assertOk()
            ->assertSee($laporan->kode_laporan)
            ->assertSee('foto-bukti.pdf');
    }

    public function test_detail_laporan_milik_orang_lain_dilarang(): void
    {
        $laporan = $this->buatLaporan($this->pelaporLain);

        $this->actingAs($this->pelapor)->get(route('laporan.show', $laporan))->assertForbidden();
    }

    // ---------------------------------------------------------------- update

    public function test_pelapor_dapat_mengubah_laporan_pending(): void
    {
        $laporan = $this->buatLaporan($this->pelapor);

        $this->actingAs($this->pelapor)->get(route('laporan.edit', $laporan))->assertOk();

        $response = $this->actingAs($this->pelapor)->put(route('laporan.update', $laporan), $this->payload());

        $response->assertRedirect(route('laporan.show', $laporan));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('laporan_kasus', [
            'laporan_id' => $laporan->laporan_id,
            'judul' => 'Judul baru',
            'kategori_kekerasan' => 'Seksual',
            'tingkat_urgensi' => 'tinggi',
        ]);
    }

    public function test_checkbox_mode_anonim_tidak_dicentang_mematikan_mode_anonim(): void
    {
        $laporan = $this->buatLaporan($this->pelapor, 'Pending', ['mode_anonim' => true]);

        $data = $this->payload();
        unset($data['mode_anonim']);

        $this->actingAs($this->pelapor)->put(route('laporan.update', $laporan), $data);

        $this->assertFalse($laporan->fresh()->mode_anonim);
    }

    public function test_laporan_non_pending_tidak_dapat_diubah(): void
    {
        $laporan = $this->buatLaporan($this->pelapor, 'Proses');

        $this->actingAs($this->pelapor)->get(route('laporan.edit', $laporan))->assertForbidden();
        $this->actingAs($this->pelapor)->put(route('laporan.update', $laporan), $this->payload())->assertForbidden();

        $this->assertDatabaseHas('laporan_kasus', ['laporan_id' => $laporan->laporan_id, 'judul' => 'Laporan uji']);
    }

    public function test_pelapor_lain_tidak_dapat_mengubah_laporan(): void
    {
        $laporan = $this->buatLaporan($this->pelaporLain);

        $this->actingAs($this->pelapor)->put(route('laporan.update', $laporan), $this->payload())->assertForbidden();
    }

    public function test_validasi_update_menolak_urgensi_dan_kategori_tidak_valid(): void
    {
        $laporan = $this->buatLaporan($this->pelapor);

        $this->actingAs($this->pelapor)
            ->put(route('laporan.update', $laporan), $this->payload([
                'tingkat_urgensi' => 'Sangat Tinggi',
                'kategori_kekerasan' => 'Tidak Ada',
            ]))
            ->assertSessionHasErrors(['tingkat_urgensi', 'kategori_kekerasan']);
    }

    public function test_pelapor_dapat_menambah_lampiran_saat_update(): void
    {
        $laporan = $this->buatLaporan($this->pelapor);

        $this->actingAs($this->pelapor)->put(route('laporan.update', $laporan), $this->payload([
            'lampiran' => [UploadedFile::fake()->create('tambahan.pdf', 50, 'application/pdf')],
        ]));

        $this->assertCount(1, $laporan->fresh()->lampirans);
    }

    public function test_update_menolak_lampiran_melebihi_batas(): void
    {
        $laporan = $this->buatLaporan($this->pelapor);

        foreach (range(1, LaporanKasus::MAKS_LAMPIRAN) as $i) {
            $this->buatLampiran($laporan, "bukti-{$i}.pdf");
        }

        $this->actingAs($this->pelapor)
            ->put(route('laporan.update', $laporan), $this->payload([
                'lampiran' => [UploadedFile::fake()->create('kelebihan.pdf', 50, 'application/pdf')],
            ]))
            ->assertSessionHasErrors('lampiran');

        $this->assertCount(LaporanKasus::MAKS_LAMPIRAN, $laporan->fresh()->lampirans);
    }

    // ---------------------------------------------------------------- delete

    public function test_pelapor_dapat_membatalkan_laporan_pending_beserta_lampirannya(): void
    {
        $laporan = $this->buatLaporan($this->pelapor);
        $lampiran = $this->buatLampiran($laporan);

        $this->actingAs($this->pelapor)
            ->delete(route('laporan.destroy', $laporan))
            ->assertRedirect(route('laporan.index'));

        $this->assertDatabaseMissing('laporan_kasus', ['laporan_id' => $laporan->laporan_id]);
        $this->assertDatabaseMissing('lampiran_laporan', ['lampiran_id' => $lampiran->lampiran_id]);
        Storage::disk('local')->assertMissing($lampiran->path_privat);
    }

    public function test_laporan_non_pending_tidak_dapat_dibatalkan(): void
    {
        $laporan = $this->buatLaporan($this->pelapor, 'Selesai');

        $this->actingAs($this->pelapor)->delete(route('laporan.destroy', $laporan))->assertForbidden();

        $this->assertDatabaseHas('laporan_kasus', ['laporan_id' => $laporan->laporan_id]);
    }

    // ---------------------------------------------------------------- lampiran

    public function test_pemilik_dapat_mengunduh_lampiran(): void
    {
        $lampiran = $this->buatLampiran($this->buatLaporan($this->pelapor), 'bukti.pdf');

        $this->actingAs($this->pelapor)
            ->get(route('laporan.lampiran.download', $lampiran))
            ->assertOk()
            ->assertDownload('bukti.pdf');
    }

    public function test_pelapor_lain_tidak_dapat_mengunduh_lampiran(): void
    {
        $lampiran = $this->buatLampiran($this->buatLaporan($this->pelaporLain));

        $this->actingAs($this->pelapor)->get(route('laporan.lampiran.download', $lampiran))->assertForbidden();
    }

    public function test_pemilik_dapat_menghapus_lampiran_saat_pending(): void
    {
        $laporan = $this->buatLaporan($this->pelapor);
        $lampiran = $this->buatLampiran($laporan);

        $this->actingAs($this->pelapor)
            ->delete(route('laporan.lampiran.destroy', $lampiran))
            ->assertRedirect(route('laporan.edit', $laporan));

        $this->assertDatabaseMissing('lampiran_laporan', ['lampiran_id' => $lampiran->lampiran_id]);
        Storage::disk('local')->assertMissing($lampiran->path_privat);
    }

    public function test_lampiran_tidak_dapat_dihapus_saat_laporan_non_pending(): void
    {
        $lampiran = $this->buatLampiran($this->buatLaporan($this->pelapor, 'Proses'));

        $this->actingAs($this->pelapor)->delete(route('laporan.lampiran.destroy', $lampiran))->assertForbidden();

        $this->assertDatabaseHas('lampiran_laporan', ['lampiran_id' => $lampiran->lampiran_id]);
    }
}
