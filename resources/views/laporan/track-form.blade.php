@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="mx-auto max-w-xl">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-slate-900">Lacak Status Laporan</h1>
            <p class="mt-1 text-sm text-slate-600">Masukkan kode pelacakan yang Anda terima saat mengirim laporan.</p>
        </div>

        <x-card class="mt-6">
            <form method="GET" action="{{ route('lacak.hasil') }}" class="space-y-4">
                <x-input name="kode_laporan" label="Kode Pelacakan" placeholder="RA-2026-XXXXXX" :error="$errors->first('kode_laporan')" required />
                <x-button type="submit" variant="primary" class="w-full">Cek Status</x-button>
            </form>
        </x-card>

        <p class="mt-4 text-center text-xs text-slate-500">
            Halaman ini hanya menampilkan status terkini dan tidak menunjukkan identitas, kronologi, atau lampiran kasus.
        </p>
    </div>
@endsection
