<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'RuangAman — Layanan Pelaporan Kekerasan Berbasis Gender')</title>
    <meta name="description" content="RuangAman — Layanan pelaporan, konseling, dan penanganan kekerasan berbasis gender dengan aman dan terjaga.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-neutral-900 antialiased font-sans selection:bg-neutral-200" x-data="{
    quickExit() {
        sessionStorage.clear();
        localStorage.clear();
        window.location.replace('https://www.google.com');
    }
}" @keydown.escape.window="quickExit()">

<<<<<<< HEAD
    {{-- Top Emergency Banner (Figma Exact) --}}
    <div class="w-full bg-[#f2f2f2] border-b border-neutral-200 text-neutral-700 text-xs py-2 px-4 sm:px-6">
        <div class="mx-auto max-w-6xl flex items-center justify-center gap-2 text-center">
            <svg class="h-3.5 w-3.5 shrink-0 text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
            <span>Utamakan keselamatan. Gunakan perangkat yang aman; keluar cepat tidak menghapus riwayat peramban.</span>
        </div>
    </div>
=======
    <x-quick-exit />
    <x-confirm-modal />
>>>>>>> pr-altaf

    {{-- Navigation Header (Figma Exact) --}}
    <header class="w-full border-b border-neutral-200 bg-white sticky top-0 z-40">
        <div class="mx-auto max-w-6xl flex items-center justify-between px-4 sm:px-6 py-4">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-neutral-900 hover:opacity-90">
                RuangAman
            </a>

<<<<<<< HEAD
            {{-- Center Navigation Links --}}
            <nav class="hidden md:flex items-center gap-8 text-sm text-neutral-600 font-medium">
                <a href="{{ url('/#layanan') }}" class="hover:text-neutral-900 transition-colors">Layanan</a>
                <a href="{{ url('/#alur-pelaporan') }}" class="hover:text-neutral-900 transition-colors">Alur pelaporan</a>
                <a href="{{ route('lacak.form') }}" class="hover:text-neutral-900 transition-colors {{ request()->routeIs('lacak.*') ? 'text-neutral-900 font-semibold' : '' }}">Lacak laporan</a>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="flex items-center gap-1 hover:text-neutral-900 transition-colors">
                        <span>Bahasa Indonesia</span>
                    </button>
                </div>
=======
            <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex" aria-label="Navigasi utama">
                <a href="{{ url('/') }}" class="hover:text-brand-700">Beranda</a>
                <a href="{{ route('laporan.create') }}" class="hover:text-brand-700">Pelaporan</a>
                <a href="{{ route('lacak.form') }}" class="hover:text-brand-700">Pelacakan</a>
                <a href="{{ route('kegiatan.index') }}" class="hover:text-brand-700 {{ request()->routeIs('kegiatan.*') ? 'text-brand-700 font-semibold' : '' }}">Edukasi</a>
                <a href="#" class="hover:text-brand-700">Bantuan</a>

                @auth
                    @if (Auth::user()->isPelapor())
                        <a href="{{ route('pendaftaran-kegiatan.index') }}" class="hover:text-brand-700 {{ request()->routeIs('pendaftaran-kegiatan.*') ? 'text-brand-700 font-semibold underline underline-offset-4' : '' }}">Pendaftaran Saya</a>
                    @endif
                    @if (Auth::user()->isKonselor())
                        <a href="{{ route('konselor.profil.edit') }}" class="hover:text-brand-700 {{ request()->routeIs('konselor.profil.*') ? 'text-brand-700 font-semibold underline underline-offset-4' : '' }}">Profil Saya</a>
                    @endif
                @endauth

                @can('admin-kasus')
                    <div class="h-4 w-px bg-slate-300"></div>
                    <a href="{{ route('admin.status-kasus.index') }}" class="inline-flex items-center gap-1 font-semibold text-brand-700 hover:text-brand-800 {{ request()->routeIs('admin.status-kasus.*') ? 'text-brand-800 underline underline-offset-4' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Status Kasus</span>
                    </a>
                    <a href="{{ route('admin.penanganan-kasus.index') }}" class="inline-flex items-center gap-1 font-semibold text-brand-700 hover:text-brand-800 {{ request()->routeIs('admin.penanganan-kasus.*') ? 'text-brand-800 underline underline-offset-4' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Penugasan Kasus</span>
                    </a>
                    <a href="{{ route('admin.kegiatan-edukasi.index') }}" class="inline-flex items-center gap-1 font-semibold text-brand-700 hover:text-brand-800 {{ request()->routeIs('admin.kegiatan-edukasi.*') ? 'text-brand-800 underline underline-offset-4' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Kegiatan Edukasi</span>
                    </a>
                    <a href="{{ route('admin.konselor.index') }}" class="inline-flex items-center gap-1 font-semibold text-brand-700 hover:text-brand-800 {{ request()->routeIs('admin.konselor.*') ? 'text-brand-800 underline underline-offset-4' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Data Konselor</span>
                    </a>
                @endcan
