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
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Alamat</dt>
                    <dd class="mt-1.5 text-sm leading-relaxed text-neutral-800">{{ $instansi->alamat }}</dd>
                </div>
                <div class="py-5">
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Deskripsi</dt>
                    <dd class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-neutral-800">{{ $instansi->deskripsi }}</dd>
                </div>
                <div class="pt-5">
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Situs Web</dt>
                    <dd class="mt-1.5 text-sm">
                        @if ($instansi->website)
                            <a href="{{ $instansi->website }}" target="_blank" rel="noopener noreferrer" class="break-all font-medium text-neutral-800 hover:text-brand-700 hover:underline">
                                {{ $instansi->website }} <span aria-hidden="true">&nearr;</span>
                                <span class="sr-only">(tab baru)</span>
                            </a>
                        @else
                            <span class="text-neutral-600">Belum tersedia</span>
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
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">ID Instansi</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">INS-{{ str_pad($instansi->instansi_id, 3, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Dibuat</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">{{ $instansi->created_at?->translatedFormat('d F Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-600">Terakhir Diperbarui</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">{{ $instansi->updated_at ? $instansi->updated_at->translatedFormat('d F Y, H.i') . ' WIB' : '—' }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>
    </div>

    {{-- PBI-08: Layanan Bantuan --}}
    <x-card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-neutral-900">Layanan Bantuan</h3>
                    <p class="text-xs text-neutral-600">Jenis pendampingan dan dukungan yang disediakan oleh instansi ini.</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-button :href="route('instansi.layanan.index', $instansi)" variant="secondary" size="sm">
                        Kelola Layanan
                    </x-button>
                    <x-button :href="route('instansi.layanan.create', $instansi)" variant="primary" size="sm">
                        + Tambah Layanan
                    </x-button>
                </div>
            </div>
        </x-slot:header>

        @if ($instansi->layananBantuan->isNotEmpty())
            <div class="divide-y divide-neutral-200/70">
                @foreach ($instansi->layananBantuan as $layanan)
                    <div class="flex items-start justify-between py-3.5 first:pt-0 last:pb-0">
                        <div class="space-y-1">
                            <h4 class="text-sm font-semibold text-neutral-900">{{ $layanan->nama_layanan }}</h4>
                            @if ($layanan->deskripsi)
                                <p class="text-xs text-neutral-600 leading-relaxed">{{ $layanan->deskripsi }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 text-xs font-medium ml-4 shrink-0">
                            <a href="{{ route('instansi.layanan.edit', [$instansi, $layanan]) }}" class="text-neutral-700 hover:text-neutral-900 hover:underline">Ubah</a>
                            <button
                                type="button"
                                @click="$dispatch('open-confirm-modal', {{ Js::from([
                                    'title'          => 'Hapus Layanan',
                                    'message'        => 'Hapus layanan "' . $layanan->nama_layanan . '" secara permanen?',
                                    'action'         => route('instansi.layanan.destroy', [$instansi, $layanan]),
                                    'method'         => 'DELETE',
                                    'confirmText'    => 'Ya, Hapus',
                                    'confirmVariant' => 'danger',
                                ]) }})"
                                class="text-safety-rose hover:underline"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-neutral-600 py-2">Belum ada layanan bantuan yang ditambahkan untuk instansi ini.</p>
        @endif
    </x-card>

    {{-- PBI-08: Kontak Bantuan --}}
    <x-card>
        <x-slot:header>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-neutral-900">Kontak Bantuan</h3>
                    <p class="text-xs text-neutral-600">Saluran komunikasi yang dapat dihubungi penyintas atau masyarakat.</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-button :href="route('instansi.kontak.index', $instansi)" variant="secondary" size="sm">
                        Kelola Kontak
                    </x-button>
                    <x-button :href="route('instansi.kontak.create', $instansi)" variant="primary" size="sm">
                        + Tambah Kontak
                    </x-button>
                </div>
            </div>
        </x-slot:header>

        @if ($instansi->kontakBantuan->isNotEmpty())
            <div class="divide-y divide-neutral-200/70">
                @foreach ($instansi->kontakBantuan as $kontak)
                    <div class="flex items-start justify-between py-3.5 first:pt-0 last:pb-0">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <x-badge variant="slate">{{ $kontak->label_jenis_kontak }}</x-badge>
                                <span class="text-sm font-semibold text-neutral-900">{{ $kontak->nilai_kontak }}</span>
                            </div>
                            @if ($kontak->keterangan)
                                <p class="text-xs text-neutral-600 leading-relaxed">{{ $kontak->keterangan }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 text-xs font-medium ml-4 shrink-0">
                            <a href="{{ route('instansi.kontak.edit', [$instansi, $kontak]) }}" class="text-neutral-700 hover:text-neutral-900 hover:underline">Ubah</a>
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
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-neutral-600 py-2">Belum ada kontak bantuan yang ditambahkan untuk instansi ini.</p>
        @endif
    </x-card>

</div>
@endsection
