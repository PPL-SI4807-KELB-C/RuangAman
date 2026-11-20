@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-neutral-900">Manajemen Penugasan Kasus</h1>
            <p class="mt-1.5 text-xs text-neutral-600 leading-relaxed max-w-2xl">
                Tugaskan konselor atau tenaga pendamping pada laporan kasus yang terverifikasi.
            </p>
        </div>
        <x-button href="{{ route('admin.penanganan-kasus.create') }}" variant="primary" size="md" class="shrink-0">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tugaskan Kasus</span>
        </x-button>
    </div>

    <!-- Filter Card -->
    <x-card>
        <form method="GET" action="{{ route('admin.penanganan-kasus.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-12">
            <div class="md:col-span-6">
                <label for="search" class="sr-only">Pencarian</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-neutral-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ $search }}"
                        placeholder="Cari kode laporan, judul kasus, atau nama petugas..."
                        class="block w-full rounded bg-white py-2.5 pl-9 pr-3 text-sm text-neutral-900 ring-1 ring-inset ring-line-strong placeholder:text-neutral-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900"
                    />
                </div>
            </div>

            <div class="md:col-span-4">
                <label for="petugas_id" class="sr-only">Filter Petugas/Konselor</label>
                <select
                    name="petugas_id"
                    id="petugas_id"
                    class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset ring-line-strong focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900"
                >
                    <option value="">Semua Petugas / Konselor</option>
                    @foreach ($daftarPetugas as $p)
                        <option value="{{ $p->user_id }}" @selected($selectedPetugas == $p->user_id)>
                            {{ $p->nama_tampilan }} ({{ $p->role?->nama_role }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 md:col-span-2">
                <x-button type="submit" variant="secondary" size="sm" class="flex-1">
                    Terapkan
                </x-button>
                @if ($search !== '' || ! empty($selectedPetugas))
                    <x-button href="{{ route('admin.penanganan-kasus.index') }}" variant="ghost" size="sm" title="Reset filter">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Table Card -->
    <div class="overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="border-b border-neutral-200 bg-surface text-[11px] uppercase tracking-wider text-neutral-600">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 font-medium">Laporan Kasus</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Status Kasus</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Petugas / Konselor</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Periode Penanganan</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Status Penugasan</th>
                        <th scope="col" class="px-6 py-3.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/70">
                    @forelse ($penangananList as $item)
                        <tr class="transition-colors hover:bg-surface/70">
                            <td class="px-6 py-4">
                                <div class="font-medium text-neutral-900">
                                    <span class="font-mono text-xs text-neutral-800 bg-surface px-1.5 py-0.5 rounded border border-neutral-300/70 mr-1.5">
                                        {{ $item->laporanKasus?->kode_laporan ?? 'N/A' }}
                                    </span>
                                    <span class="text-sm font-semibold">{{ Str::limit($item->laporanKasus?->judul ?? '-', 45) }}</span>
                                </div>
                                <div class="text-[11px] text-neutral-600 mt-1">
                                    Kategori: {{ $item->laporanKasus?->kategori_kekerasan ?? '-' }} &bull;
                                    Urgensi: <span class="capitalize">{{ $item->laporanKasus?->tingkat_urgensi ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @php
                                    $namaStatus = $item->laporanKasus?->statusKasus?->nama_status ?? 'N/A';
                                    $badgeVariant = match (strtolower($namaStatus)) {
                                        'pending' => 'warning',
                                        'proses' => 'info',
                                        'selesai' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'slate',
                                    };
                                @endphp
                                <x-badge :variant="$badgeVariant">
                                    {{ $namaStatus }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="font-medium text-neutral-900">{{ $item->petugas?->nama_tampilan ?? 'N/A' }}</div>
                                <div class="text-[11px] text-neutral-600">{{ $item->petugas?->role?->nama_role ?? '-' }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs text-neutral-600">
                                <div>Mulai: {{ $item->tanggal_mulai ? $item->tanggal_mulai->format('d M Y') : '-' }}</div>
                                <div class="text-neutral-600 text-[11px]">
                                    Selesai: {{ $item->tanggal_selesai ? $item->tanggal_selesai->format('d M Y') : 'Berlangsung' }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($item->isSelesai())
                                    <x-badge variant="success">Selesai</x-badge>
                                @else
                                    <x-badge variant="info">Sedang Ditangani</x-badge>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3 text-xs font-medium">
                                    <a
                                        href="{{ route('admin.penanganan-kasus.show', $item) }}"
                                        class="text-neutral-700 hover:text-neutral-900 hover:underline"
                                    >
                                        Detail
                                    </a>
                                    <a
                                        href="{{ route('admin.penanganan-kasus.edit', $item) }}"
                                        class="text-neutral-700 hover:text-neutral-900 hover:underline"
                                    >
                                        Ubah
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-neutral-600">
                                Belum ada data penugasan kasus yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($penangananList->hasPages())
            <div class="border-t border-neutral-200/70 bg-surface/40 px-6 py-4">
                {{ $penangananList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection