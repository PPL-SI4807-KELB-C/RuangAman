@php
    $showCaseBadge = $showCaseBadge ?? true;
    $activeLogId = $activeLogId ?? null;
@endphp

@if ($logs->isEmpty())
    <x-empty-state
        title="Belum ada catatan log penanganan"
        description="Entri log manual maupun log sistem otomatis dari perubahan status kasus akan tampil secara kronologis di sini."
    >
        @can('create', \App\Models\LogPenanganan::class)
            <x-button href="{{ route('log-penanganan.create') }}" variant="primary" size="sm">
                Tambah Catatan Manual
            </x-button>
        @endcan
    </x-empty-state>
@else
    <div class="flow-root">
        <ul role="list" class="-mb-6">
            @foreach ($logs as $log)
                @php
                    $isSistem = $log->isSistem();
                    $laporan = $log->penangananKasus?->laporanKasus ?? $log->laporanKasus;
                    $isCurrent = $activeLogId !== null && (int) $activeLogId === (int) $log->log_id;
                @endphp
                <li>
                    <div class="relative pb-6">
                        @if (! $loop->last)
                            <span class="absolute left-4 top-8 -ml-px h-[calc(100%-1.5rem)] w-0.5 bg-neutral-200" aria-hidden="true"></span>
                        @endif

                        <div class="relative flex items-start gap-3 sm:gap-4">
                            <!-- Titik penanda kronologis -->
                            <div class="relative shrink-0 pt-1">
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-full border {{ $isSistem ? 'border-neutral-300 bg-surface text-neutral-700' : 'border-blue-200 bg-blue-50 text-safety-info' }}"
                                    aria-hidden="true"
                                >
                                    @if ($isSistem)
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    @endif
                                </span>
                            </div>

                            <!-- Kartu entri timeline -->
                            <div class="min-w-0 flex-1 rounded-xl border {{ $isCurrent ? 'border-brand-600 ring-1 ring-brand-600/20' : 'border-neutral-200/70' }} bg-white p-4 sm:p-5">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-log-jenis-badge :jenis="$log->jenis_log" />

                                        @if ($showCaseBadge && $laporan)
                                            <span class="inline-flex items-center rounded border border-neutral-200 bg-surface px-2 py-0.5 font-mono text-[11px] font-semibold text-neutral-800">
                                                {{ $laporan->kode_laporan }}
                                            </span>
                                        @endif

                                        @if ($laporan?->statusKasus)
                                            @php
                                                $statusLower = strtolower($laporan->statusKasus->nama_status);
                                                $statusVariant = match ($statusLower) {
                                                    'pending' => 'warning',
                                                    'proses' => 'info',
                                                    'selesai' => 'success',
                                                    'ditolak' => 'danger',
                                                    default => 'slate',
                                                };
                                            @endphp
                                            <x-badge :variant="$statusVariant">
                                                {{ $laporan->statusKasus->nama_status }}
                                            </x-badge>
                                        @endif
                                    </div>

                                    <time
                                        datetime="{{ $log->waktu?->toIso8601String() }}"
                                        class="shrink-0 text-xs font-medium text-neutral-600"
                                    >
                                        {{ $log->waktu?->translatedFormat('d M Y, H:i') }} WIB
                                    </time>
                                </div>

                                @if ($showCaseBadge && $laporan)
                                    <p class="mt-2 text-xs font-medium text-neutral-600">
                                        Kasus: <span class="text-neutral-900">{{ $laporan->judul }}</span>
                                    </p>
                                @endif

                                <p class="mt-2.5 whitespace-pre-line text-sm leading-relaxed text-neutral-900">
                                    {{ $log->aktivitas }}
                                </p>

                                <div class="mt-4 flex flex-col gap-3 border-t border-neutral-200/70 pt-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="text-xs text-neutral-600">
                                        Dicatat oleh:
                                        <span class="font-semibold text-neutral-900">{{ $log->namaPembuatAman() }}</span>
                                        <span class="text-neutral-500">({{ $log->peranPembuatLabel() }})</span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        <a
                                            href="{{ route('log-penanganan.show', $log) }}"
                                            class="inline-flex items-center rounded border border-neutral-300/70 bg-white px-2.5 py-1.5 text-xs font-medium text-neutral-800 transition-colors hover:bg-neutral-50"
                                        >
                                            Detail
                                        </a>

                                        @if ($isSistem)
                                            <span class="inline-flex items-center gap-1 rounded bg-surface px-2.5 py-1.5 text-[11px] font-medium text-neutral-600">
                                                Terkunci otomatis
                                            </span>
                                        @else
                                            @can('update', $log)
                                                <a
                                                    href="{{ route('log-penanganan.edit', $log) }}"
                                                    class="inline-flex items-center rounded border border-neutral-300/70 bg-white px-2.5 py-1.5 text-xs font-medium text-neutral-800 transition-colors hover:bg-neutral-50"
                                                >
                                                    Ubah
                                                </a>
                                            @endcan

                                            @can('delete', $log)
                                                <button
                                                    type="button"
                                                    @click="$dispatch('open-confirm-modal', {{ Js::from([
                                                        'title' => 'Hapus Catatan Manual',
                                                        'message' => 'Apakah Anda yakin ingin menghapus catatan aktivitas penanganan #' . $log->log_id . '? Tindakan ini tidak dapat dibatalkan.',
                                                        'action' => route('log-penanganan.destroy', $log),
                                                        'method' => 'DELETE',
                                                        'confirmText' => 'Ya, Hapus Catatan',
                                                        'confirmVariant' => 'danger',
                                                    ]) }})"
                                                    class="inline-flex items-center rounded border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-medium text-safety-rose transition-colors hover:bg-red-100"
                                                >
                                                    Hapus
                                                </button>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif
