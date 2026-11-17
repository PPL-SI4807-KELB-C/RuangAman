@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tambah Data Konselor / Psikolog</h1>
            <p class="mt-1 text-sm text-slate-500">Lengkapi biodata dan status ketersediaan tenaga pendamping profesional.</p>
        </div>
        <div>
            <x-button href="{{ route('admin.konselor.index') }}" variant="secondary" size="sm">
                &larr; Kembali ke Daftar
            </x-button>
        </div>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.konselor.store') }}" class="space-y-6">
            @csrf

            @include('admin.konselor._form')

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <x-button href="{{ route('admin.konselor.index') }}" variant="secondary" size="md">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" :disabled="empty($calonPenggunaOptions)">
                    Simpan Data Konselor
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
