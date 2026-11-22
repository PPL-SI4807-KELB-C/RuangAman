@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-700">PBI-10 · Niky</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-neutral-900">Manajemen Artikel Edukasi</h1>
            <p class="mt-1 text-sm text-neutral-600">Kelola artikel, kategori referensi, dan status publikasi konten edukasi KBG.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-button href="{{ route('artikel.index') }}" variant="secondary" size="md">Lihat Halaman Publik</x-button>
            <x-button href="{{ route('admin.artikel.create') }}" variant="primary" size="md">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Artikel</span>
            </x-button>
        </div>
    </div>

    <x-card>
        <form method="GET" action="{{ route('admin.artikel.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-12">
            <div class="md:col-span-5">
                <label for="search" class="sr-only">Cari artikel</label>
                <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Cari judul atau isi artikel..." class="block w-full rounded-lg border border-line-strong px-3 py-2.5 text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900" />
            </div>
            <div class="md:col-span-2">
                <label for="kategori_id" class="sr-only">Filter kategori</label>
                <select id="kategori_id" name="kategori_id" class="block w-full rounded-lg border border-line-strong bg-white px-3 py-2.5 text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoriOptions as $id => $label)
                        <option value="{{ $id }}" @selected($selectedKategori === (int) $id)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="status" class="sr-only">Filter status</label>
                <select id="status" name="status" class="block w-full rounded-lg border border-line-strong bg-white px-3 py-2.5 text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900">
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 md:col-span-3">
                <x-button type="submit" variant="secondary" size="md" class="flex-1">Terapkan Filter</x-button>
                @if ($search !== '' || $selectedStatus !== '' || $selectedKategori > 0)
                    <x-button href="{{ route('admin.artikel.index') }}" variant="ghost" size="md">Reset</x-button>
                @endif
            </div>
        </form>
    </x-card>

    <div class="overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="border-b border-neutral-200/70 bg-surface text-xs uppercase tracking-wider text-neutral-600">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Artikel</th>
                        <th scope="col" class="px-6 py-3.5">Kategori</th>
                        <th scope="col" class="px-6 py-3.5">Penulis</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Status</th>
                        <th scope="col" class="px-6 py-3.5">Diperbarui</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/70">
                    @forelse ($artikelList as $item)
                        <tr class="transition hover:bg-neutral-50">
                            <td class="max-w-md px-6 py-4">
                                <a href="{{ route('admin.artikel.show', $item) }}" class="font-semibold text-neutral-900 hover:text-brand-700">{{ $item->judul }}</a>
                                <p class="mt-1 text-xs text-neutral-600 line-clamp-2">{{ $item->ringkasan(110) }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">{{ $item->kategori?->nama_kategori ?? 'Tanpa kategori' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ $item->penulis?->nama_tampilan ?? 'Tidak diketahui' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <x-badge :variant="$item->statusBadgeVariant()">{{ $item->statusLabel() }}</x-badge>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs">{{ $item->updated_at?->translatedFormat('d M Y, H:i') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.artikel.show', $item) }}" class="rounded-lg border border-neutral-300 bg-white px-2.5 py-1.5 text-xs font-medium text-neutral-700 hover:bg-surface">Detail</a>
                                    <a href="{{ route('admin.artikel.edit', $item) }}" class="rounded-lg border border-neutral-300 bg-white px-2.5 py-1.5 text-xs font-medium text-neutral-700 hover:bg-surface">Ubah</a>
                                    <button type="button" @click="$dispatch('open-confirm-modal', {{ Js::from([
                                        'title' => 'Hapus Artikel Edukasi',
                                        'message' => 'Hapus artikel “' . $item->judul . '”? Feedback yang terkait juga akan terhapus. Tindakan ini tidak dapat dibatalkan.',
                                        'action' => route('admin.artikel.destroy', $item),
                                        'method' => 'DELETE',
                                        'confirmText' => 'Ya, Hapus Artikel',
                                        'confirmVariant' => 'danger',
                                    ]) }})" class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-medium text-safety-rose hover:bg-red-100">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-neutral-600">Belum ada artikel yang sesuai dengan pencarian atau filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($artikelList->hasPages())
            <div class="border-t border-neutral-200/70 bg-surface px-6 py-4">{{ $artikelList->links() }}</div>
        @endif
    </div>
</div>
<x-confirm-modal />
@endsection
