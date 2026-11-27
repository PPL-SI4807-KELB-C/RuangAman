@extends('layouts.app')

@section('title', $title)

@section('content')
@php
    $iconTipe = [
        \App\Models\Notifikasi::TIPE_STATUS_BERUBAH => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        \App\Models\Notifikasi::TIPE_PENUGASAN_KASUS => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        \App\Models\Notifikasi::TIPE_JADWAL_KONSELING => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        \App\Models\Notifikasi::TIPE_KEGIATAN => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2',
    ];
    $iconDefault = 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9';
    $tabs = [
        'semua' => 'Semua',
        'belum-dibaca' => 'Belum Dibaca',
        'dibaca' => 'Sudah Dibaca',
    ];
@endphp

<div class="mx-auto max-w-5xl space-y-6 py-6">
    {{-- Header Halaman --}}
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Notifikasi Saya</h1>
            <p class="mt-1 text-sm text-slate-600">
                Pemberitahuan otomatis atas kejadian penting pada akun Anda, seperti perubahan status kasus atau penugasan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($jumlahBelumDibaca > 0)
                <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                    @csrf
                    @method('PATCH')
                    <x-button type="submit" variant="primary" size="md">Tandai Semua Dibaca</x-button>
                </form>
            @endif

            @if ($jumlahSudahDibaca > 0)
                <x-button
                    variant="outline-danger"
                    size="md"
                    @click="$dispatch('open-confirm-modal', {{ Js::from([
                        'title' => 'Hapus Notifikasi yang Sudah Dibaca',
                        'message' => 'Apakah Anda yakin ingin menghapus ' . $jumlahSudahDibaca . ' notifikasi yang sudah dibaca? Tindakan ini tidak dapat dibatalkan.',
                        'action' => route('notifikasi.hapus-dibaca'),
                        'method' => 'DELETE',
                        'confirmText' => 'Ya, Hapus',
                        'confirmVariant' => 'danger',
                    ]) }})"
                >
                    Hapus yang Sudah Dibaca
                </x-button>
            @endif
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $jumlahBelumDibaca + $jumlahSudahDibaca }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Belum Dibaca</p>
            <p class="mt-1 text-2xl font-bold text-brand-700">{{ $jumlahBelumDibaca }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sudah Dibaca</p>
            <p class="mt-1 text-2xl font-bold text-slate-700">{{ $jumlahSudahDibaca }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <nav class="inline-flex rounded-xl border border-slate-200 bg-white p-1 shadow-sm" aria-label="Filter status baca">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('notifikasi.index', array_filter(['status' => $key === 'semua' ? null : $key, 'tipe' => $tipe])) }}"
                   class="rounded-lg px-3 py-1.5 text-sm font-medium transition {{ $status === $key ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
                   @if ($status === $key) aria-current="page" @endif>
                    {{ $label }}
                    @if ($key === 'belum-dibaca' && $jumlahBelumDibaca > 0)
                        <span class="ml-1 rounded-full px-1.5 text-xs {{ $status === $key ? 'bg-white/20' : 'bg-brand-100 text-brand-800' }}">{{ $jumlahBelumDibaca }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <form method="GET" action="{{ route('notifikasi.index') }}" class="flex items-center gap-2">
            @if ($status !== 'semua')
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <label for="filter-tipe" class="sr-only">Filter jenis notifikasi</label>
            <select id="filter-tipe" name="tipe" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                <option value="">Semua Jenis</option>
                @foreach ($daftarTipe as $value => $label)
                    <option value="{{ $value }}" @selected($tipe === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <noscript><x-button type="submit" variant="secondary" size="sm">Terapkan</x-button></noscript>
        </form>
    </div>

    {{-- Daftar Notifikasi --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <ul class="divide-y divide-slate-100" role="list">
            @forelse ($notifikasiList as $notifikasi)
                @php $belum = ! $notifikasi->sudahDibaca(); @endphp
                <li class="flex flex-col gap-3 px-5 py-4 transition hover:bg-slate-50/80 sm:flex-row sm:items-start sm:justify-between {{ $belum ? 'bg-brand-50/40' : '' }}">
                    <div class="flex min-w-0 flex-1 items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $belum ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-500' }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconTipe[$notifikasi->tipe] ?? $iconDefault }}" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                @if ($belum)
                                    <span class="h-2 w-2 shrink-0 rounded-full bg-brand-600" aria-hidden="true"></span>
                                    <span class="sr-only">Belum dibaca:</span>
                                @endif
                                <a href="{{ route('notifikasi.show', $notifikasi) }}"
                                   class="truncate text-sm hover:text-brand-700 hover:underline {{ $belum ? 'font-semibold text-slate-900' : 'font-medium text-slate-700' }}">
                                    {{ $notifikasi->judul }}
                                </a>
                                <x-badge variant="{{ $belum ? 'brand' : 'slate' }}">{{ $notifikasi->labelTipe() }}</x-badge>
                            </div>
                            <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $notifikasi->pesan }}</p>
                            <p class="mt-1 text-xs text-slate-400">
                                <time datetime="{{ $notifikasi->created_at?->toIso8601String() }}">
                                    {{ $notifikasi->created_at?->translatedFormat('d M Y, H:i') }} WIB
                                </time>
                                &bull; {{ $notifikasi->created_at?->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2 sm:pl-4">
                        @if ($belum)
                            <form method="POST" action="{{ route('notifikasi.baca', $notifikasi) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                                    Tandai Dibaca
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('notifikasi.belum-dibaca', $notifikasi) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                                    Tandai Belum Dibaca
                                </button>
                            </form>
                        @endif

                        <button
                            type="button"
                            @click="$dispatch('open-confirm-modal', {{ Js::from([
                                'title' => 'Hapus Notifikasi',
                                'message' => 'Apakah Anda yakin ingin menghapus notifikasi "' . $notifikasi->judul . '"?',
                                'action' => route('notifikasi.destroy', $notifikasi),
                                'method' => 'DELETE',
                                'confirmText' => 'Ya, Hapus',
                                'confirmVariant' => 'danger',
                            ]) }})"
                            class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-medium text-safety-rose hover:bg-red-100"
                        >
                            Hapus
                        </button>
                    </div>
                </li>
            @empty
                <li class="px-6 py-12 text-center">
                    <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $iconDefault }}" />
                    </svg>
                    <p class="mt-3 text-sm font-medium text-slate-700">
                        @if ($status === 'belum-dibaca')
                            Tidak ada notifikasi yang belum dibaca.
                        @elseif ($status === 'dibaca')
                            Belum ada notifikasi yang sudah dibaca.
                        @else
                            Belum ada notifikasi.
                        @endif
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Notifikasi akan muncul otomatis saat ada perubahan status kasus, penugasan, atau jadwal konseling.</p>
                </li>
            @endforelse
        </ul>

        @if ($notifikasiList->hasPages())
            <div class="border-t border-slate-200 bg-slate-50 px-6 py-3">
                {{ $notifikasiList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
