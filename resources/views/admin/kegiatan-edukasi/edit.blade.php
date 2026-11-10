@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Ubah Kegiatan Edukasi</h1>
            <p class="mt-1 text-sm text-slate-500">Perbarui rincian, jadwal, kuota, atau status tayang kegiatan.</p>
        </div>
        <div class="flex gap-2">
            <x-button href="{{ route('admin.kegiatan-edukasi.show', $kegiatan) }}" variant="secondary" size="sm">
                Lihat Detail
            </x-button>
            <x-button href="{{ route('admin.kegiatan-edukasi.index') }}" variant="ghost" size="sm">
                &larr; Kembali
            </x-button>
        </div>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.kegiatan-edukasi.update', $kegiatan) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('admin.kegiatan-edukasi._form')

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <x-button href="{{ route('admin.kegiatan-edukasi.show', $kegiatan) }}" variant="secondary" size="md">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection

