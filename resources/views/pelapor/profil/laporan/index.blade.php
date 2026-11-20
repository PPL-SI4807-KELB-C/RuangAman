<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Laporan Saya - RuangAman</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-6">
            <a href="{{ route('welcome') }}" class="font-bold text-xl text-blue-900">RuangAman</a>
            <a href="{{ route('laporan.index') }}" class="text-sm font-semibold text-blue-900">Laporan Saya</a>
            <a href="{{ route('profil-pelapor.edit') }}" class="text-sm font-medium text-slate-600 hover:text-blue-900">Profil Anonim</a>
        </div>
        <a href="https://www.google.com" class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">Keluar Cepat (Esc)</a>
    </nav>

    <main class="max-w-5xl mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Riwayat Laporan Kasus</h1>
                <p class="text-sm text-slate-600">Pantau status penanganan dan kelola laporan yang masih berstatus Pending.</p>
            </div>
            <a href="{{ route('laporan.create') }}" class="px-4 py-2.5 bg-blue-900 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">
                + Buat Laporan Baru
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-800 border border-red-200 text-sm">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Kode Pelacakan</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Urgensi</th>
                        <th class="px-4 py-3">Mode</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($laporans as $item)
                        <tr>
                            <td class="px-4 py-3 font-mono font-semibold text-blue-900">{{ $item->kode_laporan }}</td>
                            <td class="px-4 py-3">{{ $item->kategori_kekerasan }}</td>
                            <td class="px-4 py-3">{{ $item->tingkat_urgensi }}</td>
                            <td class="px-4 py-3">{{ $item->mode_anonim ? 'Anonim' : 'Alias' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                    {{ $item->status->nama_status ?? 'Pending' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('laporan.show', $item) }}" class="text-blue-700 hover:underline font-medium">Detail</a>
                                @if(($item->status->nama_status ?? '') === 'Pending')
                                    <a href="{{ route('laporan.edit', $item) }}" class="text-amber-700 hover:underline font-medium">Edit</a>
                                    <form action="{{ route('laporan.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan dan hapus laporan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline font-medium">Batalkan</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada laporan yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $laporans->links() }}</div>
    </main>
    <script>document.addEventListener('keydown', e => { if (e.key === 'Escape') window.location.replace('https://www.google.com'); });</script>
</body>
</html><!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Laporan Saya - RuangAman</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-slate-50 text-slate-800 min-h-screen">

    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">

        <div class="flex items-center gap-6">

            <a href="{{ route('welcome') }}" class="font-bold text-xl text-blue-900">RuangAman</a>

            <a href="{{ route('laporan.index') }}" class="text-sm font-semibold text-blue-900">Laporan Saya</a>

            <a href="{{ route('profil-pelapor.edit') }}" class="text-sm font-medium text-slate-600 hover:text-blue-900">Profil Anonim</a>

        </div>

        <a href="https://www.google.com" class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">Keluar Cepat (Esc)</a>

    </nav>



    <main class="max-w-5xl mx-auto py-8 px-4">

        <div class="flex justify-between items-center mb-6">

            <div>

                <h1 class="text-2xl font-bold text-slate-900">Riwayat Laporan Kasus</h1>

                <p class="text-sm text-slate-600">Pantau status penanganan dan kelola laporan yang masih berstatus Pending.</p>

            </div>

            <a href="{{ route('laporan.create') }}" class="px-4 py-2.5 bg-blue-900 text-white text-sm font-semibold rounded-lg hover:bg-blue-800">

                + Buat Laporan Baru

            </a>

        </div>



        @if(session('success'))

            <div class="mb-4 p-4 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm">{{ session('success') }}</div>

        @endif

        @if(session('error'))

            <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-800 border border-red-200 text-sm">{{ session('error') }}</div>

        @endif



        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <table class="w-full text-left text-sm">

                <thead class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">

                    <tr>

                        <th class="px-4 py-3">Kode Pelacakan</th>

                        <th class="px-4 py-3">Kategori</th>

                        <th class="px-4 py-3">Urgensi</th>

                        <th class="px-4 py-3">Mode</th>

                        <th class="px-4 py-3">Status</th>

                        <th class="px-4 py-3 text-right">Aksi</th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-200">

                    @forelse($laporans as $item)

                        <tr>

                            <td class="px-4 py-3 font-mono font-semibold text-blue-900">{{ $item->kode_laporan }}</td>

                            <td class="px-4 py-3">{{ $item->kategori_kekerasan }}</td>

                            <td class="px-4 py-3">{{ ucfirst($item->tingkat_urgensi) }}</td>

                            <td class="px-4 py-3">{{ $item->mode_anonim ? 'Anonim' : 'Alias' }}</td>

                            <td class="px-4 py-3">

                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">

                                    {{ $item->status->nama_status ?? 'Pending' }}

                                </span>

                            </td>

                            <td class="px-4 py-3 text-right space-x-2">

                                <a href="{{ route('laporan.show', $item) }}" class="text-blue-700 hover:underline font-medium">Detail</a>

                                @if(($item->status->nama_status ?? '') === 'Pending')

                                    <a href="{{ route('laporan.edit', $item) }}" class="text-amber-700 hover:underline font-medium">Edit</a>

                                    <form action="{{ route('laporan.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan dan hapus laporan ini?')">

                                        @csrf

                                        @method('DELETE')

                                        <button type="submit" class="text-red-600 hover:underline font-medium">Batalkan</button>

                                    </form>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada laporan yang dibuat.</td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-4">{{ $laporans->links() }}</div>

    </main>

    <script>document.addEventListener('keydown', e => { if (e.key === 'Escape') window.location.replace('https://www.google.com'); });</script>

</body>

</html>
