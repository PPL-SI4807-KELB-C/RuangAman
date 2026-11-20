@extends('layouts.app')

@section('title', 'Buat Laporan Kasus - RuangAman')

@section('content')
<div class="mx-auto max-w-3xl py-6"
     x-data="{
         step: 1,
         isAnonim: {{ old('is_anonim', $profil->preferensi_anonim ?? false) ? 'true' : 'false' }},
         kategori: '{{ old('kategori_kekerasan', '') }}',
         waktu: '{{ old('waktu_kejadian', '') }}',
         lokasi: '{{ old('lokasi_kejadian', '') }}',
         urgensi: '{{ old('tingkat_urgensi', 'Sedang') }}',
         kronologi: `{{ old('kronologi', '') }}`
     }">

    {{-- Header Halaman --}}
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Buat Laporan Kasus</h1>
        <p class="mt-2 text-sm text-slate-600">
            Isi dengan tenang. Anda dapat mengaktifkan Mode Anonim di langkah pertama.
        </p>
    </div>

    {{-- Stepper Indikator (4 Langkah) --}}
    <div class="mb-8">
        <div class="grid grid-cols-4 gap-2 text-center text-xs sm:text-sm">
            <button type="button" @click="step = 1" class="flex flex-col items-center gap-2 focus:outline-none">
                <span :class="step >= 1 ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-slate-500 border-slate-300'"
                      class="flex h-8 w-8 items-center justify-center rounded-full border font-bold transition">1</span>
                <span :class="step >= 1 ? 'text-brand-700 font-semibold' : 'text-slate-500'">Identitas & Anonimitas</span>
            </button>

            <button type="button" @click="step = 2" class="flex flex-col items-center gap-2 focus:outline-none">
                <span :class="step >= 2 ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-slate-500 border-slate-300'"
                      class="flex h-8 w-8 items-center justify-center rounded-full border font-bold transition">2</span>
                <span :class="step >= 2 ? 'text-brand-700 font-semibold' : 'text-slate-500'">Detail Kejadian</span>
            </button>

            <button type="button" @click="step = 3" class="flex flex-col items-center gap-2 focus:outline-none">
                <span :class="step >= 3 ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-slate-500 border-slate-300'"
                      class="flex h-8 w-8 items-center justify-center rounded-full border font-bold transition">3</span>
                <span :class="step >= 3 ? 'text-brand-700 font-semibold' : 'text-slate-500'">Kronologi & Bukti</span>
            </button>

            <button type="button" @click="step = 4" class="flex flex-col items-center gap-2 focus:outline-none">
                <span :class="step >= 4 ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-slate-500 border-slate-300'"
                      class="flex h-8 w-8 items-center justify-center rounded-full border font-bold transition">4</span>
                <span :class="step >= 4 ? 'text-brand-700 font-semibold' : 'text-slate-500'">Konfirmasi</span>
            </button>
        </div>
    </div>

    {{-- Notifikasi Error Validasi --}}
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold mb-1">Mohon periksa kembali isian formulir Anda:</p>
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card Formulir --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- LANGKAH 1: IDENTITAS & ANONIMITAS --}}
            <div x-show="step === 1" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 1: Identitas & Pengaturan Anonimitas</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Pilih bagaimana identitas Anda ditampilkan kepada petugas penerima laporan.
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Identitas Akun Saat Ini</p>
                    <p class="mt-1 text-base font-semibold text-slate-800">{{ Auth::user()->nama_tampilan }}</p>
                    <p class="text-xs text-slate-500">
                        Ingin mengganti nama samaran (alias)?
                        <a href="{{ route('profil-pelapor.edit') }}" class="font-medium text-brand-700 hover:underline">Atur di Profil Pelapor</a>
                    </p>
                </div>

                <div class="rounded-xl border border-brand-200 bg-brand-50/60 p-4">
                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="hidden" name="is_anonim" value="0">
                        <input type="checkbox"
                               name="is_anonim"
                               value="1"
                               x-model="isAnonim"
                               class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <div>
                            <span class="block text-sm font-semibold text-slate-900">Laporkan secara anonim</span>
                            <span class="mt-0.5 block text-xs leading-relaxed text-slate-600">
                                Identitas asli dan email Anda tidak akan ditampilkan kepada petugas/konselor. Hanya nama samaran dan kode pelacakan yang digunakan.
                            </span>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="button"
                            @click="step = 2"
                            class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                        Lanjut: Detail Kejadian →
                    </button>
                </div>
            </div>

            {{-- LANGKAH 2: DETAIL KEJADIAN --}}
            <div x-show="step === 2" x-cloak class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 2: Detail Kejadian</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Berikan informasi dasar mengenai jenis, waktu, dan tempat peristiwa terjadi.
                    </p>
                </div>

                <div>
                    <label for="kategori_kekerasan" class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Kategori Kekerasan <span class="text-red-500">*</span>
                    </label>
                    <select id="kategori_kekerasan"
                            name="kategori_kekerasan"
                            x-model="kategori"
                            required
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                        <option value="">-- Pilih Kategori Kekerasan --</option>
                        <option value="Kekerasan Fisik">Kekerasan Fisik</option>
                        <option value="Kekerasan Psikis">Kekerasan Psikis</option>
                        <option value="Kekerasan Seksual">Kekerasan Seksual</option>
                        <option value="Kekerasan Berbasis Gender Online (KBGO)">Kekerasan Berbasis Gender Online (KBGO)</option>
                        <option value="Penelantaran / Ekonomi">Penelantaran / Ekonomi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="waktu_kejadian" class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Perkiraan Waktu Kejadian <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local"
                               id="waktu_kejadian"
                               name="waktu_kejadian"
                               x-model="waktu"
                               required
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                    </div>

                    <div>
                        <label for="tingkat_urgensi" class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Tingkat Urgensi <span class="text-red-500">*</span>
                        </label>
                        <select id="tingkat_urgensi"
                                name="tingkat_urgensi"
                                x-model="urgensi"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                            <option value="Rendah">Rendah (Butuh Konsultasi / Pencatatan)</option>
                            <option value="Sedang">Sedang (Butuh Pendampingan Segera)</option>
                            <option value="Tinggi">Tinggi (Darurat / Ancaman Keselamatan)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="lokasi_kejadian" class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Lokasi Kejadian <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="lokasi_kejadian"
                           name="lokasi_kejadian"
                           x-model="lokasi"
                           required
                           maxlength="255"
                           placeholder="Contoh: Area kampus gedung B, kos-kosan, atau media sosial Instagram"
                           class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <button type="button"
                            @click="step = 1"
                            class="rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
                        ← Kembali
                    </button>
                    <button type="button"
                            @click="step = 3"
                            class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                        Lanjut: Kronologi & Bukti →
                    </button>
                </div>
            </div>

            {{-- LANGKAH 3: KRONOLOGI & BUKTI --}}
            <div x-show="step === 3" x-cloak class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 3: Kronologi & Lampiran Bukti</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Ceritakan kejadian sesuai kenyamanan Anda dan unggah bukti pendukung bila ada.
                    </p>
                </div>

                <div>
                    <label for="kronologi" class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Kronologi Kejadian <span class="text-red-500">*</span>
                    </label>
                    <textarea id="kronologi"
                              name="kronologi"
                              x-model="kronologi"
                              rows="6"
                              required
                              minlength="20"
                              placeholder="Tuliskan urutan peristiwa yang dialami secara jelas (minimal 20 karakter)..."
                              class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-200"></textarea>
                </div>

                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5">
                    <label for="lampiran" class="block text-sm font-semibold text-slate-800">
                        Unggah Bukti Pendukung (Opsional)
                    </label>
                    <p class="mt-1 text-xs text-slate-500">
                        Format yang didukung: <strong>JPG, JPEG, PNG, PDF</strong> (Maksimal 5 MB per file, dapat memilih lebih dari satu file). File disimpan secara privat dan terenkripsi aksesnya.
                    </p>
                    <input type="file"
                           id="lampiran"
                           name="lampiran[]"
                           multiple
                           accept=".jpg,.jpeg,.png,.pdf"
                           class="mt-3 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <button type="button"
                            @click="step = 2"
                            class="rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
                        ← Kembali
                    </button>
                    <button type="button"
                            @click="step = 4"
                            class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                        Lanjut: Konfirmasi →
                    </button>
                </div>
            </div>

            {{-- LANGKAH 4: KONFIRMASI & KIRIM --}}
            <div x-show="step === 4" x-cloak class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Langkah 4: Konfirmasi Laporan</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Periksa ringkasan laporan Anda sebelum dikirimkan ke sistem RuangAman.
                    </p>
                </div>

                <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm">
                    <div class="flex justify-between py-2">
                        <span class="text-slate-500">Mode Pelaporan</span>
                        <span class="font-semibold text-slate-900" x-text="isAnonim ? 'Anonim (Identitas Disembunyikan)' : 'Teridentifikasi ({{ Auth::user()->nama_tampilan }})'"></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-500">Kategori</span>
                        <span class="font-semibold text-slate-900" x-text="kategori || '(Belum dipilih)'"></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-500">Waktu Kejadian</span>
                        <span class="font-semibold text-slate-900" x-text="waktu || '(Belum diisi)'"></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-500">Lokasi</span>
                        <span class="font-semibold text-slate-900" x-text="lokasi || '(Belum diisi)'"></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-slate-500">Tingkat Urgensi</span>
                        <span class="font-semibold text-slate-900" x-text="urgensi"></span>
                    </div>
                    <div class="py-2">
                        <span class="block text-slate-500 mb-1">Ringkasan Kronologi</span>
                        <p class="rounded-lg bg-white p-3 text-slate-700 border border-slate-200 whitespace-pre-line" x-text="kronologi || '(Belum diisi)'"></p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <button type="button"
                            @click="step = 3"
                            class="rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
                        ← Kembali
                    </button>
                    <button type="submit"
                            class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        Kirim Laporan Sekarang
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection