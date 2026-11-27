@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl space-y-6 py-6">
    <div>
        <a href="{{ route('notifikasi.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-brand-700 hover:text-brand-800 hover:underline">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Notifikasi
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-5">
            <div class="flex flex-wrap items-center gap-2">
                <x-badge variant="brand">{{ $notifikasi->labelTipe() }}</x-badge>
                <x-badge variant="success">Sudah Dibaca</x-badge>
            </div>
            <h1 class="mt-3 text-xl font-bold tracking-tight text-slate-900">{{ $notifikasi->judul }}</h1>
            <p class="mt-1 text-xs text-slate-500">
                Diterima
                <time datetime="{{ $notifikasi->created_at?->toIso8601String() }}">{{ $notifikasi->created_at?->translatedFormat('d M Y, H:i') }} WIB</time>
                &bull; {{ $notifikasi->created_at?->diffForHumans() }}
            </p>
        </div>

        <div class="px-6 py-6">
            <p class="whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $notifikasi->pesan }}</p>
        </div>

        <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50/50 px-6 py-4 sm:flex-row sm:justify-end sm:gap-3">
            <form method="POST" action="{{ route('notifikasi.belum-dibaca', $notifikasi) }}">
                @csrf
                @method('PATCH')
                <x-button type="submit" variant="secondary" size="md" class="w-full sm:w-auto">Tandai Belum Dibaca</x-button>
            </form>

            <x-button
                variant="danger"
                size="md"
                @click="$dispatch('open-confirm-modal', {{ Js::from([
                    'title' => 'Hapus Notifikasi',
                    'message' => 'Apakah Anda yakin ingin menghapus notifikasi ini?',
                    'action' => route('notifikasi.destroy', $notifikasi),
                    'method' => 'DELETE',
                    'confirmText' => 'Ya, Hapus',
                    'confirmVariant' => 'danger',
                ]) }})"
            >
                Hapus Notifikasi
            </x-button>
        </div>
    </div>
</div>
@endsection
