@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">

    <x-page-header
        :title="$instansi->nama_instansi"
        eyebrow="Detail instansi bantuan"
        :back="route('instansi.index')"
        back-label="Kembali ke daftar"
    >
        <x-slot:actions>
            <x-button :href="route('instansi.edit', $instansi)" variant="primary">Ubah Instansi</x-button>
            <x-button
                variant="outline-danger"
                @click="$dispatch('open-confirm-modal', {{ Js::from([
                    'title' => 'Hapus Instansi Bantuan',
                    'message' => 'Hapus permanen “' . $instansi->nama_instansi . '” dari daftar admin dan direktori publik? Jika hanya ingin menyembunyikannya, ubah status menjadi Nonaktif.',
                    'action' => route('instansi.destroy', $instansi),
                    'method' => 'DELETE',
                    'confirmText' => 'Ya, Hapus Permanen',
                    'confirmVariant' => 'danger',
                ]) }})"
            >
                Hapus
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <x-card class="lg:col-span-8">
            <dl class="divide-y divide-neutral-300/60">
                <div class="pb-5">
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Alamat</dt>
                    <dd class="mt-1.5 text-sm leading-relaxed text-neutral-800">{{ $instansi->alamat }}</dd>
                </div>
                <div class="py-5">
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Deskripsi</dt>
                    <dd class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-neutral-800">{{ $instansi->deskripsi }}</dd>
                </div>
                <div class="pt-5">
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Situs Web</dt>
                    <dd class="mt-1.5 text-sm">
                        @if ($instansi->website)
                            <a href="{{ $instansi->website }}" target="_blank" rel="noopener noreferrer" class="break-all font-medium text-neutral-800 hover:text-brand-700 hover:underline">
                                {{ $instansi->website }} <span aria-hidden="true">&nearr;</span>
                                <span class="sr-only">(tab baru)</span>
                            </a>
                        @else
                            <span class="text-neutral-500">Belum tersedia</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </x-card>

        <div class="space-y-6 lg:col-span-4">
            <x-card header="Status">
                <x-badge :variant="$instansi->status === 'aktif' ? 'success' : 'slate'">
                    {{ $instansi->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                </x-badge>
                <p class="mt-2 text-xs leading-relaxed text-neutral-600">
                    @if ($instansi->status === 'aktif')
                        Tampil di direktori publik dan dapat dilihat tanpa login.
                    @else
                        Disembunyikan dari direktori publik; hanya terlihat oleh admin.
                    @endif
                </p>
            </x-card>

            <x-card header="Riwayat">
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">ID Instansi</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">INS-{{ str_pad($instansi->instansi_id, 3, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Dibuat</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">{{ $instansi->created_at?->translatedFormat('d F Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Terakhir Diperbarui</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">{{ $instansi->updated_at ? $instansi->updated_at->translatedFormat('d F Y, H.i') . ' WIB' : '—' }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>

</div>
@endsection
