<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <x-quick-exit />

    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-6 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-brand-700" aria-label="RuangAman Beranda">
                <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 3l7 4v6c0 5.55-3.84 10.74-9 12-5.16-1.26-9-6.45-9-12V7l7-4z" />
                    <path d="M9 12l2 2 4-4" />
                </svg>
                <span class="text-2xl font-bold tracking-tight">RuangAman</span>
            </a>
        </div>

        @if (session('success'))
            <x-alert variant="success" class="mb-4 w-full max-w-md">
                {{ session('success') }}
            </x-alert>
        @endif
        @if (session('error'))
            <x-alert variant="danger" class="mb-4 w-full max-w-md">
                {{ session('error') }}
            </x-alert>
        @endif

        <div class="w-full max-w-md">
            @yield('content')
        </div>
    </div>
</body>
</html>
