@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('admin.penanganan-kasus.index') }}" class="inline-flex items-center gap-1.5 text-xs text-neutral-600 transition-colors hover:text-neutral-900">
                <span aria-hidden="true">&larr;</span>
                <span>Kembali ke daftar</span>
            </a>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-neutral-900">
                Penanganan Kasus: {{ $penanganan->laporanKasus?->kode_laporan ?? 'N/A' }}
            </h1>
            <p class="mt-1 text-xs text-neutral-500">ID Penugasan #{{ $penanganan->penanganan_id }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <x-button href="{{ route('admin.penanganan-kasus.edit', $penanganan) }}" variant="secondary" size="sm">
                Ubah Penugasan
            </x-button>
            <button
                type="button"
                @click="$dispatch('open-confirm-modal', {
                    title: 'Batalkan Penugasan Kasus',
                    message: 'Apakah Anda yakin ingin membatalkan penugasan untuk kasus {{ $penanganan->laporanKasus?->kode_laporan }}? Tindakan ini akan menghapus penugasan petugas pendamping saat ini.',
                    action: '{{ route('admin.penanganan-kasus.destroy', $penanganan) }}',
                    method: 'DELETE',
                    confirmText: 'Ya, Batalkan Penugasan',
                    confirmVariant: 'danger'
                })"
                class="text-xs font-medium text-safety-rose hover:underline"
            >
                Batalkan Penugasan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Kolom Kiri: Detail Penugasan & Petugas -->
        <div class="space-y-6 lg:col-span-1">
            <x-card>
                <h2 class="text-sm font-semibold text-neutral-900 mb-4">Informasi Penugasan</h2>

                <dl class="space-y-4 text-xs">
                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Status Penanganan</dt>
                        <dd class="mt-1">
                            @if ($penanganan->tanggal_selesai)
                                <x-badge variant="success">Selesai Ditangani</x-badge>
                            @else
                                <x-badge variant="info">Sedang Ditangani</x-badge>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Petugas / Konselor Pendamping</dt>
                        <dd class="mt-1 font-medium text-neutral-900">
                            {{ $penanganan->petugas?->nama_tampilan ?? 'User terhapus' }}
                        </dd>
                        <dd class="text-[11px] text-neutral-500">
                            {{ $penanganan->petugas?->email }}
                        </dd>
                        <dd class="mt-1">
                            <span class="inline-flex items-center rounded bg-[#f2f2f2] px-2 py-0.5 text-[11px] text-neutral-700">
                                Role: {{ $penanganan->petugas?->role?->nama_role ?? 'N/A' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Tanggal Mulai</dt>
                        <dd class="mt-1 text-neutral-800 font-medium">
                            {{ $penanganan->tanggal_mulai ? $penanganan->tanggal_mulai->translatedFormat('d F Y') : '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Tanggal Selesai</dt>
                        <dd class="mt-1 text-neutral-800 font-medium">
                            {{ $penanganan->tanggal_selesai ? $penanganan->tanggal_selesai->translatedFormat('d F Y') : 'Belum selesai (masih berjalan)' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Catatan Arahan</dt>
                        <dd class="mt-1 text-neutral-800 whitespace-pre-line bg-white rounded p-3 text-xs ring-1 ring-inset ring-neutral-300/70 leading-relaxed">
                            {{ $penanganan->catatan ?: 'Tidak ada catatan arahan khusus.' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Waktu Penugasan Dibuat</dt>
                        <dd class="mt-1 text-[11px] text-neutral-500">
                            {{ $penanganan->created_at ? $penanganan->created_at->translatedFormat('d M Y, H:i') : '-' }}
                        </dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <!-- Kolom Kanan: Detail Laporan & Riwayat Log -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Informasi Laporan Kasus -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-neutral-900">Data Laporan Kasus</h2>
                    @php
                        $statusName = $penanganan->laporanKasus?->statusKasus?->nama_status ?? 'N/A';
                        $badgeVariant = match (strtolower($statusName)) {
                            'pending' => 'warning',
                            'proses' => 'info',
                            'selesai' => 'success',
                            'ditolak' => 'danger',
                            default => 'slate',
                        };
                    @endphp
                    <x-badge :variant="$badgeVariant">
                        Status: {{ $statusName }}
                    </x-badge>
                </div>

                @if ($penanganan->laporanKasus)
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-xs">
                        <div>
                            <span class="block text-[11px] font-medium uppercase tracking-wider text-neutral-500">Kode Pelacakan</span>
                            <span class="font-mono text-sm font-bold text-neutral-900">{{ $penanganan->laporanKasus->kode_laporan }}</span>
                        </div>

                        <div>
                            <span class="block text-[11px] font-medium uppercase tracking-wider text-neutral-500">Tingkat Urgensi</span>
                            <span class="capitalize font-medium text-neutral-800">{{ $penanganan->laporanKasus->tingkat_urgensi }}</span>
                        </div>

                        <div class="sm:col-span-2">
                            <span class="block text-[11px] font-medium uppercase tracking-wider text-neutral-500">Judul Kasus</span>
                            <span class="font-bold text-neutral-900 text-sm">{{ $penanganan->laporanKasus->judul }}</span>
                        </div>

                        <div>
                            <span class="block text-[11px] font-medium uppercase tracking-wider text-neutral-500">Kategori Kekerasan</span>
                            <span class="text-neutral-800">{{ $penanganan->laporanKasus->kategori_kekerasan }}</span>
                        </div>

                        <div>
                            <span class="block text-[11px] font-medium uppercase tracking-wider text-neutral-500">Identitas Pelapor</span>
                            <span class="text-neutral-800">
                                @if ($penanganan->laporanKasus->mode_anonim)
                                    <span class="inline-flex items-center gap-1 text-neutral-700 font-medium">
                                        <svg class="h-3 w-3 text-neutral-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 016 0v2a1 1 0 102 0V7a5 5 0 00-5-5z" clip-rule="evenodd" />
                                        </svg>
                                        Anonim (Alias: {{ $penanganan->laporanKasus->user?->profilPelapor?->alias ?? 'Korban' }})
                                    </span>
                                @else
                                    {{ $penanganan->laporanKasus->user?->nama_tampilan }} ({{ $penanganan->laporanKasus->user?->email }})
                                @endif
                            </span>
                        </div>

                        <div class="sm:col-span-2">
                            <span class="block text-[11px] font-medium uppercase tracking-wider text-neutral-500">Lokasi Kejadian</span>
                            <span class="text-neutral-800">{{ $penanganan->laporanKasus->lokasi_kejadian }}</span>
                        </div>

                        <div class="sm:col-span-2">
                            <span class="block text-[11px] font-medium uppercase tracking-wider text-neutral-500">Kronologi Singkat</span>
                            <p class="mt-1 text-neutral-800 text-xs leading-relaxed bg-white p-3 rounded ring-1 ring-inset ring-neutral-300/70 whitespace-pre-line">
                                {{ $penanganan->laporanKasus->kronologi }}
                            </p>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-neutral-500 italic">Data laporan kasus tidak ditemukan atau telah dihapus.</p>
                @endif
            </x-card>

            <!-- Titik Integrasi: Log Penanganan Kasus -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-semibold text-neutral-900">Aktivitas &amp; Log Penanganan</h2>
                        <span class="rounded bg-brand-50 px-2 py-0.5 text-[11px] font-medium text-brand-700 border border-brand-200">
                            PBI-06 Integration
                        </span>
                    </div>
                    <span class="text-[11px] text-neutral-500">penanganan_id: #{{ $penanganan->penanganan_id }}</span>
                </div>

                <div>
                    @if ($penanganan->logPenanganan && $penanganan->logPenanganan->isNotEmpty())
                        <div class="flow-root">
                            <ul role="list" class="-mb-6">
                                @foreach ($penanganan->logPenanganan as $log)
                                    <li>
                                        <div class="relative pb-6">
                                            @if (! $loop->last)
                                                <span class="absolute left-3.5 top-3.5 -ml-px h-full w-0.5 bg-neutral-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-7 w-7 rounded-full bg-[#f2f2f2] border border-neutral-300 flex items-center justify-center text-neutral-700 text-xs font-semibold">
                                                        {{ $loop->iteration }}
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1">
                                                    <div>
                                                        <p class="text-xs text-neutral-800">{{ $log->aktivitas }}</p>
                                                        <p class="text-[11px] text-neutral-500 mt-0.5">Oleh: {{ $log->user?->nama_tampilan ?? 'Sistem' }} &bull; Jenis: {{ $log->jenis_log }}</p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-[11px] text-neutral-500">
                                                        <time datetime="{{ $log->waktu }}">{{ $log->waktu ? $log->waktu->translatedFormat('d M H:i') : '' }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="rounded border border-dashed border-neutral-300 p-6 text-center">
                            <p class="text-xs font-medium text-neutral-600">Belum ada catatan aktivitas penanganan.</p>
                            <p class="mt-1 text-[11px] text-neutral-500">
                                Catatan otomatis dan manual akan tercatat pada modul log penanganan.
                            </p>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection