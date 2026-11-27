@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full bg-[#FAF9F7] min-h-[calc(100vh-160px)]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        {{-- Breadcrumb --}}
        <div class="text-[13px] text-[#686563] font-normal mb-4">
            <a href="{{ url('/') }}" class="hover:text-[#1C1917]">Beranda</a> &nbsp;/&nbsp; <span>Pusat bantuan</span>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-[36px] sm:text-[44px] font-medium text-[#1C1917] leading-tight">
                Ada yang ingin Anda ketahui?
            </h1>
            <p class="text-base text-[#686563] font-normal mt-3 leading-relaxed">
                Temukan jawaban tentang akun, pelaporan, dan privasi di RuangAman.<br/>
                Anda dapat membaca informasi ini tanpa masuk ke akun.
            </p>
        </div>

        {{-- Search Bar --}}
        <form method="GET" action="{{ route('faq.index') }}" class="flex flex-col sm:flex-row items-stretch gap-3 mb-10">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#1C1917]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" />
                        <line x1="16.5" y1="16.5" x2="21" y2="21" />
                    </svg>
                </div>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       aria-label="Cari pertanyaan atau kata kunci"
                       placeholder="Cari pertanyaan atau kata kunci…"
                       class="w-full bg-[#FAF9F7] text-base text-[#1C1917] placeholder-[#686563] pl-12 pr-4 py-3 rounded-lg border border-[#DDDCD7] focus:outline-none focus:border-[#16A34A] transition">
            </div>
            <button type="submit"
                    class="bg-[#16A34A] hover:bg-[#15803d] text-[#1C1917] text-sm font-medium rounded-[2px] px-8 py-3 transition whitespace-nowrap">
                Cari jawaban
            </button>
        </form>

        {{-- Two Column Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            {{-- Left Column: FAQ List --}}
            <div class="lg:col-span-8">
                @if($faqs->isNotEmpty())
                    <div class="divide-y divide-[#DDDCD7]">
                        @foreach($faqs as $faq)
                            <article class="py-6 first:pt-2">
                                <h2 class="text-[20px] font-medium text-[#1C1917] leading-snug">
                                    {{ $faq->pertanyaan }}
                                </h2>
                                <p class="text-base text-[#686563] font-normal mt-3 leading-relaxed whitespace-pre-line">{{ $faq->jawaban }}</p>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-14 px-4 bg-[#FAF9F7] border border-dashed border-[#DDDCD7] rounded-xl">
                        <p class="text-base font-medium text-[#1C1917]">Tidak ada pertanyaan yang sesuai.</p>
                        <p class="text-sm text-[#686563] mt-1">Coba kata kunci lain atau tampilkan semua pertanyaan.</p>
                        @if($search !== '')
                            <div class="mt-4">
                                <a href="{{ route('faq.index') }}"
                                   class="inline-flex items-center justify-center bg-[#EDECE9] hover:bg-[#e4e2de] text-[#1C1917] text-sm font-medium rounded-[2px] px-5 py-2.5 transition">
                                    Tampilkan semua
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <p class="text-[12px] text-[#686563] mt-10">
                    Pratinjau pusat bantuan · Konten FAQ contoh.
                </p>
            </div>

            {{-- Right Column: Support Sidebar --}}
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-[#EDECE9] rounded-[12px] p-6 text-[#1C1917]">
                    <h2 class="text-[22px] font-medium leading-snug">
                        Butuh pilihan bantuan lain?
                    </h2>
                    <p class="text-base text-[#686563] font-normal mt-4 leading-relaxed">
                        Jelajahi instansi bantuan untuk menemukan informasi lembaga dan cara menghubunginya.
                    </p>

                    <div class="mt-6 space-y-3">
                        <a href="{{ route('instansi.publik') }}"
                           class="w-full inline-flex items-center justify-between bg-[#16A34A] hover:bg-[#15803d] text-[#1C1917] text-sm font-medium rounded-[2px] px-4 py-3 transition">
                            <span>Cari instansi bantuan</span>
                            <span aria-hidden="true">↗</span>
                        </a>
                        <a href="{{ route('lacak.form') }}"
                           class="w-full inline-flex items-center justify-start bg-[#FAF9F7] hover:bg-white border border-[#DDDCD7] text-[#1C1917] text-sm font-medium rounded-[2px] px-4 py-3 transition">
                            Lacak laporan
                        </a>
                    </div>
                </div>

                <div class="space-y-3 px-1">
                    <h3 class="text-[20px] font-medium text-[#1C1917] leading-snug">
                        Keselamatan Anda utama
                    </h3>
                    <p class="text-base text-[#686563] font-normal leading-relaxed">
                        RuangAman bukan layanan darurat. Jika ada ancaman langsung, cari tempat aman dan bantuan setempat.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
