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
        <p class="text-[11px] text-neutral-400">{{ $laporan ? 'Laporan resmi · Jangan bagikan kode ini kepada orang lain.' : 'Data contoh · Kode dan seluruh isi laporan di bawah ini bersifat fiktif.' }}</p>
    </form>

    @if ($tidakDitemukan ?? false)
        <div class="mb-8 flex items-start gap-2.5 rounded-lg border border-neutral-200/70 bg-[#f2f2f2] p-4">
            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-safety-rose" aria-hidden="true"></span>
            <p class="text-xs leading-relaxed text-neutral-700">
                Kode <span class="font-mono text-neutral-900">{{ $kode_laporan }}</span> tidak ditemukan. Periksa kembali huruf dan angkanya, atau simpan ulang kode yang tampil saat laporan Anda dibuat.
            </p>
        </div>
    @endif

    {{-- PERBAIKAN 1: BANNER TAUTKAN LAPORAN KE AKUN / BOOKING KONSELING --}}
    @if ($laporan)
        @auth
            @if ($laporan->bisaDitautkan())
                <div class="mb-8 rounded-xl bg-neutral-900 text-white p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold">Tautkan Laporan ke Akun Anda</p>
                        <p class="text-[11px] text-neutral-300">Laporan ini masih tercatat pada akun anonim bersama. Anda dapat menautkannya ke akun <strong>{{ Auth::user()->nama_tampilan }}</strong> agar tersimpan permanen di dashboard.</p>
                    </div>
                    <form method="POST" action="{{ route('lacak.tautkan') }}" class="shrink-0">
                        @csrf
                        <input type="hidden" name="kode_laporan" value="{{ $laporan->kode_laporan }}">
                        <button type="submit" class="rounded bg-[#10a349] hover:bg-[#0e8e40] text-white text-xs font-medium px-4 py-2 transition-colors">
                            Tautkan ke Akun
                        </button>
                    </form>
                </div>
            @endif
        @else
            <div class="mb-8 rounded-xl bg-[#f2f2f2] border border-neutral-300/80 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-neutral-900">Ingin Menjadwalkan Konseling Psikolog?</p>
                    <p class="text-[11px] text-neutral-600">Buat akun dengan alias untuk mengamankan riwayat laporan ini dan memesan sesi konseling terjadwal.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('register') }}" class="rounded bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-medium px-4 py-2 transition-colors">
                        Daftar Akun Pelapor
                    </a>
                </div>
            </div>
        @endauth
    @endif

    <div class="border-t border-neutral-100 pt-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            {{-- Left Column: Detail Laporan (Figma Exact) --}}
            <div class="lg:col-span-8 space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-neutral-900 tracking-tight">
                        Laporan {{ $laporan->kode_laporan ?? ($kode_laporan ?? 'Q7M2K9') }}
                    </h2>
                    
                    @php
                        $statusName = $laporan->statusKasus?->nama_status ?? 'Pending';
                    @endphp

                    {{-- PERBAIKAN 3: STATUS BADGE & PENJELASAN RAMAH (TRAUMA-INFORMED) --}}
                    <div class="flex items-center gap-2 mt-2">
                        @if ($statusName === 'Pending')
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            <span class="text-xs font-medium text-neutral-800">Menunggu peninjauan</span>
                        @elseif ($statusName === 'Proses')
                            <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                            <span class="text-xs font-medium text-neutral-800">Sedang ditangani konselor pendamping</span>
                        @elseif ($statusName === 'Selesai')
                            <span class="h-2 w-2 rounded-full bg-[#10a349]"></span>
                            <span class="text-xs font-medium text-neutral-800">Penanganan kasus selesai</span>
                        @elseif ($statusName === 'Ditolak')
                            <span class="h-2 w-2 rounded-full bg-neutral-400"></span>
                            <span class="text-xs font-medium text-neutral-800">Dialihkan / Tidak dapat diproses sistem</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-neutral-400 mt-1">
                        Diterima pada {{ ($laporan->created_at ?? now())->translatedFormat('d F Y') }}, pukul {{ ($laporan->created_at ?? now())->format('H.i') }} WIB
                    </p>
                </div>

                {{-- Kotak Alasan Khusus Jika Kasus Ditolak / Dialihkan --}}
                @if ($statusName === 'Ditolak')
                    <div class="rounded-xl bg-[#f2f2f2] p-5 border border-neutral-300 text-xs space-y-2">
                        <p class="font-semibold text-neutral-900">Catatan Pengalihan / Peninjauan:</p>
                        <p class="text-neutral-700 leading-relaxed">
                            {{ $laporan->penangananKasus?->catatan ?? 'Laporan ini berada di luar ruang lingkup penanganan kekerasan berbasis gender. Jika Anda membutuhkan bantuan darurat atau hukum perdata, Anda dapat menghubungi lembaga rujukan pada direktori bantuan kami.' }}
                        </p>
                    </div>
                @endif

                {{-- Metadata Grid --}}
                <div class="grid grid-cols-2 gap-6 pt-4 border-t border-neutral-100 text-xs">
                    <div>
                        <p class="text-neutral-400 text-[11px]">Alias</p>
                        <p class="font-medium text-neutral-800 mt-0.5">{{ $laporan?->user?->profilPelapor?->alias ?? 'Pelapor A' }}</p>
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

                {{-- Lampiran & Retensi Privasi --}}
                <div class="space-y-1 pt-4 border-t border-neutral-100">
                    <p class="text-[11px] text-neutral-400">Lampiran pendukung</p>
                    <p class="text-xs text-neutral-600">
                        @if ($laporan && $laporan->lampiran->count() > 0)
                            {{ $laporan->lampiran->count() }} berkas terlampir (tersimpan di private storage terisolasi).
                        @else
                            Belum ada lampiran.
                        @endif
                    </p>
                    <p class="text-[10px] text-neutral-400">Berkas bukti dilindungi UU PDP dan hanya dapat dilihat konselor berwenang.</p>
                </div>

                {{-- PERBAIKAN 2: PESAN DARI TIM PENDAMPING DUA ARAH (FIGMA EXACT + CHAT FEED) --}}
                <div class="rounded-xl border border-neutral-200/80 bg-white p-6 space-y-4">
                    <div class="flex items-center gap-2 text-neutral-800 font-semibold text-xs">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                        <span>Pesan dari tim pendamping</span>
                    </div>

                    @if ($laporan && $laporan->penangananKasus?->catatan)
                        <div class="rounded-lg bg-[#f2f2f2] p-4 text-xs text-neutral-800 space-y-1">
                            <p class="font-semibold text-neutral-900">Catatan Konselor:</p>
                            <p class="leading-relaxed">{{ $laporan->penangananKasus->catatan }}</p>
                        </div>
                    @else
                        <div>
                            <p class="text-xs font-medium text-neutral-900">Belum ada pesan</p>
                            <p class="text-xs text-neutral-600 leading-relaxed mt-1">
                                Laporan Anda menunggu peninjauan. Anda tidak perlu mengirim laporan baru untuk kejadian yang sama.
                            </p>
                        </div>
                    @endif

                    {{-- Form Balasan Pesan Aman Dua Arah --}}
                    @if ($laporan)
                        <form method="POST" action="{{ route('lacak.pesan') }}" class="pt-3 border-t border-neutral-100 space-y-2">
                            @csrf
                            <input type="hidden" name="kode_laporan" value="{{ $laporan->kode_laporan }}">
                            <label class="block text-[11px] font-semibold text-neutral-700">Kirim balasan atau pertanyaan ke tim pendamping:</label>
                            <textarea name="pesan" rows="2" placeholder="Tuliskan pesan tanpa menyebutkan nama asli..." required class="w-full rounded bg-[#f2f2f2] border-0 text-xs p-3 text-neutral-800 focus:ring-1 focus:ring-neutral-400 leading-relaxed"></textarea>
                            <div class="flex justify-end">
                                <button type="submit" class="rounded bg-[#10a349] hover:bg-[#0e8e40] text-white text-xs font-medium px-4 py-1.5 transition-colors">
                                    Kirim Pesan Aman
                                </button>
                            </div>
                        </form>
                    @endif

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
                            <span class="mt-1 h-2 w-2 rounded-full {{ in_array($statusName, ['Proses', 'Selesai']) ? 'bg-neutral-900' : 'bg-neutral-300' }} shrink-0"></span>
                            <div>
                                <p class="font-semibold {{ in_array($statusName, ['Proses', 'Selesai']) ? 'text-neutral-900' : 'text-neutral-400' }}">Peninjauan tim</p>
                                <p class="text-[11px] text-neutral-400 mt-0.5">
                                    {{ in_array($statusName, ['Proses', 'Selesai']) ? 'Sedang berlangsung' : 'Belum dimulai' }}
                                </p>
                            </div>
                        </div>

                        {{-- Step 3: Pendampingan --}}
                        <div class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full {{ $statusName === 'Selesai' ? 'bg-[#10a349]' : 'bg-neutral-300' }} shrink-0"></span>
                            <div>
                                <p class="font-semibold {{ $statusName === 'Selesai' ? 'text-neutral-900' : 'text-neutral-400' }}">Pendampingan & Pemulihan</p>
                                <p class="text-[11px] text-neutral-400 mt-0.5">
                                    {{ $statusName === 'Selesai' ? 'Telah selesai' : 'Sesuai kebutuhan dan persetujuan' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tentang Status Laporan --}}
                <div class="space-y-2 pt-6 border-t border-neutral-100 text-xs text-neutral-600">
                    <h4 class="font-semibold text-neutral-900">Tentang status laporan</h4>
                    <p class="leading-relaxed text-[11px]">
                        @if ($statusName === 'Pending')
                            Menunggu peninjauan berarti laporan telah tercatat, tetapi belum ditinjau oleh tim admin.
                        @elseif ($statusName === 'Proses')
                            Kasus telah diverifikasi dan konselor pendamping telah ditugaskan untuk membantu Anda.
                        @elseif ($statusName === 'Selesai')
                            Seluruh tahapan penanganan dan pendampingan telah diselesaikan secara aman.
                        @else
                            Status laporan Anda telah ditinjau dan diarahkan sesuai kebijakan penanganan.
                        @endif
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
