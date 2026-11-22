@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">

    <div>
        <a href="{{ route('admin.kategori-artikel.index') }}" class="inline-flex items-center gap-1.5 text-xs text-neutral-600 transition-colors hover:text-neutral-900">
            <span aria-hidden="true">&larr;</span>
            <span>Kembali ke daftar</span>
        </a>
        <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-neutral-900">Ubah Kategori: {{ $kategori->nama_kategori }}</h1>
        <p class="mt-1.5 text-xs text-neutral-600 leading-relaxed">
            Perubahan nama langsung terlihat pada artikel yang memakai kategori ini.
        </p>
    </div>

    <x-card class="lg:max-w-3xl">
        <form method="POST" action="{{ route('admin.kategori-artikel.update', $kategori) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('admin.kategori-artikel._form')

            <div class="flex flex-wrap items-center gap-5 border-t border-neutral-300/60 pt-5">
                <x-button type="submit" variant="primary" size="md">Simpan Perubahan</x-button>
                <x-button href="{{ route('admin.kategori-artikel.index') }}" variant="ghost" size="md">Batal</x-button>
            </div>
        </form>
    </x-card>

</div>
@endsection
