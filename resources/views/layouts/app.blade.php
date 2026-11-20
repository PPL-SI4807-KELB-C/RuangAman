@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? $__env->yieldContent('title', 'RuangAman — Layanan Pelaporan Kekerasan Berbasis Gender') }}</title>
    <meta name="description" content="RuangAman — Layanan pelaporan, konseling, dan penanganan kekerasan berbasis gender dengan aman dan terjaga.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased font-sans" x-data="{
    mobileMenuOpen: false,
    quickExit() {
        sessionStorage.clear();
        localStorage.clear();
        window.location.replace('https://www.google.com');
    }
}" @keydown.escape.window="quickExit()">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:bg-brand-600 focus:text-white focus:px-3 focus:py-2 focus:rounded">
        Langsung ke konten utama
    </a>

    <x-quick-exit />
    <x-confirm-modal />

    {{-- Top Emergency Banner --}}
    <div class="w-full border-b border-slate-200 bg-slate-100 px-4 py-2 text-xs text-slate-700 sm:px-6">
        <div class="mx-auto flex max-w-7xl items-center justify-center gap-2 text-center">
            <svg class="h-3.5 w-3.5 shrink-0 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
            <span>Utamakan keselamatan. Gunakan perangkat yang aman; keluar cepat tidak menghapus riwayat peramban.</span>
        </div>
    </div>

    {{-- Header Navigasi (Grid 12 Kolom Terkunci) --}}
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 md:grid md:grid-cols-12 md:gap-2 lg:px-8">
            {{-- Kolom Kiri: Logo (2 kolom) --}}
            <div class="flex items-center md:col-span-2">
                <a href="{{ url('/') }}" class="flex items-center gap-2 text-brand-700 hover:text-brand-800" aria-label="RuangAman Beranda">
                    <svg class="h-8 w-8 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 3l7 4v6c0 5.55-3.84 10.74-9 12-5.16-1.26-9-6.45-9-12V7l7-4z" />
                        <path d="M9 12l2 2 4-4" />
                    </svg>
                    <span class="text-lg font-bold tracking-tight">RuangAman</span>
                </a>
            </div>

            {{-- Kolom Tengah: Navigasi Utama Terkunci Mati di Tengah (8 kolom) --}}
            <nav class="hidden flex-wrap items-center justify-center gap-4 text-sm font-medium text-slate-600 md:col-span-8 md:flex lg:gap-5" aria-label="Navigasi utama">
                <a href="{{ url('/') }}" class="whitespace-nowrap transition-colors hover:text-brand-700">Beranda</a>
                <a href="{{ url('/#layanan') }}" class="whitespace-nowrap transition-colors hover:text-brand-700">Layanan</a>
                <a href="{{ route('laporan.create') }}" class="whitespace-nowrap transition-colors hover:text-brand-700">Pelaporan</a>
                <a href="{{ route('lacak.form') }}" class="whitespace-nowrap transition-colors hover:text-brand-700 {{ request()->routeIs('lacak.*') ? 'font-semibold text-brand-700' : '' }}">Lacak Laporan</a>
                <a href="{{ route('artikel.index') }}" class="whitespace-nowrap transition-colors hover:text-brand-700 {{ request()->routeIs('artikel.*') ? 'font-semibold text-brand-700' : '' }}">Artikel</a>
                <a href="{{ route('kegiatan.index') }}" class="whitespace-nowrap transition-colors hover:text-brand-700 {{ request()->routeIs('kegiatan.*') ? 'font-semibold text-brand-700' : '' }}">Kegiatan</a>
                <a href="{{ route('instansi.publik') }}" class="whitespace-nowrap transition-colors hover:text-brand-700 {{ request()->routeIs('instansi.publik') ? 'font-semibold text-brand-700' : '' }}">Instansi Bantuan</a>

                @auth
                    @if (Auth::user()->isPelapor())
                        <a href="{{ route('laporan.index') }}" class="whitespace-nowrap transition-colors hover:text-brand-700 {{ request()->routeIs('laporan.index', 'laporan.show', 'laporan.edit') ? 'font-semibold text-brand-700 underline underline-offset-4' : '' }}">Laporan Saya</a>
                        <a href="{{ route('pendaftaran-kegiatan.index') }}" class="whitespace-nowrap transition-colors hover:text-brand-700 {{ request()->routeIs('pendaftaran-kegiatan.*') ? 'font-semibold text-brand-700 underline underline-offset-4' : '' }}">Pendaftaran Saya</a>
                        <a href="{{ route('profil-pelapor.edit') }}" class="whitespace-nowrap transition-colors hover:text-brand-700 {{ request()->routeIs('profil-pelapor.*') ? 'font-semibold text-brand-700 underline underline-offset-4' : '' }}">Profil</a>
                    @endif
                    @if (Auth::user()->isKonselor())
                        <a href="{{ route('konselor.profil.edit') }}" class="whitespace-nowrap transition-colors hover:text-brand-700 {{ request()->routeIs('konselor.profil.*') ? 'font-semibold text-brand-700 underline underline-offset-4' : '' }}">Profil Saya</a>
                    @endif
                @endauth

                @can('admin-kasus')
                    <div class="h-4 w-px bg-slate-300"></div>
                    <a href="{{ route('admin.status-kasus.index') }}" class="inline-flex items-center gap-1 whitespace-nowrap font-semibold text-brand-700 hover:text-brand-800 {{ request()->routeIs('admin.status-kasus.*') ? 'text-brand-800 underline underline-offset-4' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Status Kasus</span>
                    </a>
                    <a href="{{ route('admin.penanganan-kasus.index') }}" class="inline-flex items-center gap-1 whitespace-nowrap font-semibold text-brand-700 hover:text-brand-800 {{ request()->routeIs('admin.penanganan-kasus.*') ? 'text-brand-800 underline underline-offset-4' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Penugasan Kasus</span>
                    </a>
                    <a href="{{ route('admin.kegiatan-edukasi.index') }}" class="inline-flex items-center gap-1 whitespace-nowrap font-semibold text-brand-700 hover:text-brand-800 {{ request()->routeIs('admin.kegiatan-edukasi.*') ? 'text-brand-800 underline underline-offset-4' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Kegiatan Edukasi</span>
                    </a>
                    <a href="{{ route('admin.artikel.index') }}" class="inline-flex items-center gap-1 whitespace-nowrap font-semibold text-brand-700 hover:text-brand-800 {{ request()->routeIs('admin.artikel.*') ? 'text-brand-800 underline underline-offset-4' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
                        </svg>
                        <span>Artikel Edukasi</span>
                    </a>
                    <a href="{{ route('admin.konselor.index') }}" class="inline-flex items-center gap-1 whitespace-nowrap font-semibold text-brand-700 hover:text-brand-800 {{ request()->routeIs('admin.konselor.*') ? 'text-brand-800 underline underline-offset-4' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Data Konselor</span>
                    </a>
                    <a href="{{ route('instansi.index') }}" class="inline-flex items-center gap-1 whitespace-nowrap font-semibold text-brand-700 hover:text-brand-800 {{ (request()->routeIs('instansi.*') && !request()->routeIs('instansi.publik')) || request()->routeIs('admin.instansi.*') ? 'text-brand-800 underline underline-offset-4' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Instansi Bantuan</span>
                    </a>
                @endcan
            </nav>

            {{-- Kolom Kanan: Nama Alias/Anonim & Tombol Keluar (2 kolom) --}}
            <div class="hidden items-center justify-end gap-3 md:col-span-2 md:flex">
                @auth
                    @if (Auth::user()->isPelapor())
                        @php
                            $profilPelapor = Auth::user()->profilPelapor;
                            $isAnonim = $profilPelapor?->preferensi_anonim ?? false;
                            $realName = $profilPelapor?->alias ?? Auth::user()->nama_tampilan;
                        @endphp
                        <a href="{{ route('profil-pelapor.edit') }}"
                           id="navbar-user-name"
                           data-real-name="{{ $realName }}"
                           title="Kelola Profil Alias & Mode Anonim"
                           class="inline-block w-28 truncate text-right text-sm font-medium text-slate-600 transition hover:text-brand-700 hover:underline">
                            {{ $isAnonim ? 'Anonim' : $realName }}
                        </a>
                    @else
                        <span class="truncate text-sm text-slate-500">{{ Auth::user()->nama_tampilan }}</span>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <x-button type="submit" variant="secondary" size="sm">Keluar</x-button>
                    </form>
                @else
                    <x-button href="{{ route('login') }}" variant="ghost" size="sm">Masuk</x-button>
                    <x-button href="{{ route('register') }}" variant="primary" size="sm">Daftar</x-button>
                @endauth
            </div>

            {{-- Mobile Menu Toggle --}}
            <button type="button" class="rounded-md p-2 text-slate-600 hover:bg-slate-100 md:hidden" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()" aria-label="Buka menu">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="border-t border-slate-100 bg-white md:hidden" x-cloak>
            <div class="space-y-1 px-4 py-3">
                <a href="{{ url('/') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Beranda</a>
                <a href="{{ url('/#layanan') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Layanan</a>
                <a href="{{ route('laporan.create') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Pelaporan</a>
                <a href="{{ route('lacak.form') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Lacak Laporan</a>
                <a href="{{ route('artikel.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Artikel</a>
                <a href="{{ route('kegiatan.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Kegiatan</a>
                <a href="{{ route('instansi.publik') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Instansi Bantuan</a>

                @auth
                    @if (Auth::user()->isPelapor())
                        <a href="{{ route('laporan.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Laporan Saya</a>
                        <a href="{{ route('pendaftaran-kegiatan.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Pendaftaran Saya</a>
                        <a href="{{ route('profil-pelapor.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Profil</a>
                    @endif
                    @if (Auth::user()->isKonselor())
                        <a href="{{ route('konselor.profil.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Profil Saya</a>
                    @endif
                @endauth

                @can('admin-kasus')
                    <div class="mt-2 border-t border-slate-100 pt-2">
                        <div class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Admin Kasus</div>
                        <a href="{{ route('admin.status-kasus.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Status Kasus</a>
                        <a href="{{ route('admin.penanganan-kasus.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Penugasan Kasus</a>
                        <a href="{{ route('admin.kegiatan-edukasi.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Kegiatan Edukasi</a>
                        <a href="{{ route('admin.artikel.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Artikel Edukasi</a>
                        <a href="{{ route('admin.konselor.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Data Konselor</a>
                        <a href="{{ route('instansi.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Instansi Bantuan</a>
                    </div>
                @endcan

                @auth
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-2">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-safety-rose hover:bg-red-50">Keluar</button>
                    </form>
                @else
                    <div class="flex flex-col gap-2 border-t border-slate-100 pt-3">
                        <x-button href="{{ route('login') }}" variant="ghost" size="sm">Masuk</x-button>
                        <x-button href="{{ route('register') }}" variant="primary" size="sm">Daftar</x-button>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    {{-- Main Content Slot --}}
    <main id="main-content" class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @if (session('success'))
            <x-alert variant="success" class="mb-4">
                {{ session('success') }}
            </x-alert>
        @endif
        @if (session('error'))
            <x-alert variant="danger" class="mb-4">
                {{ session('error') }}
            </x-alert>
        @endif
        @if (session('status'))
            <x-alert variant="info" class="mb-4">
                {{ session('status') }}
            </x-alert>
        @endif

        @yield('content')
        {{ $slot ?? '' }}
    </main>

    {{-- Footer --}}
    <footer class="mt-auto border-t border-slate-200 bg-white py-8">
        <div class="mx-auto max-w-7xl px-4 text-center text-sm text-slate-500 sm:px-6 lg:px-8">
            <p class="font-medium text-slate-700">RuangAman</p>
            <p class="mt-1">Pelaporan aman kekerasan berbasis gender sesuai UU TPKS & UU PDP.</p>
            <p class="mt-3 text-xs">&copy; {{ date('Y') }} RuangAman. Dibangun oleh PPL-SI4807-KELB-C.</p>
        </div>
    </footer>
</body>
</html>