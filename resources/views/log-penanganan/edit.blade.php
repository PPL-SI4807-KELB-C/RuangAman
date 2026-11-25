@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6 py-10 space-y-6">
    <x-page-header
        eyebrow="PBI-06 • Log Penanganan Kasus"
        title="Ubah Catatan Aktivitas Manual"
        description="Perbarui isi catatan manual selama penanganan kasus masih berstatus aktif."
        :back="route('log-penanganan.show', $log)"
        backLabel="Kembali ke Detail Log"
    />

    <x-card>
        <form method="POST" action="{{ route('log-penanganan.update', $log) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('log-penanganan._form')

            <div class="flex flex-col-reverse gap-3 border-t border-neutral-200/70 pt-5 sm:flex-row sm:justify-end">
                <x-button href="{{ route('log-penanganan.show', $log) }}" variant="secondary" size="md">
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
