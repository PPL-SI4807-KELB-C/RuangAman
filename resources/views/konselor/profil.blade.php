@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <x-badge :variant="$konselor->statusBadgeVariant()">{{ $konselor->statusLabel() }}</x-badge>
                <x-badge variant="brand">Role Konselor</x-badge>
            </div>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Profil Konselor Saya</h1>
            <p class="mt-1 text-sm text-slate-500">
                Perbarui biodata profesional Anda. Status ketersediaan dan akun pengguna dikelola oleh Admin Kasus.
            </p>
        </div>
    </div>

    <x-card>
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
                helper="Bio ditampilkan pada halaman profil konselor untuk membantu pelapor memilih pendamping."
                :error="$errors->first('bio')"
            />

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <x-button href="{{ url('/') }}" variant="secondary" size="md">
                    Kembali ke Beranda
                </x-button>
                <x-button type="submit" variant="primary" size="md">
                    Simpan Profil
                </x-button>
            </div>
        </form>
    </x-card>

    <x-card>
        <h2 class="text-base font-semibold text-slate-900 mb-4">Informasi Akun & Ketersediaan</h2>
        <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-3">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Nama Tampilan</dt>
                <dd class="mt-1 font-medium text-slate-800">{{ $konselor->user?->nama_tampilan ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Status Ketersediaan</dt>
                <dd class="mt-1">
                    <x-badge :variant="$konselor->statusBadgeVariant()">{{ $konselor->statusLabel() }}</x-badge>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-slate-400">Slot Jadwal Tercatat</dt>
                <dd class="mt-1 font-medium text-slate-800">{{ $jumlahJadwal }}</dd>
            </div>
        </dl>
        <p class="mt-4 text-xs text-slate-500">
            Ingin mengubah status ketersediaan (aktif, cuti, nonaktif)? Ajukan kepada Admin Kasus melalui kanal koordinasi tim.
        </p>
    </x-card>
</div>
@endsection
