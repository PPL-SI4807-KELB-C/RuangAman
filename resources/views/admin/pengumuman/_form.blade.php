<div class="space-y-5">
    <x-input
        name="judul"
        label="Judul Pengumuman"
        :value="$pengumuman->judul"
        placeholder="Contoh: Perubahan jam layanan konseling online"
        :error="$errors->first('judul')"
        required
    />

    <x-textarea
        name="isi"
        label="Isi Pengumuman"
        :value="$pengumuman->isi"
        rows="8"
        placeholder="Tulis informasi yang perlu diketahui pelapor. Baris baru tampil sebagaimana adanya."
        :error="$errors->first('isi')"
        required
    />

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-input
            name="mulai_tayang"
            label="Mulai Tayang"
            type="date"
            :value="$pengumuman->mulai_tayang?->format('Y-m-d')"
            helper="Pengumuman baru tampil untuk publik pada tanggal ini."
            :error="$errors->first('mulai_tayang')"
            required
        />

        <x-input
            name="akhir_tayang"
            label="Akhir Periode Tayang"
            type="date"
            :value="$pengumuman->akhir_tayang?->format('Y-m-d')"
            helper="Kosongkan bila pengumuman tayang tanpa batas waktu."
            :error="$errors->first('akhir_tayang')"
        />
    </div>

    <x-select
        name="status"
        label="Status"
        :options="$statusOptions"
        :value="$pengumuman->status"
        helper="Pengumuman berstatus arsip berhenti tampil di daftar publik tetapi datanya tetap tersimpan."
        :error="$errors->first('status')"
        required
    />
</div>
