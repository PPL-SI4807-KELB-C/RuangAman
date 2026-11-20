@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full">

    {{-- Hero: identitas peran --}}
    <section class="w-full bg-white pt-14 pb-10 sm:pt-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-8 space-y-3">
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">
                    Ruang Pelapor &middot; Akun alias
                </p>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                    Dashboard Pelapor
                </h1>
                <p class="mt-3 text-sm text-neutral-600 leading-relaxed max-w-xl">
                    Anda masuk sebagai <span class="font-medium text-neutral-800">{{ $alias }}</span>.
                    Pantau status laporan, jadwal konseling, dan pemberitahuan perubahan status di sini.
                </p>
            </div>

            <div class="lg:col-span-4 lg:pt-9 space-y-4">
                <x-button href="{{ route('laporan.create') }}" variant="primary" size="md" class="w-full">
                    Buat laporan baru
                </x-button>
                <a href="{{ route('pendaftaran-kegiatan.index') }}" class="block text-sm font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                    Pendaftaran kegiatan saya &rarr;
                </a>
            </div>
        </div>
    </section>

    {{-- Ringkasan & daftar milik sendiri --}}
    <section class="w-full bg-[#f2f2f2] border-t border-neutral-200/70 py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 space-y-12">

            {{-- FR-06: kartu ringkasan --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-neutral-200/60 bg-white p-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Laporan saya</p>
                    <p class="mt-5 text-3xl font-extrabold tracking-tight text-neutral-900">{{ $totalLaporan }}</p>
                    <p class="mt-1 text-xs text-neutral-500">Hanya laporan milik akun ini</p>
                </div>
                <div class="rounded-xl border border-neutral-200/60 bg-white p-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Sedang diproses</p>
                    <p class="mt-5 text-3xl font-extrabold tracking-tight text-neutral-900">{{ $totalProses }}</p>
                    <p class="mt-1 text-xs text-neutral-500">Konselor Anda sudah ditugaskan</p>
                </div>
                <div class="rounded-xl border border-neutral-200/60 bg-white p-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Jadwal konseling</p>
                    <p class="mt-5 text-3xl font-extrabold tracking-tight text-neutral-900">{{ $totalJadwal }}</p>
                    <p class="mt-1 text-xs text-neutral-500">Sesi mendatang yang belum dibatalkan</p>
                </div>
                <div class="rounded-xl border border-neutral-200/60 bg-white p-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Belum dibaca</p>
                    <p class="mt-5 text-3xl font-extrabold tracking-tight text-neutral-900">{{ $belumDibaca }}</p>
                    <p class="mt-1 text-xs text-neutral-500">Pemberitahuan perubahan status</p>
                </div>
            </div>

            {{-- FR-06: status laporan --}}
            <div>
                <div class="flex items-baseline justify-between gap-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Status laporan saya</p>
                    <a href="{{ route('lacak.form') }}" class="text-sm font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                        Lacak lewat kode &rarr;
                    </a>
                </div>

                @if ($laporanSaya->isEmpty())
                    <div class="mt-5 rounded-xl border border-dashed border-neutral-300 bg-white p-8 text-center">
                        <p class="text-xs text-neutral-600 leading-relaxed">
                            Anda belum membuat laporan. Sampaikan kapan saja &mdash; identitas Anda tetap berupa alias.
                        </p>
                        <a href="{{ route('laporan.create') }}" class="mt-3 inline-flex text-xs font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                            Buat laporan pertama &rarr;
                        </a>
                    </div>
                @else
                    <div class="mt-5 overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-neutral-600">
                                <thead class="border-b border-neutral-200 bg-[#f2f2f2] text-[11px] uppercase tracking-wider text-neutral-500">
                                    <tr>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Laporan</th>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Status</th>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Kode lacak</th>
                                        <th scope="col" class="px-6 py-3.5 font-medium">Diperbarui</th>
                                        <th scope="col" class="px-6 py-3.5 text-right font-medium">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-200/70">
                                    @foreach ($laporanSaya as $laporan)
                                        <tr class="transition-colors hover:bg-[#f2f2f2]/70">
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-neutral-900">{{ $laporan->judul }}</div>
                                                <p class="mt-0.5 text-[11px] text-neutral-500">
                                                    {{ $laporan->kategori_kekerasan }}
                                                    &bull; {{ $laporan->mode_anonim ? 'Mode anonim aktif' : 'Identitas alias terlihat konselor' }}
                                                </p>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <x-badge :variant="$laporan->statusKasus?->statusBadgeVariant() ?? 'slate'">
                                                    {{ $laporan->statusKasus?->nama_status ?? 'Tanpa status' }}
                                                </x-badge>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-neutral-800">
                                                {{ $laporan->kode_laporan }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-xs text-neutral-600">
                                                {{ $laporan->updated_at?->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                                <form method="POST" action="{{ route('lacak.cari') }}" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="kode_laporan" value="{{ $laporan->kode_laporan }}">
                                                    <button type="submit" class="text-xs font-medium text-neutral-700 hover:text-neutral-900 hover:underline">
                                                        Lacak
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- FR-06: jadwal konseling & pemberitahuan --}}
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
                <div class="lg:col-span-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Jadwal konseling saya</p>

                    <div class="mt-5 overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
                        <ul class="divide-y divide-neutral-200/70">
                            @forelse ($jadwalMendatang as $jadwal)
                                <li class="px-6 py-4">
                                    <p class="text-sm font-semibold text-neutral-900">
                                        {{ \Illuminate\Support\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }}
                                        <span class="font-normal text-neutral-600">
                                            &bull; {{ substr((string) $jadwal->waktu_mulai, 0, 5) }} WIB
                                        </span>
                                    </p>
                                    <p class="mt-1 text-xs text-neutral-600">
                                        {{ ucfirst($jadwal->metode) }} &bull; {{ $jadwal->lokasi_atau_tautan ?: 'Lokasi/tautan menyusul' }}
                                    </p>
                                </li>
                            @empty
                                <li class="px-6 py-10 text-center text-xs text-neutral-500">
                                    Belum ada jadwal konseling. Pemesanan sesi dibuka setelah modul konseling tersedia.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Pemberitahuan perubahan status</p>

                    <div class="mt-5 overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
                        <ul class="divide-y divide-neutral-200/70">
                            @forelse ($notifikasi as $item)
                                <li class="px-6 py-4">
                                    <div class="flex items-baseline justify-between gap-4">
                                        <span class="text-sm font-semibold text-neutral-900">{{ $item->judul }}</span>
                                        <span class="shrink-0 text-[11px] text-neutral-500">
                                            {{ \Illuminate\Support\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs leading-relaxed text-neutral-600">{{ $item->pesan }}</p>
                                    @if (empty($item->read_at))
                                        <p class="mt-1.5 inline-flex items-center gap-1.5 text-[11px] font-medium text-brand-700">
                                            <span class="h-2 w-2 shrink-0 rounded-full bg-brand-600" aria-hidden="true"></span>
                                            Belum dibaca
                                        </p>
                                    @endif
                                </li>
                            @empty
                                <li class="px-6 py-10 text-center text-xs text-neutral-500">
                                    Belum ada pemberitahuan. Notifikasi hanya dikirim untuk kejadian penting, bukan pengumuman rutin.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>
@endsection
