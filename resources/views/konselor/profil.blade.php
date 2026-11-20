@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <x-badge :variant="$konselor->statusBadgeVariant()">{{ $konselor->statusLabel() }}</x-badge>
                <x-badge variant="brand">Role Konselor</x-badge>
            </div>
            <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-neutral-900">Profil Konselor Saya</h1>
            <p class="mt-1.5 text-xs text-neutral-600 leading-relaxed">
                Perbarui biodata profesional Anda. Status ketersediaan dan akun pengguna dikelola oleh Admin Kasus.
            </p>
        </div>
    </div>

    <x-card class="lg:max-w-3xl">
        <form method="POST" action="{{ route('konselor.profil.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <x-input
                    name="nama"
                    label="Nama Lengkap & Gelar"
                    :value="$konselor->nama"
                    placeholder="Contoh: Rina Kusuma, M.Psi."
                    :error="$errors->first('nama')"
                    required
                />

                <x-input
                    name="spesialisasi"
                    label="Spesialisasi / Kualifikasi"
                    :value="$konselor->spesialisasi"
                    placeholder="Contoh: Psikologi Klinis Dewasa"
                    :error="$errors->first('spesialisasi')"
                    required
                />
            </div>

            <x-textarea
                name="bio"
                label="Bio Profesional"
                :value="$konselor->bio"
                rows="4"
                placeholder="Ringkasan latar belakang profesional, pendekatan konseling, dan area pendampingan..."
                helper="Bio ditampilkan pada direktori konselor untuk membantu pelapor memilih pendamping."
                :error="$errors->first('bio')"
            />

            <!-- Informasi Readonly -->
            <div class="rounded bg-white p-4 ring-1 ring-inset ring-neutral-300/70">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-neutral-600">Status Ketersediaan Saat Ini</p>
                <div class="mt-1 flex items-center gap-2">
                    <x-badge :variant="$konselor->statusBadgeVariant()">{{ $konselor->statusLabel() }}</x-badge>
                    <span class="text-[11px] text-neutral-600">
                        Status hanya dapat diubah oleh Admin Kasus melalui menu Data Konselor.
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-5 border-t border-neutral-300/60 pt-5">
                <x-button type="submit" variant="primary" size="md">
                    Simpan Perubahan Profil
                </x-button>
                <x-button href="{{ url('/') }}" variant="ghost" size="md">
                    Batal
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection