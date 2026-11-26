<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Terkirim - RuangAman</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-sm border border-slate-200 text-center space-y-4">
        <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto font-bold text-xl">✓</div>
        <h1 class="text-xl font-bold text-slate-900">Laporan Berhasil Dikirim</h1>
        <p class="text-sm text-slate-600">Simpan kode pelacakan berikut untuk memantau status laporanmu kapan saja tanpa perlu login:</p>
        <div class="p-4 bg-slate-100 rounded-xl font-mono text-xl font-bold text-blue-900 tracking-wider">
            {{ $laporan->kode_laporan }}
        </div>
        <div class="pt-2 flex flex-col gap-2">
            <a href="{{ route('laporan.show', $laporan) }}" class="w-full py-2.5 bg-blue-900 text-white text-sm font-semibold rounded-lg">Lihat Detail Laporan</a>
            <a href="{{ route('laporan.index') }}" class="w-full py-2.5 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg">Ke Daftar Laporan</a>
        </div>
    </div>
</body>
</html>