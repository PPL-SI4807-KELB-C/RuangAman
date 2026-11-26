@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6 py-10 space-y-6">
    <x-page-header
        eyebrow="PBI-06 • Log Penanganan Kasus"
        title="Tambah Catatan Aktivitas Manual"
        description="Catat perkembangan penanganan kasus secara manual untuk dokumentasi pendampingan yang akuntabel."
        :back="route('log-penanganan.index')"
        backLabel="Kembali ke Daftar Log"
    />

    <x-card>
        <form method="POST" action="{{ route('log-penanganan.store') }}" class="space-y-6">
            @csrf

            @include('log-penanganan._form')

            <div class="flex flex-col-reverse gap-3 border-t border-neutral-200/70 pt-5 sm:flex-row sm:justify-end">
                <x-button href="{{ route('log-penanganan.index') }}" variant="secondary" size="md">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md">
                    Simpan Catatan Manual
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
