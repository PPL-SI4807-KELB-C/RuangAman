@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">

    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-4">
                <x-badge :variant="$pengumuman->statusBadgeVariant()">{{ $pengumuman->statusLabel() }}</x-badge>
                @unless ($pengumuman->sedangTayang())
                    <x-badge variant="slate">{{ $pengumuman->statusTayangLabel() }}</x-badge>
                @endunless
            </div>
            <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-neutral-900">{{ $pengumuman->judul }}</h1>
            <p class="mt-1.5 text-xs text-neutral-500">
                oleh {{ $pengumuman->pembuat?->nama_tampilan ?? 'Akun tidak ditemukan' }}
            </p>
        </div>
        <div class="flex items-center gap-5">
            <x-button href="{{ route('admin.pengumuman.edit', $pengumuman) }}" variant="primary" size="md" class="shrink-0">
                Ubah Pengumuman
            </x-button>
            <x-button href="{{ route('admin.pengumuman.index') }}" variant="ghost" size="md">
                &larr; Kembali ke Daftar
            </x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <x-card>
                <p class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Isi Pengumuman</p>
                <div class="mt-4 whitespace-pre-line text-sm leading-relaxed text-neutral-700">{{ $pengumuman->isi }}</div>
            </x-card>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <x-card header="Periode Tayang">
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Mulai Tayang</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">{{ $pengumuman->mulai_tayang->translatedFormat('l, d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Akhir Tayang</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">
                            {{ $pengumuman->akhir_tayang ? $pengumuman->akhir_tayang->translatedFormat('l, d F Y') : 'Tanpa batas waktu' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Posisi Saat Ini</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">{{ $pengumuman->statusTayangLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Dibuat</dt>
                        <dd class="mt-1.5 font-medium text-neutral-800">{{ $pengumuman->created_at->translatedFormat('d M Y, H:i') }} WIB</dd>
                    </div>
                </dl>
            </x-card>

            <x-card header="Tautan Publik">
                @if ($pengumuman->sedangTayang())
                    <a href="{{ $tautanPublik }}" class="break-all text-xs font-medium text-neutral-800 hover:text-brand-700 hover:underline">
                        {{ $tautanPublik }}
                    </a>
                    <p class="mt-2 text-[11px] leading-relaxed text-neutral-500">
                        Pengumuman ini tampil pada daftar pengumuman publik.
                    </p>
                @else
                    <p class="text-xs leading-relaxed text-neutral-600">
                        Pengumuman hanya dapat dibuka pengunjung selama periode tayang dan berstatus Aktif.
                    </p>
                @endif
            </x-card>
        </div>
    </div>

</div>
@endsection
