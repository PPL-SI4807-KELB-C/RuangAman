@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-neutral-900">Manajemen Status Kasus</h1>
            <p class="mt-1.5 text-xs text-neutral-500 leading-relaxed max-w-2xl">
                Kelola master status kasus dalam alur penanganan laporan kekerasan berbasis gender.
            </p>
        </div>
        <x-button href="{{ route('admin.status-kasus.create') }}" variant="primary" size="md" class="shrink-0">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Status</span>
        </x-button>
    </div>

    <!-- Search Box -->
    <x-card>
        <form method="GET" action="{{ route('admin.status-kasus.index') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-neutral-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari berdasarkan nama status atau deskripsi..."
                        class="block w-full rounded bg-white py-2.5 pl-9 pr-3 text-sm text-neutral-900 ring-1 ring-inset ring-neutral-300/70 placeholder:text-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400"
                    />
                </div>
            </div>
            <div class="flex gap-3">
                <x-button type="submit" variant="secondary" size="sm">
                    Cari
                </x-button>
                @if ($search !== '')
                    <x-button href="{{ route('admin.status-kasus.index') }}" variant="ghost" size="sm">
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
                <thead class="border-b border-neutral-200 bg-[#f2f2f2] text-[11px] uppercase tracking-wider text-neutral-500">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 font-medium">ID</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Nama Status</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Deskripsi</th>
                        <th scope="col" class="px-6 py-3.5 text-center font-medium">Laporan Terkait</th>
                        <th scope="col" class="px-6 py-3.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/70">
                    @forelse ($statusKasus as $item)
                        <tr class="transition-colors hover:bg-[#f2f2f2]/70">
                            <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-neutral-500">
                                #{{ $item->status_id }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-neutral-900">
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
                            <td class="px-6 py-4 text-xs text-neutral-600">
                                {{ $item->deskripsi ?: '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded bg-[#f2f2f2] px-2.5 py-0.5 text-xs font-semibold text-neutral-700">
                                    {{ $item->laporan_kasus_count }} laporan
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3 text-xs font-medium">
                                    <a
                                        href="{{ route('admin.status-kasus.edit', $item) }}"
                                        class="text-neutral-700 hover:text-neutral-900 hover:underline"
                                    >
                                        Ubah
                                    </a>

                                    @if ($item->laporan_kasus_count > 0)
                                        <span
                                            title="Tidak dapat dihapus karena masih digunakan oleh {{ $item->laporan_kasus_count }} laporan."
                                            class="cursor-not-allowed text-neutral-400"
                                        >
                                            Hapus
                                        </span>
                                    @else
                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Hapus Status Kasus',
                                                message: 'Apakah Anda yakin ingin menghapus status \'{{ $item->nama_status }}\'? Tindakan ini tidak dapat dibatalkan.',
                                                action: '{{ route('admin.status-kasus.destroy', $item) }}',
                                                method: 'DELETE',
                                                confirmText: 'Ya, Hapus',
                                                confirmVariant: 'danger'
                                            })"
                                            class="text-safety-rose hover:underline"
                                        >
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-neutral-500">
                                Belum ada data status kasus yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($statusKasus->hasPages())
            <div class="border-t border-neutral-200/70 bg-[#f2f2f2]/40 px-6 py-4">
                {{ $statusKasus->links() }}
            </div>
        @endif
    </div>
</div>
@endsection