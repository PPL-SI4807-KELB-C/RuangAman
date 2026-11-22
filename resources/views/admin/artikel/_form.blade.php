@php
    $isEdit = $artikel->exists;
@endphp

<div class="space-y-5">
    @if ($isEdit)
        <div class="rounded-lg border border-neutral-200/70 bg-surface px-4 py-3">
            <p class="text-xs font-semibold uppercase tracking-wider text-neutral-600">Penulis</p>
            <p class="mt-1 text-sm font-semibold text-neutral-900">{{ $artikel->penulis?->nama_tampilan ?? 'Pengguna tidak ditemukan' }}</p>
            <p class="mt-1 text-xs text-neutral-600">Slug saat ini: <span class="font-mono">{{ $artikel->slug }}</span>. Slug diperbarui otomatis ketika judul berubah.</p>
        </div>
    @else
        <div class="rounded-lg border border-brand-100 bg-brand-50 px-4 py-3 text-sm text-brand-900">
            Artikel akan mencatat <span class="font-semibold">{{ auth()->user()->nama_tampilan }}</span> sebagai penulis. Slug dibuat otomatis dari judul.
        </div>
    @endif

    <x-input
        name="judul"
        label="Judul Artikel"
        :value="$artikel->judul"
        placeholder="Contoh: Mengenal Bentuk Kekerasan Berbasis Gender"
        :error="$errors->first('judul')"
        required
    />

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-select
            name="kategori_id"
            label="Kategori Artikel"
            :options="$kategoriOptions"
            :value="$artikel->kategori_id"
            placeholder="Umum (bawaan)"
            helper="Opsional. Bila dikosongkan, artikel masuk ke kategori Umum."
            :error="$errors->first('kategori_id')"
        />

        <x-select
            name="status_publikasi"
            label="Status Publikasi"
            :options="$statusOptions"
            :value="$artikel->status_publikasi"
            helper="Hanya artikel Terpublikasi yang tampil di halaman publik."
            :error="$errors->first('status_publikasi')"
            required
        />
    </div>

    <x-textarea
        name="isi"
        label="Isi Artikel"
        :value="$artikel->isi"
        rows="16"
        placeholder="Tulis materi edukasi dengan bahasa yang jelas, empatik, dan tidak menghakimi..."
        helper="Gunakan teks biasa dan pisahkan paragraf dengan baris baru. Keluaran selalu di-escape untuk mencegah XSS."
        :error="$errors->first('isi')"
        required
    />
</div>
