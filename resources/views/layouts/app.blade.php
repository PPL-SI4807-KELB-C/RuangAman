<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'RuangAman — Layanan Pelaporan Kekerasan Berbasis Gender')</title>
    <meta name="description" content="RuangAman — Layanan pelaporan, konseling, dan penanganan kekerasan berbasis gender dengan aman dan terjaga.">

    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#15803d">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-neutral-900 antialiased font-sans selection:bg-neutral-200" x-data="quickExit('{{ route('logout') }}')" @keydown.escape.window="handleEscape()">

    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded focus:bg-neutral-900 focus:px-4 focus:py-2 focus:text-xs focus:font-medium focus:text-white">Lompat ke konten utama</a>

    {{-- Banner keselamatan + kontak darurat --}}
    <x-safety-banner />

    {{-- Modal konfirmasi global: dipakai semua tombol hapus/nonaktifkan di aplikasi --}}
    <x-confirm-modal />

    {{-- Navigation Header (Figma Exact) --}}
    <header class="w-full border-b border-neutral-200 bg-white sticky top-0 z-40" x-data="{ mobileMenuOpen: false }">
        <div class="mx-auto max-w-6xl flex items-center justify-between gap-6 px-4 sm:px-6 py-4">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="shrink-0 whitespace-nowrap rounded hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2" aria-label="RuangAman — beranda">
                <x-logo />
            </a>

            {{-- Center Navigation Links --}}
            <nav class="hidden xl:flex items-center gap-4 text-sm font-medium text-neutral-600" aria-label="Navigasi utama">
                <a href="{{ url('/') }}" class="whitespace-nowrap hover:text-neutral-900 transition-colors">Beranda</a>
                <a href="{{ url('/#layanan') }}" class="whitespace-nowrap hover:text-neutral-900 transition-colors">Layanan</a>
                @auth
                    @if (Auth::user()->isPelapor())
                        <a href="{{ route('laporan.create') }}" class="whitespace-nowrap hover:text-neutral-900 transition-colors {{ request()->routeIs('laporan.*') ? 'text-neutral-900 font-semibold' : '' }}">Pelaporan</a>
                    @endif
                @endauth
                <a href="{{ route('lacak.form') }}" class="whitespace-nowrap hover:text-neutral-900 transition-colors {{ request()->routeIs('lacak.*') ? 'text-neutral-900 font-semibold' : '' }}">Lacak laporan</a>
                <a href="{{ route('artikel.index') }}" class="whitespace-nowrap hover:text-neutral-900 transition-colors {{ request()->routeIs('artikel.*') ? 'text-neutral-900 font-semibold' : '' }}">Artikel</a>
                <a href="{{ route('kegiatan.index') }}" class="whitespace-nowrap hover:text-neutral-900 transition-colors {{ request()->routeIs('kegiatan.*') ? 'text-neutral-900 font-semibold' : '' }}">Kegiatan</a>
                <a href="{{ route('instansi.publik') }}" class="whitespace-nowrap hover:text-neutral-900 transition-colors {{ request()->routeIs('instansi.publik') ? 'text-neutral-900 font-semibold' : '' }}">Instansi bantuan</a>
                <a href="{{ route('glosarium.index') }}" class="whitespace-nowrap hover:text-neutral-900 transition-colors {{ request()->routeIs('glosarium.*') ? 'text-neutral-900 font-semibold' : '' }}">Glosarium</a>
                <a href="{{ route('pengumuman.index') }}" class="whitespace-nowrap hover:text-neutral-900 transition-colors {{ request()->routeIs('pengumuman.*') ? 'text-neutral-900 font-semibold' : '' }}">Pengumuman</a>

                @can('admin-kasus')
                    <div class="h-4 w-px shrink-0 bg-neutral-300"></div>
                    <div class="relative shrink-0" x-data="{ adminMenuOpen: false }" @click.outside="adminMenuOpen = false">
                        <button type="button" @click="adminMenuOpen = !adminMenuOpen" :aria-expanded="adminMenuOpen.toString()"
                            class="inline-flex items-center gap-1.5 whitespace-nowrap font-semibold text-neutral-900 transition-colors hover:text-neutral-700 {{ request()->routeIs('admin.*') || request()->routeIs('instansi.*') ? 'underline underline-offset-4' : '' }}">
                            <span>Ruang Admin</span>
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="adminMenuOpen" x-cloak x-transition
                            class="absolute right-0 z-50 mt-2 w-64 rounded-xl border border-neutral-200/70 bg-white p-1.5 shadow-lg">
                            <p class="px-3 pb-1 pt-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-600">Penanganan Kasus</p>
                            <a href="{{ route('admin.status-kasus.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('admin.status-kasus.*') ? 'bg-neutral-50 text-neutral-900' : '' }}">Status Kasus</a>
                            <a href="{{ route('admin.penanganan-kasus.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('admin.penanganan-kasus.*') ? 'bg-neutral-50 text-neutral-900' : '' }}">Penugasan Kasus</a>
                            <a href="{{ route('admin.kegiatan-edukasi.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('admin.kegiatan-edukasi.*') ? 'bg-neutral-50 text-neutral-900' : '' }}">Kegiatan Edukasi</a>
                            <a href="{{ route('admin.konselor.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('admin.konselor.*') ? 'bg-neutral-50 text-neutral-900' : '' }}">Data Konselor</a>
                            <a href="{{ route('instansi.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('instansi.*') && !request()->routeIs('instansi.publik') || request()->routeIs('admin.instansi.*') ? 'bg-neutral-50 text-neutral-900' : '' }}">Instansi Bantuan</a>
                            <div class="my-1.5 border-t border-neutral-200/70"></div>
                            <p class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-neutral-600">Tata Kelola Konten</p>
                            <a href="{{ route('admin.artikel.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('admin.artikel.*') ? 'bg-neutral-50 text-neutral-900' : '' }}">Artikel Edukasi</a>
                            <a href="{{ route('admin.halaman-informasi.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('admin.halaman-informasi.*') ? 'bg-neutral-50 text-neutral-900' : '' }}">Halaman Informasi</a>
                            <a href="{{ route('admin.glosarium.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('admin.glosarium.*') ? 'bg-neutral-50 text-neutral-900' : '' }}">Glosarium KBG</a>
                            <a href="{{ route('admin.pengumuman.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('admin.pengumuman.*') ? 'bg-neutral-50 text-neutral-900' : '' }}">Pengumuman</a>
                        </div>
                    </div>
                @endcan
            </nav>

            {{-- Right Actions: Auth Status & Quick Exit Button --}}
            <div class="flex shrink-0 items-center gap-4">
                @auth
                    <div class="relative hidden xl:block" x-data="{ akunMenuOpen: false }" @click.outside="akunMenuOpen = false">
                        <button type="button" @click="akunMenuOpen = !akunMenuOpen" :aria-expanded="akunMenuOpen.toString()" aria-label="Menu akun"
                            class="inline-flex items-center gap-2 whitespace-nowrap text-xs font-medium text-neutral-700 transition-colors hover:text-neutral-900">
                            <span class="h-2 w-2 shrink-0 rounded-full bg-brand-600"></span>
                            <span>Akun</span>
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="akunMenuOpen" x-cloak x-transition
                            class="absolute right-0 z-50 mt-2 w-60 rounded-xl border border-neutral-200/70 bg-white p-1.5 shadow-lg">
                            <p class="truncate px-3 pb-1 pt-2 text-sm font-semibold text-neutral-900">{{ Auth::user()->nama_tampilan }}</p>
                            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-600">{{ Auth::user()->role?->nama_role ?? 'User' }}</p>
                            <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('dashboard') ? 'bg-neutral-50 text-neutral-900' : '' }}">Dashboard Saya</a>
                            @if (Auth::user()->isPelapor())
                                <a href="{{ route('laporan.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('laporan.index', 'laporan.show', 'laporan.edit') ? 'bg-neutral-50 text-neutral-900' : '' }}">Laporan Saya</a>
                                <a href="{{ route('pendaftaran-kegiatan.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Pendaftaran Saya</a>
                                <a href="{{ route('profil-pelapor.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50 {{ request()->routeIs('profil-pelapor.*') ? 'bg-neutral-50 text-neutral-900' : '' }}">Profil &amp; mode anonim</a>
                            @endif
                            @if (Auth::user()->isKonselor())
                                <a href="{{ route('konselor.profil.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Profil Saya</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-neutral-200/70 pt-1.5">
                                @csrf
                                <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-safety-rose transition-colors hover:bg-neutral-50">Keluar Akun</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-2 text-xs">
                        <a href="{{ route('login') }}" class="text-neutral-700 hover:text-neutral-900 font-medium px-2 py-1">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="rounded bg-neutral-900 hover:bg-neutral-800 text-white font-medium px-3 py-1.5 transition-colors">
                            Daftar
                        </a>
                    </div>
                @endauth

                {{-- Tombol Darurat Keluar Cepat (FR-28) --}}
                <x-quick-exit />

                {{-- Mobile Menu Toggle --}}
                <button type="button" class="rounded-md p-2 text-neutral-600 hover:bg-neutral-100 xl:hidden" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()" aria-label="Buka menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="border-t border-neutral-100 bg-white xl:hidden" x-cloak>
            <div class="space-y-1 px-4 py-3">
                <a href="{{ url('/') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Beranda</a>
                <a href="{{ url('/#layanan') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Layanan</a>
                <a href="{{ route('lacak.form') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Lacak laporan</a>
                <a href="{{ route('artikel.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Artikel</a>
                <a href="{{ route('kegiatan.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Kegiatan</a>
                <a href="{{ route('instansi.publik') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Instansi bantuan</a>
                <a href="{{ route('glosarium.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Glosarium</a>
                <a href="{{ route('pengumuman.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Pengumuman</a>

                @auth
                    <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-900 hover:bg-neutral-50">Dashboard Saya</a>
                    @if (Auth::user()->isPelapor())
                        <a href="{{ route('laporan.create') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Pelaporan</a>
                        <a href="{{ route('laporan.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-900 hover:bg-neutral-50">Laporan Saya</a>
                        <a href="{{ route('pendaftaran-kegiatan.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-900 hover:bg-neutral-50">Pendaftaran Saya</a>
                        <a href="{{ route('profil-pelapor.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-900 hover:bg-neutral-50">Profil &amp; mode anonim</a>
                    @endif
                    @if (Auth::user()->isKonselor())
                        <a href="{{ route('konselor.profil.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-900 hover:bg-neutral-50">Profil Saya</a>
                    @endif
                @endauth

                @can('admin-kasus')
                    <div class="mt-2 border-t border-neutral-200/70 pt-2">
                        <p class="px-3 pb-1 pt-1 text-[11px] font-semibold uppercase tracking-wider text-neutral-600">Penanganan Kasus</p>
                        <a href="{{ route('admin.status-kasus.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Status Kasus</a>
                        <a href="{{ route('admin.penanganan-kasus.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Penugasan Kasus</a>
                        <a href="{{ route('admin.kegiatan-edukasi.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Kegiatan Edukasi</a>
                        <a href="{{ route('admin.konselor.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Data Konselor</a>
                        <a href="{{ route('instansi.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Instansi Bantuan</a>
                        <p class="px-3 pb-1 pt-2 text-[11px] font-semibold uppercase tracking-wider text-neutral-600">Tata Kelola Konten</p>
                        <a href="{{ route('admin.artikel.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Artikel Edukasi</a>
                        <a href="{{ route('admin.halaman-informasi.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Halaman Informasi</a>
                        <a href="{{ route('admin.glosarium.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Glosarium KBG</a>
                        <a href="{{ route('admin.pengumuman.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Pengumuman</a>
                    </div>
                @endcan

                @auth
                    <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-neutral-200/70 pt-2">
                        @csrf
                        <button type="submit" class="w-full text-left rounded-lg px-3 py-2 text-sm font-medium text-safety-rose hover:bg-neutral-50">Keluar</button>
                    </form>
                @else
                    <div class="flex flex-col gap-2 border-t border-neutral-200/70 pt-3">
                        <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">Masuk</a>
                        <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2 text-sm font-medium bg-neutral-900 text-white hover:bg-neutral-800 text-center">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    {{-- Main Content Slot --}}
    <main id="main-content" class="w-full">
        @if (session('success'))
            <div class="mx-auto max-w-6xl px-4 sm:px-6 pt-6">
                <x-alert variant="success" role="status">{{ session('success') }}</x-alert>
            </div>
        @endif
        @if (session('error'))
            <div class="mx-auto max-w-6xl px-4 sm:px-6 pt-6">
                <x-alert variant="danger">{{ session('error') }}</x-alert>
            </div>
        @endif
        @if (session('status'))
            <div class="mx-auto max-w-6xl px-4 sm:px-6 pt-6">
                <x-alert variant="info" role="status">{{ session('status') }}</x-alert>
            </div>
        @endif

        @yield('content')
        {{ $slot ?? '' }}
    </main>

    {{-- Footer (Figma Exact) --}}
    <footer class="w-full border-t border-neutral-200 bg-white py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <x-logo size="sm" />
                <p class="text-xs text-neutral-600 mt-1">Pelaporan dan pendampingan kekerasan berbasis gender.</p>
            </div>
            <nav class="flex flex-wrap gap-x-5 gap-y-2 text-xs text-neutral-600" aria-label="Tautan informasi">
                <a href="{{ route('halaman.show', 'tentang-ruangaman') }}" class="hover:text-neutral-900 hover:underline">Tentang</a>
                <a href="{{ route('halaman.show', 'panduan-menggunakan-layanan') }}" class="hover:text-neutral-900 hover:underline">Panduan</a>
                <a href="{{ route('halaman.show', 'kebijakan-privasi') }}" class="hover:text-neutral-900 hover:underline">Kebijakan privasi</a>
                <a href="{{ route('instansi.publik') }}" class="hover:text-neutral-900 hover:underline">Instansi bantuan</a>
            </nav>
        </div>
    </footer>

</body>
</html>
