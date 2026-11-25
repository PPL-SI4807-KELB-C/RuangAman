@extends('layouts.guest')

@section('title', 'Daftar Pelapor — RuangAman')

@section('content')
<div class="mx-auto max-w-md space-y-6">

    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-neutral-900">Daftar sebagai Pelapor</h1>
        <p class="mt-1.5 text-xs leading-relaxed text-neutral-600">
            Gunakan alias. Nama asli dan identitas resmi tidak diperlukan.
        </p>
    </div>

    @if (session('draf_laporan'))
        <x-alert variant="info">
            Isian laporan Anda dari beranda tersimpan sementara dan akan muncul kembali di formulir laporan setelah Anda mendaftar.
        </x-alert>
    @endif

    <x-card>
        <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="{ alias: @js(old('alias', old('nama_tampilan', ''))) }">
            @csrf

            <x-input
                name="alias"
                label="Alias"
                :value="old('alias', old('nama_tampilan'))"
                placeholder="Contoh: Bintang Laut"
                helper="Nama yang terlihat di aplikasi. Jangan gunakan nama asli."
                :error="$errors->first('alias') ?: $errors->first('nama_tampilan')"
                x-model="alias"
                autocomplete="nickname"
                required
            />
            {{-- AuthController masih membutuhkan nama_tampilan; disamakan dengan alias. --}}
            <input type="hidden" name="nama_tampilan" :value="alias">

            <x-input
                name="email"
                type="email"
                label="Email pribadi"
                placeholder="nama@email.com"
                helper="Hanya untuk masuk dan mengatur ulang kata sandi. Tidak ditampilkan ke konselor."
                autocomplete="email"
                :error="$errors->first('email')"
                required
            />

            <x-input
                name="password"
                type="password"
                label="Kata sandi"
                helper="Minimal 8 karakter, kombinasi huruf dan angka."
                autocomplete="new-password"
                :error="$errors->first('password')"
                required
            />

            <x-input
                name="password_confirmation"
                type="password"
                label="Konfirmasi kata sandi"
                autocomplete="new-password"
                required
            />

            <p class="rounded-lg bg-white px-3.5 py-3 text-[11px] leading-relaxed text-neutral-600 ring-1 ring-inset ring-neutral-300/70">
                Akun terdaftar sebagai <strong>Pelapor</strong> dengan <strong>mode anonim aktif</strong> secara bawaan, sesuai prinsip UU TPKS dan UU PDP.
            </p>

            <x-button type="submit" variant="primary" class="w-full">Daftar akun pelapor</x-button>
        </form>
    </x-card>

    <p class="text-center text-xs text-neutral-600">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="font-semibold text-neutral-900 hover:underline">Masuk di sini</a>
    </p>

</div>
@endsection
