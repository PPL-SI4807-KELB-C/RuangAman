@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full">

    {{-- Isi halaman --}}
    <section class="w-full bg-white py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-8">
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">
                    Informasi layanan
                </p>
                <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                    {{ $halaman->judul }}
                </h1>
                <p class="mt-3 text-[11px] text-neutral-500">
                    Terakhir diperbarui {{ $halaman->updated_at->translatedFormat('d F Y') }}
                </p>

                @unless ($halaman->isPublished())
                    <div class="mt-6 max-w-xl">
                        <x-alert variant="warning" title="Draf halaman">
                            Halaman ini belum diterbitkan dan hanya dapat dilihat oleh Admin Kasus.
                        </x-alert>
                    </div>
                @endunless

                <article class="mt-8 max-w-2xl whitespace-pre-line text-sm sm:text-base leading-relaxed text-neutral-700">
                    {{ $halaman->isi }}
                </article>
            </div>
        </div>
    </section>

    {{-- Tautan ke glosarium --}}
    <section class="w-full bg-[#f2f2f2] border-t border-neutral-200/70 py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-neutral-600 leading-relaxed max-w-sm">
                Masih ada istilah yang belum jelas? Glosarium menghimpun definisi yang dipakai tim pendamping.
            </p>
            <x-button href="{{ route('glosarium.index') }}" variant="primary" size="md" class="shrink-0">
                Buka glosarium
            </x-button>
        </div>
    </section>

</div>
@endsection
