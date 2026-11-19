@extends('layouts.app')

@section('title', 'Buat Laporan — RuangAman')

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-8">

    {{-- Back Link --}}
    <div class="mb-6">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs text-neutral-600 hover:text-neutral-900 transition-colors">
            <span>&larr;</span>
            <span>Kembali ke beranda</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        {{-- Left Column: Form (Figma Exact) --}}
        <div class="lg:col-span-8 space-y-8">
            <div>
                <h1 class="text-3xl font-extrabold text-neutral-900 tracking-tight">Buat laporan</h1>
                <p class="text-xs text-neutral-500 mt-2">
                    Ceritakan sebatas yang Anda siap bagikan. Kolom wajib ditandai dengan *.
                </p>
                <div class="mt-4 rounded bg-[#f2f2f2] px-3.5 py-2 text-[11px] text-neutral-600 border border-neutral-200/60">
                    Data contoh · Isian ini menggambarkan laporan fiktif, bukan kasus nyata.
                </div>
            </div>

            <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Hidden defaults to satisfy backend requirements --}}
                <input type="hidden" name="judul" value="Laporan Kasus">
                <input type="hidden" name="tingkat_urgensi" value="sedang">
                <input type="hidden" name="lokasi_kejadian" value="Tidak disebutkan / Dilaporkan via web">
                <input type="hidden" name="waktu_kejadian" value="{{ now()->format('Y-m-d H:i:s') }}">
                <input type="hidden" name="mode_anonim" value="1">

                {{-- Field 1: Alias --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-neutral-800">
                        Alias *
                    </label>
                    <input type="text" name="alias" value="{{ old('alias', request('alias', 'Pelapor A')) }}" required class="w-full rounded bg-[#f2f2f2] border-0 text-xs px-3.5 py-2.5 text-neutral-800 focus:ring-1 focus:ring-neutral-400">
                    <p class="text-[11px] text-neutral-400">Konselor hanya melihat alias. Jangan gunakan nama asli.</p>
                </div>

                {{-- Field 2: Kategori Kejadian (Radio Pill Grid - Figma Exact) --}}
                <div class="space-y-2">
                    <div>
                        <label class="block text-xs font-semibold text-neutral-800">
                            Kategori kejadian *
                        </label>
                        <p class="text-[11px] text-neutral-400">Pilih kategori yang paling mendekati. Tidak apa-apa jika belum yakin.</p>
                    </div>

                    @php
                        $selectedKat = old('kategori_kekerasan', request('kategori', 'Siber'));
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        {{-- Siber / Digital --}}
                        <label class="flex items-center gap-2.5 rounded bg-[#f2f2f2] hover:bg-neutral-200/70 p-3 text-xs text-neutral-800 cursor-pointer border border-transparent has-[:checked]:border-neutral-400 transition-colors">
                            <input type="radio" name="kategori_kekerasan" value="Siber" class="text-neutral-900 focus:ring-0" {{ $selectedKat === 'Siber' ? 'checked' : '' }}>
                            <span>Kekerasan digital</span>
                        </label>

                        {{-- Seksual --}}
                        <label class="flex items-center gap-2.5 rounded bg-[#f2f2f2] hover:bg-neutral-200/70 p-3 text-xs text-neutral-800 cursor-pointer border border-transparent has-[:checked]:border-neutral-400 transition-colors">
                            <input type="radio" name="kategori_kekerasan" value="Seksual" class="text-neutral-900 focus:ring-0" {{ $selectedKat === 'Seksual' ? 'checked' : '' }}>
                            <span>Kekerasan seksual</span>
                        </label>

                        {{-- Psikis --}}
                        <label class="flex items-center gap-2.5 rounded bg-[#f2f2f2] hover:bg-neutral-200/70 p-3 text-xs text-neutral-800 cursor-pointer border border-transparent has-[:checked]:border-neutral-400 transition-colors">
                            <input type="radio" name="kategori_kekerasan" value="Psikis" class="text-neutral-900 focus:ring-0" {{ $selectedKat === 'Psikis' ? 'checked' : '' }}>
                            <span>Kekerasan psikis</span>
                        </label>

                        {{-- Fisik --}}
                        <label class="flex items-center gap-2.5 rounded bg-[#f2f2f2] hover:bg-neutral-200/70 p-3 text-xs text-neutral-800 cursor-pointer border border-transparent has-[:checked]:border-neutral-400 transition-colors">
                            <input type="radio" name="kategori_kekerasan" value="Fisik" class="text-neutral-900 focus:ring-0" {{ $selectedKat === 'Fisik' ? 'checked' : '' }}>
                            <span>Kekerasan fisik</span>
                        </label>

                        {{-- Ekonomi --}}
                        <label class="flex items-center gap-2.5 rounded bg-[#f2f2f2] hover:bg-neutral-200/70 p-3 text-xs text-neutral-800 cursor-pointer border border-transparent has-[:checked]:border-neutral-400 transition-colors">
                            <input type="radio" name="kategori_kekerasan" value="Ekonomi" class="text-neutral-900 focus:ring-0" {{ $selectedKat === 'Ekonomi' ? 'checked' : '' }}>
                            <span>Kekerasan ekonomi</span>
                        </label>

                        {{-- Belum Yakin / Lainnya --}}
                        <label class="flex items-center gap-2.5 rounded bg-[#f2f2f2] hover:bg-neutral-200/70 p-3 text-xs text-neutral-800 cursor-pointer border border-transparent has-[:checked]:border-neutral-400 transition-colors">
                            <input type="radio" name="kategori_kekerasan" value="Lainnya" class="text-neutral-900 focus:ring-0" {{ $selectedKat === 'Lainnya' ? 'checked' : '' }}>
                            <span>Belum yakin / lainnya</span>
                        </label>
                    </div>
                </div>

                {{-- Field 3: Kronologi Kejadian --}}
                <div class="space-y-1.5">
                    <div>
                        <label class="block text-xs font-semibold text-neutral-800">
                            Kronologi kejadian *
                        </label>
                        <p class="text-[11px] text-neutral-400">Anda dapat menyertakan waktu, konteks, dan bantuan yang dibutuhkan.</p>
                    </div>
                    <textarea name="kronologi" rows="6" required class="w-full rounded bg-[#f2f2f2] border-0 text-xs p-3.5 text-neutral-800 focus:ring-1 focus:ring-neutral-400 leading-relaxed">{{ old('kronologi', request('kronologi', 'Saya menerima pesan yang mengancam untuk menyebarkan informasi pribadi saya tanpa persetujuan. Saya ingin mengetahui pilihan bantuan dan cara menyimpan bukti dengan aman.')) }}</textarea>
                    <p class="text-[11px] text-neutral-400">Hindari nama asli, alamat lengkap, atau nomor identitas.</p>
                </div>

                {{-- Field 4: Lampiran Pendukung (Opsional) --}}
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-neutral-800">
                        Lampiran pendukung (opsional)
                    </label>

                    <div class="relative rounded border border-dashed border-neutral-300 bg-white p-6 text-center hover:bg-neutral-50 transition-colors">
                        <input type="file" name="lampiran[]" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="space-y-1">
                            <div class="flex justify-center text-neutral-500">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="17 8 12 3 7 8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-neutral-800">Pilih berkas</p>
                            <p class="text-[10px] text-neutral-400">JPG, PNG, atau PDF · Maksimal 10 MB per berkas</p>
                            <p class="text-[10px] text-neutral-400">Hanya unggah jika aman. Lampiran tidak wajib untuk melapor.</p>
                        </div>
                    </div>
                    <p class="text-[11px] text-neutral-400 leading-relaxed">
                        Periksa nama berkas dan informasi pribadi sebelum mengunggah. Jangan mengambil bukti baru jika hal itu membahayakan Anda.
                    </p>
                </div>

                {{-- Agreement Checkbox --}}
                <div class="pt-2">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="persetujuan" value="1" required checked class="mt-0.5 rounded border-neutral-300 text-neutral-900 focus:ring-0">
                        <span class="text-xs text-neutral-700 leading-relaxed">
                            Saya memahami bahwa informasi ini akan ditinjau oleh tim RuangAman untuk penanganan laporan sesuai kebijakan privasi.
                        </span>
                    </label>
                </div>

                {{-- Actions --}}
                <div class="pt-4 flex items-center gap-4">
                    <button type="submit" class="inline-flex items-center justify-center rounded bg-[#10a349] hover:bg-[#0e8e40] text-white font-medium text-xs px-6 py-2.5 transition-colors">
                        Kirim laporan
                    </button>
                    <a href="{{ url('/') }}" class="text-xs text-neutral-600 hover:text-neutral-900">
                        Batalkan
                    </a>
                </div>
                <p class="text-[11px] text-neutral-400">Setelah dikirim, Anda akan menerima kode laporan enam karakter.</p>
            </form>
        </div>

        {{-- Right Column: Information Sidebar (Figma Exact) --}}
        <div class="lg:col-span-4 space-y-6 lg:pl-6 lg:border-l lg:border-neutral-100">
            <div class="space-y-6 text-xs text-neutral-600">
                <div class="space-y-2">
                    <div class="text-neutral-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-neutral-900">Sebelum mengirim</h3>
                    <p class="leading-relaxed text-neutral-600">
                        Anda tidak harus menceritakan semua detail untuk mulai mendapatkan arahan.
                    </p>
                </div>

                <div class="space-y-1.5 pt-4 border-t border-neutral-100">
                    <h4 class="font-semibold text-neutral-900">Identitas tetap berupa alias</h4>
                    <p class="leading-relaxed text-neutral-600">
                        Tidak ada permintaan KTP, nama lengkap, atau alamat rumah.
                    </p>
                </div>

                <div class="space-y-1.5 pt-4 border-t border-neutral-100">
                    <h4 class="font-semibold text-neutral-900">Tidak diteruskan otomatis</h4>
                    <p class="leading-relaxed text-neutral-600">
                        Pilihan rujukan dan pendampingan dibahas dengan persetujuan Anda.
                    </p>
                </div>

                <div class="pt-6 border-t border-neutral-100">
                    <p class="text-[11px] text-neutral-400 leading-relaxed">
                        Jika belum siap, Anda dapat berhenti. Pastikan perangkat tetap aman.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
