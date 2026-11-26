@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-neutral-900">Manajemen Kategori Artikel</h1>
            <p class="mt-1.5 text-xs text-neutral-600 leading-relaxed max-w-2xl">
                Kelompokkan artikel edukasi berdasarkan tema. Kategori yang masih dipakai artikel dan kategori bawaan tidak dapat dihapus.
            </p>
        </div>
        <x-button href="{{ route('admin.kategori-artikel.create') }}" variant="primary" size="md" class="shrink-0">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Kategori</span>
        </x-button>
    </div>

    {{-- Pencarian --}}
    <x-card>
        <form method="GET" action="{{ route('admin.kategori-artikel.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-12">
            <div class="md:col-span-10">
                <label for="search" class="sr-only">Cari kategori</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama atau deskripsi kategori..."
                    class="block w-full rounded bg-white px-3.5 py-2.5 text-sm text-neutral-900 ring-1 ring-inset ring-line-strong placeholder:text-neutral-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900"
                />
            </div>
            <div class="flex gap-3 md:col-span-2">
                <x-button type="submit" variant="secondary" size="sm" class="flex-1">Cari</x-button>
                @if ($search !== '')
                    <x-button href="{{ route('admin.kategori-artikel.index') }}" variant="ghost" size="sm">Reset</x-button>
                @endif
            </div>
        </form>
    </x-card>

    {{-- Daftar Kategori --}}
    <div class="overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="border-b border-neutral-200 bg-surface text-[11px] uppercase tracking-wider text-neutral-600">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 font-medium">Nama Kategori</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Deskripsi</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Artikel</th>
                        <th scope="col" class="px-6 py-3.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/70">
                    @forelse ($kategoriList as $item)
                        <tr class="transition-colors hover:bg-surface/70">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-neutral-900">{{ $item->nama_kategori }}</span>
                                @if ($item->isDefault())
                                    <x-badge variant="slate" class="ml-2">Bawaan</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-neutral-600">
                                <p class="line-clamp-2 max-w-prose">{{ $item->deskripsi ?: 'Tanpa deskripsi' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs">{{ $item->artikel_count }} artikel</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-4 text-xs font-medium">
                                    <a href="{{ route('admin.kategori-artikel.edit', $item) }}" class="text-neutral-700 hover:text-neutral-900 hover:underline">
                                        Ubah
                                    </a>
                                    @if ($item->isDefault() || $item->artikel_count > 0)
                                        <span class="cursor-not-allowed text-neutral-400" title="{{ $item->isDefault() ? 'Kategori bawaan tidak dapat dihapus' : 'Masih dipakai artikel' }}">Hapus</span>
                                    @else
                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-modal', {{ Js::from([
                                                'title' => 'Hapus Kategori Artikel',
                                                'message' => 'Apakah Anda yakin ingin menghapus kategori “' . $item->nama_kategori . '”?',
                                                'action' => route('admin.kategori-artikel.destroy', $item),
                                                'method' => 'DELETE',
                                                'confirmText' => 'Ya, Hapus',
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
                            <td colspan="4" class="px-6 py-12 text-center text-xs text-neutral-600">
                                Belum ada kategori yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kategoriList->hasPages())
            <div class="border-t border-neutral-200/70 px-6 py-4">
                {{ $kategoriList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
