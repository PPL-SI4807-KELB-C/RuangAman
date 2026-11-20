@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full">

    {{-- Hero --}}
    <section class="w-full bg-white pt-14 pb-10 sm:pt-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-8 space-y-3">
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">
                    Edukasi &amp; Pencegahan KBG
                </p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                    Kegiatan &amp; Webinar Edukasi RuangAman
                </h1>
                <p class="mt-3 text-sm text-neutral-600 leading-relaxed max-w-xl">
                    Ikuti sesi edukasi daring maupun tatap muka bersama psikolog dan praktisi hukum secara aman. Identitas Anda dijaga kerahasiaannya selama kegiatan berlangsung.
                </p>
            </div>

            @auth
                @if (auth()->user()->isPelapor())
                    <div class="lg:col-span-4 lg:pt-9">
                        <a href="{{ route('pendaftaran-kegiatan.index') }}" class="text-sm font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                            Pendaftaran Saya &rarr;
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    </section>

    {{-- Filter & daftar kegiatan --}}
    <section class="w-full bg-[#f2f2f2] border-t border-neutral-200/70 py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-8 space-y-8">
                <form method="GET" action="{{ route('kegiatan.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-12">
                    <div class="sm:col-span-6">
                        <label for="search" class="sr-only">Cari kegiatan</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4 w-4 text-neutral-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Cari topik webinar atau narasumber..."
                                class="block w-full rounded bg-white py-2.5 pl-9 pr-3 text-sm text-neutral-900 ring-1 ring-inset ring-neutral-300/70 placeholder:text-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400"
                            />
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="metode" class="sr-only">Metode pelaksanaan</label>
                        <select
                            id="metode"
                            name="metode"
                            class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset ring-neutral-300/70 focus:outline-none focus:ring-1 focus:ring-neutral-400"
                        >
                            <option value="">Semua Metode</option>
                            @foreach ($metodeOptions as $val => $label)
                                <option value="{{ $val }}" @selected($selectedMetode === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-3 sm:col-span-3">
                        <x-button type="submit" variant="secondary" size="sm" class="flex-1">Cari</x-button>
                        @if ($search !== '' || $selectedMetode !== '')
                            <x-button href="{{ route('kegiatan.index') }}" variant="ghost" size="sm">Reset</x-button>
                        @endif
                    </div>
                </form>

                @if ($kegiatanList->isEmpty())
                    <div class="rounded-xl border border-dashed border-neutral-300 bg-white p-8 text-center">
                        <p class="text-xs text-neutral-600 leading-relaxed">
                            Belum ada jadwal kegiatan atau webinar yang tersedia saat ini. Cek kembali nanti atau sampaikan laporan Anda kapan saja.
                        </p>
                    </div>
                @else
                    <ul class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        @foreach ($kegiatanList as $item)
                            @php
                                $sisa = $item->sisaKuota();
                                $sudahMulai = $item->sudahDimulai();
                                $pendaftaranSaya = $pendaftaranUserMap[$item->kegiatan_id] ?? null;
                            @endphp
                            <li class="flex flex-col rounded-xl border border-neutral-200/60 bg-white p-6">
                                <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1">
                                    <x-badge :variant="$item->metode === 'online' ? 'info' : 'brand'">
                                        {{ $item->metodeLabel() }}
                                    </x-badge>

                                    @if ($pendaftaranSaya && $pendaftaranSaya->status_pendaftaran !== \App\Models\PendaftaranKegiatan::STATUS_DIBATALKAN)
                                        <x-badge :variant="$pendaftaranSaya->statusBadgeVariant()">
                                            {{ $pendaftaranSaya->statusLabel() }}
                                        </x-badge>
                                    @elseif ($sudahMulai)
                                        <x-badge variant="slate">Sudah Dimulai</x-badge>
                                    @elseif ($sisa === 0)
                                        <x-badge variant="danger">Kuota Penuh</x-badge>
                                    @else
                                        <x-badge variant="success">Sisa {{ $sisa }} kursi</x-badge>
                                    @endif
                                </div>

                                <h2 class="mt-4 text-base font-bold text-neutral-900 tracking-tight leading-snug">
                                    <a href="{{ route('kegiatan.show', $item) }}" class="hover:text-brand-700">
                                        {{ $item->judul }}
                                    </a>
                                </h2>

                                @if ($item->narasumber)
                                    <p class="mt-1.5 text-xs text-neutral-600">
                                        Narasumber: <span class="font-medium text-neutral-800">{{ $item->narasumber }}</span>
                                    </p>
                                @endif

                                @if ($item->deskripsi)
                                    <p class="mt-3 line-clamp-3 text-xs leading-relaxed text-neutral-600">
                                        {{ $item->deskripsi }}
                                    </p>
                                @endif

                                <div class="mt-5 border-t border-neutral-300/60 pt-4">
                                    <p class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 text-xs text-neutral-600">
                                        <span>{{ $item->tanggal_mulai->translatedFormat('d M Y') }} &bull; {{ substr((string) $item->waktu_mulai, 0, 5) }} WIB</span>
                                        <span>{{ $item->terisiKuotaCount() }}/{{ $item->kuota }} peserta</span>
                                    </p>
                                    <a href="{{ route('kegiatan.show', $item) }}" class="mt-3 inline-flex text-xs font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                                        Lihat detail &amp; pendaftaran &rarr;
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    @if ($kegiatanList->hasPages())
                        <div class="border-t border-neutral-300/60 pt-6">
                            {{ $kegiatanList->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </section>

</div>
@endsection
