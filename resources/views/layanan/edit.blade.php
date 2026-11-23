@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6 py-10 space-y-8">

    <x-page-header
        title="Ubah Layanan Bantuan"
        :description="'Perbarui informasi layanan untuk ' . $instansi->nama_instansi . '.'"
        :back="route('instansi.layanan.index', $instansi)"
        back-label="Kembali ke daftar layanan"
    />

    <x-card>
        <form method="POST" action="{{ route('instansi.layanan.update', [$instansi, $layanan]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-form-group name="nama_layanan" label="Nama Layanan" required>
                <x-input
                    name="nama_layanan"
                    :value="old('nama_layanan', $layanan->nama_layanan)"
                    placeholder="mis. Konseling Psikologis, Bantuan Hukum…"
                    maxlength="150"
                    required
                />
            </x-form-group>

            <x-form-group name="deskripsi" label="Deskripsi">
                <x-textarea
                    name="deskripsi"
                    rows="4"
                    placeholder="Jelaskan layanan ini secara singkat (opsional)…"
                >{{ old('deskripsi', $layanan->deskripsi) }}</x-textarea>
            </x-form-group>

            <div class="flex justify-end gap-3 pt-2">
                <x-button :href="route('instansi.layanan.index', $instansi)" variant="secondary">Batal</x-button>
                <x-button type="submit" variant="primary">Perbarui Layanan</x-button>
            </div>
        </form>
    </x-card>

</div>
@endsection
