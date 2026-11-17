@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Data Konselor / Psikolog</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola biodata, spesialisasi/kualifikasi, dan status ketersediaan tenaga pendamping profesional.</p>
        </div>
        <div>
            <x-button href="{{ route('admin.konselor.create') }}" variant="primary" size="md">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Konselor</span>
            </x-button>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <x-card>
        <form method="GET" action="{{ route('admin.konselor.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-12">
            <div class="md:col-span-8">
                <label for="search" class="sr-only">Cari konselor</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama konselor atau spesialisasi..."
                        class="block w-full rounded-lg border border-slate-300 py-2.5 pl-9 pr-3 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                    />
                </div>
            </div>

            <div class="md:col-span-2">
                <label for="status" class="sr-only">Filter Status</label>
                <select
                    id="status"
                    name="status"
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                >
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $val => $label)
                        <option value="{{ $val }}" @selected($selectedStatus === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 md:col-span-2">
                <x-button type="submit" variant="secondary" size="md" class="flex-1">
                    Filter
                </x-button>
                @if ($search !== '' || $selectedStatus !== '')
                    <x-button href="{{ route('admin.konselor.index') }}" variant="ghost" size="md">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Daftar Konselor -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Konselor & Spesialisasi</th>
                        <th scope="col" class="px-6 py-3.5">Akun Pengguna</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Slot Jadwal</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($konselorList as $item)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.konselor.show', $item) }}" class="font-semibold text-slate-900 hover:text-brand-700">
                                    {{ $item->nama }}
                                </a>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $item->spesialisasi }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <div class="font-medium text-slate-800">
                                    {{ $item->user?->nama_tampilan ?? 'Akun tidak ditemukan' }}
                                </div>
                                <div class="mt-0.5 text-slate-500">Akun #{{ $item->user_id }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <span class="font-semibold text-slate-800">{{ (int) $item->jadwal_count }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <x-badge :variant="$item->statusBadgeVariant()">
                                    {{ $item->statusLabel() }}
                                </x-badge>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a
                                        href="{{ route('admin.konselor.show', $item) }}"
                                        class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                    >
                                        Detail
                                    </a>
                                    <a
                                        href="{{ route('admin.konselor.edit', $item) }}"
                                        class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                    >
                                        Ubah
                                    </a>

                                    @if ((int) $item->jadwal_count > 0)
                                        <button
                                            type="button"
                                            disabled
                                            title="Tidak dapat dihapus karena sudah memiliki slot jadwal konseling. Ubah status menjadi Nonaktif."
                                            class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1.5 text-xs font-medium text-slate-400 cursor-not-allowed"
                                        >
                                            Hapus
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-modal', {{ Js::from([
                                                'title' => 'Hapus Data Konselor',
                                                'message' => 'Apakah Anda yakin ingin menghapus permanen profil konselor \"' . $item->nama . '\"? Profil akun penggunanya tetap ada.',
                                                'action' => route('admin.konselor.destroy', $item),
                                                'method' => 'DELETE',
                                                'confirmText' => 'Ya, Hapus Permanen',
                                                'confirmVariant' => 'danger',
                                            ]) }})"
                                            class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-medium text-safety-rose hover:bg-red-100"
                                        >
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">
                                Belum ada data konselor yang sesuai dengan pencarian atau filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($konselorList->hasPages())
            <div class="border-t border-slate-200 bg-slate-50/50 px-6 py-4">
                {{ $konselorList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
