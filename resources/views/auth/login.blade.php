@extends('layouts.guest')

@section('title', $title)

@section('content')
    <x-card>
        <div class="text-center">
            <h1 class="text-xl font-bold text-slate-900">Masuk ke RuangAman</h1>
            <p class="mt-1 text-sm text-slate-500">Gunakan akun pelapor Anda untuk melanjutkan.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf
            <x-input name="email" type="email" label="Email" placeholder="nama@email.com" :error="$errors->first('email')" required />
            <x-input name="password" type="password" label="Kata Sandi" :error="$errors->first('password')" required />

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    Ingat saya
                </label>
            </div>

            <x-button type="submit" variant="primary" class="w-full">Masuk</x-button>
        </form>

        <p class="mt-4 text-center text-sm text-slate-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-medium text-brand-700 hover:underline">Daftar dengan alias</a>
        </p>
    </x-card>
@endsection
