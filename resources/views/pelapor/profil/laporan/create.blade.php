<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan Kasus - RuangAman</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">
        <a href="{{ route('laporan.index') }}" class="font-bold text-xl text-blue-900">RuangAman</a>
        <a href="https://www.google.com" class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">Keluar Cepat (Esc)</a>
    </nav>

    <main class="max-w-3xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Buat Laporan Kasus</h1>
        <div class="mb-6 p-3.5 rounded-lg bg-teal-50 text-teal-900 border border-teal-200 text-sm">
            Kamu aman di sini. Data pribadimu tidak wajib diisi dan lampiran bukti disimpan secara privat.
        </div>

        <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori Kekerasan *</label>
                    <select name="kategori_kekerasan" required class="w-full rounded-lg border-slate-300">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ old('kategori_kekerasan') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                    @error('kategori_kekerasan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Waktu Kejadian *</label>
                    <input type="datetime-local" name="waktu_kejadian" value="{{ old('waktu_kejadian') }}" required class="w-full rounded-lg border-slate-300">
                    @error('waktu_kejadian') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Lokasi Kejadian *</label>
                <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" placeholder="Tuliskan lokasi kejadian (teks bebas)" required class="w-full rounded-lg border-slate-300">
                @error('lokasi_kejadian') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tingkat Urgensi *</label>
                <div class="flex gap-6 mt-1">
                    @foreach(['Rendah', 'Sedang', 'Tinggi'] as $urgensi)
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="radio" name="tingkat_urgensi" value="{{ $urgensi }}" {{ old('tingkat_urgensi', 'Sedang') === $urgensi ? 'checked' : '' }} class="text-blue-900">
                            <span>{{ $urgensi }}</span>
                        </label>
                    @endforeach
                </div>
                @error('tingkat_urgensi') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kronologi Kejadian *</label>
                <textarea name="kronologi" rows="5" placeholder="Ceritakan kronologi kejadian dengan bahasamu sendiri..." required class="w-full rounded-lg border-slate-300">{{ old('kronologi') }}</textarea>
                @error('kronologi') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Unggah Bukti Pendukung (Opsional - JPG, PNG, PDF maks. 5MB)</label>
                <input type="file" name="lampiran[]" multiple accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-900 file:font-semibold">
                @error('lampiran.*') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 p-3.5 bg-slate-50 rounded-lg border border-slate-200">
                <input type="checkbox" id="mode_anonim" name="mode_anonim" value="1" {{ old('mode_anonim', $defaultAnonim) ? 'checked' : '' }} class="rounded text-blue-900">
                <label for="mode_anonim" class="text-sm text-slate-700">Laporkan secara anonim (identitas profil/alias disembunyikan dari konselor)</label>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('laporan.index') }}" class="px-4 py-2.5 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-900 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">Kirim Laporan</button>
            </div>
        </form>
    </main>
    <script>document.addEventListener('keydown', e => { if (e.key === 'Escape') window.location.replace('https://www.google.com'); });</script>
</body>
</html>