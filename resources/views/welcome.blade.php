@extends('layouts.app')

@section('title', 'RuangAman — Layanan Pelaporan Kekerasan Berbasis Gender')

@section('content')
<div class="w-full">

    {{-- SECTION 1: HERO (PUTIH) --}}
    <section class="w-full bg-white py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            {{-- Hero Left Content --}}
            <div class="lg:col-span-7 space-y-6">
                <p class="text-xs font-medium text-neutral-500 uppercase tracking-wider">
                    Pelaporan dan pendampingan
                </p>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                    RuangAman,<br>
                    layanan pelaporan<br>
                    kekerasan berbasis gender.
                </h1>
                <p class="text-base text-neutral-600 leading-relaxed max-w-xl">
                    Sampaikan pengalaman sesuai kesiapan Anda. Gunakan alias, pilih informasi yang ingin dibagikan, dan lacak tindak lanjutnya.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-4">
                    <a href="{{ auth()->user()?->isPelapor() ? route('laporan.create') : (auth()->check() ? route('dashboard') : route('register')) }}" class="inline-flex items-center justify-center rounded bg-brand-600 hover:bg-brand-700 text-white font-medium text-sm px-6 py-2.5 transition-colors shadow-xs">
                        Buat laporan
                    </a>
                    <a href="{{ route('lacak.form') }}" class="text-sm font-medium text-neutral-800 hover:text-neutral-900 hover:underline px-2 py-1">
                        Lacak laporan Anda
                    </a>
                </div>
            </div>

            {{-- Hero Right Card (Abu-abu di dalam background putih) --}}
            <div class="lg:col-span-5">
                <div class="rounded-xl bg-[#f2f2f2] p-6 sm:p-7 space-y-5 border border-neutral-200/60">
                    <div class="flex items-center gap-2 text-neutral-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <h2 class="text-sm font-semibold text-neutral-900">Kendali tetap pada Anda</h2>
                    </div>
                    
                    <ul class="space-y-2 text-xs text-neutral-700">
                        <li>Tidak perlu nama asli.</li>
                        <li>Akun cukup memakai alias.</li>
                        <li>Lampiran bersifat opsional.</li>
                    </ul>

                    <p class="text-[11px] leading-relaxed text-neutral-500 pt-3 border-t border-neutral-300/60">
                        Bukan layanan darurat. Jika ada ancaman langsung, cari tempat aman dan bantuan setempat.
                    </p>
                </div>
            </div>
        </div>

        @if ($pengumumanTerbaru->isNotEmpty())
            {{-- FR-31: pengumuman terbaru tampil di beranda --}}
            <div class="mx-auto max-w-6xl px-4 sm:px-6 mt-14 sm:mt-16">
                <div class="border-t border-neutral-300/60 pt-8">
                    <div class="flex items-baseline justify-between gap-6">
                        <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Pengumuman terbaru</p>
                        <a href="{{ route('pengumuman.index') }}" class="text-sm font-medium text-neutral-800 hover:text-neutral-900 hover:underline">
                            Semua pengumuman
                        </a>
                    </div>
                    <ul class="mt-6 border-y border-neutral-200/70">
                        @foreach ($pengumumanTerbaru as $item)
                            <li class="border-t border-neutral-200/70 first:border-t-0">
                                <a href="{{ route('pengumuman.show', $item) }}" class="group grid grid-cols-1 gap-x-6 gap-y-1 py-4 sm:grid-cols-[8rem_1fr]">
                                    <span class="text-xs text-neutral-500">{{ $item->mulai_tayang->translatedFormat('d F Y') }}</span>
                                    <span>
                                        <span class="block text-sm font-semibold text-neutral-900 group-hover:text-brand-700">{{ $item->judul }}</span>
                                        <span class="mt-1 line-clamp-1 text-xs leading-relaxed text-neutral-600">{{ $item->isi }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </section>

    {{-- SECTION 2: DUKUNGAN SESUAI KEBUTUHAN ANDA (ABU-ABU #f2f2f2 PENUH) --}}
    <section id="layanan" class="w-full bg-[#f2f2f2] py-16 sm:py-20 border-y border-neutral-200/70">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-neutral-900 tracking-tight">Dukungan sesuai kebutuhan Anda</h2>
                <p class="text-xs text-neutral-500 mt-1">Anda dapat memulai dari layanan yang paling relevan.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Fitur 1 --}}
                <div class="space-y-2.5">
                    <div class="text-neutral-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-neutral-900">Laporan anonim</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Ceritakan kejadian tanpa mencantumkan nama asli.
                    </p>
                </div>

                {{-- Fitur 2 --}}
                <div class="space-y-2.5">
                    <div class="text-neutral-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-neutral-900">Konseling</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Ajukan pendampingan melalui laporan Anda.
                    </p>
                </div>

                {{-- Fitur 3 --}}
                <div class="space-y-2.5">
                    <div class="text-neutral-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-neutral-900">Lacak status</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Lihat perkembangan dengan kode laporan pribadi.
                    </p>
                </div>

                {{-- Fitur 4 --}}
                <div class="space-y-2.5">
                    <div class="text-neutral-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-neutral-900">Edukasi & direktori</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Kenali bentuk kekerasan dan pilihan layanan bantuan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 3: ALUR PELAPORAN (PUTIH) --}}
    <section id="alur-pelaporan" class="w-full bg-white py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-neutral-900 tracking-tight">Alur pelaporan</h2>
                <p class="text-xs text-neutral-500 mt-1">Tahapan yang dapat Anda ikuti, tanpa harus menceritakan semuanya sekaligus.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="space-y-2">
                    <span class="text-xs text-neutral-500 font-mono">01</span>
                    <h3 class="text-sm font-semibold text-neutral-900">Sampaikan laporan</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Pilih kategori dan tulis kronologi sebatas yang nyaman Anda bagikan.
                    </p>
                </div>

                <div class="space-y-2">
                    <span class="text-xs text-neutral-500 font-mono">02</span>
                    <h3 class="text-sm font-semibold text-neutral-900">Simpan kode Anda</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Kode enam karakter digunakan untuk membuka perkembangan laporan.
                    </p>
                </div>

                <div class="space-y-2">
                    <span class="text-xs text-neutral-500 font-mono">03</span>
                    <h3 class="text-sm font-semibold text-neutral-900">Tentukan tindak lanjut</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Tim meninjau laporan. Pendampingan dilakukan dengan persetujuan Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 4: PRIVASI SEJAK LANGKAH PERTAMA (ABU-ABU #f2f2f2 PENUH) --}}
    <section class="w-full bg-[#f2f2f2] py-16 sm:py-20 border-y border-neutral-200/70">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-5 space-y-1">
                <h2 class="text-2xl font-bold text-neutral-900 tracking-tight">Privasi sejak langkah pertama</h2>
                <p class="text-xs text-neutral-500">Bagikan hanya yang diperlukan.</p>
            </div>

            <div class="lg:col-span-7 space-y-6">
                <div class="space-y-1 border-b border-neutral-300/60 pb-5">
                    <h3 class="text-sm font-semibold text-neutral-900">Konselor hanya melihat alias</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Gunakan nama samaran. Hindari alamat lengkap, nomor identitas, dan informasi pribadi orang lain di dalam kronologi.
                    </p>
                </div>

                <div class="space-y-1">
                    <h3 class="text-sm font-semibold text-neutral-900">Akses laporan melalui kode pribadi</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Simpan kode di tempat yang aman. Jangan bagikan kepada orang lain.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 5: PERTANYAAN UMUM (PUTIH) --}}
    <section class="w-full bg-white py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-5 space-y-1">
                <h2 class="text-2xl font-bold text-neutral-900 tracking-tight">Pertanyaan umum</h2>
                <p class="text-xs text-neutral-500">Informasi sebelum melapor.</p>
            </div>

            <div class="lg:col-span-7 space-y-6">
                <div class="space-y-1 border-b border-neutral-200 pb-5">
                    <h3 class="text-sm font-semibold text-neutral-900">Apakah saya harus menggunakan nama asli?</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Tidak. Anda cukup memakai alias. Akun Pelapor tetap diperlukan agar Anda dapat memantau status dan menerima pemberitahuan, sementara mode anonim menyembunyikan identitas Anda dari konselor.
                    </p>
                </div>

                <div class="space-y-1 border-b border-neutral-200 pb-5">
                    <h3 class="text-sm font-semibold text-neutral-900">Bagaimana jika saya belum memiliki bukti?</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Lampiran tidak wajib. Anda tetap dapat menyampaikan laporan.
                    </p>
                </div>

                <div class="space-y-1 border-b border-neutral-200 pb-5">
                    <h3 class="text-sm font-semibold text-neutral-900">Apakah laporan langsung diteruskan ke pihak lain?</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Tidak secara otomatis. Rencana tindak lanjut dibahas dengan Anda.
                    </p>
                </div>

                <div class="space-y-1">
                    <h3 class="text-sm font-semibold text-neutral-900">Bagaimana jika kode laporan hilang?</h3>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Laporan yang Anda buat dari akun tersimpan di dashboard, sehingga tetap dapat dibuka meski kode lupa. Kode dipakai untuk membuka laporan lewat halaman lacak tanpa login, jadi simpan dengan aman.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 6: MULAI LAPORAN (ABU-ABU #f2f2f2 PENUH DENGAN FORM PUTIH) --}}
    <section class="w-full bg-[#f2f2f2] py-16 sm:py-20 border-t border-neutral-200/70">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <div class="lg:col-span-5 space-y-3">
                <h2 class="text-2xl font-bold text-neutral-900 tracking-tight">Mulai laporan</h2>
                <p class="text-xs text-neutral-600 leading-relaxed max-w-sm">
                    Buat akun alias lebih dulu, lalu lanjutkan ke formulir laporan lengkap. Alias dibawa otomatis dari profil Anda.
                </p>
                <p class="text-xs text-neutral-500">
                    Tanpa nama asli. Mode anonim menyembunyikan identitas Anda dari konselor.
                </p>
            </div>

            <div class="lg:col-span-7">
                {{-- POST + session: kronologi tidak boleh masuk URL/riwayat peramban. --}}
                <form action="{{ route('laporan.draf') }}" method="POST" class="rounded-xl border border-neutral-200/80 bg-white p-6 sm:p-7 space-y-5">
                    @csrf
                    <p class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Isian awal</p>

                    <x-select
                        id="draf_kategori"
                        name="kategori"
                        label="Kategori kejadian"
                        placeholder="Pilih yang paling mendekati"
                        :options="[
                            'Siber' => 'Kekerasan digital',
                            'Seksual' => 'Kekerasan seksual',
                            'Psikis' => 'Kekerasan psikis',
                            'Fisik' => 'Kekerasan fisik',
                            'Ekonomi' => 'Kekerasan ekonomi',
                            'Lainnya' => 'Belum yakin / lainnya',
                        ]"
                    />

                    <x-textarea
                        id="draf_kronologi"
                        name="kronologi"
                        label="Kronologi awal"
                        rows="3"
                        placeholder="Tuliskan hal yang ingin Anda sampaikan…"
                        helper="Hindari nama asli dan alamat lengkap. Lampiran dapat ditambahkan di langkah berikutnya."
                    />

                    <x-button type="submit" variant="primary" class="w-full sm:w-auto">
                        {{ auth()->check() && ! auth()->user()->isPelapor() ? 'Buka dashboard' : 'Lanjutkan laporan' }}
                    </x-button>
                </form>
            </div>
        </div>
    </section>

</div>
@endsection
