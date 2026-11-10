@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pendaftaran Kegiatan Saya</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau status pendaftaran webinar atau lokakarya edukasi yang Anda ikuti.</p>
        </div>
        <div>
            <x-button href="{{ route('kegiatan.index') }}" variant="primary" size="md">
                Jelajahi Kegiatan Edukasi
            </x-button>
        </div>
    </div>

    @if ($pendaftaranList->isEmpty())
        <x-card class="py-12 text-center">
            <p class="text-sm text-slate-500">Anda belum mendaftar pada kegiatan edukasi apa pun.</p>
            <div class="mt-4">
                <x-button href="{{ route('kegiatan.index') }}" variant="secondary" size="sm">
                    Lihat Daftar Kegiatan Tersedia
                </x-button>
            </div>
        </x-card>
    @else
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-3.5">Kegiatan Edukasi</th>
                            <th scope="col" class="px-6 py-3.5">Jadwal &amp; Metode</th>
                            <th scope="col" class="px-6 py-3.5 text-center">Sisa Kuota</th>
                            <th scope="col" class="px-6 py-3.5 text-center">Status Pendaftaran</th>
                            <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pendaftaranList as $pendaftaran)
                            @php
                                $kegiatan = $pendaftaran->kegiatanEdukasi;
                            @endphp
                            <tr class="hover:bg-slate-50/75 transition">
                                <td class="px-6 py-4">
                                    <a href="{{ route('kegiatan.show', $kegiatan) }}" class="font-semibold text-slate-900 hover:text-brand-700">
                                        {{ $kegiatan->judul }}
                                    </a>
                                    <div class="mt-0.5 text-xs text-slate-500">
                                        Narasumber: {{ $kegiatan->narasumber ?: '-' }}
                                    </div>
                                    @if (in_array($pendaftaran->status_pendaftaran, [\App\Models\PendaftaranKegiatan::STATUS_AKTIF, \App\Models\PendaftaranKegiatan::STATUS_HADIR], true) && $kegiatan->lokasi_atau_tautan)
                                        <div class="mt-1 text-xs text-brand-700 break-all">
                                            {{ $kegiatan->metode === 'online' ? 'Tautan: ' : 'Lokasi: ' }}{{ $kegiatan->lokasi_atau_tautan }}
                                        </div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-xs">
                                    <div class="font-medium text-slate-800">{{ $kegiatan->tanggal_mulai->translatedFormat('d M Y') }}</div>
                                    <div class="text-slate-500">{{ substr((string) $kegiatan->waktu_mulai, 0, 5) }} WIB &bull; {{ $kegiatan->metodeLabel() }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-xs">
                                    <span class="font-semibold text-slate-800">{{ $kegiatan->sisaKuota() }}</span> / {{ $kegiatan->kuota }} kursi
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <x-badge :variant="$pendaftaran->statusBadgeVariant()">
                                        {{ $pendaftaran->statusLabel() }}
                                    </x-badge>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a
                                            href="{{ route('kegiatan.show', $kegiatan) }}"
                                            class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                        >
                                            Detail
                                        </a>

                                        @if ($pendaftaran->bisaDibatalkan())
                                            <button
                                                type="button"
                                                @click="$dispatch('open-confirm-modal', {{ Js::from([
                                                    'title' => 'Batalkan Pendaftaran',
                                                    'message' => 'Apakah Anda yakin ingin membatalkan pendaftaran untuk kegiatan \"' . $kegiatan->judul . '\"?',
                                                    'action' => route('pendaftaran-kegiatan.cancel', $pendaftaran),
                                                    'method' => 'PATCH',
                                                    'confirmText' => 'Ya, Batalkan',
                                                    'confirmVariant' => 'danger',
                                                ]) }})"
                                                class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-medium text-safety-rose hover:bg-red-100"
                                            >
                                                Batalkan Pendaftaran
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($pendaftaranList->hasPages())
                <div class="border-t border-slate-200 bg-slate-50/50 px-6 py-4">
                    {{ $pendaftaranList->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

