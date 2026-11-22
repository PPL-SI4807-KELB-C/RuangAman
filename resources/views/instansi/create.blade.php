@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">

    <x-page-header
        title="Tambah Instansi Bantuan"
        description="Lengkapi profil lembaga yang akan ditampilkan pada direktori bantuan publik. Kolom bertanda * wajib diisi."
        :back="route('instansi.index')"
        back-label="Kembali ke daftar"
    />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        <x-card class="lg:col-span-8">
            <form method="POST" action="{{ route('instansi.store') }}" class="space-y-6">
                @csrf

                @include('instansi._form', ['instansi' => new \App\Models\InstansiBantuan()])

                <div class="flex flex-wrap items-center gap-5 border-t border-neutral-300/60 pt-5">
                    <x-button type="submit" variant="primary">Simpan Instansi</x-button>
                    <x-button :href="route('instansi.index')" variant="ghost">Batal</x-button>
                </div>
            </form>
        </x-card>

        @include('instansi._sidebar')
    </div>

</div>
@endsection
