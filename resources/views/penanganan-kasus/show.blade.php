@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('admin.penanganan-kasus.index') }}" class="hover:text-brand-600">Penugasan Kasus</a>
                <span>/</span>
                <span class="text-slate-700">Detail #{{ $penanganan->penanganan_id }}</span>
            </div>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                Penanganan Kasus: {{ $penanganan->laporanKasus?->kode_laporan ?? 'N/A' }}
            </h1>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-button href="{{ route('admin.penanganan-kasus.index') }}" variant="secondary" size="sm">
                Kembali
            </x-button>
            <x-button href="{{ route('admin.penanganan-kasus.edit', $penanganan) }}" variant="secondary" size="sm">
                Ubah Penugasan
            </x-button>
            <x-button
                type="button"
                variant="danger"
                size="sm"
                @click="$dispatch('open-confirm-modal', {
                    title: 'Batalkan Penugasan Kasus',
                    message: 'Apakah Anda yakin ingin membatalkan penugasan untuk kasus {{ $penanganan->laporanKasus?->kode_laporan }}? Tindakan ini akan menghapus penugasan petugas pendamping saat ini.',
                    action: '{{ route('admin.penanganan-kasus.destroy', $penanganan) }}',
                    method: 'DELETE',
                    confirmText: 'Ya, Batalkan Penugasan',
                    confirmVariant: 'danger'
                })"
            >
                Batalkan Penugasan
            </x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Kolom Kiri: Detail Penugasan & Petugas -->
        <div class="space-y-6 lg:col-span-1">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900 border-b border-slate-100 pb-3">Informasi Penugasan</h2>

                <dl class="mt-4 space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Status Penanganan</dt>
                        <dd class="mt-1">
                            @if ($penanganan->tanggal_selesai)
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">
                                    Selesai Ditangani
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">
                                    Sedang Ditangani
                                </span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Petugas / Konselor Pendamping</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $penanganan->petugas?->nama_tampilan ?? 'User terhapus' }}
                        </dd>
                        <dd class="text-xs text-slate-500">
                            {{ $penanganan->petugas?->email }}
                        </dd>
                        <dd class="mt-1">
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-700">
                                Role: {{ $penanganan->petugas?->role?->nama_role ?? 'N/A' }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal Mulai</dt>
                        <dd class="mt-1 text-slate-700 font-medium">
                            {{ $penanganan->tanggal_mulai ? $penanganan->tanggal_mulai->translatedFormat('d F Y') : '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tanggal Selesai</dt>
                        <dd class="mt-1 text-slate-700 font-medium">
                            {{ $penanganan->tanggal_selesai ? $penanganan->tanggal_selesai->translatedFormat('d F Y') : 'Belum selesai (masih berjalan)' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Catatan Arahan</dt>
                        <dd class="mt-1 text-slate-700 whitespace-pre-line bg-slate-50 rounded-lg p-3 text-xs border border-slate-100">
                            {{ $penanganan->catatan ?: 'Tidak ada catatan arahan khusus.' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">Waktu Penugasan Dibuat</dt>
                        <dd class="mt-1 text-xs text-slate-500">
                            {{ $penanganan->created_at ? $penanganan->created_at->translatedFormat('d M Y, H:i') : '-' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Kolom Kanan: Detail Laporan & Riwayat Log (Integrasi PBI-06) -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Informasi Laporan Kasus -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-base font-semibold text-slate-900">Data Laporan Kasus</h2>
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
                        Status Laporan: {{ $statusName }}
                    </x-badge>
                </div>

                @if ($penanganan->laporanKasus)
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                        <div>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Kode Pelacakan</span>
                            <span class="font-mono text-sm font-bold text-brand-700">{{ $penanganan->laporanKasus->kode_laporan }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Tingkat Urgensi</span>
                            <span class="capitalize font-medium text-slate-800">{{ $penanganan->laporanKasus->tingkat_urgensi }}</span>
                        </div>

                        <div class="sm:col-span-2">
                            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Judul Kasus</span>
                            <span class="font-semibold text-slate-900 text-base">{{ $penanganan->laporanKasus->judul }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Kategori Kekerasan</span>
                            <span class="text-slate-800">{{ $penanganan->laporanKasus->kategori_kekerasan }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Identitas Pelapor</span>
                            <span class="text-slate-800">
                                @if ($penanganan->laporanKasus->mode_anonim)
                                    <span class="inline-flex items-center gap-1 text-slate-600 font-medium">
                                        <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
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
                            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Lokasi Kejadian</span>
                            <span class="text-slate-800">{{ $penanganan->laporanKasus->lokasi_kejadian }}</span>
                        </div>

                        <div class="sm:col-span-2">
                            <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Kronologi Singkat</span>
                            <p class="mt-1 text-slate-700 text-xs leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-100 whitespace-pre-line">
                                {{ $penanganan->laporanKasus->kronologi }}
                            </p>
                        </div>
                    </div>
                @else
                    <p class="mt-4 text-sm text-slate-500 italic">Data laporan kasus tidak ditemukan atau telah dihapus.</p>
                @endif
            </div>

            <!-- Titik Integrasi PBI-06: Log Penanganan Kasus -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-semibold text-slate-900">Aktivitas & Log Penanganan</h2>
                        <span class="rounded bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700 border border-brand-200">
                            PBI-06 Integration
                        </span>
                    </div>
                    <span class="text-xs text-slate-400">penanganan_id: #{{ $penanganan->penanganan_id }}</span>
                </div>

                <div class="mt-4">
                    @if ($penanganan->logPenanganan && $penanganan->logPenanganan->isNotEmpty())
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach ($penanganan->logPenanganan as $log)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (! $loop->last)
                                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-brand-100 flex items-center justify-center ring-8 ring-white text-brand-600 text-xs font-bold">
                                                        {{ $loop->iteration }}
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-xs text-slate-800">{{ $log->aktivitas }}</p>
                                                        <p class="text-[11px] text-slate-400 mt-0.5">Oleh: {{ $log->user?->nama_tampilan ?? 'Sistem' }} &bull; Jenis: {{ $log->jenis_log }}</p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-[11px] text-slate-400">
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
                        <div class="rounded-lg border border-dashed border-slate-200 p-6 text-center">
                            <svg class="mx-auto h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-xs font-medium text-slate-600">Belum ada catatan aktivitas penanganan (PBI-06).</p>
                            <p class="mt-0.5 text-[11px] text-slate-400">
                                Catatan otomatis dan manual akan tercatat pada tabel <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700">log_penanganan</code> dengan foreign key <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700">penanganan_id = {{ $penanganan->penanganan_id }}</code>.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

