@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">

    <x-page-header
        title="Kontak Bantuan"
        :description="'Kelola kontak yang dapat dihubungi di ' . $instansi->nama_instansi . '.'"
        :back="route('instansi.show', $instansi)"
        back-label="Kembali ke detail instansi"
    >
        <x-slot:actions>
            <x-button :href="route('instansi.kontak.create', $instansi)" variant="primary">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Kontak</span>
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <section aria-labelledby="judul-daftar-kontak" class="overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
        <h2 id="judul-daftar-kontak" class="sr-only">Daftar kontak bantuan</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="border-b border-neutral-200 bg-surface text-[11px] uppercase tracking-wider text-neutral-600">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 font-medium">Jenis</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Nilai Kontak</th>
                        <th scope="col" class="px-6 py-3.5 font-medium">Keterangan</th>
                        <th scope="col" class="px-6 py-3.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/70">
                    @forelse ($kontaks as $kontak)
                        <tr class="transition-colors hover:bg-surface/70">
                            <td class="whitespace-nowrap px-6 py-4">
                                <x-badge variant="slate">{{ $kontak->label_jenis_kontak }}</x-badge>
                            </td>
                            <td class="px-6 py-4 font-medium text-neutral-900">{{ $kontak->nilai_kontak }}</td>
                            <td class="px-6 py-4 text-xs leading-relaxed text-neutral-600">
                                {{ $kontak->keterangan ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-4 text-xs font-medium">
                                    <a href="{{ route('instansi.kontak.edit', [$instansi, $kontak]) }}"
                                       class="text-neutral-700 hover:text-neutral-900 hover:underline">Ubah</a>
                                    <button
                                        type="button"
                                        @click="$dispatch('open-confirm-modal', {{ Js::from([
                                            'title'          => 'Hapus Kontak',
                                            'message'        => 'Hapus kontak "' . $kontak->nilai_kontak . '" secara permanen?',
                                            'action'         => route('instansi.kontak.destroy', [$instansi, $kontak]),
                                            'method'         => 'DELETE',
                                            'confirmText'    => 'Ya, Hapus',
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
                            <td colspan="4" class="px-6 py-12 text-center text-xs text-neutral-600">
                                Belum ada kontak yang ditambahkan untuk instansi ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-neutral-200/70 px-6 py-4 text-xs text-neutral-600">
            {{ $kontaks->count() }} kontak terdaftar
        </div>
    </section>

</div>
@endsection
