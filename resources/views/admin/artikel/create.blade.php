@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-700">PBI-10 · Niky</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Tambah Artikel Edukasi</h1>
            <p class="mt-1 text-sm text-slate-500">Tulis konten edukasi KBG yang aman, informatif, dan tidak menghakimi.</p>
        </div>
        <x-button href="{{ route('admin.artikel.index') }}" variant="secondary" size="sm">
            &larr; Kembali ke Daftar
        </x-button>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.artikel.store') }}" class="space-y-6">
            @csrf

            @include('admin.artikel._form')

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <x-button href="{{ route('admin.artikel.index') }}" variant="secondary" size="md">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" :disabled="empty($kategoriOptions)">
                    Simpan Artikel
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
