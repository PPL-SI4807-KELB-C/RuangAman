<div class="space-y-5">
    @if ($halaman->exists)
        <div class="rounded-lg border border-neutral-200/70 bg-white px-4 py-3">
            <p class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Slug Halaman</p>
            <p class="mt-1.5 text-sm font-semibold text-neutral-900">{{ $halaman->slug }}</p>
            <p class="mt-1.5 text-[11px] leading-relaxed text-neutral-500">
                Slug dibangkitkan otomatis dari judul dan hanya berubah bila judul diubah.
            </p>
        </div>
    @endif

    <x-input
        name="judul"
        label="Judul Halaman"
        :value="$halaman->judul"
        placeholder="Contoh: Kebijakan Privasi"
        helper="Judul sekaligus dipakai sebagai dasar slug dan judul browser halaman publik."
        :error="$errors->first('judul')"
        required
    />

    <x-textarea
        name="isi"
        label="Isi Halaman"
        :value="$halaman->isi"
        rows="14"
        placeholder="Tulis isi halaman. Baris baru akan tampil sebagaimana adanya di halaman publik."
        helper="Gunakan paragraf pendek dan bahasa yang mudah dipahami penyintas."
        :error="$errors->first('isi')"
        required
    />

    <x-select
        name="status_publikasi"
        label="Status Publikasi"
        :options="$statusOptions"
        :value="$halaman->status_publikasi"
        helper="Hanya halaman berstatus Published yang dapat diakses pengunjung tanpa login."
        :error="$errors->first('status_publikasi')"
        required
    />
</div>
