<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan {{ $laporan->kode_laporan }} - RuangAman</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">
        <a href="{{ route('laporan.index') }}" class="font-bold text-xl text-blue-900">RuangAman</a>
        <a href="https://www.google.com" class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">Keluar Cepat (Esc)</a>
    </nav>

    <main class="max-w-3xl mx-auto py-8 px-4 space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-xs font-mono text-slate-500">KODE PELACAKAN</span>
                <h1 class="text-2xl font-bold text-blue-900 font-mono">{{ $laporan->kode_laporan }}</h1>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">
                {{ $laporan->status->nama_status ?? 'Pending' }}
            </span>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-4 text-sm">
            <div class="grid grid-cols-2 gap-4">
                <div><span class="text-slate-500 block">Kategori Kekerasan</span><strong>{{ $laporan->kategori_kekerasan }}</strong></div>
                <div><span class="text-slate-500 block">Tingkat Urgensi</span><strong>{{ $laporan->tingkat_urgensi }}</strong></div>
                <div><span class="text-slate-500 block">Waktu Kejadian</span><strong>{{ $laporan->waktu_kejadian }}</strong></div>
                <div><span class="text-slate-500 block">Mode Privasi</span><strong>{{ $laporan->mode_anonim ? 'Anonim' : 'Alias Terlihat' }}</strong></div>
            </div>
            <div>
                <span class="text-slate-500 block">Lokasi Kejadian</span>
                <p class="font-medium">{{ $laporan->lokasi_kejadian }}</p>
            </div>
            <div>
                <span class="text-slate-500 block">Kronologi</span>
                <p class="whitespace-pre-line text-slate-700 mt-1">{{ $laporan->kronologi }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-base font-semibold text-slate-900 mb-3">Lampiran Bukti Privat ({{ $laporan->lampirans->count() }})</h2>
            @forelse($laporan->lampirans as $file)
                <div class="py-2 flex justify-between items-center border-b last:border-0 border-slate-100 text-sm">
                    <span>{{ $file->nama_file }} ({{ number_format($file->ukuran_file / 1024, 1) }} KB)</span>
                    <a href="{{ route('laporan.lampiran.download', $file) }}" class="text-blue-700 font-semibold hover:underline">Unduh / Lihat</a>
                </div>
            @empty
                <p class="text-sm text-slate-500">Tidak ada lampiran bukti.</p>
            @endforelse
        </div>

        <div class="flex justify-between">
            <a href="{{ route('laporan.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium">Kembali ke Daftar</a>
            @if(($laporan->status->nama_status ?? '') === 'Pending')
                <a href="{{ route('laporan.edit', $laporan) }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-semibold hover:bg-amber-700">Edit Laporan</a>
            @endif
        </div>
    </main>
    <script>document.addEventListener('keydown', e => { if (e.key === 'Escape') window.location.replace('https://www.google.com'); });</script>
</body>
</html>