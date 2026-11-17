@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('admin.penanganan-kasus.index') }}" class="hover:text-brand-600">Penugasan Kasus</a>
                <span>/</span>
                <span class="text-slate-700">Ubah Penugasan</span>
            </div>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Ubah Penugasan Kasus</h1>
            <p class="text-xs text-slate-500 mt-0.5">Penugasan untuk Laporan #{{ $penanganan->laporanKasus?->kode_laporan }}</p>
        </div>
        <x-button href="{{ route('admin.penanganan-kasus.show', $penanganan) }}" variant="secondary" size="sm">
            Kembali ke Detail
        </x-button>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.penanganan-kasus.update', $penanganan) }}">
            @csrf
            @method('PUT')

            @include('penanganan-kasus._form')

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <x-button href="{{ route('admin.penanganan-kasus.show', $penanganan) }}" variant="secondary" size="md">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md">
                    Perbarui Penugasan
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection

