@extends('layouts.app')

@section('title', $title)

@section('content')
@php
    $sudahMulai = $kegiatan->sudahDimulai();
    $terdaftarAktif = $pendaftaranSaya && $pendaftaranSaya->status_pendaftaran === \App\Models\PendaftaranKegiatan::STATUS_AKTIF;
    $terdaftarHadir = $pendaftaranSaya && $pendaftaranSaya->status_pendaftaran === \App\Models\PendaftaranKegiatan::STATUS_HADIR;
    $sudahDaftarSelainBatal = $pendaftaranSaya && $pendaftaranSaya->status_pendaftaran !== \App\Models\PendaftaranKegiatan::STATUS_DIBATALKAN;
@endphp

<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <x-button href="{{ route('kegiatan.index') }}" variant="ghost" size="sm">
            &larr; Kembali ke Daftar Kegiatan
        </x-button>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Informasi Utama -->
        <x-card class="lg:col-span-2">
            <div class="flex flex-wrap items-center gap-2">
                <x-badge :variant="$kegiatan->metode === 'online' ? 'info' : 'brand'">
                    {{ $kegiatan->metodeLabel() }}
                </x-badge>
                <x-badge :variant="$kegiatan->statusBadgeVariant()">
                    {{ $kegiatan->statusLabel() }}
                </x-badge>
            </div>

            <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900">{{ $kegiatan->judul }}</h1>
            <p class="mt-1.5 text-sm text-slate-600">
                Narasumber: <span class="font-semibold text-slate-800">{{ $kegiatan->narasumber ?: 'Akan diumumkan' }}</span>
            </p>

            <div class="mt-6 border-t border-slate-100 pt-5">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500">Deskripsi Kegiatan</h2>
                <div class="mt-2.5 text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                    {{ $kegiatan->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 rounded-xl border border-slate-200 bg-slate-50/70 p-4 text-sm sm:grid-cols-2">
                <div>
                    <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">Mulai</div>
                    <div class="mt-1 font-semibold text-slate-800">
                        {{ $kegiatan->tanggal_mulai->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="text-xs text-slate-600">
                        Pukul {{ substr((string) $kegiatan->waktu_mulai, 0, 5) }} WIB
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">Selesai</div>
                    <div class="mt-1 font-semibold text-slate-800">
                        @if ($kegiatan->tanggal_selesai || $kegiatan->waktu_selesai)
                            {{ ($kegiatan->tanggal_selesai ?? $kegiatan->tanggal_mulai)->translatedFormat('l, d F Y') }}
                        @else
                            Menyesuaikan acara
                        @endif
                    </div>
                    @if ($kegiatan->waktu_selesai)
                        <div class="text-xs text-slate-600">
                            Pukul {{ substr((string) $kegiatan->waktu_selesai, 0, 5) }} WIB
                        </div>
                    @endif
                </div>

                <div class="sm:col-span-2 border-t border-slate-200 pt-3">
                    <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">
                        {{ $kegiatan->metode === 'online' ? 'Tautan Akses Webinar' : 'Lokasi Pelaksanaan' }}
                    </div>
                    <div class="mt-1 text-slate-800">
                        @if ($kegiatan->metode === 'online')
                            @if ($terdaftarAktif || $terdaftarHadir || (auth()->check() && auth()->user()->isAdminKasus()))
                                <span class="font-medium text-brand-700 break-all">
                                    {{ $kegiatan->lokasi_atau_tautan ?: 'Tautan akan dibagikan menjelang sesi.' }}
                                </span>
                            @else
                                <span class="italic text-slate-500">
                                    Tautan meeting akan ditampilkan setelah Anda berhasil mendaftar.
                                </span>
                            @endif
                        @else
                            {{ $kegiatan->lokasi_atau_tautan ?: 'Informasi lokasi menyusul.' }}
                        @endif
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Panel Kuota & Aksi Pendaftaran -->
        <div class="space-y-4">
            <x-card>
                <h2 class="text-base font-semibold text-slate-900">Informasi Kuota</h2>

                <div class="mt-4 space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Total Kuota</span>
                        <span class="font-semibold text-slate-900">{{ $kegiatan->kuota }} peserta</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Terdaftar Aktif</span>
                        <span class="font-semibold text-slate-900">{{ $terisiCount }} peserta</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-100 pt-2">
                        <span class="font-medium text-slate-700">Sisa Kuota</span>
                        <span class="font-bold {{ $sisaKuota > 0 ? 'text-emerald-600' : 'text-safety-rose' }}">
                            {{ $sisaKuota }} kursi
                        </span>
                    </div>
                </div>

                <div class="mt-5 border-t border-slate-100 pt-4">
                    @guest
                        <p class="mb-3 text-xs text-slate-500">
                            Silakan masuk dengan akun Pelapor Anda untuk mendaftar pada kegiatan ini.
                        </p>
                        <x-button href="{{ route('login') }}" variant="primary" size="md" class="w-full">
                            Masuk untuk Mendaftar
                        </x-button>
                    @else
                        @if (auth()->user()->isPelapor())
                            @if ($sudahDaftarSelainBatal)
                                <div class="mb-3 rounded-lg border border-slate-200 bg-slate-50 p-3 text-center">
                                    <div class="text-xs text-slate-500">Status Pendaftaran Anda</div>
                                    <div class="mt-1">
                                        <x-badge :variant="$pendaftaranSaya->statusBadgeVariant()">
                                            {{ $pendaftaranSaya->statusLabel() }}
                                        </x-badge>
                                    </div>
                                </div>

                                @if ($pendaftaranSaya->bisaDibatalkan())
                                    <button
                                        type="button"
                                        @click="$dispatch('open-confirm-modal', {{ Js::from([
                                            'title' => 'Batalkan Pendaftaran Kegiatan',
                                            'message' => 'Apakah Anda yakin ingin membatalkan pendaftaran pada kegiatan \"' . $kegiatan->judul . '\"? Kursi kuota Anda akan dilepas untuk peserta lain.',
                                            'action' => route('pendaftaran-kegiatan.cancel', $pendaftaranSaya),
                                            'method' => 'PATCH',
                                            'confirmText' => 'Ya, Batalkan Pendaftaran',
                                            'confirmVariant' => 'danger',
                                        ]) }})"
                                        class="inline-flex w-full items-center justify-center rounded-lg border border-safety-rose bg-white px-4 py-2.5 text-sm font-medium text-safety-rose hover:bg-red-50 transition"
                                    >
                                        Batalkan Pendaftaran
                                    </button>
                                @endif

                                <div class="mt-2">
                                    <x-button href="{{ route('pendaftaran-kegiatan.index') }}" variant="ghost" size="sm" class="w-full">
                                        Lihat Semua Pendaftaran Saya
                                    </x-button>
                                </div>
                            @elseif ($kegiatan->bisaDidaftar())
                                <button
                                    type="button"
                                    @click="$dispatch('open-confirm-modal', {{ Js::from([
                                        'title' => 'Konfirmasi Pendaftaran Kegiatan',
                                        'message' => 'Apakah Anda yakin ingin mendaftar sebagai peserta pada kegiatan \"' . $kegiatan->judul . '\"?',
                                        'action' => route('kegiatan.daftar', $kegiatan),
                                        'method' => 'POST',
                                        'confirmText' => 'Ya, Daftar Sekarang',
                                        'confirmVariant' => 'primary',
                                    ]) }})"
                                    class="inline-flex w-full items-center justify-center rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition"
                                >
                                    Daftar Sekarang
                                </button>
                            @elseif ($sudahMulai)
                                <x-button type="button" variant="secondary" size="md" class="w-full" disabled>
                                    Pendaftaran Ditutup
                                </x-button>
                                <p class="mt-2 text-center text-xs text-slate-500">
                                    Waktu pelaksanaan kegiatan telah dimulai atau terlewati.
                                </p>
                            @elseif ($sisaKuota === 0)
                                <x-button type="button" variant="secondary" size="md" class="w-full" disabled>
                                    Kuota Penuh
                                </x-button>
                            @else
                                <x-button type="button" variant="secondary" size="md" class="w-full" disabled>
                                    Tidak Tersedia
                                </x-button>
                            @endif
                        @else
                            <div class="rounded-lg bg-slate-50 p-3 text-xs text-slate-600">
                                Pendaftaran mandiri hanya berlaku untuk pengguna dengan peran <strong>Pelapor</strong>.
                                @if (auth()->user()->isAdminKasus())
                                    <a href="{{ route('admin.kegiatan-edukasi.show', $kegiatan) }}" class="mt-2 block font-semibold text-brand-700 hover:underline">
                                        Kelola sebagai Admin &rarr;
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endguest
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

