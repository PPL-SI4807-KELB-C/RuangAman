@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full">

    {{-- Hero: identitas peran --}}
    <section class="w-full bg-white pt-14 pb-10 sm:pt-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-8 space-y-3">
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-600">
                    Ruang Admin &middot; Penanganan Kasus
                </p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                    Dashboard Admin Kasus
                </h1>
                <p class="mt-3 text-sm text-neutral-600 leading-relaxed max-w-xl">
                    Pantau verifikasi laporan, penugasan konselor, dan persetujuan perubahan status.
                    Anda tidak membuat laporan &mdash; laporan masuk dari Pelapor.
                </p>
            </div>

            <div class="lg:col-span-4 lg:pt-9 space-y-4">
                <x-button href="{{ route('admin.penanganan-kasus.index') }}" variant="primary" size="md" class="w-full">
                    Kelola penugasan kasus
                </x-button>
                <a href="{{ route('admin.status-kasus.index') }}" class="block text-sm font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                    Kelola master status &rarr;
                </a>
            </div>
        </div>
    </section>

    {{-- Statistik & laporan terbaru --}}
    <section class="w-full bg-surface border-t border-neutral-200/70 py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 space-y-12">

            {{-- FR-04: jumlah laporan per status --}}
            <div>
                <div class="flex items-baseline justify-between gap-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-600">Jumlah laporan per status</p>
                    <p class="text-xs text-neutral-600">{{ $totalLaporan }} laporan total</p>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($statusList as $status)
                        <div class="rounded-xl border border-neutral-200/60 bg-white p-6">
                            <x-badge :variant="$status->statusBadgeVariant()">{{ $status->nama_status }}</x-badge>
                            <p class="mt-5 text-3xl font-extrabold tracking-tight text-neutral-900">{{ $status->laporan_kasus_count }}</p>
                            <p class="mt-1 text-xs text-neutral-600">
                                {{ $totalLaporan > 0 ? round($status->laporan_kasus_count / $totalLaporan * 100) : 0 }}% dari seluruh laporan
                            </p>
                        </div>
                    @endforeach
                </div>

                @if ($menungguVerifikasi > 0)
                    <p class="mt-4 text-xs leading-relaxed text-neutral-600">
                        {{ $menungguVerifikasi }} laporan menunggu verifikasi Anda.
                        <a href="{{ route('admin.penanganan-kasus.create') }}" class="font-medium text-neutral-800 hover:text-neutral-900 hover:underline">Tugaskan konselor &rarr;</a>
                    </p>
                @endif
            </div>

            {{-- FR-04: grafik konseling per bulan --}}
            <div class="rounded-xl border border-neutral-200/60 bg-white p-6 sm:p-8">
                <div class="flex items-baseline justify-between gap-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-600">Aktivitas konseling per bulan</p>
                    <p class="text-xs text-neutral-600">{{ $konselingPerBulan->sum('jumlah') }} sesi 6 bulan terakhir</p>
                </div>

                <div class="mt-8 flex items-end gap-3 sm:gap-6">
                    @php $maksimum = max(1, $konselingPerBulan->max('jumlah')); @endphp
                    @foreach ($konselingPerBulan as $bulan)
                        <div class="flex flex-1 flex-col items-center gap-2">
                            <span class="text-xs font-medium text-neutral-800">{{ $bulan['jumlah'] }}</span>
                            <div class="flex h-32 w-full items-end overflow-hidden rounded bg-neutral-200/70">
                                <div
                                    class="w-full bg-brand-600"
                                    style="height: {{ (int) round($bulan['jumlah'] / $maksimum * 100) }}%"
                                    role="img"
                                    aria-label="{{ $bulan['jumlah'] }} sesi pada {{ $bulan['label'] }}"
                                ></div>
                            </div>
                            <span class="text-[11px] text-neutral-600">{{ $bulan['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <p class="mt-6 border-t border-neutral-300/60 pt-4 text-[11px] leading-relaxed text-neutral-600">
                    Grafik dihitung dari tabel sesi konseling. Modul jadwal &amp; sesi konseling belum diimplementasikan,
                    sehingga angka akan muncul setelah modul tersebut beroperasi.
                </p>
            </div>

            {{-- FR-04: laporan terbaru --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-600">Laporan terbaru</p>

                <div class="mt-5 overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-neutral-600">
                            <thead class="border-b border-neutral-200 bg-surface text-[11px] uppercase tracking-wider text-neutral-600">
                                <tr>
                                    <th scope="col" class="px-6 py-3.5 font-medium">Laporan</th>
                                    <th scope="col" class="px-6 py-3.5 font-medium">Status</th>
                                    <th scope="col" class="px-6 py-3.5 font-medium">Penanganan</th>
                                    <th scope="col" class="px-6 py-3.5 font-medium">Diterima</th>
                                    <th scope="col" class="px-6 py-3.5 text-right font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200/70">
                                @forelse ($laporanTerbaru as $laporan)
                                    <tr class="transition-colors hover:bg-surface/70">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-neutral-900">{{ $laporan->judul }}</div>
                                            <p class="mt-0.5 text-[11px] text-neutral-600">
                                                {{ $laporan->kategori_kekerasan }} &bull; Kode {{ $laporan->kode_laporan }}
                                            </p>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <x-badge :variant="$laporan->statusKasus?->statusBadgeVariant() ?? 'slate'">
                                                {{ $laporan->statusKasus?->nama_status ?? 'Tanpa status' }}
                                            </x-badge>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-xs">
                                            {{ $laporan->penangananKasus?->petugas?->nama_tampilan ?? 'Belum ditugaskan' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-xs text-neutral-600">
                                            {{ $laporan->created_at?->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right">
                                            @if ($laporan->penangananKasus)
                                                <a href="{{ route('admin.penanganan-kasus.show', $laporan->penangananKasus) }}" class="text-xs font-medium text-neutral-700 hover:text-neutral-900 hover:underline">
                                                    Lihat penanganan
                                                </a>
                                            @else
                                                <a href="{{ route('admin.penanganan-kasus.create') }}" class="text-xs font-medium text-neutral-700 hover:text-neutral-900 hover:underline">
                                                    Tugaskan konselor
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-xs text-neutral-600">
                                            Belum ada laporan masuk.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>
@endsection
