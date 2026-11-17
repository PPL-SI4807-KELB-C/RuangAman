@extends('layouts.app')

@section('title', 'Lacak Laporan — RuangAman')

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-8">

    {{-- Header --}}
    <div class="space-y-2 mb-8">
        <h1 class="text-3xl font-extrabold text-neutral-900 tracking-tight">Lacak laporan</h1>
        <p class="text-xs text-neutral-500">
            Gunakan kode pribadi untuk melihat status dan tindak lanjut laporan.
        </p>
    </div>

    {{-- Search Input (Figma Exact) --}}
    <form method="GET" action="{{ route('lacak.hasil') }}" class="space-y-2 max-w-2xl mb-8">
        <label class="block text-xs font-semibold text-neutral-800">Kode laporan</label>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <input type="text" name="kode_laporan" value="{{ old('kode_laporan', $kode_laporan ?? 'Q7M2K9') }}" placeholder="Contoh: Q7M2K9" required class="flex-1 rounded bg-[#f2f2f2] border-0 text-xs px-3.5 py-2.5 text-neutral-800 focus:ring-1 focus:ring-neutral-400 font-mono">
            <button type="submit" class="inline-flex items-center justify-center rounded bg-[#10a349] hover:bg-[#0e8e40] text-white font-medium text-xs px-6 py-2.5 transition-colors shrink-0">
                Periksa status
            </button>
        </div>
        <p class="text-[11px] text-neutral-400">Data contoh · Kode dan seluruh isi laporan di bawah ini bersifat fiktif.</p>
    </form>

    <div class="border-t border-neutral-100 pt-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            {{-- Left Column: Detail Laporan (Figma Exact) --}}
            <div class="lg:col-span-8 space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-neutral-900 tracking-tight">
                        Laporan {{ $laporan->kode_laporan ?? ($kode_laporan ?? 'Q7M2K9') }}
                    </h2>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        <span class="text-xs font-medium text-neutral-800">
                            {{ $laporan->statusKasus?->nama_status === 'Pending' ? 'Menunggu peninjauan' : ($laporan->statusKasus?->nama_status ?? 'Menunggu peninjauan') }}
                        </span>
                    </div>
                    <p class="text-[11px] text-neutral-400 mt-1">
                        Diterima pada {{ ($laporan->created_at ?? now())->translatedFormat('d F Y') }}, pukul {{ ($laporan->created_at ?? now())->format('H.i') }} WIB
                    </p>
                </div>

                {{-- Metadata Grid --}}
                <div class="grid grid-cols-2 gap-6 pt-4 border-t border-neutral-100 text-xs">
                    <div>
                        <p class="text-neutral-400 text-[11px]">Alias</p>
                        <p class="font-medium text-neutral-800 mt-0.5">{{ $laporan?->profilPelapor?->alias ?? 'Pelapor A' }}</p>
                    </div>
                    <div>
                        <p class="text-neutral-400 text-[11px]">Kategori</p>
                        <p class="font-medium text-neutral-800 mt-0.5">
                            {{ $laporan?->kategori_kekerasan === 'Siber' ? 'Kekerasan digital' : ($laporan?->kategori_kekerasan ?? 'Kekerasan digital') }}
                        </p>
                    </div>
                </div>

                {{-- Kronologi --}}
                <div class="space-y-2 pt-4 border-t border-neutral-100">
                    <h3 class="text-xs font-semibold text-neutral-900">Kronologi yang Anda bagikan</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        {{ $laporan?->kronologi ?? 'Saya menerima pesan yang mengancam untuk menyebarkan informasi pribadi saya tanpa persetujuan. Saya ingin mengetahui pilihan bantuan dan cara menyimpan bukti dengan aman.' }}
                    </p>
                </div>

                {{-- Lampiran --}}
                <div class="space-y-1 pt-4 border-t border-neutral-100">
                    <p class="text-[11px] text-neutral-400">Lampiran pendukung</p>
                    <p class="text-xs text-neutral-600">
                        @if ($laporan && $laporan->lampiran->count() > 0)
                            {{ $laporan->lampiran->count() }} berkas terlampir (tersimpan privat).
                        @else
                            Belum ada lampiran.
                        @endif
                    </p>
                </div>

                {{-- Pesan dari tim pendamping (Figma Exact) --}}
                <div class="rounded-xl border border-neutral-200/80 bg-white p-6 space-y-3">
                    <div class="flex items-center gap-2 text-neutral-800 font-semibold text-xs">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                        <span>Pesan dari tim pendamping</span>
                    </div>
                    <p class="text-xs font-medium text-neutral-900">Belum ada pesan</p>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Laporan Anda menunggu peninjauan. Anda tidak perlu mengirim laporan baru untuk kejadian yang sama.
                    </p>
                    <p class="text-[11px] text-neutral-400 pt-2 border-t border-neutral-100">
                        Periksa kembali melalui kode laporan ini saat Anda merasa aman.
                    </p>
                </div>
            </div>

            {{-- Right Column: Tahapan Laporan Stepper (Figma Exact) --}}
            <div class="lg:col-span-4 space-y-8 lg:pl-6 lg:border-l lg:border-neutral-100">
                <div class="space-y-6">
                    <h3 class="text-sm font-semibold text-neutral-900">Tahapan laporan</h3>

                    {{-- Stepper List --}}
                    <div class="space-y-6 text-xs relative">
                        {{-- Step 1: Laporan diterima --}}
                        <div class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-neutral-900 shrink-0"></span>
                            <div>
                                <p class="font-semibold text-neutral-900">Laporan diterima</p>
                                <p class="text-[11px] text-neutral-400 mt-0.5">
                                    {{ ($laporan->created_at ?? now())->translatedFormat('d F Y') }} · {{ ($laporan->created_at ?? now())->format('H.i') }} WIB
                                </p>
                            </div>
                        </div>

                        {{-- Step 2: Peninjauan tim --}}
                        <div class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-neutral-300 shrink-0"></span>
                            <div>
                                <p class="font-semibold text-neutral-400">Peninjauan tim</p>
                                <p class="text-[11px] text-neutral-400 mt-0.5">Belum dimulai</p>
                            </div>
                        </div>

                        {{-- Step 3: Pendampingan --}}
                        <div class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-neutral-300 shrink-0"></span>
                            <div>
                                <p class="font-semibold text-neutral-400">Pendampingan</p>
                                <p class="text-[11px] text-neutral-400 mt-0.5">Sesuai kebutuhan dan persetujuan</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tentang Status Laporan --}}
                <div class="space-y-2 pt-6 border-t border-neutral-100 text-xs text-neutral-600">
                    <h4 class="font-semibold text-neutral-900">Tentang status laporan</h4>
                    <p class="leading-relaxed text-[11px]">
                        Menunggu peninjauan berarti laporan telah tercatat, tetapi belum ditinjau oleh tim.
                    </p>
                </div>

                {{-- Footer Info --}}
                <div class="pt-6 border-t border-neutral-100 text-[11px] text-neutral-400 space-y-1">
                    <p>Jaga kode tetap pribadi.</p>
                    <p>Keluar cepat tidak menghapus riwayat peramban Anda.</p>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
