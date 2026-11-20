@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="w-full bg-[#FAF9F7] min-h-[calc(100vh-160px)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
            {{-- Breadcrumb --}}
            <div class="text-[13px] text-[#686563] font-normal mb-3">
                <a href="{{ url('/') }}" class="hover:text-[#1C1917]">Beranda</a> &nbsp;/&nbsp; <span>Instansi bantuan</span>
            </div>

            {{-- Page Header --}}
            <div class="mb-8">
                <h1 class="text-[44px] font-medium text-[#1C1917] leading-tight">
                    Temukan pilihan bantuan Anda.
                </h1>
                <p class="text-base text-[#686563] font-normal mt-3 leading-relaxed">
                    Kenali lembaga pendamping dan pilih dukungan sesuai kebutuhan Anda.<br/>
                    Anda dapat menjelajahi direktori ini tanpa masuk ke akun.
                </p>
            </div>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('instansi.publik') }}" class="flex flex-col sm:flex-row items-stretch gap-3 mb-10">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#1C1917]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7" />
                            <line x1="16.5" y1="16.5" x2="21" y2="21" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari nama instansi atau alamat…" 
                           class="w-full bg-[#FAF9F7] text-base text-[#1C1917] placeholder-[#686563] pl-12 pr-4 py-3 rounded-lg border border-[#DDDCD7] focus:outline-none focus:border-[#16A34A] transition">
                </div>
                <button type="submit" 
                        class="bg-[#16A34A] hover:bg-[#15803d] text-[#1C1917] text-sm font-medium rounded-[2px] px-8 py-3 transition whitespace-nowrap">
                    Cari instansi
                </button>
            </form>

            {{-- Section Title & Count --}}
            <div class="flex items-center justify-between pb-4 border-b border-[#DDDCD7] mb-6">
                <h2 class="text-[20px] font-medium text-[#1C1917]">
                    {{ $totalAktif }} instansi bantuan
                </h2>
                <span class="text-[13px] text-[#686563]">
                    Menampilkan instansi aktif
                </span>
            </div>

            {{-- Grid Cards --}}
            @if($instansis->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($instansis as $instansi)
                        <div class="bg-[#FAF9F7] border border-[#DDDCD7] rounded-[12px] p-6 flex flex-col justify-between hover:shadow-sm transition min-h-[240px]">
                            <div>
                                <h3 class="text-[22px] font-medium text-[#1C1917] leading-snug">
                                    {{ $instansi->nama_instansi }}
                                </h3>
                                <p class="text-[13px] text-[#686563] font-normal mt-1.5">
                                    {{ $instansi->alamat }}
                                </p>
                                <p class="text-base text-[#686563] font-normal mt-3 leading-relaxed">
                                    {{ $instansi->deskripsi }}
                                </p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-[#DDDCD7]">
                                @if($instansi->website)
                                    <a href="{{ $instansi->website }}" target="_blank" rel="noopener noreferrer" class="text-[13px] font-medium text-[#1C1917] hover:underline inline-flex items-center gap-1.5">
                                        <span>{{ parse_url($instansi->website, PHP_URL_HOST) ?? $instansi->website }}</span>
                                        <span class="text-xs">↗</span>
                                    </a>
                                @else
                                    <span class="text-[13px] font-medium text-[#686563]">
                                        Situs web belum tersedia
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 px-4 bg-[#FAF9F7] border border-dashed border-[#DDDCD7] rounded-xl">
                    <p class="text-base font-medium text-[#1C1917]">Tidak ada instansi bantuan ditemukan.</p>
                    <p class="text-sm text-[#686563] mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                </div>
            @endif

            {{-- Footer Disclaimers --}}
            <div class="mt-12 pt-6 border-t border-[#DDDCD7]/60 space-y-2">
                <p class="text-[13px] text-[#686563] font-normal">
                    Bukan layanan darurat. Jika ada ancaman langsung, cari tempat aman dan bantuan setempat.
                </p>
                <p class="text-[12px] text-[#686563] font-normal">
                    Data contoh · Nama, alamat, dan situs instansi pada rancangan ini bersifat fiktif.
                </p>
            </div>
        </div>
    </div>
@endsection
