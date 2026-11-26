@extends('layouts.app')

@section('title', $title)

@section('content')
@php
    $laporan = $log->penangananKasus?->laporanKasus ?? $log->laporanKasus;
    $statusKasus = $laporan?->statusKasus?->nama_status ?? 'N/A';
@endphp

<div class="mx-auto max-w-5xl px-4 sm:px-6 py-10 space-y-8">
    <x-page-header
        eyebrow="PBI-06 • Detail Catatan Log #{{ $log->log_id }}"
        title="{{ $laporan?->kode_laporan ?? 'Log Penanganan Kasus' }}"
        description="Rincian aktivitas penanganan dan linimasa kronologis untuk kasus terkait."
        :back="route('log-penanganan.index')"
        backLabel="Kembali ke Riwayat Log"
    >
        <x-slot:actions>
            @if ($log->isManual())
                @can('update', $log)
                    <x-button href="{{ route('log-penanganan.edit', $log) }}" variant="secondary" size="sm">
                        Ubah Catatan
                    </x-button>
                @endcan
                @can('delete', $log)
                    <button
                        type="button"
                        @click="$dispatch('open-confirm-modal', {{ Js::from([
                            'title' => 'Hapus Catatan Manual',
                            'message' => 'Apakah Anda yakin ingin menghapus catatan log #' . $log->log_id . '? Tindakan ini tidak dapat dikembalikan.',
                            'action' => route('log-penanganan.destroy', $log),
                            'method' => 'DELETE',
                            'confirmText' => 'Ya, Hapus Catatan',
                            'confirmVariant' => 'danger',
                        ]) }})"
                        class="inline-flex items-center justify-center rounded border border-safety-rose bg-white px-4 py-2 text-xs font-medium text-safety-rose transition-colors hover:bg-red-50"
                    >
                        Hapus Catatan
                    </button>
                @endcan
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Ringkasan Aman Entri & Kasus -->
        <x-card class="lg:col-span-1">
            <h2 class="border-b border-neutral-200/70 pb-3 text-sm font-semibold text-neutral-900">
                Informasi Log &amp; Kasus
            </h2>

            <dl class="mt-4 space-y-4 text-xs">
                <div>
                    <dt class="font-semibold uppercase tracking-wider text-neutral-500">Jenis Entri</dt>
                    <dd class="mt-1.5">
                        <x-log-jenis-badge :jenis="$log->jenis_log" />
                    </dd>
                </div>

                <div>
                    <dt class="font-semibold uppercase tracking-wider text-neutral-500">Waktu Tercatat</dt>
                    <dd class="mt-1 font-medium text-neutral-900">
                        {{ $log->waktu?->translatedFormat('l, d F Y • H:i') }} WIB
                    </dd>
                </div>

                <div>
                    <dt class="font-semibold uppercase tracking-wider text-neutral-500">Pembuat Catatan</dt>
                    <dd class="mt-1 font-medium text-neutral-900">
                        {{ $log->namaPembuatAman() }}
                    </dd>
                    <dd class="text-neutral-600">
                        Peran: {{ $log->peranPembuatLabel() }}
                    </dd>
                </div>

                @if ($laporan)
                    <div class="border-t border-neutral-200/70 pt-3">
                        <dt class="font-semibold uppercase tracking-wider text-neutral-500">Kode Laporan &amp; Status</dt>
                        <dd class="mt-1 flex flex-wrap items-center gap-2">
                            <span class="rounded border border-neutral-200 bg-surface px-2 py-0.5 font-mono font-semibold text-neutral-900">
                                {{ $laporan->kode_laporan }}
                            </span>
                            <x-badge variant="info">{{ $statusKasus }}</x-badge>
                        </dd>
                        <dd class="mt-1.5 font-medium text-neutral-900">{{ $laporan->judul }}</dd>
                    </div>
                @endif

                @if ($log->penangananKasus)
                    <div>
                        <dt class="font-semibold uppercase tracking-wider text-neutral-500">Pendamping Kasus</dt>
                        <dd class="mt-1 font-medium text-neutral-900">
                            {{ $log->penangananKasus->petugas?->nama_tampilan ?? 'Belum ditetapkan' }}
                        </dd>
                        <dd class="mt-1 text-neutral-600">
                            Kondisi kasus: {{ $log->isKasusAktif() ? 'Aktif' : 'Selesai / Ditutup' }}
                        </dd>
                    </div>
                @endif
            </dl>
        </x-card>

        <!-- Detail Aktivitas + Timeline Kasus -->
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-neutral-200/70 pb-3">
                    <h2 class="text-sm font-semibold text-neutral-900">Isi Catatan Aktivitas</h2>
                    @if ($log->isSistem())
                        <span class="text-[11px] font-medium text-neutral-600">
                            Entri otomatis sistem terkunci (hanya-baca)
                        </span>
                    @elseif (! $log->isKasusAktif())
                        <span class="text-[11px] font-medium text-safety-warning">
                            Kasus sudah tidak aktif — catatan terkunci
                        </span>
                    @endif
                </div>

                <p class="mt-4 whitespace-pre-line text-sm leading-relaxed text-neutral-900">
                    {{ $log->aktivitas }}
                </p>
            </x-card>

            <div class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-neutral-600">
                        Timeline Lengkap Kasus Ini ({{ $timelineLogs->count() }} entri)
                    </h2>
                    @if ($log->penangananKasus && $log->isKasusAktif())
                        @can('createForPenanganan', [\App\Models\LogPenanganan::class, $log->penangananKasus])
                            <x-button href="{{ route('log-penanganan.create', ['penanganan_id' => $log->penanganan_id]) }}" variant="secondary" size="sm">
                                + Tambah Log Baru
                            </x-button>
                        @endcan
                    @endif
                </div>

                <x-log-timeline
                    :logs="$timelineLogs"
                    :activeLogId="$log->log_id"
                    :showCaseBadge="false"
                />
            </div>
        </div>
    </div>
</div>
@endsection
