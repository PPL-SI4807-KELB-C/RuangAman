@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <x-badge :variant="$konselor->statusBadgeVariant()">{{ $konselor->statusLabel() }}</x-badge>
                @if ($konselor->bisaMenerimaJadwal())
                    <x-badge variant="brand">Siap Menerima Jadwal</x-badge>
                @endif
            </div>
            <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-neutral-900">{{ $konselor->nama }}</h1>
            <p class="mt-1 text-xs text-neutral-500">{{ $konselor->spesialisasi }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <x-button href="{{ route('admin.konselor.edit', $konselor) }}" variant="secondary" size="sm">
                Ubah Data
            </x-button>
            @if ($jumlahJadwal > 0)
                <span
                    title="Tidak dapat dihapus karena sudah memiliki slot jadwal konseling. Ubah status menjadi Nonaktif."
                    class="cursor-not-allowed text-xs text-neutral-500 py-1.5 px-2"
                >
                    Hapus Data
                </span>
            @else
                <button
                    type="button"
                    @click="$dispatch('open-confirm-modal', {{ Js::from([
                        'title' => 'Hapus Data Konselor',
                        'message' => 'Apakah Anda yakin ingin menghapus permanen profil konselor “' . $konselor->nama . '”? Profil akun penggunanya tetap ada.',
                        'action' => route('admin.konselor.destroy', $konselor),
                        'method' => 'DELETE',
                        'confirmText' => 'Ya, Hapus Permanen',
                        'confirmVariant' => 'danger',
                    ]) }})"
                    class="text-xs font-medium text-safety-rose hover:underline"
                >
                    Hapus Data
                </button>
            @endif
            <x-button href="{{ route('admin.konselor.index') }}" variant="ghost" size="sm">
                &larr; Kembali
            </x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Profil Profesional -->
        <x-card class="lg:col-span-2">
            <h2 class="text-sm font-semibold text-neutral-900 mb-4">Profil Profesional</h2>
            <dl class="space-y-4 text-xs">
                <div>
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Nama Lengkap &amp; Gelar</dt>
                    <dd class="mt-1 font-medium text-neutral-900">{{ $konselor->nama }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Spesialisasi / Kualifikasi</dt>
                    <dd class="mt-1 text-neutral-800">{{ $konselor->spesialisasi }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Bio Profesional</dt>
                    <dd class="mt-1 whitespace-pre-line text-neutral-700 leading-relaxed">
                        {{ $konselor->bio ?: 'Bio profesional belum diisi.' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Status Ketersediaan</dt>
                    <dd class="mt-1">
                        <x-badge :variant="$konselor->statusBadgeVariant()">{{ $konselor->statusLabel() }}</x-badge>
                    </dd>
                </div>
            </dl>
        </x-card>

        <div class="space-y-6">
            <!-- Informasi Akun -->
            <x-card>
                <h2 class="text-sm font-semibold text-neutral-900 mb-4">Informasi Akun</h2>
                <dl class="space-y-4 text-xs">
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Nama Tampilan</dt>
                        <dd class="mt-1 font-medium text-neutral-900">
                            {{ $konselor->user?->nama_tampilan ?? 'Akun tidak ditemukan' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">ID Akun</dt>
                        <dd class="mt-1 text-neutral-800">#{{ $konselor->user_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Status Akun</dt>
                        <dd class="mt-1">
                            @if ($konselor->user?->status === 'aktif')
                                <x-badge variant="success">Aktif</x-badge>
                            @else
                                <x-badge variant="danger">Nonaktif</x-badge>
                            @endif
                        </dd>
                    </div>
                </dl>
                <p class="mt-4 text-[11px] text-neutral-500">
                    Email akun tidak ditampilkan pada halaman manajemen demi menjaga privasi data pengguna.
                </p>
            </x-card>

            <!-- Ringkasan Jadwal -->
            <x-card>
                <h2 class="text-sm font-semibold text-neutral-900 mb-2">Slot Jadwal Konseling</h2>
                <p class="text-3xl font-extrabold text-neutral-900">{{ $jumlahJadwal }}</p>
                <p class="mt-1 text-[11px] text-neutral-500">
                    @if ($jumlahJadwal > 0)
                        Profil ini sudah dipakai pada jadwal konseling sehingga tidak dapat dihapus permanen.
                    @else
                        Belum ada slot jadwal. Profil masih dapat dihapus permanen bila diperlukan.
                    @endif
                </p>
            </x-card>
        </div>
    </div>
</div>
@endsection