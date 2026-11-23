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
        <h1 class="text-3xl sm:text-4xl font-medium text-[#1C1917] tracking-tight">Ubah layanan bantuan</h1>
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

            <form method="POST" action="{{ route('instansi.layanan.update', [$instansi, $layanan]) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Nama Layanan --}}
                <div class="space-y-2">
                    <label for="nama_layanan" class="block text-[13px] font-medium text-[#1C1917]">
                        Nama layanan <span class="text-[#B42318]">*</span>
                    </label>
                    <input
                        type="text"
                        id="nama_layanan"
                        name="nama_layanan"
                        value="{{ old('nama_layanan', $layanan->nama_layanan) }}"
                        placeholder="Contoh: Pendampingan psikologis"
                        maxlength="150"
                        required
                        class="w-full h-12 rounded-[8px] bg-[#FAF9F7] px-4 text-[16px] text-[#1C1917] placeholder:text-[#686563] outline outline-1 -outline-offset-1 outline-[#DDDCD7] focus:outline-2 focus:outline-[#1C1917] transition-all"
                    />
                    @error('nama_layanan')
                        <p class="text-[12px] text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="space-y-2">
                    <label for="deskripsi" class="block text-[13px] font-medium text-[#1C1917]">
                        Deskripsi <span class="text-[#B42318]">*</span>
                    </label>
                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        rows="5"
                        placeholder="Jelaskan bantuan yang diberikan oleh layanan ini."
                        required
                        class="w-full min-h-[144px] rounded-[8px] bg-[#FAF9F7] p-4 text-[16px] text-[#1C1917] placeholder:text-[#686563] outline outline-1 -outline-offset-1 outline-[#DDDCD7] focus:outline-2 focus:outline-[#1C1917] transition-all resize-y"
                    >{{ old('deskripsi', $layanan->deskripsi) }}</textarea>
                    <div class="text-[12px] text-[#686563]">
                        Gunakan bahasa yang jelas, tidak menghakimi, dan mudah dipahami.
                    </div>
                    @error('deskripsi')
                        <p class="text-[12px] text-[#B42318]">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-4 pt-4">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center h-12 px-8 rounded-[2px] bg-[#16A34A] text-[14px] font-medium text-[#1C1917] hover:bg-[#15803d] transition-colors"
                    >
                        Simpan perubahan
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
                <h2 class="text-[20px] font-medium leading-tight text-[#1C1917]">Bantuan yang mudah dipahami</h2>
                <p class="text-[16px] text-[#686563] leading-relaxed">
                    Jelaskan bentuk pendampingan dan tujuannya agar pengguna dapat memahami pilihan bantuan.
                </p>
                <div class="h-[1px] w-full bg-[#DDDCD7]"></div>
                <p class="text-[14px] text-[#686563] leading-relaxed">
                    Satu layanan untuk setiap entri. Tambahkan layanan lain dari daftar.
                </p>
            </div>

            <div class="text-[12px] text-[#686563] px-2">
                Data contoh · Seluruh informasi pada rancangan ini bersifat fiktif.
            </div>
        </div>
    </div>

</div>
@endsection
