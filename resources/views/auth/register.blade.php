@extends('layouts.guest')

@section('title', $title)

@section('content')
    <x-card>
        <div class="text-center">
            <h1 class="text-xl font-bold text-slate-900">Daftar Pelapor</h1>
            <p class="mt-1 text-sm text-slate-500">Gunakan alias/pseudonim. Nama asli tidak wajib.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
            @csrf
            <x-input name="nama_tampilan" label="Nama Tampilan" placeholder="Bintang Laut" :error="$errors->first('nama_tampilan')" required />
            <x-input name="email" type="email" label="Email" placeholder="nama@email.com" helper="Digunakan untuk login dan reset kata sandi." :error="$errors->first('email')" required />
            <x-input name="alias" label="Alias Pelapor" placeholder="Bintang Laut" helper="Nama yang akan dilihat konselor jika mode anonim nonaktif." :error="$errors->first('alias')" required />
            <x-input name="password" type="password" label="Kata Sandi" :error="$errors->first('password')" required />
            <x-input name="password_confirmation" type="password" label="Konfirmasi Kata Sandi" required />

            <x-button type="submit" variant="primary" class="w-full">Daftar</x-button>
        </form>

        <p class="mt-4 text-center text-sm text-slate-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-brand-700 hover:underline">Masuk</a>
        </p>
    </x-card>
@endsection
