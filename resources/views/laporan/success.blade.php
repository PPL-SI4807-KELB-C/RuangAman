@extends('layouts.app')

@section('title', 'Laporan Anda telah diterima — RuangAman')

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6 py-12 space-y-8">

    <div>
        <svg class="h-7 w-7 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <path d="m9 12 2 2 4-4" />
        </svg>
        <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-neutral-900 sm:text-3xl">Laporan Anda telah diterima</h1>
        <p class="mt-2 max-w-xl text-sm leading-relaxed text-neutral-600">
            Terima kasih telah menyampaikan laporan. Anda dapat memeriksa perkembangannya dengan kode di bawah ini.
        </p>
    </div>

    {{-- Kode laporan --}}
    <x-card>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" x-data="copyText(@js($kode_laporan))">
            <div>
                <p class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Kode laporan Anda</p>
                <p class="mt-2 font-mono text-4xl font-extrabold tracking-[0.2em] text-neutral-900 sm:text-5xl">{{ $kode_laporan }}</p>
            </div>
            <x-button variant="secondary" @click="copy()" class="shrink-0">
                <span x-text="copied ? 'Kode tersalin' : 'Salin kode'">Salin kode</span>
            </x-button>
        </div>

        <x-slot:footer>
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-neutral-700">
                <span>Alias: {{ session('alias', auth()->user()?->profilPelapor?->alias ?? auth()->user()?->nama_tampilan) }}</span>
                <x-badge variant="warning">Menunggu peninjauan</x-badge>
            </div>
        </x-slot:footer>
    </x-card>

    <div class="space-y-2">
        <h2 class="text-sm font-semibold text-neutral-900">Simpan kode ini di tempat yang aman.</h2>
        <p class="text-xs leading-relaxed text-neutral-600">
            Kode memberi akses ke laporan Anda di halaman lacak. Jangan bagikan kepada orang lain. Jika kode hilang, riwayat laporan tetap tampil di dashboard akun Anda.
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-5">
        <form method="POST" action="{{ route('lacak.cari') }}">
            @csrf
            <input type="hidden" name="kode_laporan" value="{{ $kode_laporan }}">
            <x-button type="submit" variant="primary">Lacak laporan</x-button>
        </form>
        <x-button :href="route('dashboard')" variant="ghost">Ke dashboard</x-button>
    </div>

    <div class="space-y-2 border-t border-neutral-200/70 pt-8">
        <h2 class="text-sm font-semibold text-neutral-900">Apa yang terjadi selanjutnya?</h2>
        <p class="text-xs leading-relaxed text-neutral-600">
            Tim akan meninjau informasi yang Anda bagikan. Pembaruan dan pilihan pendampingan dapat dilihat melalui halaman pelacakan laporan.
        </p>
    </div>

</div>
@endsection
