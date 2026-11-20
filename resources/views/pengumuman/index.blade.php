@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full">

    {{-- Hero --}}
    <section class="w-full bg-white pt-14 pb-10 sm:pt-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-8 space-y-3">
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">
                    Informasi layanan
                </p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                    Pengumuman
                </h1>
                <p class="mt-3 text-sm text-neutral-600 leading-relaxed max-w-xl">
                    Informasi layanan terbaru dari RuangAman. Hanya pengumuman dalam periode tayang yang ditampilkan di sini.
                </p>
            </div>
        </div>
    </section>

    {{-- Daftar pengumuman --}}
    <section class="w-full bg-[#f2f2f2] border-t border-neutral-200/70 py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-8 space-y-6">
                @forelse ($pengumumanList as $item)
                    <article class="rounded-xl border border-neutral-200/60 bg-white p-6 sm:p-7">
                        <h2 class="text-base font-bold text-neutral-900 tracking-tight">
                            <a href="{{ route('pengumuman.show', $item) }}" class="hover:text-brand-700">
                                {{ $item->judul }}
                            </a>
                        </h2>
                        <p class="mt-1.5 text-[11px] text-neutral-500">
                            Tayang {{ $item->mulai_tayang->translatedFormat('d F Y') }}
                            @if ($item->akhir_tayang)
                                &ndash; {{ $item->akhir_tayang->translatedFormat('d F Y') }}
                            @else
                                &ndash; berlaku terus
                            @endif
                        </p>
                        <div class="mt-3 whitespace-pre-line text-xs leading-relaxed text-neutral-600">
                            {{ $item->isi }}
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-neutral-300 bg-white p-8 text-center">
                        <p class="text-xs text-neutral-600 leading-relaxed">
                            Tidak ada pengumuman saat ini. Layanan berjalan seperti biasa dan Anda tetap dapat membuat laporan kapan saja.
                        </p>
                    </div>
                @endforelse

                @if ($pengumumanList->hasPages())
                    <div class="border-t border-neutral-300/60 pt-6">
                        {{ $pengumumanList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>

</div>
@endsection
