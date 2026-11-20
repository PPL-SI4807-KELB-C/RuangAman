@extends('layouts.app')

@section('title', 'Pengaturan Profil & Mode Anonim - RuangAman')

@section('content')
<div class="mx-auto max-w-2xl py-6">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

        <div class="mb-6 border-b border-slate-100 pb-5">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-600"></span>
                Privasi & Anonimitas Pelapor
            </span>
            <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900">
                Pengaturan Profil & Mode Anonim
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Atur nama samaran (alias) dan preferensi anonimitas bawaan saat Anda membuat laporan kasus.
            </p>
        </div>

        {{-- Notifikasi Error Validasi --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profil-pelapor.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Input Nama Alias --}}
            <div>
                <label for="alias" class="mb-1.5 block text-sm font-semibold text-slate-700">
                    Alias / Nama Samaran <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="alias"
                       name="alias"
                       value="{{ old('alias', $profil->alias ?? $user->nama_tampilan) }}"
                       required
                       minlength="3"
                       maxlength="50"
                       placeholder="Contoh: Bintang Laut"
                       class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                <p class="mt-1.5 text-xs text-slate-500">
                    Nama samaran ini ditampilkan di bilah navigasi dan digunakan untuk melindungi identitas asli Anda.
                </p>
            </div>

            {{-- Input Kontak Opsional --}}
            <div>
                <label for="kontak_opsional" class="mb-1.5 block text-sm font-semibold text-slate-700">
                    Kontak Tambahan (Opsional)
                </label>
                <input type="text"
                       id="kontak_opsional"
                       name="kontak_opsional"
                       value="{{ old('kontak_opsional', $profil->kontak_opsional) }}"
                       maxlength="100"
                       placeholder="Contoh: Telegram / No. HP alternatif yang aman dihubungi"
                       class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                <p class="mt-1.5 text-xs text-slate-500">
                    Hanya dapat diakses oleh konselor atau petugas berwenang apabila Anda memerlukan pendampingan lanjutan.
                </p>
            </div>

            {{-- Checkbox Preferensi Mode Anonim Bawaan --}}
            <div class="rounded-xl border border-brand-200 bg-brand-50/60 p-4">
                <label for="preferensi_anonim" class="flex cursor-pointer items-start gap-3">
                    <input type="hidden" name="preferensi_anonim" value="0">
                    <input type="checkbox"
                           id="preferensi_anonim"
                           name="preferensi_anonim"
                           value="1"
                           {{ old('preferensi_anonim', $profil->preferensi_anonim) ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <div>
                        <span class="block text-sm font-semibold text-slate-900">
                            Aktifkan Mode Anonim Bawaan
                        </span>
                        <span class="mt-0.5 block text-xs leading-relaxed text-slate-600">
                            Jika dicentang, setiap kali Anda membuat laporan baru, opsi sembunyikan identitas dari konselor akan aktif secara otomatis.
                        </span>
                    </div>
                </label>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('laporan.index') }}"
                   class="rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                    Kembali ke Laporan
                </a>
                <button type="submit"
                        class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxAnonim = document.getElementById('preferensi_anonim');
        const navbarName = document.getElementById('navbar-user-name');
        const aliasInput = document.getElementById('alias');

        if (!checkboxAnonim || !navbarName) return;

        function syncNavbarName() {
            const currentAlias = aliasInput && aliasInput.value.trim() !== ''
                ? aliasInput.value.trim()
                : navbarName.dataset.realName;

            navbarName.textContent = checkboxAnonim.checked ? 'Anonim' : currentAlias;
        }

        syncNavbarName();
        checkboxAnonim.addEventListener('change', syncNavbarName);
        if (aliasInput) {
            aliasInput.addEventListener('input', syncNavbarName);
        }
    });
</script>
@endsection