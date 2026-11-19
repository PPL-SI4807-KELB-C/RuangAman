@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl space-y-6 px-4 py-8 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-700">PBI-10 · Niky</p>
            <div class="mt-2 flex flex-wrap items-center gap-2">
                <x-badge :variant="$artikel->statusBadgeVariant()">{{ $artikel->statusLabel() }}</x-badge>
                <x-badge variant="brand">{{ $artikel->kategori?->nama_kategori ?? 'Tanpa kategori' }}</x-badge>
            </div>
            <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $artikel->judul }}</h1>
            <p class="mt-2 text-sm text-slate-500">Ditulis oleh {{ $artikel->penulis?->nama_tampilan ?? 'Pengguna tidak ditemukan' }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-button href="{{ route('artikel.show', $artikel) }}" variant="ghost" size="sm">Pratinjau Artikel</x-button>
            <x-button href="{{ route('admin.artikel.edit', $artikel) }}" variant="secondary" size="sm">Ubah Artikel</x-button>
            <button type="button" @click="$dispatch('open-confirm-modal', {{ Js::from([
                'title' => 'Hapus Artikel Edukasi',
                'message' => 'Hapus artikel \"' . $artikel->judul . '\"? Feedback yang terkait juga akan terhapus. Tindakan ini tidak dapat dibatalkan.',
                'action' => route('admin.artikel.destroy', $artikel),
                'method' => 'DELETE',
                'confirmText' => 'Ya, Hapus Artikel',
                'confirmVariant' => 'danger',
            ]) }})" class="inline-flex items-center justify-center rounded-lg border border-safety-rose bg-white px-3 py-1.5 text-sm font-medium text-safety-rose transition hover:bg-red-50">Hapus</button>
            <x-button href="{{ route('admin.artikel.index') }}" variant="secondary" size="sm">&larr; Kembali</x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <x-card class="lg:col-span-3">
            <article class="whitespace-pre-line text-sm leading-7 text-slate-700 sm:text-base sm:leading-8">{{ $artikel->isi }}</article>
        </x-card>

        <div class="space-y-6">
            <x-card>
                <h2 class="text-base font-semibold text-slate-900">Informasi Publikasi</h2>
                <dl class="mt-4 space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Slug</dt>
                        <dd class="mt-1 break-all font-mono text-xs text-slate-700">{{ $artikel->slug }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Kategori</dt>
                        <dd class="mt-1 text-slate-700">{{ $artikel->kategori?->nama_kategori ?? 'Tanpa kategori' }}</dd>
                        @if ($artikel->kategori?->deskripsi)
                            <dd class="mt-1 text-xs text-slate-500">{{ $artikel->kategori->deskripsi }}</dd>
                        @endif
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Dibuat</dt>
                        <dd class="mt-1 text-slate-700">{{ $artikel->created_at?->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Terakhir Diperbarui</dt>
                        <dd class="mt-1 text-slate-700">{{ $artikel->updated_at?->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                </dl>
            </x-card>
            <x-alert variant="info" title="Privasi penulis">
                Halaman ini hanya menampilkan nama tampilan penulis. Email akun tidak disertakan.
            </x-alert>
        </div>
    </div>
</div>
<x-confirm-modal />
@endsection
