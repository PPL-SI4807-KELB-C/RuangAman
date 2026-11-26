@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6 py-10 space-y-8">

    <x-page-header
        :title="$kontak->nilai_kontak"
        eyebrow="Detail kontak bantuan"
        :back="route('instansi.kontak.index', $instansi)"
        back-label="Kembali ke daftar kontak"
    >
        <x-slot:actions>
            <x-button :href="route('instansi.kontak.edit', [$instansi, $kontak])" variant="primary">Ubah Kontak</x-button>
            <x-button
                variant="outline-danger"
                @click="$dispatch('open-confirm-modal', {{ Js::from([
                    'title'          => 'Hapus Kontak',
                    'message'        => 'Hapus kontak "' . $kontak->nilai_kontak . '" secara permanen?',
                    'action'         => route('instansi.kontak.destroy', [$instansi, $kontak]),
                    'method'         => 'DELETE',
                    'confirmText'    => 'Ya, Hapus',
                    'confirmVariant' => 'danger',
                ]) }})"
            >
                Hapus
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <dl class="divide-y divide-neutral-300/60">
            <div class="pb-5">
                <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Jenis Kontak</dt>
                <dd class="mt-1.5">
                    <x-badge variant="slate">{{ $kontak->label_jenis_kontak }}</x-badge>
                </dd>
            </div>
            <div class="py-5">
                <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Nilai Kontak</dt>
                <dd class="mt-1.5 text-sm font-medium text-neutral-900">{{ $kontak->nilai_kontak }}</dd>
            </div>
            <div class="py-5">
                <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Keterangan</dt>
                <dd class="mt-1.5 text-sm text-neutral-800">{{ $kontak->keterangan ?? '—' }}</dd>
            </div>
            <div class="py-5">
                <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Instansi</dt>
                <dd class="mt-1.5 text-sm text-neutral-800">
                    <a href="{{ route('instansi.show', $instansi) }}"
                       class="font-medium text-neutral-800 hover:text-brand-700 hover:underline">
                        {{ $instansi->nama_instansi }}
                    </a>
                </dd>
            </div>
            <div class="pt-5">
                <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Dibuat</dt>
                <dd class="mt-1.5 text-sm text-neutral-800">
                    {{ $kontak->created_at?->translatedFormat('d F Y') ?? '—' }}
                </dd>
            </div>
        </dl>
    </x-card>

</div>
@endsection
