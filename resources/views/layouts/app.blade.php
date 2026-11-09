<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="RuangAman — Pelaporan, konseling, dan penanganan kekerasan berbasis gender dengan aman dan anonim.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased" x-data="{ mobileMenuOpen: false }">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:bg-brand-600 focus:text-white focus:px-3 focus:py-2 focus:rounded">
        Langsung ke konten utama
    </a>

    <x-quick-exit />

    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-brand-700 hover:text-brand-800" aria-label="RuangAman Beranda">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 3l7 4v6c0 5.55-3.84 10.74-9 12-5.16-1.26-9-6.45-9-12V7l7-4z" />
                    <path d="M9 12l2 2 4-4" />
                </svg>
                <span class="text-lg font-bold tracking-tight">RuangAman</span>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex" aria-label="Navigasi utama">
                <a href="{{ url('/') }}" class="hover:text-brand-700">Beranda</a>
                <a href="{{ route('laporan.create') }}" class="hover:text-brand-700">Pelaporan</a>
                <a href="{{ route('lacak.form') }}" class="hover:text-brand-700">Pelacakan</a>
                <a href="#" class="hover:text-brand-700">Edukasi</a>
                @auth
                    <a href="{{ route('instansi.index') }}" class="hover:text-brand-700">Instansi Bantuan</a>
                @endauth
            </nav>

            <div class="hidden items-center gap-3 md:flex">
                @auth
                    <span class="text-sm text-slate-500">{{ Auth::user()->nama_tampilan }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-button type="submit" variant="secondary" size="sm">Keluar</x-button>
                    </form>
                @else
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
                    <a href="#" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Edukasi</a>
                    @auth
                        <a href="{{ route('instansi.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Instansi Bantuan</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-2">
                            @csrf
                            <button type="submit" class="w-full text-left rounded-lg px-3 py-2 text-sm font-medium text-safety-rose hover:bg-red-50">Keluar</button>
                        </form>
                    @else
                        <div class="flex flex-col gap-2 border-t border-slate-100 pt-3">
                            <x-button href="{{ route('login') }}" variant="ghost" size="sm">Masuk</x-button>
                            <x-button href="{{ route('register') }}" variant="primary" size="sm">Daftar</x-button>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

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
    </main>

    <footer class="mt-auto border-t border-slate-200 bg-white py-8">
        <div class="mx-auto max-w-7xl px-4 text-center text-sm text-slate-500 sm:px-6 lg:px-8">
            <p class="font-medium text-slate-700">RuangAman</p>
            <p class="mt-1">Pelaporan aman kekerasan berbasis gender sesuai UU TPKS & UU PDP.</p>
            <p class="mt-3 text-xs">&copy; {{ date('Y') }} RuangAman. Dibangun oleh PPL-SI4807-KELB-C.</p>
        </div>
    </footer>
</body>
</html>
