@extends('layouts.app')

@section('title', 'Edit Laporan ' . $laporan->kode_laporan . ' - RuangAman')

@section('content')
<div class="mx-auto max-w-3xl py-6 space-y-6">
    {{-- Header Halaman --}}
    <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Edit Laporan <span class="font-mono text-brand-700">({{ $laporan->kode_laporan }})</span>
            </h1>
            <p class="mt-1 text-sm text-slate-600">
                Perbarui informasi laporan Anda selama status masih Pending.
            </p>
        </div>
    </div>

    {{-- Pesan Sukses / Error Validasi --}}
    @if (session('success'))
        <div class="rounded-xl border border-brand-200 bg-brand-50 p-4 text-sm font-medium text-brand-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="mb-1 font-semibold">Mohon periksa kembali isian formulir Anda:</p>
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Edit Laporan --}}
    <form action="{{ route('laporan.update', $laporan) }}" method="POST" enctype="multipart/form-data"
          class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        @csrf
        @method('PUT')

        <div>
            <label for="judul" class="mb-1 block text-sm font-medium text-slate-700">Judul Laporan *</label>
            <input type="text"
                   id="judul"
                   name="judul"
                   value="{{ old('judul', $laporan->judul) }}"
                   required
                   maxlength="200"
                   class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="kategori_kekerasan" class="mb-1 block text-sm font-medium text-slate-700">Kategori Kekerasan *</label>
                <select id="kategori_kekerasan"
                        name="kategori_kekerasan"
                        required
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @foreach ($kategoriList as $kat)
                        <option value="{{ $kat }}" @selected(old('kategori_kekerasan', $laporan->kategori_kekerasan) === $kat)>
                            {{ $kat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="waktu_kejadian" class="mb-1 block text-sm font-medium text-slate-700">Waktu Kejadian *</label>
                <input type="datetime-local"
                       id="waktu_kejadian"
                       name="waktu_kejadian"
                       value="{{ old('waktu_kejadian', \Carbon\Carbon::parse($laporan->waktu_kejadian)->format('Y-m-d\TH:i')) }}"
                       required
                       class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
        </div>

        <div>
            <label for="lokasi_kejadian" class="mb-1 block text-sm font-medium text-slate-700">Lokasi Kejadian *</label>
            <input type="text"
                   id="lokasi_kejadian"
                   name="lokasi_kejadian"
                   value="{{ old('lokasi_kejadian', $laporan->lokasi_kejadian) }}"
                   required
                   class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <span class="mb-2 block text-sm font-medium text-slate-700">Tingkat Urgensi *</span>
            <div class="flex flex-wrap gap-6">
                @foreach (\App\Models\LaporanKasus::URGENSI as $urgensi)
                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                        <input type="radio"
                               name="tingkat_urgensi"
                               value="{{ $urgensi }}"
                               class="border-slate-300 text-brand-600 focus:ring-brand-500"
                               @checked(old('tingkat_urgensi', $laporan->tingkat_urgensi) === $urgensi)>
                        <span>{{ ucfirst($urgensi) }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label for="kronologi" class="mb-1 block text-sm font-medium text-slate-700">Kronologi Kejadian *</label>
            <textarea id="kronologi"
                      name="kronologi"
                      rows="5"
                      required
                      class="w-full rounded-xl border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('kronologi', $laporan->kronologi) }}</textarea>
        </div>

        <div>
            <label for="lampiran" class="mb-1 block text-sm font-medium text-slate-700">
                Tambah Lampiran Baru <span class="font-normal text-slate-500">(Opsional, maks. 5 file)</span>
            </label>
            <input type="file"
                   id="lampiran"
                   name="lampiran[]"
                   multiple
                   accept=".jpg,.jpeg,.png,.webp,.pdf,.mp3,.mp4,.mov"
                   class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:font-medium file:text-brand-700 hover:file:bg-brand-100">
        </div>

        <div class="rounded-xl border border-brand-200 bg-brand-50 p-4">
            <label for="mode_anonim" class="flex cursor-pointer items-start gap-3">
                <input type="checkbox"
                       id="mode_anonim"
                       name="mode_anonim"
                       value="1"
                       class="mt-0.5 h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                       @checked(old('mode_anonim', $laporan->mode_anonim))>
                <div>
                    <span class="block text-sm font-semibold text-slate-900">Laporkan secara anonim</span>
                    <span class="block text-xs text-slate-600">Identitas profil Anda disembunyikan dari konselor yang menangani kasus ini.</span>
                </div>
            </label>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-5">
            <a href="{{ route('laporan.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                Kembali
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- Daftar Lampiran Terunggah --}}
    @if ($laporan->lampirans->isNotEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="mb-3 text-base font-semibold text-slate-900">Lampiran Terunggah</h2>
            <ul class="divide-y divide-slate-200 text-sm">
                @foreach ($laporan->lampirans as $file)
                    <li class="flex items-center justify-between gap-4 py-3">
                        <a href="{{ route('laporan.lampiran.download', $file) }}"
                           class="truncate font-medium text-brand-700 hover:text-brand-800 hover:underline">
                            {{ $file->nama_file }}
                        </a>

                        <form action="{{ route('laporan.lampiran.destroy', $file) }}"
                              method="POST"
                              onsubmit="return confirm('Hapus bukti ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                                Hapus
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection