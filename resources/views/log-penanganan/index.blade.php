@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">
    <x-page-header
        eyebrow="PBI-06 • Log Penanganan Kasus"
        title="Riwayat & Log Penanganan Kasus"
        description="Pantau jejak aktivitas manual konselor/admin serta log perubahan status otomatis dari sistem secara kronologis."
    >
        <x-slot:actions>
            @can('create', \App\Models\LogPenanganan::class)
                <x-button href="{{ route('log-penanganan.create', array_filter(['penanganan_id' => $filters['penanganan_id']])) }}" variant="primary" size="md">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Catatan Manual</span>
                </x-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <!-- Panel Filter & Pencarian -->
    <x-card>
        <form method="GET" action="{{ route('log-penanganan.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                <div class="md:col-span-5">
                    <x-search-input
                        name="search"
                        :value="$filters['search']"
                        label="Cari aktivitas atau kode kasus"
                        placeholder="Cari isi aktivitas, kode laporan, atau judul kasus..."
                    />
                </div>

                <div class="md:col-span-3">
                    <label for="jenis_log" class="sr-only">Filter Jenis Log</label>
                    <select
                        id="jenis_log"
                        name="jenis_log"
                        class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset ring-line-strong focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900"
                    >
                        <option value="">Semua Jenis Log</option>
                        @foreach ($jenisOptions as $val => $label)
                            <option value="{{ $val }}" @selected($filters['jenis_log'] === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-4">
                    <label for="pembuat_id" class="sr-only">Filter Pembuat Log</label>
                    <select
                        id="pembuat_id"
                        name="pembuat_id"
                        class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset ring-line-strong focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900"
                    >
                        <option value="">Semua Pembuat</option>
                        @foreach ($daftarPembuat as $pembuat)
                            <option value="{{ $pembuat->user_id }}" @selected((string) $filters['pembuat_id'] === (string) $pembuat->user_id)>
                                {{ $pembuat->nama_tampilan }} ({{ $pembuat->role?->nama_role ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-12 md:items-end">
                <div class="md:col-span-5">
                    <label for="penanganan_id" class="mb-1 block text-xs font-medium text-neutral-600">Kasus Ditangani</label>
                    <select
                        id="penanganan_id"
                        name="penanganan_id"
                        class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset ring-line-strong focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900"
                    >
                        <option value="">Semua Penanganan Kasus</option>
                        @foreach ($daftarPenanganan as $kasus)
                            <option value="{{ $kasus->penanganan_id }}" @selected((string) $filters['penanganan_id'] === (string) $kasus->penanganan_id)>
                                {{ $kasus->laporanKasus?->kode_laporan ?? '#' . $kasus->penanganan_id }}
                                — {{ $kasus->laporanKasus?->judul ?? 'Tanpa Judul' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="dari_tanggal" class="mb-1 block text-xs font-medium text-neutral-600">Dari Tanggal</label>
                    <input
                        type="date"
                        id="dari_tanggal"
                        name="dari_tanggal"
                        value="{{ $filters['dari_tanggal'] }}"
                        class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset ring-line-strong focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900"
                    />
                </div>

                <div class="md:col-span-2">
                    <label for="sampai_tanggal" class="mb-1 block text-xs font-medium text-neutral-600">Sampai Tanggal</label>
                    <input
                        type="date"
                        id="sampai_tanggal"
                        name="sampai_tanggal"
                        value="{{ $filters['sampai_tanggal'] }}"
                        class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset ring-line-strong focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900"
                    />
                </div>

                <div class="flex gap-2 md:col-span-3">
                    <x-button type="submit" variant="secondary" size="md" class="flex-1">
                        Terapkan Filter
                    </x-button>
                    @if (collect($filters)->filter(fn ($v) => $v !== '' && $v !== null)->isNotEmpty())
                        <x-button href="{{ route('log-penanganan.index') }}" variant="ghost" size="md">
                            Reset
                        </x-button>
                    @endif
                </div>
            </div>
        </form>
    </x-card>

    <!-- Timeline Kronologis Log Penanganan -->
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-neutral-600">
                Timeline Kronologis ({{ $logList->total() }} entri)
            </h2>
            <div class="flex flex-wrap items-center gap-2 text-xs text-neutral-600">
                <x-log-jenis-badge jenis="manual" />
                <x-log-jenis-badge jenis="sistem" />
            </div>
        </div>

        <x-log-timeline :logs="$logList" />

        @if ($logList->hasPages())
            <div class="pt-4">
                {{ $logList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
