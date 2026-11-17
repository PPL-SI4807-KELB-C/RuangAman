@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Penugasan Kasus</h1>
            <p class="mt-1 text-sm text-slate-500">Tugaskan konselor atau tenaga pendamping pada laporan kasus yang terverifikasi.</p>
        </div>
        <div>
            <x-button href="{{ route('admin.penanganan-kasus.create') }}" variant="primary" size="md">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tugaskan Kasus</span>
            </x-button>
        </div>
    </div>

    <!-- Filter Card -->
    <x-card>
        <form method="GET" action="{{ route('admin.penanganan-kasus.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-12">
            <div class="md:col-span-6">
                <label for="search" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Pencarian</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ $search }}"
                        placeholder="Cari kode laporan, judul kasus, atau nama petugas..."
                        class="block w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                    />
                </div>
            </div>

            <div class="md:col-span-4">
                <label for="petugas_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Filter Petugas/Konselor</label>
                <select
                    name="petugas_id"
                    id="petugas_id"
                    class="block w-full rounded-lg border border-slate-300 bg-white py-2 px-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                >
                    <option value="">Semua Petugas / Konselor</option>
                    @foreach ($daftarPetugas as $p)
                        <option value="{{ $p->user_id }}" @selected($selectedPetugas == $p->user_id)>
                            {{ $p->nama_tampilan }} ({{ $p->role?->nama_role }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex items-end gap-2">
                <x-button type="submit" variant="secondary" size="md" class="w-full">
                    Terapkan
                </x-button>
                @if ($search !== '' || ! empty($selectedPetugas))
                    <x-button href="{{ route('admin.penanganan-kasus.index') }}" variant="ghost" size="md" title="Reset filter">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Table Card -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Laporan Kasus</th>
                        <th scope="col" class="px-6 py-3.5">Status Kasus</th>
                        <th scope="col" class="px-6 py-3.5">Petugas / Konselor</th>
                        <th scope="col" class="px-6 py-3.5">Periode Penanganan</th>
                        <th scope="col" class="px-6 py-3.5">Status Penugasan</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($penangananList as $item)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">
                                    <span class="font-mono text-xs text-brand-700 bg-brand-50 px-1.5 py-0.5 rounded border border-brand-200 mr-1.5">
                                        {{ $item->laporanKasus?->kode_laporan ?? 'N/A' }}
                                    </span>
                                    <span class="text-sm font-semibold">{{ Str::limit($item->laporanKasus?->judul ?? '-', 45) }}</span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    Kategori: {{ $item->laporanKasus?->kategori_kekerasan ?? '-' }} &bull;
                                    Urgensi: <span class="capitalize">{{ $item->laporanKasus?->tingkat_urgensi ?? '-' }}</span>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                @php
                                    $statusName = $item->laporanKasus?->statusKasus?->nama_status ?? 'N/A';
                                    $badgeVariant = match (strtolower($statusName)) {
                                        'pending' => 'warning',
                                        'proses' => 'info',
                                        'selesai' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'slate',
                                    };
                                @endphp
                                <x-badge :variant="$badgeVariant">
                                    {{ $statusName }}
                                </x-badge>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $item->petugas?->nama_tampilan ?? 'User terhapus' }}</div>
                                <div class="text-xs text-slate-500">{{ $item->petugas?->email }} &bull; {{ $item->petugas?->role?->nama_role }}</div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-xs text-slate-700">
                                <div>Mulai: <span class="font-medium">{{ $item->tanggal_mulai ? $item->tanggal_mulai->translatedFormat('d M Y') : '-' }}</span></div>
                                @if ($item->tanggal_selesai)
                                    <div>Selesai: <span class="font-medium">{{ $item->tanggal_selesai->translatedFormat('d M Y') }}</span></div>
                                @else
                                    <span class="text-slate-400 italic">Belum selesai</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($item->tanggal_selesai)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">
                                        Selesai Ditangani
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">
                                        Sedang Berjalan
                                    </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a
                                        href="{{ route('admin.penanganan-kasus.show', $item) }}"
                                        class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-500"
                                        title="Lihat Detail Penanganan"
                                    >
                                        Detail
                                    </a>

                                    <a
                                        href="{{ route('admin.penanganan-kasus.edit', $item) }}"
                                        class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-500"
                                        title="Ubah Penugasan"
                                    >
                                        Ubah
                                    </a>

                                    <button
                                        type="button"
                                        @click="$dispatch('open-confirm-modal', {
                                            title: 'Batalkan Penugasan Kasus',
                                            message: 'Apakah Anda yakin ingin membatalkan penugasan untuk laporan {{ $item->laporanKasus?->kode_laporan }} (Petugas: {{ $item->petugas?->nama_tampilan }})? Penugasan ini akan dihapus dan laporan dapat ditugaskan kembali.',
                                            action: '{{ route('admin.penanganan-kasus.destroy', $item) }}',
                                            method: 'DELETE',
                                            confirmText: 'Ya, Batalkan Penugasan',
                                            confirmVariant: 'danger'
                                        })"
                                        class="inline-flex items-center rounded-lg border border-red-200 bg-white px-2.5 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                                        title="Batalkan Penugasan"
                                    >
                                        Batalkan
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <p class="mt-2 text-sm font-medium text-slate-700">Tidak ada data penugasan kasus.</p>
                                @if ($search !== '' || ! empty($selectedPetugas))
                                    <p class="mt-1 text-xs text-slate-500">Coba ubah filter atau kata kunci pencarian Anda.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($penangananList->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $penangananList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

