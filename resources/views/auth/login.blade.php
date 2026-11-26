@extends('layouts.guest')

@section('title', 'Masuk — RuangAman')

@section('content')
<div class="mx-auto max-w-md space-y-6">

    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-neutral-900">Masuk ke RuangAman</h1>
        <p class="mt-1.5 text-xs leading-relaxed text-neutral-600">
            Gunakan email terdaftar Anda (Pelapor, Konselor, atau Admin Kasus).
        </p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <x-input
                name="email"
                type="email"
                label="Email"
                placeholder="nama@email.com"
                autocomplete="email"
                :error="$errors->first('email')"
                autofocus
                required
            />

            <x-input
                name="password"
                type="password"
                label="Kata sandi"
                autocomplete="current-password"
                :error="$errors->first('password')"
                required
            />

            <x-checkbox name="remember">Ingat saya di perangkat ini</x-checkbox>

            <x-button type="submit" variant="primary" class="w-full">Masuk</x-button>
        </form>
    </x-card>

    <div class="space-y-2 text-center text-xs text-neutral-600">
        <p>
            Belum punya akun pelapor?
            <a href="{{ route('register') }}" class="font-semibold text-neutral-900 hover:underline">Daftar dengan alias</a>
        </p>
        <p>
            Sudah punya kode lacak?
            <a href="{{ route('lacak.form') }}" class="font-semibold text-neutral-900 hover:underline">Lacak status laporan</a>
        </p>
    </div>

    @if (app()->environment('local', 'testing'))
        <x-alert variant="info" title="Akun uji coba (seeder, hanya lingkungan lokal)">
            <ul class="space-y-0.5">
                <li><strong>Admin:</strong> <code>admin@ruangaman.test</code></li>
                <li><strong>Konselor:</strong> <code>konselor@ruangaman.test</code></li>
                <li><strong>Pelapor:</strong> <code>pelapor@ruangaman.test</code></li>
                <li>Kata sandi semua akun: <code>password</code></li>
            </ul>
        </x-alert>
    @endif

</div>
@endsection