>>>>>>> pr-altaf
            </nav>

            {{-- Right Actions: Auth Status & Quick Exit Button --}}
            <div class="flex items-center gap-4">
                @auth
                    <div class="hidden sm:flex items-center gap-3 text-xs text-neutral-700">
                        <span class="inline-flex items-center gap-1.5 font-medium">
                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                            {{ Auth::user()->nama_tampilan }} ({{ Auth::user()->role?->nama_role ?? 'User' }})
                        </span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-neutral-500 hover:text-red-600 transition-colors">
                                Keluar Akun
                            </button>
                        </form>
                    </div>
                @else
<<<<<<< HEAD
                    <div class="hidden sm:flex items-center gap-2 text-xs">
                        <a href="{{ route('login') }}" class="text-neutral-700 hover:text-neutral-900 font-medium px-2 py-1">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="rounded bg-neutral-900 hover:bg-neutral-800 text-white font-medium px-3 py-1.5 transition-colors">
                            Daftar
                        </a>
=======
                    <x-button href="{{ route('login') }}" variant="ghost" size="sm">Masuk</x-button>
                    <x-button href="{{ route('register') }}" variant="primary" size="sm">Daftar</x-button>
                @endauth
            </div>

            <button type="button" class="rounded-md p-2 text-slate-600 hover:bg-slate-100 md:hidden" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen.toString()" aria-label="Buka menu">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="border-t border-slate-100 bg-white md:hidden" x-cloak>
            <div class="space-y-1 px-4 py-3">
                <a href="{{ url('/') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Beranda</a>
                <a href="{{ route('laporan.create') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Pelaporan</a>
                <a href="{{ route('lacak.form') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Pelacakan</a>
                <a href="{{ route('kegiatan.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Edukasi</a>
                <a href="#" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Bantuan</a>

                @auth
                    @if (Auth::user()->isPelapor())
                        <a href="{{ route('pendaftaran-kegiatan.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Pendaftaran Saya</a>
                    @endif
                    @if (Auth::user()->isKonselor())
                        <a href="{{ route('konselor.profil.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Profil Saya</a>
                    @endif
                @endauth

                @can('admin-kasus')
                    <div class="border-t border-slate-100 pt-2 mt-2">
                        <div class="px-3 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider">Admin Kasus</div>
                        <a href="{{ route('admin.status-kasus.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Status Kasus</a>
                        <a href="{{ route('admin.penanganan-kasus.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Penugasan Kasus</a>
                        <a href="{{ route('admin.kegiatan-edukasi.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Kegiatan Edukasi</a>
                        <a href="{{ route('admin.konselor.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-50">Data Konselor</a>
                    </div>
                @endcan
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-2">
                        @csrf
                        <button type="submit" class="w-full text-left rounded-lg px-3 py-2 text-sm font-medium text-safety-rose hover:bg-red-50">Keluar</button>
                    </form>
                @else
                    <div class="flex flex-col gap-2 border-t border-slate-100 pt-3">
                        <x-button href="{{ route('login') }}" variant="ghost" size="sm">Masuk</x-button>
                        <x-button href="{{ route('register') }}" variant="primary" size="sm">Daftar</x-button>
>>>>>>> pr-altaf
                    </div>
                @endauth

                {{-- Tombol Darurat Keluar Cepat (Figma Exact) --}}
                <button type="button" @click="quickExit()" class="inline-flex items-center gap-1.5 rounded bg-[#f2f2f2] hover:bg-neutral-200 text-neutral-800 text-xs font-medium px-3.5 py-1.5 transition-colors border border-neutral-300/70" title="Keluar seketika ke Google (shortcut: tombol Esc)">
                    <span>Keluar Cepat</span>
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    {{-- Main Content Slot --}}
    <main id="main-content" class="w-full">
        @if (session('success'))
            <div class="mx-auto max-w-6xl px-4 sm:px-6 pt-4">
                <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm p-4">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="mx-auto max-w-6xl px-4 sm:px-6 pt-4">
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm p-4">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer (Figma Exact) --}}
    <footer class="w-full border-t border-neutral-200 bg-white py-12">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <p class="text-base font-bold text-neutral-900">RuangAman</p>
                <p class="text-xs text-neutral-500 mt-1">Pelaporan dan pendampingan kekerasan berbasis gender.</p>
            </div>
            <div class="text-left sm:text-right text-xs text-neutral-500 space-y-1">
                <p>Pratinjau layanan · Data contoh</p>
                <p class="text-neutral-400">
                    <a href="#" class="hover:underline">Kebijakan privasi</a> / 
                    <a href="#" class="hover:underline">Ketentuan layanan</a>
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
