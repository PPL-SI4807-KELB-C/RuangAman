@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('admin.penanganan-kasus.index') }}" class="hover:text-brand-600">Penugasan Kasus</a>
                <span>/</span>
                <span class="text-slate-700">Tugaskan Kasus</span>
            </div>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Penugasan Kasus Baru</h1>
        </div>
        <x-button href="{{ route('admin.penanganan-kasus.index') }}" variant="secondary" size="sm">
            Kembali
        </x-button>
    </div>

    @if ($laporanList->isEmpty())
        <x-alert variant="info">
            <div class="font-medium">Tidak ada laporan yang menunggu penugasan.</div>
            <div class="mt-1 text-xs">Semua laporan kasus saat ini telah memiliki petugas pendamping atau belum ada laporan baru yang masuk.</div>
        </x-alert>
    @endif

    <x-card>
        <form method="POST" action="{{ route('admin.penanganan-kasus.store') }}">
            @csrf

            @include('penanganan-kasus._form')

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <x-button href="{{ route('admin.penanganan-kasus.index') }}" variant="secondary" size="md">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" :disabled="$laporanList->isEmpty()">
                    Simpan Penugasan
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection

