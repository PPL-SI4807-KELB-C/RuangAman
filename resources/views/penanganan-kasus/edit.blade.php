@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('admin.penanganan-kasus.index') }}" class="inline-flex items-center gap-1.5 text-xs text-neutral-600 transition-colors hover:text-neutral-900">
                <span aria-hidden="true">&larr;</span>
                <span>Kembali ke daftar</span>
            </a>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-neutral-900">Ubah Penugasan Kasus</h1>
            <p class="mt-1.5 text-xs text-neutral-500 leading-relaxed">
                Penugasan untuk Laporan #{{ $penanganan->laporanKasus?->kode_laporan }}
            </p>
        </div>
        <div>
            <x-button href="{{ route('admin.penanganan-kasus.show', $penanganan) }}" variant="secondary" size="sm">
                Lihat Detail
            </x-button>
        </div>
    </div>

    <x-card class="lg:max-w-3xl">
        <form method="POST" action="{{ route('admin.penanganan-kasus.update', $penanganan) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('penanganan-kasus._form')

            <div class="flex flex-wrap items-center gap-5 border-t border-neutral-300/60 pt-5">
                <x-button type="submit" variant="primary" size="md">
                    Perbarui Penugasan
                </x-button>
                <x-button href="{{ route('admin.penanganan-kasus.show', $penanganan) }}" variant="ghost" size="md">
                    Batal
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection