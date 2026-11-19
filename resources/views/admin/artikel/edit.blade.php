@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-700">PBI-10 · Niky</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Ubah Artikel Edukasi</h1>
            <p class="mt-1 text-sm text-slate-500">Perbarui judul, kategori, isi, atau status publikasi artikel.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-button href="{{ route('admin.artikel.show', $artikel) }}" variant="ghost" size="sm">Detail</x-button>
            <x-button href="{{ route('admin.artikel.index') }}" variant="secondary" size="sm">&larr; Daftar</x-button>
        </div>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.artikel.update', $artikel) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('admin.artikel._form')

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <x-button href="{{ route('admin.artikel.show', $artikel) }}" variant="secondary" size="md">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" :disabled="empty($kategoriOptions)">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
