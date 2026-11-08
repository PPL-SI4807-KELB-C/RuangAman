@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Status Kasus</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola master status kasus dalam alur penanganan laporan kekerasan berbasis gender.</p>
        </div>
        <div>
            <x-button href="{{ route('admin.status-kasus.create') }}" variant="primary" size="md">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Status</span>
            </x-button>
        </div>
    </div>

    <!-- Search Box -->
    <x-card>
        <form method="GET" action="{{ route('admin.status-kasus.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari berdasarkan nama status atau deskripsi..."
                        class="block w-full rounded-lg border border-slate-300 py-2.5 pl-9 pr-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                    />
                </div>
            </div>
            <div class="flex gap-2">
                <x-button type="submit" variant="secondary" size="md">
                    Cari
                </x-button>
                @if ($search !== '')
                    <x-button href="{{ route('admin.status-kasus.index') }}" variant="ghost" size="md">
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
                        <th scope="col" class="px-6 py-3.5">ID</th>
                        <th scope="col" class="px-6 py-3.5">Nama Status</th>
                        <th scope="col" class="px-6 py-3.5">Deskripsi</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Laporan Terkait</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($statusKasus as $item)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-slate-500">
                                #{{ $item->status_id }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">
                                @php
                                    $badgeVariant = match (strtolower($item->nama_status)) {
                                        'pending' => 'warning',
                                        'proses' => 'info',
                                        'selesai' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'slate',
                                    };
                                @endphp
                                <x-badge :variant="$badgeVariant">
                                    {{ $item->nama_status }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $item->deskripsi ?: '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                                    {{ $item->laporan_kasus_count }} laporan
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a
                                        href="{{ route('admin.status-kasus.edit', $item) }}"
                                        class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-500"
                                    >
                                        Ubah
                                    </a>

                                    @if ($item->laporan_kasus_count > 0)
                                        <button
                                            type="button"
                                            disabled
                                            title="Tidak dapat dihapus karena sedang digunakan oleh {{ $item->laporan_kasus_count }} laporan kasus"
                                            class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1.5 text-xs font-medium text-slate-400 cursor-not-allowed"
                                        >
                                            Hapus
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Status Kasus',
                                                message: 'Apakah Anda yakin ingin menghapus status \'{{ $item->nama_status }}\'? Tindakan ini akan menghapus data status secara permanen.',
                                                action: '{{ route('admin.status-kasus.destroy', $item) }}',
                                                method: 'DELETE',
                                                confirmText: 'Ya, Hapus Status',
                                                confirmVariant: 'danger'
                                            })"
                                            class="inline-flex items-center rounded-lg border border-red-200 bg-white px-2.5 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                                        >
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-sm font-medium text-slate-700">Tidak ada data status kasus.</p>
                                @if ($search !== '')
                                    <p class="mt-1 text-xs text-slate-500">Coba ubah kata kunci pencarian Anda.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($statusKasus->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $statusKasus->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

