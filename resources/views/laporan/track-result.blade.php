@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="mx-auto max-w-xl">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-slate-900">Hasil Pelacakan</h1>
            <p class="mt-1 text-sm text-slate-600">Kode: <span class="font-medium text-slate-900">{{ $kode_laporan }}</span></p>
        </div>

        <x-card class="mt-6 text-center">
            @if ($laporan)
                <p class="text-sm text-slate-500">Status saat ini</p>
                <div class="mt-3">
                    @if ($laporan->statusKasus?->nama_status === 'Pending')
                        <x-badge variant="warning">Pending</x-badge>
                    @elseif ($laporan->statusKasus?->nama_status === 'Proses')
                        <x-badge variant="info">Proses</x-badge>
                    @elseif ($laporan->statusKasus?->nama_status === 'Selesai')
                        <x-badge variant="success">Selesai</x-badge>
                    @elseif ($laporan->statusKasus?->nama_status === 'Ditolak')
                        <x-badge variant="danger">Ditolak</x-badge>
                    @else
                        <x-badge>{{ $laporan->statusKasus?->nama_status ?? '-' }}</x-badge>
                    @endif
                </div>
                <p class="mt-4 text-xs text-slate-500">Terakhir diperbarui: {{ $laporan->updated_at?->translatedFormat('d F Y H:i') ?? '-' }} WIB</p>
            @else
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="mt-3 text-lg font-semibold text-slate-900">Kode tidak ditemukan</h2>
                <p class="mt-1 text-sm text-slate-600">Periksa kembali kode pelacakan yang Anda masukkan.</p>
            @endif
        </x-card>

        <div class="mt-6 flex justify-center">
            <x-button href="{{ route('lacak.form') }}" variant="secondary">Cek Kode Lain</x-button>
        </div>
    </div>
@endsection
