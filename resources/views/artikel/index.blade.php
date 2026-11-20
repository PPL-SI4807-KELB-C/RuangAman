@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full">

    {{-- Hero (pola sama dengan Pengumuman, Kegiatan, Glosarium) --}}
    <section class="w-full bg-white pt-14 pb-10 sm:pt-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-600">
                    Artikel edukasi &amp; pencegahan KBG
                </p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                    Temukan informasi untuk memahami dan bertindak aman
                </h1>
                <p class="mt-3 text-sm text-neutral-600 leading-relaxed max-w-xl">
                    Baca materi mengenai pencegahan, hak korban, kesehatan mental, dan pemulihan dengan bahasa yang empatik serta tidak menghakimi.
                </p>
            </div>
            <x-button :href="route('kegiatan.index')" variant="secondary" class="shrink-0">Lihat kegiatan edukasi</x-button>
        </div>
    </section>

    {{-- Pencarian & daftar --}}
    <section class="w-full bg-surface border-t border-neutral-200/70 py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 space-y-8">

            <form method="GET" action="{{ route('artikel.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-12 sm:items-end">
                <x-search-input
                    name="search"
                    :value="$search"
                    label="Cari artikel"
                    placeholder="Cari topik, judul, atau isi artikel…"
                    class="sm:col-span-6"
                />
                <x-select
                    name="kategori_id"
                    :options="['' => 'Semua kategori'] + collect($kategoriOptions)->all()"
                    :value="$selectedKategori ?: ''"
                    aria-label="Kategori artikel"
                    class="sm:col-span-4"
                />
                <div class="flex gap-2 sm:col-span-2">
                    <x-button type="submit" variant="primary" class="flex-1">Cari</x-button>
                    @if ($search !== '' || $selectedKategori > 0)
                        <x-button :href="route('artikel.index')" variant="ghost">Reset</x-button>
                    @endif
                </div>
            </form>

            @if ($artikelList->isEmpty())
                <x-empty-state
                    title="Artikel belum ditemukan"
                    description="Coba gunakan kata kunci atau kategori lain."
                />
            @else
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($artikelList as $item)
                        <article class="flex flex-col justify-between rounded-xl border border-neutral-200/60 bg-white p-6 transition-colors hover:border-neutral-400">
                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <x-badge variant="brand">{{ $item->kategori?->nama_kategori ?? 'Edukasi' }}</x-badge>
                                    <time datetime="{{ $item->updated_at?->toISOString() }}" class="text-xs text-neutral-600">{{ $item->updated_at?->translatedFormat('d M Y') }}</time>
                                </div>
                                <h2 class="mt-4 text-base font-bold leading-snug tracking-tight text-neutral-900">
                                    <a href="{{ route('artikel.show', $item) }}" class="hover:text-brand-700">{{ $item->judul }}</a>
                                </h2>
                                <p class="mt-2 text-sm leading-relaxed text-neutral-600">{{ $item->ringkasan() }}</p>
                            </div>
                            <div class="mt-5 border-t border-neutral-200/70 pt-4">
                                <p class="mb-3 text-xs text-neutral-600">Oleh {{ $item->penulis?->nama_tampilan ?? 'Tim RuangAman' }}</p>
                                <x-button :href="route('artikel.show', $item)" variant="secondary" size="sm" class="w-full">Baca artikel</x-button>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($artikelList->hasPages())
                    <div class="border-t border-neutral-300/60 pt-6">{{ $artikelList->links() }}</div>
                @endif
            @endif
        </div>
    </section>

</div>
@endsection
