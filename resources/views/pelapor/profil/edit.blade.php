<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Alias & Privasi - RuangAman</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-6">
            <a href="{{ route('welcome') }}" class="font-bold text-xl text-blue-900">RuangAman</a>
            <a href="{{ route('laporan.index') }}" class="text-sm font-medium text-slate-600 hover:text-blue-900">Laporan Saya</a>
            <a href="{{ route('profil-pelapor.edit') }}" class="text-sm font-semibold text-blue-900">Profil Anonim</a>
        </div>
        <a href="https://www.google.com" class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">Keluar Cepat (Esc)</a>
    </nav>

    <main class="max-w-2xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold text-slate-900 mb-1">Profil Alias & Mode Anonim</h1>
        <p class="text-sm text-slate-600 mb-6">Identitas aslimu tidak ditampilkan ke publik. Atur alias dan preferensi anonim di sini.</p>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm">{{ session('success') }}</div>
        @endif

        <form action="{{ route('profil-pelapor.update') }}" method="POST" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Alias / Nama Samaran *</label>
                <input type="text" name="alias" value="{{ old('alias', $profil->alias) }}" required
                    class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                @error('alias') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kontak Tambahan (Opsional)</label>
                <input type="text" name="kontak_opsional" value="{{ old('kontak_opsional', $profil->kontak_opsional) }}"
                    placeholder="Contoh: Telegram / No. HP alternatif yang aman dihubungi"
                    class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                @error('kontak_opsional') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-start gap-3 p-4 bg-blue-50/60 rounded-lg border border-blue-100">
                <input type="checkbox" id="preferensi_anonim" name="preferensi_anonim" value="1"
                    {{ old('preferensi_anonim', $profil->preferensi_anonim) ? 'checked' : '' }}
                    class="mt-1 rounded text-blue-900 focus:ring-blue-600">
                <label for="preferensi_anonim" class="text-sm text-slate-700">
                    <span class="font-semibold text-slate-900 block">Aktifkan Mode Anonim Bawaan</span>
                    Jika dicentang, setiap kali kamu membuat laporan baru, opsi sembunyikan identitas dari konselor akan aktif secara otomatis.
                </label>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-blue-900 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </main>
    <script>document.addEventListener('keydown', e => { if (e.key === 'Escape') window.location.replace('https://www.google.com'); });</script>
</body>
</html>