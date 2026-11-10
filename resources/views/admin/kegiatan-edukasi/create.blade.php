@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tambah Kegiatan Edukasi</h1>
            <p class="mt-1 text-sm text-slate-500">Buat jadwal webinar atau lokakarya edukasi baru untuk pengguna RuangAman.</p>
        </div>
        <div>
            <x-button href="{{ route('admin.kegiatan-edukasi.index') }}" variant="secondary" size="sm">
                &larr; Kembali ke Daftar
            </x-button>
        </div>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.kegiatan-edukasi.store') }}" class="space-y-6">
            @csrf

            @include('admin.kegiatan-edukasi._form')

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <x-button href="{{ route('admin.kegiatan-edukasi.index') }}" variant="secondary" size="md">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md">
                    Simpan Kegiatan
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection

