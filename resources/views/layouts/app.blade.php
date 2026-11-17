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

    {{-- Navigation Header (Figma Exact) --}}
    <header class="w-full border-b border-neutral-200 bg-white sticky top-0 z-40">
        <div class="mx-auto max-w-6xl flex items-center justify-between px-4 sm:px-6 py-4">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-neutral-900 hover:opacity-90">
                RuangAman
            </a>

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
            </nav>

            {{-- Right Quick Exit Button (Figma Exact) --}}
            <div class="flex items-center gap-3">
                <button type="button" @click="quickExit()" class="inline-flex items-center gap-1.5 rounded bg-[#f2f2f2] hover:bg-neutral-200 text-neutral-800 text-sm font-medium px-3.5 py-1.5 transition-colors border border-neutral-300/70" title="Keluar seketika (shortcut: tombol Esc)">
                    <span>Keluar</span>
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
    <footer class="w-full border-t border-neutral-200 bg-white py-12 mt-20">
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
