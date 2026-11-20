@extends('layouts.app')

@section('title', $title)

@section('content')
@php
    $jadwalBadge = ['tersedia' => 'slate', 'dipesan' => 'info', 'selesai' => 'success', 'dibatalkan' => 'danger'];
@endphp

<div class="w-full">

    {{-- Hero: identitas peran --}}
    <section class="w-full bg-white pt-14 pb-10 sm:pt-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-8 space-y-3">
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">
                    Ruang Pendamping &middot; Konselor
                </p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                    Dashboard Konselor
                </h1>
                <p class="mt-3 text-sm text-neutral-600 leading-relaxed max-w-xl">
                    Anda melihat kasus yang ditugaskan kepada {{ $namaKonselor }} beserta catatan penanganannya.
                    Identitas pelapor tetap berupa alias, dan Anda tidak membuat atau menyetujui status laporan.
                </p>
            </div>

            <div class="lg:col-span-4 lg:pt-9 space-y-4">
                <x-button href="{{ route('konselor.profil.edit') }}" variant="secondary" size="md" class="w-full">
                    Perbarui profil konselor
                </x-button>
                <a href="{{ route('pengumuman.index') }}" class="block text-sm font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                    Baca pengumuman layanan &rarr;
                </a>
            </div>
        </div>
    </section>

    {{-- Ringkasan & daftar kerja --}}
    <section class="w-full bg-[#f2f2f2] border-t border-neutral-200/70 py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 space-y-12">

            {{-- FR-05: kartu ringkasan --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div class="rounded-xl border border-neutral-200/60 bg-white p-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Kasus ditugaskan</p>
                    <p class="mt-5 text-3xl font-extrabold tracking-tight text-neutral-900">{{ $totalKasus }}</p>
                    <p class="mt-1 text-xs text-neutral-500">Termasuk kasus yang sudah selesai ditangani</p>
                </div>
                <div class="rounded-xl border border-neutral-200/60 bg-white p-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Jadwal konseling</p>
                    <p class="mt-5 text-3xl font-extrabold tracking-tight text-neutral-900">{{ $totalJadwal }}</p>
                    <p class="mt-1 text-xs text-neutral-500">Sesi mendatang yang belum dibatalkan</p>
                </div>
                <div class="rounded-xl border border-neutral-200/60 bg-white p-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Aktivitas tercatat</p>
                    <p class="mt-5 text-3xl font-extrabold tracking-tight text-neutral-900">{{ $totalAktivitas }}</p>
                    <p class="mt-1 text-xs text-neutral-500">Catatan manual dan entri sistem pada kasus Anda</p>
                </div>
            </div>

            {{-- FR-05: kasus yang ditugaskan --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Kasus yang ditugaskan kepada saya</p>

                @if ($kasusSaya->isEmpty())
                    <div class="mt-5 rounded-xl border border-dashed border-neutral-300 bg-white p-8 text-center">
                        <p class="text-xs text-neutral-600 leading-relaxed">
                            Belum ada kasus yang ditugaskan kepada Anda. Penugasan dilakukan Admin Kasus melalui ruang penanganan kasus.
                        </p>
                    </div>
                @else
                    <div class="mt-5 overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-neutral-600">
                                <thead class="border-b border-neutral-200 bg-[#f2f2f2] text-[11px] uppercase tracking-wider text-neutral-500">
                                    <tr>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Laporan</th>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Status</th>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Rentang Penanganan</th>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-200/70">
                                    @foreach ($kasusSaya as $penanganan)
                                        <tr class="transition-colors hover:bg-[#f2f2f2]/70">
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-neutral-900">{{ $penanganan->laporanKasus?->judul ?? 'Laporan tidak ditemukan' }}</div>
                                                <p class="mt-0.5 text-[11px] text-neutral-500">
                                                    Kode {{ $penanganan->laporanKasus?->kode_laporan ?? '-' }}
                                                    &bull; {{ $penanganan->laporanKasus?->kategori_kekerasan ?? '-' }}
                                                </p>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <x-badge :variant="$penanganan->laporanKasus?->statusKasus?->statusBadgeVariant() ?? 'slate'">
                                                    {{ $penanganan->laporanKasus?->statusKasus?->nama_status ?? 'Tanpa status' }}
                                                </x-badge>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-xs">
                                                <div class="font-medium text-neutral-800">{{ $penanganan->tanggal_mulai?->translatedFormat('d M Y') ?? '-' }}</div>
                                                <div class="mt-0.5 text-neutral-500">
                                                    s/d {{ $penanganan->tanggal_selesai ? $penanganan->tanggal_selesai->translatedFormat('d M Y') : 'berjalan' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-xs leading-relaxed text-neutral-600">
                                                <span class="line-clamp-2">{{ $penanganan->catatan ?: 'Belum ada catatan penanganan.' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- FR-05: jadwal konseling mendatang --}}
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
                <div class="lg:col-span-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Jadwal konseling mendatang</p>

                    <div class="mt-5 overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
                        <ul class="divide-y divide-neutral-200/70">
                            @forelse ($jadwalMendatang as $jadwal)
                                <li class="px-6 py-4">
                                    <div class="flex items-baseline justify-between gap-4">
                                        <span class="text-sm font-semibold text-neutral-900">
                                            {{ \Illuminate\Support\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }}
                                        </span>
                                        <x-badge :variant="$jadwalBadge[$jadwal->status] ?? 'slate'">{{ ucfirst($jadwal->status) }}</x-badge>
                                    </div>
                                    <p class="mt-1 text-xs text-neutral-600">
                                        {{ substr((string) $jadwal->waktu_mulai, 0, 5) }}&ndash;{{ substr((string) $jadwal->waktu_selesai, 0, 5) }} WIB
                                        &bull; {{ ucfirst($jadwal->metode) }}
                                        &bull; {{ $jadwal->lokasi_atau_tautan ?: 'Lokasi/tautan menyusul' }}
                                    </p>
                                </li>
                            @empty
                                <li class="px-6 py-10 text-center text-xs text-neutral-500">
                                    Belum ada jadwal konseling mendatang. Modul penjadwalan konseling masih dalam pengembangan tim.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                {{-- FR-05: aktivitas penanganan terbaru --}}
                <div class="lg:col-span-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Aktivitas penanganan terbaru</p>

                    <div class="mt-5 overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
                        <ul class="divide-y divide-neutral-200/70">
                            @forelse ($aktivitasTerbaru as $log)
                                <li class="px-6 py-4">
                                    <p class="text-sm leading-relaxed text-neutral-800">{{ $log->aktivitas }}</p>
                                    <p class="mt-1 text-[11px] text-neutral-500">
                                        {{ $log->waktu?->translatedFormat('d M Y H:i') }}
                                        &bull; {{ $log->user?->nama_tampilan ?? 'Sistem' }}
                                        &bull; {{ $log->laporanKasus?->kode_laporan ?? '-' }}
                                    </p>
                                </li>
                            @empty
                                <li class="px-6 py-10 text-center text-xs text-neutral-500">
                                    Belum ada aktivitas tercatat pada kasus yang Anda pegang.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>
@endsection
