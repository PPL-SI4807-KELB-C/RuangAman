@extends('layouts.guest')

@section('title', 'Daftar Pelapor — RuangAman')

@section('content')
<div class="mx-auto max-w-md space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Daftar Pelapor</h1>
        <p class="text-xs text-neutral-500 mt-1">
            Gunakan alias/pseudonim. Nama asli dan identitas resmi tidak diwajibkan.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Alias / Nama Tampilan --}}
        <div class="space-y-1">
            <label class="block text-xs font-semibold text-neutral-800">Alias / Nama Tampilan *</label>
            <input type="text" name="alias" value="{{ old('alias', old('nama_tampilan')) }}" required placeholder="Contoh: Pelapor A atau Bintang Laut" class="w-full rounded bg-[#f2f2f2] border-0 text-xs px-3.5 py-2.5 text-neutral-800 focus:ring-1 focus:ring-neutral-400">
            <input type="hidden" name="nama_tampilan" id="hidden_nama_tampilan" value="{{ old('alias', 'Pelapor A') }}">
            <p class="text-[11px] text-neutral-400">Nama yang akan digunakan di aplikasi. Jangan gunakan nama asli.</p>
            @error('alias')
                <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="space-y-1">
            <label class="block text-xs font-semibold text-neutral-800">Email Pribadi *</label>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com" class="w-full rounded bg-[#f2f2f2] border-0 text-xs px-3.5 py-2.5 text-neutral-800 focus:ring-1 focus:ring-neutral-400">
            <p class="text-[11px] text-neutral-400">Hanya untuk login dan reset sandi. Tidak akan ditampilkan ke publik atau konselor.</p>
            @error('email')
                <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="space-y-1">
            <label class="block text-xs font-semibold text-neutral-800">Kata Sandi *</label>
            <input type="password" name="password" required placeholder="Minimal 8 karakter (huruf & angka)" class="w-full rounded bg-[#f2f2f2] border-0 text-xs px-3.5 py-2.5 text-neutral-800 focus:ring-1 focus:ring-neutral-400">
            @error('password')
                <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Confirmation --}}
        <div class="space-y-1">
            <label class="block text-xs font-semibold text-neutral-800">Konfirmasi Kata Sandi *</label>
            <input type="password" name="password_confirmation" required placeholder="Ketik ulang kata sandi" class="w-full rounded bg-[#f2f2f2] border-0 text-xs px-3.5 py-2.5 text-neutral-800 focus:ring-1 focus:ring-neutral-400">
        </div>

        {{-- Privacy Notice Box (Figma Style) --}}
        <div class="rounded bg-[#f2f2f2] p-3.5 text-[11px] text-neutral-600 border border-neutral-200/60 leading-relaxed">
            Akun Anda akan secara otomatis terdaftar dengan role <strong>Pelapor</strong> dan preferensi <strong>Mode Anonim aktif</strong> secara bawaan sesuai prinsip UU TPKS dan UU PDP.
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <button type="submit" class="w-full inline-flex items-center justify-center rounded bg-[#10a349] hover:bg-[#0e8e40] text-white font-medium text-xs py-2.5 transition-colors shadow-xs">
                Daftar Akun Pelapor
            </button>
        </div>
    </form>

    {{-- Switch to Login --}}
    <div class="pt-4 border-t border-neutral-100 text-center text-xs text-neutral-600">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="font-semibold text-neutral-900 hover:underline">Masuk di sini</a>
    </div>

</div>

<script>
    // Keep nama_tampilan in sync with alias for AuthController requirement
    const aliasInput = document.querySelector('input[name="alias"]');
    const hiddenNama = document.getElementById('hidden_nama_tampilan');
    if (aliasInput && hiddenNama) {
        aliasInput.addEventListener('input', (e) => {
            hiddenNama.value = e.target.value;
        });
    }
</script>
@endsection
