@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl space-y-8 px-4 py-8 sm:px-6 sm:py-10">
    <div class="rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-50 via-white to-slate-50 p-6 sm:p-8">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="max-w-2xl">
                <x-badge variant="brand">Artikel Edukasi &amp; Pencegahan KBG</x-badge>
                <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Temukan Informasi untuk Memahami dan Bertindak Aman</h1>
                <p class="mt-3 text-sm leading-relaxed text-slate-600">Baca materi mengenai pencegahan, hak korban, kesehatan mental, dan pemulihan dengan bahasa yang empatik serta tidak menghakimi.</p>
            </div>
            <x-button href="{{ route('kegiatan.index') }}" variant="secondary" size="md" class="shrink-0">Lihat Kegiatan Edukasi</x-button>
        </div>
    </div>

    <x-card>
        <form method="GET" action="{{ route('artikel.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-12">
            <div class="sm:col-span-6">
                <label for="search" class="sr-only">Cari artikel</label>
                <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Cari topik, judul, atau isi artikel..." class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" />
            </div>
            <div class="sm:col-span-4">
                <label for="kategori_id" class="sr-only">Kategori artikel</label>
                <select id="kategori_id" name="kategori_id" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoriOptions as $id => $label)
                        <option value="{{ $id }}" @selected($selectedKategori === (int) $id)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 sm:col-span-2">
                <x-button type="submit" variant="primary" size="md" class="flex-1">Cari</x-button>
                @if ($search !== '' || $selectedKategori > 0)
                    <x-button href="{{ route('artikel.index') }}" variant="ghost" size="md">Reset</x-button>
                @endif
            </div>
        </form>
    </x-card>

    @if ($artikelList->isEmpty())
        <x-card class="py-12 text-center">
            <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
            </svg>
            <h2 class="mt-3 text-base font-semibold text-slate-800">Artikel belum ditemukan</h2>
            <p class="mt-1 text-sm text-slate-500">Coba gunakan kata kunci atau kategori lain.</p>
        </x-card>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($artikelList as $item)
                <article class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-brand-300 hover:shadow-md">
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <x-badge variant="brand">{{ $item->kategori?->nama_kategori ?? 'Edukasi' }}</x-badge>
                            <time datetime="{{ $item->updated_at?->toISOString() }}" class="text-xs text-slate-400">{{ $item->updated_at?->translatedFormat('d M Y') }}</time>
                        </div>
                        <h2 class="mt-4 text-lg font-bold leading-snug text-slate-900">
                            <a href="{{ route('artikel.show', $item) }}" class="hover:text-brand-700">{{ $item->judul }}</a>
                        </h2>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $item->ringkasan() }}</p>
                    </div>
                    <div class="mt-5 border-t border-slate-100 pt-4">
                        <p class="mb-3 text-xs text-slate-500">Oleh {{ $item->penulis?->nama_tampilan ?? 'Tim RuangAman' }}</p>
                        <x-button href="{{ route('artikel.show', $item) }}" variant="secondary" size="sm" class="w-full">Baca Artikel</x-button>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($artikelList->hasPages())
            <div>{{ $artikelList->links() }}</div>
        @endif
    @endif
</div>
@endsection
