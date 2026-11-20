<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'RuangAman — Masuk / Daftar')</title>

    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#15803d">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white text-neutral-900 antialiased font-sans selection:bg-neutral-200" x-data="quickExit('{{ route('logout') }}')" @keydown.escape.window="handleEscape()">

    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded focus:bg-neutral-900 focus:px-4 focus:py-2 focus:text-xs focus:font-medium focus:text-white">Lompat ke konten utama</a>

    {{-- Banner keselamatan + kontak darurat --}}
    <x-safety-banner />

    {{-- Header ringkas --}}
    <header class="w-full border-b border-neutral-200 bg-white">
        <div class="mx-auto max-w-6xl flex items-center justify-between px-4 sm:px-6 py-4">
            <a href="{{ url('/') }}" class="rounded hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2" aria-label="RuangAman — beranda">
                <x-logo />
            </a>

            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}" class="hidden text-xs text-neutral-600 hover:text-neutral-900 sm:inline">
                    &larr; Kembali ke beranda
                </a>
                <x-quick-exit />
            </div>
        </div>
    </header>

    {{-- Konten --}}
    <main id="main-content" class="w-full flex-1 py-12 px-4 sm:px-6">
        @if (session('success'))
            <div class="mx-auto max-w-md mb-6">
                <x-alert variant="success" role="status">{{ session('success') }}</x-alert>
            </div>
        @endif
        @if (session('error'))
            <div class="mx-auto max-w-md mb-6">
                <x-alert variant="danger">{{ session('error') }}</x-alert>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="w-full border-t border-neutral-200 bg-white py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs text-neutral-600">
            <p>RuangAman · Layanan pelaporan dan pendampingan kekerasan berbasis gender.</p>
            <a href="{{ route('halaman.show', 'kebijakan-privasi') }}" class="hover:text-neutral-900 hover:underline">Kebijakan privasi</a>
        </div>
    </footer>

</body>
</html>
