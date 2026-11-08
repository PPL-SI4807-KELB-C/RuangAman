@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('admin.status-kasus.index') }}" class="hover:text-brand-600">Status Kasus</a>
                <span>/</span>
                <span class="text-slate-700">Ubah</span>
            </div>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Ubah Status Kasus</h1>
        </div>
        <x-button href="{{ route('admin.status-kasus.index') }}" variant="secondary" size="sm">
            Kembali
        </x-button>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.status-kasus.update', $statusKasus) }}">
            @csrf
            @method('PUT')

            @include('status-kasus._form')

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <x-button href="{{ route('admin.status-kasus.index') }}" variant="secondary" size="md">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md">
                    Perbarui Status
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection

