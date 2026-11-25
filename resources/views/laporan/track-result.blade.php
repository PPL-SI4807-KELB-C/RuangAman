@extends('layouts.app')

@section('title', 'Lacak Laporan — RuangAman')

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">

    <x-page-header
        title="Lacak laporan"
        description="Gunakan kode pribadi untuk melihat status dan tindak lanjut laporan."
    />

    {{-- POST + session: kode laporan tidak masuk URL/riwayat peramban. --}}
    <x-card class="max-w-2xl">
        <form method="POST" action="{{ route('lacak.cari') }}" class="flex flex-col gap-3 sm:flex-row sm:items-start">
            @csrf
            <x-input
                class="flex-1"
                name="kode_laporan"
                label="Kode laporan"
                :value="$kode_laporan"
                placeholder="Contoh: Q7M2K9"
                helper="Jangan bagikan kode ini kepada orang lain."
                :error="$errors->first('kode_laporan')"
                autocomplete="off"
                autocapitalize="characters"
                spellcheck="false"
                required
            />
            <x-button type="submit" variant="primary" class="sm:mt-[22px]">Periksa status</x-button>
        </form>
    </x-card>

    @if ($tidakDitemukan ?? false)
        <x-alert variant="danger" class="max-w-2xl">
            Kode <span class="font-mono font-semibold text-neutral-900">{{ $kode_laporan }}</span> tidak ditemukan. Periksa kembali huruf dan angkanya, atau salin ulang kode yang tampil saat laporan Anda dibuat.
        </x-alert>
    @endif

    {{-- PERBAIKAN 1: BANNER TAUTKAN LAPORAN KE AKUN / BOOKING KONSELING --}}
    @if ($laporan)
        @auth
            @if ($laporan->bisaDitautkan())
                <x-card>
                    <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <p class="text-sm font-semibold text-neutral-900">Tautkan laporan ke akun Anda</p>
                            <p class="mt-1 text-xs leading-relaxed text-neutral-600">Laporan ini masih tercatat pada akun anonim bersama. Tautkan ke akun <strong>{{ Auth::user()->nama_tampilan }}</strong> agar tersimpan di dashboard.</p>
                        </div>
                        <form method="POST" action="{{ route('lacak.tautkan') }}" class="shrink-0">
                            @csrf
                            <input type="hidden" name="kode_laporan" value="{{ $laporan->kode_laporan }}">
                            <x-button type="submit" variant="primary">Tautkan ke akun</x-button>
                        </form>
                    </div>
                </x-card>
            @endif
        @else
            <x-card>
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <p class="text-sm font-semibold text-neutral-900">Ingin menjadwalkan konseling?</p>
                        <p class="mt-1 text-xs leading-relaxed text-neutral-600">Buat akun dengan alias untuk menyimpan riwayat laporan ini dan memesan sesi konseling.</p>
                    </div>
                    <x-button :href="route('register')" variant="secondary" class="shrink-0">Daftar akun pelapor</x-button>
                </div>
            </x-card>
        @endauth
    @endif

    {{-- Detail hanya tampil untuk laporan nyata; tanpa laporan tidak ada data contoh yang ditampilkan. --}}
    @if ($laporan)
    <div class="border-t border-neutral-100 pt-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            {{-- Left Column: Detail Laporan (Figma Exact) --}}
            <div class="lg:col-span-8 space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-neutral-900 tracking-tight">
                        Laporan {{ $laporan->kode_laporan }}
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
                            <span class="h-2 w-2 rounded-full bg-brand-600"></span>
                            <span class="text-xs font-medium text-neutral-800">Penanganan kasus selesai</span>
                        @elseif ($statusName === 'Ditolak')
                            <span class="h-2 w-2 rounded-full bg-neutral-400"></span>
                            <span class="text-xs font-medium text-neutral-800">Dialihkan / Tidak dapat diproses sistem</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-neutral-600 mt-1">
                        Diterima pada {{ ($laporan->created_at ?? now())->translatedFormat('d F Y') }}, pukul {{ ($laporan->created_at ?? now())->format('H.i') }} WIB
                    </p>
                </div>

                {{-- Kotak Alasan Khusus Jika Kasus Ditolak / Dialihkan --}}
                @if ($statusName === 'Ditolak')
                    <div class="rounded-xl bg-surface p-5 border border-neutral-300 text-xs space-y-2">
                        <p class="font-semibold text-neutral-900">Catatan Pengalihan / Peninjauan:</p>
                        <p class="text-neutral-700 leading-relaxed">
                            {{ $laporan->penangananKasus?->catatan ?? 'Laporan ini berada di luar ruang lingkup penanganan kekerasan berbasis gender. Jika Anda membutuhkan bantuan darurat atau hukum perdata, Anda dapat menghubungi lembaga rujukan pada direktori bantuan kami.' }}
                        </p>
                    </div>
                @endif

                {{-- Metadata Grid --}}
                <div class="grid grid-cols-2 gap-6 pt-4 border-t border-neutral-100 text-xs">
                    <div>
                        <p class="text-neutral-600 text-[11px]">Alias</p>
                        <p class="font-medium text-neutral-800 mt-0.5">{{ $laporan->user?->profilPelapor?->alias ?? 'Alias tidak tersedia' }}</p>
                    </div>
                    <div>
                        <p class="text-neutral-600 text-[11px]">Kategori</p>
                        <p class="font-medium text-neutral-800 mt-0.5">
                            {{ [
                                'Siber' => 'Kekerasan digital',
                                'Seksual' => 'Kekerasan seksual',
                                'Psikis' => 'Kekerasan psikis',
                                'Fisik' => 'Kekerasan fisik',
                                'Ekonomi' => 'Kekerasan ekonomi',
                                'Lainnya' => 'Belum yakin / lainnya',
                            ][$laporan->kategori_kekerasan] ?? $laporan->kategori_kekerasan }}
                        </p>
                    </div>
                </div>

                {{-- Kronologi: tersembunyi secara bawaan agar aman dari orang yang melihat layar --}}
                <details class="group pt-4 border-t border-neutral-100">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 rounded text-xs font-semibold text-neutral-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400">
                        <span>Kronologi yang Anda bagikan</span>
                        <span class="text-[11px] font-medium text-neutral-600 underline group-open:hidden">Tampilkan</span>
                        <span class="hidden text-[11px] font-medium text-neutral-600 underline group-open:inline">Sembunyikan</span>
                    </summary>
                    <p class="mt-2 whitespace-pre-line text-xs leading-relaxed text-neutral-600">{{ $laporan->kronologi }}</p>
                </details>

                {{-- Lampiran & Retensi Privasi --}}
                <div class="space-y-1 pt-4 border-t border-neutral-100">
                    <p class="text-[11px] text-neutral-600">Lampiran pendukung</p>
                    <p class="text-xs text-neutral-600">
                        @if ($laporan && $laporan->lampiran->count() > 0)
                            {{ $laporan->lampiran->count() }} berkas terlampir (tersimpan di private storage terisolasi).
                        @else
                            Belum ada lampiran.
                        @endif
                    </p>
                    <p class="text-[11px] text-neutral-600">Berkas bukti dilindungi UU PDP dan hanya dapat dilihat konselor berwenang.</p>
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
                        <div class="rounded-lg bg-surface p-4 text-xs text-neutral-800 space-y-1">
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
                        <form method="POST" action="{{ route('lacak.pesan') }}" class="pt-3 border-t border-neutral-100 space-y-3">
                            @csrf
                            <input type="hidden" name="kode_laporan" value="{{ $laporan->kode_laporan }}">
                            <x-textarea
                                name="pesan"
                                label="Kirim balasan atau pertanyaan ke tim pendamping"
                                rows="3"
                                placeholder="Tuliskan pesan tanpa menyebutkan nama asli…"
                                :error="$errors->first('pesan')"
                                maxlength="1000"
                                required
                            />
                            <div class="flex justify-end">
                                <x-button type="submit" variant="primary" size="sm">Kirim pesan</x-button>
                            </div>
                        </form>
                    @endif

                    <p class="text-[11px] text-neutral-600 pt-2 border-t border-neutral-100">
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
                                <p class="text-[11px] text-neutral-600 mt-0.5">
                                    {{ ($laporan->created_at ?? now())->translatedFormat('d F Y') }} · {{ ($laporan->created_at ?? now())->format('H.i') }} WIB
                                </p>
                            </div>
                        </div>

                        {{-- Step 2: Peninjauan tim --}}
                        <div class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full {{ in_array($statusName, ['Proses', 'Selesai']) ? 'bg-neutral-900' : 'bg-neutral-300' }} shrink-0"></span>
                            <div>
                                <p class="font-semibold {{ in_array($statusName, ['Proses', 'Selesai']) ? 'text-neutral-900' : 'text-neutral-600' }}">Peninjauan tim</p>
                                <p class="text-[11px] text-neutral-600 mt-0.5">
                                    {{ in_array($statusName, ['Proses', 'Selesai']) ? 'Sedang berlangsung' : 'Belum dimulai' }}
                                </p>
                            </div>
                        </div>

                        {{-- Step 3: Pendampingan --}}
                        <div class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full {{ $statusName === 'Selesai' ? 'bg-brand-600' : 'bg-neutral-300' }} shrink-0"></span>
                            <div>
                                <p class="font-semibold {{ $statusName === 'Selesai' ? 'text-neutral-900' : 'text-neutral-600' }}">Pendampingan & Pemulihan</p>
                                <p class="text-[11px] text-neutral-600 mt-0.5">
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
                <div class="pt-6 border-t border-neutral-100 text-[11px] text-neutral-600 space-y-1">
                    <p>Jaga kode tetap pribadi.</p>
                    <p>Keluar cepat tidak menghapus riwayat peramban Anda.</p>
                </div>
            </div>

        </div>
    </div>
    @endif

</div>
@endsection
