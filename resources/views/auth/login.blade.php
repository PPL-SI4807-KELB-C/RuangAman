@extends('layouts.guest')

@section('title', 'Masuk — RuangAman')

@section('content')
<div class="mx-auto max-w-md space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Masuk ke RuangAman</h1>
        <p class="text-xs text-neutral-500 mt-1">
            Gunakan email terdaftar Anda (Pelapor, Konselor, atau Admin Kasus).
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div class="space-y-1">
            <label class="block text-xs font-semibold text-neutral-800">Email *</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com" class="w-full rounded bg-[#f2f2f2] border-0 text-xs px-3.5 py-2.5 text-neutral-800 focus:ring-1 focus:ring-neutral-400">
            @error('email')
                <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-semibold text-neutral-800">Kata Sandi *</label>
            </div>
            <input type="password" name="password" required placeholder="••••••••" class="w-full rounded bg-[#f2f2f2] border-0 text-xs px-3.5 py-2.5 text-neutral-800 focus:ring-1 focus:ring-neutral-400">
            @error('password')
                <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember me --}}
        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-neutral-300 text-neutral-900 focus:ring-0">
                <span class="text-xs text-neutral-600">Ingat saya</span>
            </label>
        </div>

        {{-- Submit Button (Figma Green Exact) --}}
        <div class="pt-2">
            <button type="submit" class="w-full inline-flex items-center justify-center rounded bg-[#10a349] hover:bg-[#0e8e40] text-white font-medium text-xs py-2.5 transition-colors shadow-xs">
                Masuk
            </button>
        </div>
    </form>

    {{-- Switch to Register --}}
    <div class="pt-4 border-t border-neutral-100 text-center text-xs text-neutral-600 space-y-2">
        <p>
            Belum punya akun pelapor?
            <a href="{{ route('register') }}" class="font-semibold text-neutral-900 hover:underline">Daftar dengan alias</a>
        </p>
        <p class="text-[11px] text-neutral-400">
            Sudah punya kode lacak?
            <a href="{{ route('lacak.form') }}" class="text-neutral-700 hover:underline">Lacak status laporan tanpa login</a>
        </p>
    </div>

    {{-- Demo Account Hints --}}
    <div class="rounded-lg bg-[#f2f2f2] p-4 text-[11px] text-neutral-600 border border-neutral-200/60 space-y-1.5">
        <p class="font-semibold text-neutral-800">Akun Uji Coba Demo (Seeder):</p>
        <ul class="space-y-0.5 text-neutral-600">
            <li>• <strong>Admin:</strong> <code class="text-neutral-800">admin@ruangaman.test</code> / <code class="text-neutral-800">password</code></li>
            <li>• <strong>Konselor:</strong> <code class="text-neutral-800">konselor@ruangaman.test</code> / <code class="text-neutral-800">password</code></li>
            <li>• <strong>Pelapor:</strong> <code class="text-neutral-800">pelapor@ruangaman.test</code> / <code class="text-neutral-800">password</code></li>
        </ul>
    </div>

</div>
@endsection
