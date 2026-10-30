@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="mx-auto max-w-xl text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-700">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h1 class="mt-6 text-2xl font-bold text-slate-900">Laporan Berhasil Dikirim</h1>
        <p class="mt-2 text-slate-600">Simpan kode pelacakan ini untuk memantau status penanganan tanpa perlu login.</p>

        <div class="mt-6 rounded-xl border-2 border-dashed border-brand-300 bg-brand-50 p-6">
            <p class="text-sm font-medium text-brand-800">Kode Pelacakan</p>
            <p class="mt-2 text-3xl font-extrabold tracking-widest text-brand-900" id="kode-laporan">{{ $kode_laporan }}</p>
            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('kode-laporan').innerText).then(() => alert('Kode disalin'))" class="mt-4 text-sm font-medium text-brand-700 hover:underline">
                Salin kode
            </button>
        </div>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
            <x-button href="{{ route('lacak.form') }}" variant="primary">Lacak Status</x-button>
            <x-button href="{{ url('/') }}" variant="secondary">Kembali ke Beranda</x-button>
        </div>
    </div>
@endsection
