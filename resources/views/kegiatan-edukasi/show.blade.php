@extends('layouts.app')

@section('title', $title)

@section('content')
@php
    $sudahMulai = $kegiatan->sudahDimulai();
    $terdaftarAktif = $pendaftaranSaya && $pendaftaranSaya->status_pendaftaran === \App\Models\PendaftaranKegiatan::STATUS_AKTIF;
    $terdaftarHadir = $pendaftaranSaya && $pendaftaranSaya->status_pendaftaran === \App\Models\PendaftaranKegiatan::STATUS_HADIR;
    $sudahDaftarSelainBatal = $pendaftaranSaya && $pendaftaranSaya->status_pendaftaran !== \App\Models\PendaftaranKegiatan::STATUS_DIBATALKAN;
@endphp

<div class="w-full">
    <section class="w-full bg-white py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            {{-- Informasi utama --}}
            <div class="lg:col-span-8 space-y-10">
                <a href="{{ route('kegiatan.index') }}" class="inline-flex text-sm font-medium text-neutral-700 hover:text-neutral-900 hover:underline">
                    &larr; Kembali ke daftar kegiatan
                </a>

                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-1">
                        <x-badge :variant="$kegiatan->metode === 'online' ? 'info' : 'brand'">
                            {{ $kegiatan->metodeLabel() }}
                        </x-badge>
                        <x-badge :variant="$kegiatan->statusBadgeVariant()">
                            {{ $kegiatan->statusLabel() }}
                        </x-badge>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                        {{ $kegiatan->judul }}
                    </h1>

                    <p class="text-sm text-neutral-600">
                        Narasumber: <span class="font-medium text-neutral-800">{{ $kegiatan->narasumber ?: 'Akan diumumkan' }}</span>
                    </p>
                </div>

                {{-- Jadwal & akses --}}
                <dl class="border-t border-neutral-300/60">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-1 border-b border-neutral-300/60 py-4 sm:grid-cols-[10rem_1fr]">
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500">Mulai</dt>
                        <dd class="text-sm text-neutral-800">
                            {{ $kegiatan->tanggal_mulai->translatedFormat('l, d F Y') }}
                            <span class="text-neutral-600">&bull; Pukul {{ substr((string) $kegiatan->waktu_mulai, 0, 5) }} WIB</span>
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-1 border-b border-neutral-300/60 py-4 sm:grid-cols-[10rem_1fr]">
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500">Selesai</dt>
                        <dd class="text-sm text-neutral-800">
                            @if ($kegiatan->tanggal_selesai || $kegiatan->waktu_selesai)
                                {{ ($kegiatan->tanggal_selesai ?? $kegiatan->tanggal_mulai)->translatedFormat('l, d F Y') }}
                                @if ($kegiatan->waktu_selesai)
                                    <span class="text-neutral-600">&bull; Pukul {{ substr((string) $kegiatan->waktu_selesai, 0, 5) }} WIB</span>
                                @endif
                            @else
                                <span class="text-neutral-600">Menyesuaikan acara</span>
                            @endif
                        </dd>
                    </div>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-1 border-b border-neutral-300/60 py-4 sm:grid-cols-[10rem_1fr]">
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500">
                            {{ $kegiatan->metode === 'online' ? 'Tautan akses' : 'Lokasi' }}
                        </dt>
                        <dd class="text-sm">
                            @if ($kegiatan->metode === 'online')
                                @if ($terdaftarAktif || $terdaftarHadir || (auth()->check() && auth()->user()->isAdminKasus()))
                                    <span class="break-all text-neutral-800">
                                        {{ $kegiatan->lokasi_atau_tautan ?: 'Tautan akan dibagikan menjelang sesi.' }}
                                    </span>
                                @else
                                    <span class="text-neutral-600">
                                        Tautan meeting akan ditampilkan setelah Anda berhasil mendaftar.
                                    </span>
                                @endif
                            @else
                                <span class="text-neutral-800">{{ $kegiatan->lokasi_atau_tautan ?: 'Informasi lokasi menyusul.' }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>

                {{-- Deskripsi --}}
                <div>
                    <h2 class="text-xs font-medium uppercase tracking-wider text-neutral-500">Deskripsi kegiatan</h2>
                    <div class="mt-3 max-w-2xl whitespace-pre-line text-sm leading-relaxed text-neutral-700">
                        {{ $kegiatan->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}
                    </div>
                </div>
            </div>

            {{-- Kuota & aksi pendaftaran --}}
            <aside class="lg:col-span-4 space-y-5">
                <x-card>
                    <h2 class="text-sm font-semibold text-neutral-900">Informasi kuota</h2>

                    <dl class="mt-4 space-y-2.5 text-xs">
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-neutral-600">Total kuota</dt>
                            <dd class="font-medium text-neutral-900">{{ $kegiatan->kuota }} peserta</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-neutral-600">Terdaftar aktif</dt>
                            <dd class="font-medium text-neutral-900">{{ $terisiCount }} peserta</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4 border-t border-neutral-300/60 pt-2.5">
                            <dt class="text-neutral-600">Sisa kuota</dt>
                            <dd class="font-semibold {{ $sisaKuota > 0 ? 'text-brand-700' : 'text-safety-rose' }}">{{ $sisaKuota }} kursi</dd>
                        </div>
                    </dl>

                    <div class="mt-5 border-t border-neutral-300/60 pt-4">
                        @guest
                            <p class="mb-3 text-xs leading-relaxed text-neutral-600">
                                Silakan masuk dengan akun Pelapor Anda untuk mendaftar pada kegiatan ini.
                            </p>
                            <x-button href="{{ route('login') }}" variant="primary" size="md" class="w-full">
                                Masuk untuk mendaftar
                            </x-button>
                        @else
                            @if (auth()->user()->isPelapor())
                                @if ($sudahDaftarSelainBatal)
                                    <p class="mb-2 text-xs text-neutral-600">Status pendaftaran Anda</p>
                                    <div class="mb-4">
                                        <x-badge :variant="$pendaftaranSaya->statusBadgeVariant()">
                                            {{ $pendaftaranSaya->statusLabel() }}
                                        </x-badge>
                                    </div>

                                    @if ($pendaftaranSaya->bisaDibatalkan())
                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-modal', {{ Js::from([
                                                'title' => 'Batalkan Pendaftaran Kegiatan',
                                                'message' => 'Apakah Anda yakin ingin membatalkan pendaftaran pada kegiatan "' . $kegiatan->judul . '"? Kursi kuota Anda akan dilepas untuk peserta lain.',
                                                'action' => route('pendaftaran-kegiatan.cancel', $pendaftaranSaya),
                                                'method' => 'PATCH',
                                                'confirmText' => 'Ya, Batalkan Pendaftaran',
                                                'confirmVariant' => 'danger',
                                            ]) }})"
                                            class="inline-flex w-full items-center justify-center rounded border border-neutral-300/70 bg-white px-5 py-2.5 text-xs font-medium text-safety-rose transition-colors hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400"
                                        >
                                            Batalkan pendaftaran
                                        </button>
                                    @endif

                                    <a href="{{ route('pendaftaran-kegiatan.index') }}" class="mt-3 inline-flex text-xs font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                                        Lihat semua pendaftaran saya &rarr;
                                    </a>
                                @elseif ($kegiatan->bisaDidaftar())
                                    <button
                                        type="button"
                                        @click="$dispatch('open-confirm-modal', {{ Js::from([
                                            'title' => 'Konfirmasi Pendaftaran Kegiatan',
                                            'message' => 'Apakah Anda yakin ingin mendaftar sebagai peserta pada kegiatan "' . $kegiatan->judul . '"?',
                                            'action' => route('kegiatan.daftar', $kegiatan),
                                            'method' => 'POST',
                                            'confirmText' => 'Ya, Daftar Sekarang',
                                            'confirmVariant' => 'primary',
                                        ]) }})"
                                        class="inline-flex w-full items-center justify-center rounded bg-brand-600 px-5 py-2.5 text-xs font-medium text-white transition-colors hover:bg-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400"
                                    >
                                        Daftar sekarang
                                    </button>
                                @elseif ($sudahMulai)
                                    <x-button type="button" variant="secondary" size="md" class="w-full" disabled>
                                        Pendaftaran ditutup
                                    </x-button>
                                    <p class="mt-2 text-[11px] leading-relaxed text-neutral-500">
                                        Waktu pelaksanaan kegiatan telah dimulai atau terlewati.
                                    </p>
                                @elseif ($sisaKuota === 0)
                                    <x-button type="button" variant="secondary" size="md" class="w-full" disabled>
                                        Kuota penuh
                                    </x-button>
                                @else
                                    <x-button type="button" variant="secondary" size="md" class="w-full" disabled>
                                        Tidak tersedia
                                    </x-button>
                                @endif
                            @else
                                <p class="text-xs leading-relaxed text-neutral-600">
                                    Pendaftaran mandiri hanya berlaku untuk pengguna dengan peran <strong class="font-semibold text-neutral-800">Pelapor</strong>.
                                </p>
                                @if (auth()->user()->isAdminKasus())
                                    <a href="{{ route('admin.kegiatan-edukasi.show', $kegiatan) }}" class="mt-3 inline-flex text-xs font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                                        Kelola sebagai Admin &rarr;
                                    </a>
                                @endif
                            @endif
                        @endguest
                    </div>
                </x-card>

                <p class="text-[11px] leading-relaxed text-neutral-500">
                    Identitas Anda tidak dibagikan kepada peserta lain selama kegiatan berlangsung.
                </p>
            </aside>

        </div>
    </section>
</div>
@endsection
