@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6 py-10 space-y-8">

    <x-page-header
        :title="$layanan->nama_layanan"
        eyebrow="Detail layanan bantuan"
        :back="route('instansi.layanan.index', $instansi)"
        back-label="Kembali ke daftar layanan"
    >
        <x-slot:actions>
            <x-button :href="route('instansi.layanan.edit', [$instansi, $layanan])" variant="primary">Ubah Layanan</x-button>
            <x-button
                variant="outline-danger"
                @click="$dispatch('open-confirm-modal', {{ Js::from([
                    'title'          => 'Hapus Layanan',
                    'message'        => 'Hapus layanan "' . $layanan->nama_layanan . '" secara permanen?',
                    'action'         => route('instansi.layanan.destroy', [$instansi, $layanan]),
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
                <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Nama Layanan</dt>
                <dd class="mt-1.5 text-sm font-medium text-neutral-900">{{ $layanan->nama_layanan }}</dd>
            </div>
            <div class="py-5">
                <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Deskripsi</dt>
                <dd class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-neutral-800">
                    {{ $layanan->deskripsi ?? '—' }}
                </dd>
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
                    {{ $layanan->created_at?->translatedFormat('d F Y') ?? '—' }}
                </dd>
            </div>
        </dl>
    </x-card>

</div>
@endsection
