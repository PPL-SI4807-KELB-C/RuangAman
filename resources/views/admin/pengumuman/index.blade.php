@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-neutral-900">Manajemen Pengumuman</h1>
            <p class="mt-1.5 text-xs text-neutral-500 leading-relaxed max-w-2xl">
                Kelola pengumuman publik beserta periode tayangnya. Pengumuman tidak memicu notifikasi otomatis.
            </p>
        </div>
        <x-button href="{{ route('admin.pengumuman.create') }}" variant="primary" size="md" class="shrink-0">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Pengumuman</span>
        </x-button>
    </div>

    {{-- Filter & Pencarian --}}
    <x-card>
        <form method="GET" action="{{ route('admin.pengumuman.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-12">
            <div class="md:col-span-8">
                <label for="search" class="sr-only">Cari pengumuman</label>
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
                        placeholder="Cari judul atau isi pengumuman..."
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
                <x-button type="submit" variant="secondary" size="sm" class="flex-1">Filter</x-button>
                @if ($search !== '' || $selectedStatus !== '')
                    <x-button href="{{ route('admin.pengumuman.index') }}" variant="ghost" size="sm">Reset</x-button>
                @endif
            </div>
        </form>
    </x-card>

    {{-- Daftar Pengumuman --}}
    <div class="overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="border-b border-neutral-200 bg-[#f2f2f2] text-[11px] uppercase tracking-wider text-neutral-500">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 font-medium">Judul &amp; Pembuat</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Periode Tayang</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Status</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Daya Tayang</th>
                        <th scope="col" class="px-6 py-3.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/70">
                    @forelse ($pengumumanList as $item)
                        <tr class="transition-colors hover:bg-[#f2f2f2]/70">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.pengumuman.show', $item) }}" class="font-semibold text-neutral-900 hover:text-brand-700">
                                    {{ $item->judul }}
                                </a>
                                <p class="mt-0.5 text-[11px] text-neutral-500">
                                    oleh {{ $item->pembuat?->nama_tampilan ?? 'Akun tidak ditemukan' }}
                                </p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs text-neutral-600">
                                <div class="font-medium text-neutral-800">{{ $item->mulai_tayang->translatedFormat('d M Y') }}</div>
                                <div class="mt-0.5 text-neutral-500">
                                    s/d {{ $item->akhir_tayang ? $item->akhir_tayang->translatedFormat('d M Y') : 'tanpa batas' }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <x-badge :variant="$item->statusBadgeVariant()">{{ $item->statusLabel() }}</x-badge>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs">
                                @if ($item->sedangTayang())
                                    <span class="inline-flex items-center gap-1.5 font-medium text-neutral-800">
                                        <span class="h-2 w-2 shrink-0 rounded-full bg-brand-600" aria-hidden="true"></span>
                                        {{ $item->statusTayangLabel() }}
                                    </span>
                                @else
                                    <span class="text-neutral-500">{{ $item->statusTayangLabel() }}</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-4 text-xs font-medium">
                                    <a href="{{ route('admin.pengumuman.show', $item) }}" class="text-neutral-700 hover:text-neutral-900 hover:underline">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.pengumuman.edit', $item) }}" class="text-neutral-700 hover:text-neutral-900 hover:underline">
                                        Ubah
                                    </a>
                                    <button
                                        type="button"
                                        @click="$dispatch('open-confirm-modal', {{ Js::from([
                                            'title' => 'Hapus Pengumuman',
                                            'message' => 'Apakah Anda yakin ingin menghapus permanen pengumuman “' . $item->judul . '”? Arsip historinya tidak tersisa.',
                                            'action' => route('admin.pengumuman.destroy', $item),
                                            'method' => 'DELETE',
                                            'confirmText' => 'Ya, Hapus Permanen',
                                            'confirmVariant' => 'danger',
                                        ]) }})"
                                        class="text-safety-rose hover:underline"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-xs text-neutral-500">
                                Belum ada pengumuman yang sesuai dengan pencarian atau filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengumumanList->hasPages())
            <div class="border-t border-neutral-200/70 px-6 py-4">
                {{ $pengumumanList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
