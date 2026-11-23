@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6 py-10 space-y-8">

    <x-page-header
        title="Tambah Kontak Bantuan"
        :description="'Tambahkan kontak baru untuk ' . $instansi->nama_instansi . '.'"
        :back="route('instansi.kontak.index', $instansi)"
        back-label="Kembali ke daftar kontak"
    />

    <x-card>
        <form method="POST" action="{{ route('instansi.kontak.store', $instansi) }}" class="space-y-6">
            @csrf

            <x-form-group name="jenis_kontak" label="Jenis Kontak" required>
                <select
                    id="jenis_kontak"
                    name="jenis_kontak"
                    class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset ring-line-strong focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900"
                    required
                >
                    <option value="">— Pilih jenis kontak —</option>
                    @foreach ($jenisKontak as $jenis)
                        <option value="{{ $jenis }}" @selected(old('jenis_kontak') === $jenis)>
                            {{ ucfirst($jenis) }}
                        </option>
                    @endforeach
                </select>
            </x-form-group>

            <x-form-group name="nilai_kontak" label="Nilai Kontak" required>
                <x-input
                    name="nilai_kontak"
                    :value="old('nilai_kontak')"
                    placeholder="mis. 021-5555-1234, admin@instansi.org…"
                    maxlength="150"
                    required
                />
            </x-form-group>

            <x-form-group name="keterangan" label="Keterangan">
                <x-input
                    name="keterangan"
                    :value="old('keterangan')"
                    placeholder="mis. Jam operasional, khusus darurat… (opsional)"
                    maxlength="200"
                />
            </x-form-group>

            <div class="flex justify-end gap-3 pt-2">
                <x-button :href="route('instansi.kontak.index', $instansi)" variant="secondary">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan Kontak</x-button>
            </div>
        </form>
    </x-card>

</div>
@endsection
