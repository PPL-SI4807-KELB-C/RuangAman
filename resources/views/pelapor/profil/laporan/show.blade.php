@extends('layouts.app')

@section('title', 'Detail Laporan ' . $laporan->kode_laporan . ' - RuangAman')

@section('content')
<div class="mx-auto max-w-4xl py-6">
    {{-- Header Kode Pelacakan & Status --}}
    @php
        $statusName = $laporan->status->nama_status ?? 'Pending';
        $isPending = strtolower($statusName) === 'pending';
    @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kode Pelacakan</span>
            <h1 class="mt-1 font-mono text-2xl font-bold tracking-tight text-brand-700 sm:text-3xl">
                {{ $laporan->kode_laporan }}
            </h1>
            @if ($laporan->judul)
                <p class="mt-1 text-sm font-medium text-slate-700">{{ $laporan->judul }}</p>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center rounded-full px-3.5 py-1.5 text-xs font-semibold {{ $isPending ? 'bg-amber-100 text-amber-800' : 'bg-brand-100 text-brand-800' }}">
                {{ $statusName }}
            </span>
        </div>
    </div>

    {{-- Kartu Detail Informasi Laporan --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="p-6 sm:p-8">
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <span class="block text-xs font-medium text-slate-500">Kategori Kekerasan</span>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $laporan->kategori_kekerasan }}</p>
                </div>

                <div>
                    <span class="block text-xs font-medium text-slate-500">Tingkat Urgensi</span>
                    <p class="mt-1 text-sm font-semibold capitalize text-slate-900">{{ $laporan->tingkat_urgensi }}</p>
                </div>

                <div>
                    <span class="block text-xs font-medium text-slate-500">Waktu Kejadian</span>
                    <p class="mt-1 text-sm font-semibold text-slate-900">
                        {{ $laporan->waktu_kejadian?->format('Y-m-d H:i:s') ?? '-' }}
                    </p>
                </div>

                <div>
                    <span class="block text-xs font-medium text-slate-500">Mode Privasi</span>
                    <div class="mt-1">
                        @if ($laporan->mode_anonim)
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-700">
                                Anonim
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                                Teridentifikasi
                            </span>
                        @endif
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <span class="block text-xs font-medium text-slate-500">Lokasi Kejadian</span>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $laporan->lokasi_kejadian }}</p>
                </div>
            </div>

            <hr class="my-6 border-slate-200">

            {{-- Kronologi Kejadian --}}
            <div>
                <span class="block text-xs font-medium text-slate-500">Kronologi Kejadian</span>
                <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-relaxed text-slate-800 whitespace-pre-wrap">{{ $laporan->kronologi }}</div>
            </div>

            {{-- Lampiran Bukti Privat --}}
            <div class="mt-6">
                <span class="block text-xs font-medium text-slate-500">Lampiran Bukti</span>

                @if ($laporan->lampirans && $laporan->lampirans->isNotEmpty())
                    <ul class="mt-2 divide-y divide-slate-200 rounded-xl border border-slate-200 bg-white">
                        @foreach ($laporan->lampirans as $lampiran)
                            <li class="flex items-center justify-between gap-4 px-4 py-3 text-sm">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-medium text-slate-900">{{ $lampiran->nama_file }}</p>
                                    @if ($lampiran->ukuran_byte)
                                        <p class="text-xs text-slate-500">{{ number_format($lampiran->ukuran_byte / 1024, 1) }} KB</p>
                                    @endif
                                </div>

                                <a href="{{ route('laporan.lampiran.download', $lampiran) }}"
                                   class="inline-flex shrink-0 items-center rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-100">
                                    Unduh Bukti
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-1.5 text-sm text-slate-500">Tidak ada lampiran bukti yang diunggah.</p>
                @endif
            </div>
        </div>

        {{-- Footer Aksi: Hanya Tombol Kembali --}}
        <div class="flex items-center justify-end border-t border-slate-200 bg-slate-50 px-6 py-4">
            <a href="{{ route('laporan.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection