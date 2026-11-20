@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-neutral-900">Manajemen Data Konselor / Psikolog</h1>
            <p class="mt-1.5 text-xs text-neutral-500 leading-relaxed max-w-2xl">
                Kelola biodata, spesialisasi/kualifikasi, dan status ketersediaan tenaga pendamping profesional.
            </p>
        </div>
        <x-button href="{{ route('admin.konselor.create') }}" variant="primary" size="md" class="shrink-0">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Konselor</span>
        </x-button>
    </div>

    <!-- Filter & Pencarian -->
    <x-card>
        <form method="GET" action="{{ route('admin.konselor.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-12">
            <div class="md:col-span-8">
                <label for="search" class="sr-only">Cari konselor</label>
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
                        placeholder="Cari nama konselor atau spesialisasi..."
                        class="block w-full rounded bg-white py-2.5 pl-9 pr-3 text-sm text-neutral-900 ring-1 ring-inset ring-neutral-300/70 placeholder:text-neutral-400 focus:outline-none focus:ring-1 focus:ring-neutral-400"
                    />
                </div>
            </div>

            <div class="md:col-span-2">
                <label for="status" class="sr-only">Filter Status</label>
                <select
                    id="status"
                    name="status"
                    class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset ring-neutral-300/70 focus:outline-none focus:ring-1 focus:ring-neutral-400"
                >
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $val => $label)
                        <option value="{{ $val }}" @selected($selectedStatus === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 md:col-span-2">
                <x-button type="submit" variant="secondary" size="sm" class="flex-1">
                    Filter
                </x-button>
                @if ($search !== '' || $selectedStatus !== '')
                    <x-button href="{{ route('admin.konselor.index') }}" variant="ghost" size="sm">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Daftar Konselor -->
    <div class="overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="border-b border-neutral-200 bg-[#f2f2f2] text-[11px] uppercase tracking-wider text-neutral-500">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 font-medium">Konselor &amp; Spesialisasi</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Akun Pengguna</th>
                        <th scope="col" class="px-6 py-3.5 text-center font-medium">Slot Jadwal</th>
                        <th scope="col" class="px-6 py-3.5 text-center font-medium">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/70">
                    @forelse ($konselorList as $item)
                        <tr class="transition-colors hover:bg-[#f2f2f2]/70">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.konselor.show', $item) }}" class="font-semibold text-neutral-900 hover:underline">
                                    {{ $item->nama }}
                                </a>
                                <p class="mt-0.5 text-[11px] text-neutral-500">{{ $item->spesialisasi }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs text-neutral-600">
                                <div class="font-medium text-neutral-900">
                                    {{ $item->user?->nama_tampilan ?? 'Akun tidak ditemukan' }}
                                </div>
                                <div class="mt-0.5 text-[11px] text-neutral-500">Akun #{{ $item->user_id }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <span class="font-semibold text-neutral-900">{{ (int) $item->jadwal_count }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <x-badge :variant="$item->statusBadgeVariant()">
                                    {{ $item->statusLabel() }}
                                </x-badge>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3 text-xs font-medium">
                                    <a
                                        href="{{ route('admin.konselor.show', $item) }}"
                                        class="text-neutral-700 hover:text-neutral-900 hover:underline"
                                    >
                                        Detail
                                    </a>
                                    <a
                                        href="{{ route('admin.konselor.edit', $item) }}"
                                        class="text-neutral-700 hover:text-neutral-900 hover:underline"
                                    >
                                        Ubah
                                    </a>
                                    @if ($item->jadwal_count > 0)
                                        <span
                                            title="Tidak dapat dihapus karena sudah memiliki slot jadwal konseling. Ubah status menjadi Nonaktif."
                                            class="cursor-not-allowed text-neutral-500"
                                        >
                                            Hapus
                                        </span>
                                    @else
                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-modal', {{ Js::from([
                                                'title' => 'Hapus Data Konselor',
                                                'message' => 'Apakah Anda yakin ingin menghapus profil konselor “' . $item->nama . '”? Data akun penggunanya tetap ada.',
                                                'action' => route('admin.konselor.destroy', $item),
                                                'method' => 'DELETE',
                                                'confirmText' => 'Ya, Hapus Permanen',
                                                'confirmVariant' => 'danger',
                                            ]) }})"
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
                                Belum ada data konselor yang sesuai dengan pencarian atau filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($konselorList->hasPages())
            <div class="border-t border-neutral-200/70 bg-[#f2f2f2]/40 px-6 py-4">
                {{ $konselorList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection