@extends('layouts.app')

@section('title', 'Laporan Anda telah diterima — RuangAman')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 py-12">

    <div class="space-y-6">
        {{-- Success Check Icon --}}
        <div class="text-neutral-900">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <path d="m9 12 2 2 4-4" />
            </svg>
        </div>

        <div>
            <h1 class="text-3xl font-extrabold text-neutral-900 tracking-tight">Laporan Anda telah diterima</h1>
            <p class="text-xs text-neutral-600 mt-2 max-w-xl leading-relaxed">
                Terima kasih telah menyampaikan laporan. Anda dapat memeriksa perkembangannya menggunakan kode di bawah ini.
            </p>
        </div>

        {{-- Big Code Card (Figma Exact) --}}
        <div class="rounded-xl bg-[#f2f2f2] p-8 space-y-6 border border-neutral-200/60 max-w-2xl">
            <div>
                <p class="text-[11px] text-neutral-500">Kode laporan Anda</p>
                <p class="text-4xl sm:text-5xl font-extrabold text-neutral-900 tracking-wider font-mono mt-2" id="kode-teks">
                    {{ $kode_laporan }}
                </p>
            </div>

            <div class="pt-4 border-t border-neutral-300/60 flex items-center justify-between text-xs text-neutral-700">
                <span>Alias: {{ session('alias', auth()->user()?->profilPelapor?->alias ?? auth()->user()?->nama_tampilan) }}</span>
                <span class="inline-flex items-center gap-1.5 text-neutral-800">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                    Menunggu peninjauan
                </span>
            </div>
        </div>

        {{-- Warning Box & Instructions --}}
        <div class="space-y-2 max-w-xl pt-2">
            <h2 class="text-sm font-semibold text-neutral-900">Catat kode ini di tempat yang aman.</h2>
            <p class="text-xs text-neutral-600 leading-relaxed">
                Kode memberi akses ke laporan Anda di halaman lacak. Jangan bagikan kepada orang lain. Jika kode hilang, riwayat laporan tetap tampil di dashboard akun Anda.
            </p>
        </div>

        {{-- Actions --}}
        <div class="pt-2 flex flex-wrap items-center gap-4">
            <form method="POST" action="{{ route('lacak.cari') }}">
                @csrf
                <input type="hidden" name="kode_laporan" value="{{ $kode_laporan }}">
                <button type="submit" class="inline-flex items-center justify-center rounded bg-[#10a349] hover:bg-[#0e8e40] text-white font-medium text-xs px-6 py-2.5 transition-colors">
                    Lacak laporan
                </button>
            </form>
            <a href="{{ url('/') }}" class="text-xs text-neutral-700 hover:text-neutral-900">
                Kembali ke beranda
            </a>
        </div>

        {{-- What's Next --}}
        <div class="pt-10 border-t border-neutral-100 max-w-xl space-y-2">
            <h3 class="text-sm font-semibold text-neutral-900">Apa yang terjadi selanjutnya?</h3>
            <p class="text-xs text-neutral-600 leading-relaxed">
                Tim akan meninjau informasi yang Anda bagikan. Pembaruan dan pilihan pendampingan dapat dilihat melalui halaman pelacakan laporan.
            </p>
        </div>
    </div>

</div>
@endsection
