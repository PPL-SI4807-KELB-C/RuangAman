<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Laporan {{ $laporan->kode_laporan }} - RuangAman</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-slate-50 text-slate-800 min-h-screen">

    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">

        <a href="{{ route('laporan.index') }}" class="font-bold text-xl text-blue-900">RuangAman</a>

        <a href="https://www.google.com" class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">Keluar Cepat (Esc)</a>

    </nav>



    <main class="max-w-3xl mx-auto py-8 px-4 space-y-6">

        <h1 class="text-2xl font-bold text-slate-900">Edit Laporan ({{ $laporan->kode_laporan }})</h1>



        @if(session('success'))

            <div class="p-4 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm">{{ session('success') }}</div>

        @endif



        @if($errors->any())

            <div class="p-4 rounded-lg bg-red-50 text-red-800 border border-red-200 text-sm">

                <ul class="list-disc pl-5 space-y-1">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif



        <form action="{{ route('laporan.update', $laporan) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-5">

            @csrf

            @method('PUT')



            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">Judul Laporan *</label>

                <input type="text" name="judul" value="{{ old('judul', $laporan->judul) }}" required maxlength="200" class="w-full rounded-lg border-slate-300">

            </div>



            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori Kekerasan *</label>

                    <select name="kategori_kekerasan" required class="w-full rounded-lg border-slate-300">

                        @foreach($kategoriList as $kat)

                            <option value="{{ $kat }}" {{ old('kategori_kekerasan', $laporan->kategori_kekerasan) === $kat ? 'selected' : '' }}>{{ $kat }}</option>

                        @endforeach

                    </select>

                </div>

                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-1">Waktu Kejadian *</label>

                    <input type="datetime-local" name="waktu_kejadian" value="{{ old('waktu_kejadian', \Carbon\Carbon::parse($laporan->waktu_kejadian)->format('Y-m-d\TH:i')) }}" required class="w-full rounded-lg border-slate-300">

                </div>

            </div>



            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">Lokasi Kejadian *</label>

                <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian', $laporan->lokasi_kejadian) }}" required class="w-full rounded-lg border-slate-300">

            </div>



            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">Tingkat Urgensi *</label>

                <div class="flex gap-6 mt-1">

                    @foreach(\App\Models\LaporanKasus::URGENSI as $urgensi)

                        <label class="inline-flex items-center gap-2 text-sm">

                            <input type="radio" name="tingkat_urgensi" value="{{ $urgensi }}" {{ old('tingkat_urgensi', $laporan->tingkat_urgensi) === $urgensi ? 'checked' : '' }}>

                            <span>{{ ucfirst($urgensi) }}</span>

                        </label>

                    @endforeach

                </div>

            </div>



            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">Kronologi Kejadian *</label>

                <textarea name="kronologi" rows="5" required class="w-full rounded-lg border-slate-300">{{ old('kronologi', $laporan->kronologi) }}</textarea>

            </div>



            <div>

                <label class="block text-sm font-medium text-slate-700 mb-1">Tambah Lampiran Baru (Opsional)</label>

                <input type="file" name="lampiran[]" multiple accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-slate-600">

            </div>



            <div class="flex items-center gap-3 p-3.5 bg-slate-50 rounded-lg border border-slate-200">

                <input type="checkbox" id="mode_anonim" name="mode_anonim" value="1" {{ old('mode_anonim', $laporan->mode_anonim) ? 'checked' : '' }}>

                <label for="mode_anonim" class="text-sm text-slate-700">Laporkan secara anonim</label>

            </div>



            <div class="flex justify-end gap-3">

                <a href="{{ route('laporan.show', $laporan) }}" class="px-4 py-2.5 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg">Kembali</a>

                <button type="submit" class="px-5 py-2.5 bg-blue-900 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">Simpan Perubahan</button>

            </div>

        </form>



        @if($laporan->lampirans->isNotEmpty())

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">

                <h2 class="text-base font-semibold text-slate-900 mb-3">Lampiran Terunggah</h2>

                <ul class="divide-y divide-slate-200 text-sm">

                    @foreach($laporan->lampirans as $file)

                        <li class="py-2.5 flex justify-between items-center">

                            <a href="{{ route('laporan.lampiran.download', $file) }}" class="text-blue-700 hover:underline">{{ $file->nama_file }}</a>

                            <form action="{{ route('laporan.lampiran.destroy', $file) }}" method="POST" onsubmit="return confirm('Hapus bukti ini?')">

                                @csrf

                                @method('DELETE')

                                <button type="submit" class="text-red-600 text-xs font-semibold hover:underline">Hapus</button>

                            </form>

                        </li>

                    @endforeach

                </ul>

            </div>

        @endif

    </main>

    <script>document.addEventListener('keydown', e => { if (e.key === 'Escape') window.location.replace('https://www.google.com'); });</script>

</body>

</html>
