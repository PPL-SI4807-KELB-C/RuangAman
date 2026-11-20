@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full">

    {{-- Hero --}}
    <section class="w-full bg-white pt-14 pb-10 sm:pt-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-8 space-y-3">
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">
                    Edukasi &amp; Pencegahan KBG
                </p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                    Pendaftaran Kegiatan Saya
                </h1>
                <p class="mt-3 text-sm text-neutral-600 leading-relaxed max-w-xl">
                    Pantau status pendaftaran webinar atau lokakarya edukasi yang Anda ikuti.
                </p>
            </div>

            <div class="lg:col-span-4 lg:pt-9">
                <a href="{{ route('kegiatan.index') }}" class="text-sm font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                    Jelajahi kegiatan edukasi &rarr;
                </a>
            </div>
        </div>
    </section>

    {{-- Daftar pendaftaran --}}
    <section class="w-full bg-[#f2f2f2] border-t border-neutral-200/70 py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-8">
                @if ($pendaftaranList->isEmpty())
                    <div class="rounded-xl border border-dashed border-neutral-300 bg-white p-8 text-center">
                        <p class="text-xs text-neutral-600 leading-relaxed">
                            Anda belum mendaftar pada kegiatan edukasi apa pun.
                        </p>
                        <a href="{{ route('kegiatan.index') }}" class="mt-3 inline-flex text-xs font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                            Lihat daftar kegiatan tersedia &rarr;
                        </a>
                    </div>
                @else
                    <div class="overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-neutral-600">
                                <thead class="border-b border-neutral-200 bg-[#f2f2f2] text-[11px] uppercase tracking-wider text-neutral-500">
                                    <tr>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Kegiatan Edukasi</th>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Jadwal &amp; Metode</th>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Sisa Kuota</th>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Status Pendaftaran</th>
                                        <th scope="col" class="px-6 py-3.5 text-right font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-200/70">
                                    @foreach ($pendaftaranList as $pendaftaran)
                                        @php
                                            $kegiatan = $pendaftaran->kegiatanEdukasi;
                                        @endphp
                                        <tr class="transition-colors hover:bg-[#f2f2f2]/70">
                                            <td class="px-6 py-4">
                                                <a href="{{ route('kegiatan.show', $kegiatan) }}" class="font-semibold text-neutral-900 hover:text-brand-700">
                                                    {{ $kegiatan->judul }}
                                                </a>
                                                <div class="mt-0.5 text-xs text-neutral-600">
                                                    Narasumber: {{ $kegiatan->narasumber ?: '-' }}
                                                </div>
                                                @if (in_array($pendaftaran->status_pendaftaran, [\App\Models\PendaftaranKegiatan::STATUS_AKTIF, \App\Models\PendaftaranKegiatan::STATUS_HADIR], true) && $kegiatan->lokasi_atau_tautan)
                                                    <div class="mt-1 break-all text-xs text-neutral-700">
                                                        {{ $kegiatan->metode === 'online' ? 'Tautan: ' : 'Lokasi: ' }}{{ $kegiatan->lokasi_atau_tautan }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-xs">
                                                <div class="font-medium text-neutral-800">{{ $kegiatan->tanggal_mulai->translatedFormat('d M Y') }}</div>
                                                <div class="text-neutral-600">{{ substr((string) $kegiatan->waktu_mulai, 0, 5) }} WIB &bull; {{ $kegiatan->metodeLabel() }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-xs">
                                                <span class="font-medium text-neutral-900">{{ $kegiatan->sisaKuota() }}</span> / {{ $kegiatan->kuota }} kursi
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <x-badge :variant="$pendaftaran->statusBadgeVariant()">
                                                    {{ $pendaftaran->statusLabel() }}
                                                </x-badge>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                                <div class="inline-flex items-center gap-4">
                                                    <a href="{{ route('kegiatan.show', $kegiatan) }}" class="text-xs font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                                                        Detail
                                                    </a>

                                                    @if ($pendaftaran->bisaDibatalkan())
                                                        <button
                                                            type="button"
                                                            @click="$dispatch('open-confirm-modal', {{ Js::from([
                                                                'title' => 'Batalkan Pendaftaran',
                                                                'message' => 'Apakah Anda yakin ingin membatalkan pendaftaran untuk kegiatan "' . $kegiatan->judul . '"?',
                                                                'action' => route('pendaftaran-kegiatan.cancel', $pendaftaran),
                                                                'method' => 'PATCH',
                                                                'confirmText' => 'Ya, Batalkan',
                                                                'confirmVariant' => 'danger',
                                                            ]) }})"
                                                            class="inline-flex items-center justify-center rounded border border-neutral-300/70 bg-white px-4 py-2 text-xs font-medium text-safety-rose transition-colors hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400"
                                                        >
                                                            Batalkan
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
                            <div class="border-t border-neutral-200/70 px-6 py-4">
                                {{ $pendaftaranList->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

</div>
@endsection
