@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full">

    <article class="w-full bg-white py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
          <div class="lg:col-span-8">
            <a href="{{ route('pengumuman.index') }}" class="inline-flex items-center gap-1.5 text-xs text-neutral-600 hover:text-neutral-900 transition-colors">
                <span aria-hidden="true">&larr;</span>
                <span>Semua pengumuman</span>
            </a>

            <h1 class="mt-6 text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                {{ $pengumuman->judul }}
            </h1>

            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] text-neutral-500">
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-2 w-2 shrink-0 rounded-full bg-amber-500" aria-hidden="true"></span>
                    Tayang {{ $pengumuman->mulai_tayang->translatedFormat('d F Y') }}
                    @if ($pengumuman->akhir_tayang)
                        &ndash; {{ $pengumuman->akhir_tayang->translatedFormat('d F Y') }}
                    @else
                        &ndash; berlaku terus
                    @endif
                </span>
                <span>Diumumkan oleh {{ $pengumuman->pembuat?->nama_tampilan ?? 'RuangAman' }}</span>
            </div>

            <div class="mt-8 max-w-2xl whitespace-pre-line text-sm sm:text-base leading-relaxed text-neutral-700">
                {{ $pengumuman->isi }}
            </div>

            <p class="mt-10 border-t border-neutral-200 pt-6 text-xs text-neutral-500 leading-relaxed max-w-2xl">
                Pengumuman ini bersifat informasi layanan. Jika Anda atau orang terdekat membutuhkan bantuan, gunakan jalur pelaporan resmi RuangAman.
            </p>
          </div>
        </div>
    </article>

    {{-- Aksi lanjutan --}}
    <section class="w-full bg-[#f2f2f2] border-t border-neutral-200/70 py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-neutral-600 leading-relaxed max-w-sm">
                Butuh bantuan sekarang? Sampaikan laporan lewat akun alias Anda, tanpa nama asli.
            </p>
            <div class="flex items-center gap-5">
                @guest
                    <x-button href="{{ route('register') }}" variant="primary" size="md" class="shrink-0">
                        Daftar dengan alias
                    </x-button>
                @endguest
                @auth
                    @if (auth()->user()->isPelapor())
                        <x-button href="{{ route('laporan.create') }}" variant="primary" size="md" class="shrink-0">
                            Buat laporan
                        </x-button>
                    @endif
                @endauth
                <a href="{{ route('lacak.form') }}" class="text-xs font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                    Lacak laporan
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
