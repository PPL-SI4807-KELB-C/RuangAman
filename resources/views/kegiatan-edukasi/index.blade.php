@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="space-y-6">
    <!-- Hero Header -->
    <div class="rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-50 via-white to-slate-50 p-6 sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="max-w-2xl">
                <x-badge variant="brand">Edukasi &amp; Pencegahan KBG</x-badge>
                <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Kegiatan &amp; Webinar Edukasi RuangAman
                </h1>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                    Ikuti sesi edukasi daring maupun tatap muka bersama psikolog dan praktisi hukum secara aman. Identitas Anda dijaga kerahasiaannya selama kegiatan berlangsung.
                </p>
            </div>
            @auth
                @if (auth()->user()->isPelapor())
                    <div class="shrink-0">
                        <x-button href="{{ route('pendaftaran-kegiatan.index') }}" variant="secondary" size="md">
                            Pendaftaran Saya
                        </x-button>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <x-card>
        <form method="GET" action="{{ route('kegiatan.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-12">
            <div class="sm:col-span-6">
                <label for="search" class="sr-only">Cari kegiatan</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari topik webinar atau narasumber..."
                    class="block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                />
            </div>

            <div class="sm:col-span-4">
                <label for="metode" class="sr-only">Metode pelaksanaan</label>
                <select
                    id="metode"
                    name="metode"
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                >
                    <option value="">Semua Metode</option>
                    @foreach ($metodeOptions as $val => $label)
                        <option value="{{ $val }}" @selected($selectedMetode === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 sm:col-span-2">
                <x-button type="submit" variant="primary" size="md" class="flex-1">
                    Cari
                </x-button>
                @if ($search !== '' || $selectedMetode !== '')
                    <x-button href="{{ route('kegiatan.index') }}" variant="ghost" size="md">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Grid Kartu Kegiatan -->
    @if ($kegiatanList->isEmpty())
        <x-card class="py-10 text-center">
            <p class="text-sm text-slate-500">Belum ada jadwal kegiatan atau webinar yang tersedia saat ini.</p>
        </x-card>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($kegiatanList as $item)
                @php
                    $sisa = $item->sisaKuota();
                    $sudahMulai = $item->sudahDimulai();
                    $pendaftaranSaya = $pendaftaranUserMap[$item->kegiatan_id] ?? null;
                @endphp
                <div class="flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-brand-300 hover:shadow-md">
                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-2">
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

                        <h2 class="mt-4 text-lg font-bold text-slate-900 line-clamp-2">
                            <a href="{{ route('kegiatan.show', $item) }}" class="hover:text-brand-700">
                                {{ $item->judul }}
                            </a>
                        </h2>

                        @if ($item->narasumber)
                            <p class="mt-1.5 text-xs font-medium text-slate-500">
                                Narasumber: {{ $item->narasumber }}
                            </p>
                        @endif

                        @if ($item->deskripsi)
                            <p class="mt-3 text-sm text-slate-600 line-clamp-3 leading-relaxed">
                                {{ $item->deskripsi }}
                            </p>
                        @endif
                    </div>

                    <div class="mt-5 border-t border-slate-100 pt-4 space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>{{ $item->tanggal_mulai->translatedFormat('d M Y') }} &bull; {{ substr((string) $item->waktu_mulai, 0, 5) }} WIB</span>
                            <span>Kuota: {{ $item->terisiKuotaCount() }}/{{ $item->kuota }}</span>
                        </div>

                        <x-button href="{{ route('kegiatan.show', $item) }}" variant="secondary" size="sm" class="w-full">
                            Lihat Detail &amp; Pendaftaran
                        </x-button>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($kegiatanList->hasPages())
            <div class="pt-2">
                {{ $kegiatanList->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

