@extends('layouts.app')

@section('title', 'Riwayat Laporan Kasus - RuangAman')

@section('content')
<div class="mx-auto max-w-5xl py-6">
    {{-- Header Halaman --}}
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Riwayat Laporan Kasus</h1>
            <p class="mt-1 text-sm text-slate-600">
                Pantau status penanganan dan kelola laporan yang masih berstatus Pending.
            </p>
        </div>

        <a href="{{ route('laporan.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            <span class="text-base leading-none">+</span> Buat Laporan Baru
        </a>
    </div>

    {{-- Tabel Riwayat Laporan --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th scope="col" class="px-6 py-4">Kode Pelacakan</th>
                        <th scope="col" class="px-6 py-4">Kategori</th>
                        <th scope="col" class="px-6 py-4">Urgensi</th>
                        <th scope="col" class="px-6 py-4">Mode</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($laporans as $laporan)
                        @php
                            $statusName = $laporan->status->nama_status ?? 'Pending';
                            $isPending = strtolower($statusName) === 'pending';
                        @endphp
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-6 py-4 font-mono font-semibold text-brand-700">
                                <a href="{{ route('laporan.show', $laporan) }}" class="hover:underline">
                                    {{ $laporan->kode_laporan }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-slate-700">
                                {{ $laporan->kategori_kekerasan }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 capitalize text-slate-700">
                                {{ $laporan->tingkat_urgensi }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($laporan->mode_anonim)
                                    <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700">
                                        Anonim
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                        Teridentifikasi
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $isPending ? 'bg-amber-100 text-amber-800' : 'bg-brand-100 text-brand-800' }}">
                                    {{ $statusName }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-medium">
                                <div class="inline-flex items-center justify-end gap-3">
                                    <a href="{{ route('laporan.show', $laporan) }}"
                                       class="text-brand-700 transition hover:text-brand-800 hover:underline">
                                        Detail
                                    </a>

                                    @if ($isPending)
                                        <a href="{{ route('laporan.edit', $laporan) }}"
                                           class="text-amber-600 transition hover:text-amber-700 hover:underline">
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('laporan.destroy', $laporan) }}"
                                              onsubmit="return confirm('Yakin ingin membatalkan dan menghapus laporan ini?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 transition hover:text-red-700 hover:underline">
                                                Batalkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="text-sm font-medium text-slate-700">Belum ada laporan kasus yang dibuat.</p>
                                <p class="mt-1 text-xs text-slate-500">Klik tombol "+ Buat Laporan Baru" untuk mulai melaporkan kejadian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($laporans->hasPages())
            <div class="border-t border-slate-200 bg-slate-50 px-6 py-3">
                {{ $laporans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection