@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">

    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-4">
                <x-badge :variant="$halaman->statusBadgeVariant()">{{ $halaman->statusLabel() }}</x-badge>
                <span class="text-[11px] text-neutral-500">/halaman/{{ $halaman->slug }}</span>
            </div>
            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-neutral-900">{{ $halaman->judul }}</h1>
        </div>
        <div class="flex items-center gap-5">
            <x-button href="{{ route('admin.halaman-informasi.edit', $halaman) }}" variant="primary" size="md" class="shrink-0">
                Ubah Halaman
            </x-button>
            <x-button href="{{ route('admin.halaman-informasi.index') }}" variant="ghost" size="md">
                &larr; Kembali ke Daftar
            </x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <x-card>
                <p class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Isi Halaman</p>
                <div class="mt-4 whitespace-pre-line text-sm leading-relaxed text-neutral-700">{{ $halaman->isi }}</div>
            </x-card>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <x-card header="Informasi Publikasi">
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Status</dt>
                        <dd class="mt-1.5">
                            <x-badge :variant="$halaman->statusBadgeVariant()">{{ $halaman->statusLabel() }}</x-badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Dibuat</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">{{ $halaman->created_at->translatedFormat('d M Y, H:i') }} WIB</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Terakhir Diubah</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">{{ $halaman->updated_at->translatedFormat('d M Y, H:i') }} WIB</dd>
                    </div>
                </dl>
            </x-card>

            <x-card header="Tautan Publik">
                @if ($halaman->isPublished())
                    <a href="{{ $tautanPublik }}" class="break-all text-xs font-medium text-neutral-800 hover:text-brand-700 hover:underline">
                        {{ $tautanPublik }}
                    </a>
                    <p class="mt-2 text-[11px] leading-relaxed text-neutral-500">
                        Halaman ini dapat dibuka pengunjung tanpa login.
                    </p>
                @else
                    <p class="text-xs leading-relaxed text-neutral-600">
                        Halaman berstatus draft. Ubah status publikasi menjadi Published agar tautan ini dapat diakses publik.
                    </p>
                @endif
            </x-card>
        </div>
    </div>

</div>
@endsection
