@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-8 space-y-8">

    {{-- Back Link --}}
    <div>
        <a
            href="{{ route('instansi.layanan.index', $instansi) }}"
            class="inline-flex items-center gap-2 text-[13px] text-[#686563] hover:text-[#1C1917] hover:underline transition-colors"
        >
            <span>&larr;</span>
            <span>Kembali ke layanan & kontak</span>
        </a>
    </div>

    {{-- Title & Subtitle --}}
    <div class="space-y-2">
        <h1 class="text-3xl sm:text-4xl font-medium text-[#1C1917] tracking-tight">Tambah kontak bantuan</h1>
        <p class="text-[16px] text-[#686563]">Kolom bertanda * wajib diisi. Informasi ini ditampilkan kepada publik.</p>
    </div>

    {{-- Two Column Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Left Column: Form (approx 704px) --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Instansi Box (Read Only) --}}
            <div class="rounded-[8px] bg-[#EDECE9] p-5 space-y-1">
                <div class="text-[12px] font-medium tracking-wider text-[#686563] uppercase">INSTANSI BANTUAN *</div>
                <div class="text-[18px] font-medium text-[#1C1917]">{{ $instansi->nama_instansi }}</div>
                <div class="text-[12px] text-[#686563]">Instansi dipilih dari daftar. Data instansi tidak diubah pada formulir ini.</div>
            </div>

            <form method="POST" action="{{ route('instansi.kontak.store', $instansi) }}" class="space-y-6">
                @csrf

                {{-- Jenis Kontak --}}
                <div class="space-y-2">
                    <label for="jenis_kontak" class="block text-[13px] font-medium text-[#1C1917]">
                        Jenis kontak <span class="text-[#B42318]">*</span>
                    </label>
                    <div class="relative">
                        <select
                            id="jenis_kontak"
                            name="jenis_kontak"
                            required
                            class="w-full h-12 appearance-none rounded-[8px] bg-[#FAF9F7] px-4 pr-10 text-[16px] text-[#1C1917] outline outline-1 -outline-offset-1 outline-[#DDDCD7] focus:outline-2 focus:outline-[#1C1917] transition-all cursor-pointer"
                        >
                            <option value="">Pilih jenis kontak</option>
                            @foreach ($jenisKontak as $jenis)
                                <option value="{{ $jenis }}" @selected(old('jenis_kontak') === $jenis)>
                                    {{ match ($jenis) {
                                        'telepon'   => 'Telepon',
                                        'whatsapp'  => 'WhatsApp',
                                        'email'     => 'Email',
                                        'hotline'   => 'Hotline',
                                        'website'   => 'Situs web',
                                        default     => ucfirst($jenis),
                                    } }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#686563]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    @error('jenis_kontak')
                        <p class="text-[12px] text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nilai Kontak --}}
                <div class="space-y-2">
                    <label for="nilai_kontak" class="block text-[13px] font-medium text-[#1C1917]">
                        Nilai kontak <span class="text-[#B42318]">*</span>
                    </label>
                    <input
                        type="text"
                        id="nilai_kontak"
                        name="nilai_kontak"
                        value="{{ old('nilai_kontak') }}"
                        placeholder="Contoh: bantuan@instansi.org"
                        maxlength="150"
                        required
                        class="w-full h-12 rounded-[8px] bg-[#FAF9F7] px-4 text-[16px] text-[#1C1917] placeholder:text-[#686563] outline outline-1 -outline-offset-1 outline-[#DDDCD7] focus:outline-2 focus:outline-[#1C1917] transition-all"
                    />
                    <div class="text-[12px] text-[#686563]">
                        Sesuaikan format dengan jenis kontak: email, telepon, atau situs web.
                    </div>
                    @error('nilai_kontak')
                        <p class="text-[12px] text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Keterangan (opsional) --}}
                <div class="space-y-2">
                    <label for="keterangan" class="block text-[13px] font-medium text-[#1C1917]">
                        Keterangan (opsional)
                    </label>
                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="3"
                        maxlength="200"
                        placeholder="Contoh: Senin–Jumat, 09.00–16.00 WIB"
                        class="w-full min-h-[104px] rounded-[8px] bg-[#FAF9F7] p-4 text-[16px] text-[#1C1917] placeholder:text-[#686563] outline outline-1 -outline-offset-1 outline-[#DDDCD7] focus:outline-2 focus:outline-[#1C1917] transition-all resize-y"
                    >{{ old('keterangan') }}</textarea>
                    <div class="text-[12px] text-[#686563]">
                        Tambahkan tujuan penggunaan kontak atau waktu yang dapat dihubungi.
                    </div>
                    @error('keterangan')
                        <p class="text-[12px] text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-4 pt-4">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center h-12 px-8 rounded-[2px] bg-[#16A34A] text-[14px] font-medium text-[#1C1917] hover:bg-[#15803d] transition-colors"
                    >
                        Simpan kontak
                    </button>
                    <a
                        href="{{ route('instansi.layanan.index', $instansi) }}"
                        class="inline-flex items-center justify-center h-12 px-6 rounded-[2px] bg-[#EDECE9] text-[14px] font-medium text-[#1C1917] hover:bg-[#DDDCD7] transition-colors"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Right Column: Information Sidebar (approx 320px) --}}
        <div class="lg:col-span-4 space-y-4">
            <div class="rounded-[12px] bg-[#EDECE9] p-6 space-y-4">
                <h2 class="text-[20px] font-medium leading-tight text-[#1C1917]">Kontak yang mudah diakses</h2>
                <p class="text-[16px] text-[#686563] leading-relaxed">
                    Gunakan kontak resmi instansi yang dapat diakses publik. Jangan memasukkan nomor pribadi tanpa izin.
                </p>
                <div class="h-[1px] w-full bg-[#DDDCD7]"></div>
                <p class="text-[14px] text-[#686563] leading-relaxed">
                    Satu kontak untuk setiap entri. Tambahkan kontak lain dari daftar.
                </p>
            </div>

            <div class="text-[12px] text-[#686563] px-2">
                Data contoh · Seluruh informasi pada rancangan ini bersifat fiktif.
            </div>
        </div>
    </div>

</div>
@endsection
