@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">

    <x-page-header
        title="Manajemen Instansi Bantuan"
        description="Kelola lembaga yang ditampilkan pada direktori bantuan publik. Instansi nonaktif hanya terlihat oleh admin."
    >
        <x-slot:actions>
            <x-button :href="route('instansi.publik')" variant="secondary">Lihat direktori publik</x-button>
            <x-button :href="route('instansi.create')" variant="primary">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Instansi</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Ringkasan --}}
    <dl class="grid grid-cols-3 gap-4">
        @foreach ([['Total instansi', $totalCount], ['Aktif', $aktifCount], ['Nonaktif', $nonaktifCount]] as [$labelRingkas, $jumlah])
            <div class="rounded-xl border border-neutral-200/70 bg-white px-5 py-4">
                <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">{{ $labelRingkas }}</dt>
                <dd class="mt-1 text-2xl font-extrabold tracking-tight text-neutral-900">{{ $jumlah }}</dd>
            </div>
        @endforeach
    </dl>

    {{-- Filter & Pencarian --}}
    <x-card>
        <form method="GET" action="{{ route('instansi.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-12">
            <x-search-input
                class="md:col-span-8"
                :value="$search"
                label="Cari instansi"
                placeholder="Cari nama atau alamat instansi…"
            />

            <div class="md:col-span-2">
                <label for="status" class="sr-only">Filter status</label>
                <select id="status" name="status" class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset ring-line-strong focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected($statusFilter === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($statusFilter === 'nonaktif')>Nonaktif</option>
                </select>
            </div>

            <div class="flex gap-3 md:col-span-2">
                <x-button type="submit" variant="secondary" size="sm" class="flex-1">Filter</x-button>
                @if (filled($search) || filled($statusFilter))
                    <x-button :href="route('instansi.index')" variant="ghost" size="sm">Reset</x-button>
                @endif
            </div>
        </form>
    </x-card>

    {{-- Daftar instansi --}}
    <section aria-labelledby="judul-daftar-instansi" class="overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
        <h2 id="judul-daftar-instansi" class="sr-only">Daftar instansi</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="border-b border-neutral-200 bg-surface text-[11px] uppercase tracking-wider text-neutral-600">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 font-medium">Nama Instansi</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Alamat</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Situs Web</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Status</th>
                        <th scope="col" class="px-6 py-3.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/70">
                    @forelse ($instansis as $instansi)
                        <tr class="transition-colors hover:bg-surface/70">
                            <td class="px-6 py-4">
                                <a href="{{ route('instansi.show', $instansi) }}" class="font-semibold text-neutral-900 hover:text-brand-700">
                                    {{ $instansi->nama_instansi }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-xs leading-relaxed text-neutral-600">{{ $instansi->alamat }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs">
                                @if ($instansi->website)
                                    <a href="{{ $instansi->website }}" target="_blank" rel="noopener noreferrer" class="font-medium text-neutral-800 hover:underline">
                                        {{ parse_url($instansi->website, PHP_URL_HOST) ?? 'Tersedia' }} <span aria-hidden="true">&nearr;</span>
                                        <span class="sr-only">(tab baru)</span>
                                    </a>
                                @else
                                    <span class="text-neutral-600">&mdash;</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <x-badge :variant="$instansi->status === 'aktif' ? 'success' : 'slate'">
                                    {{ $instansi->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                </x-badge>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-4 text-xs font-medium">
                                    <a href="{{ route('instansi.show', $instansi) }}" class="text-neutral-700 hover:text-neutral-900 hover:underline">Detail</a>
                                    <a href="{{ route('instansi.edit', $instansi) }}" class="text-neutral-700 hover:text-neutral-900 hover:underline">Ubah</a>
                                    <button
                                        type="button"
                                        @click="$dispatch('open-confirm-modal', {{ Js::from([
                                            'title' => 'Hapus Instansi Bantuan',
                                            'message' => 'Hapus permanen “' . $instansi->nama_instansi . '” dari daftar admin dan direktori publik? Jika hanya ingin menyembunyikannya, ubah status menjadi Nonaktif.',
                                            'action' => route('instansi.destroy', $instansi),
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
                            <td colspan="5" class="px-6 py-12 text-center text-xs text-neutral-600">
                                Belum ada instansi yang sesuai dengan pencarian atau filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-neutral-200/70 px-6 py-4 text-xs text-neutral-600">
            Menampilkan {{ $instansis->count() }} dari {{ $totalCount }} instansi
        </div>
    </section>

</div>
@endsection
