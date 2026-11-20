@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full">

    {{-- Hero --}}
    <section class="w-full bg-white pt-14 pb-10 sm:pt-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 space-y-6">
            <div class="max-w-2xl space-y-3">
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Direktori bantuan</p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                    Temukan pilihan bantuan Anda.
                </h1>
                <p class="text-sm text-neutral-600 leading-relaxed">
                    Kenali lembaga pendamping dan pilih dukungan sesuai kebutuhan Anda. Direktori ini dapat dibuka tanpa masuk ke akun.
                </p>
            </div>

            <form method="GET" action="{{ route('instansi.publik') }}" class="flex max-w-2xl flex-col gap-3 sm:flex-row">
                <x-search-input
                    class="flex-1"
                    :value="$search"
                    label="Cari instansi bantuan"
                    placeholder="Cari nama instansi atau alamat…"
                />
                <x-button type="submit" variant="primary">Cari instansi</x-button>
            </form>
        </div>
    </section>

    {{-- Daftar instansi --}}
    <section class="w-full bg-[#f2f2f2] border-t border-neutral-200/70 py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 space-y-6">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h2 class="text-lg font-bold text-neutral-900 tracking-tight">{{ $totalAktif }} instansi bantuan</h2>
                <p class="text-xs text-neutral-500">Menampilkan instansi aktif</p>
            </div>

            @if ($instansis->count() > 0)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach ($instansis as $instansi)
                        <article class="flex flex-col rounded-xl border border-neutral-200/60 bg-white p-6 sm:p-7">
                            <h3 class="text-base font-bold text-neutral-900 tracking-tight">{{ $instansi->nama_instansi }}</h3>
                            <p class="mt-1.5 text-[11px] text-neutral-500">{{ $instansi->alamat }}</p>
                            <p class="mt-3 flex-1 text-xs leading-relaxed text-neutral-600">{{ $instansi->deskripsi }}</p>

                            <div class="mt-5 border-t border-neutral-200/70 pt-4 text-xs">
                                @if ($instansi->website)
                                    <a href="{{ $instansi->website }}" target="_blank" rel="noopener noreferrer" class="font-medium text-neutral-800 hover:text-brand-700 hover:underline">
                                        {{ parse_url($instansi->website, PHP_URL_HOST) ?? $instansi->website }} <span aria-hidden="true">&nearr;</span>
                                        <span class="sr-only">(tab baru)</span>
                                    </a>
                                @else
                                    <span class="text-neutral-500">Situs web belum tersedia</span>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <x-empty-state
                    title="Tidak ada instansi bantuan yang ditemukan."
                    description="Coba kata kunci lain, atau kosongkan pencarian untuk melihat semua instansi aktif."
                >
                    @if (filled($search))
                        <x-button :href="route('instansi.publik')" variant="secondary" size="sm">Tampilkan semua</x-button>
                    @endif
                </x-empty-state>
            @endif

            <x-alert variant="warning">
                Bukan layanan darurat. Jika ada ancaman langsung, cari tempat aman dan hubungi bantuan setempat.
            </x-alert>
        </div>
    </section>

</div>
@endsection
