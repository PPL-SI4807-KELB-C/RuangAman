@php
    $instansi ??= null;
@endphp

{{-- Nama Instansi --}}
<div>
    <label for="nama_instansi" class="block text-[13px] font-medium text-[#1C1917] mb-1.5">
        Nama instansi <span class="text-[#B42318]">*</span>
    </label>
    <input type="text"
           name="nama_instansi"
           id="nama_instansi"
           value="{{ old('nama_instansi', $instansi?->nama_instansi) }}"
           placeholder="Contoh: Pusat Bantuan Harmoni"
           required
           class="w-full bg-[#FAF9F7] text-base text-[#1C1917] placeholder-[#686563] px-4 py-3 rounded-lg border @error('nama_instansi') border-[#B42318] @else border-[#DDDCD7] @enderror focus:outline-none focus:border-[#16A34A] transition">
    @error('nama_instansi')
        <p class="text-xs text-[#B42318] mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Alamat Lengkap --}}
<div>
    <label for="alamat" class="block text-[13px] font-medium text-[#1C1917] mb-1.5">
        Alamat lengkap <span class="text-[#B42318]">*</span>
    </label>
    <textarea name="alamat"
              id="alamat"
              rows="3"
              required
              placeholder="Tuliskan alamat lengkap instansi"
              class="w-full bg-[#FAF9F7] text-base text-[#1C1917] placeholder-[#686563] px-4 py-3 rounded-lg border @error('alamat') border-[#B42318] @else border-[#DDDCD7] @enderror focus:outline-none focus:border-[#16A34A] transition resize-y">{{ old('alamat', $instansi?->alamat) }}</textarea>
    <p class="text-[12px] text-[#686563] mt-1">
        Isi sebagai teks bebas, termasuk kota/kabupaten dan provinsi.
    </p>
    @error('alamat')
        <p class="text-xs text-[#B42318] mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Deskripsi --}}
<div>
    <label for="deskripsi" class="block text-[13px] font-medium text-[#1C1917] mb-1.5">
        Deskripsi <span class="text-[#B42318]">*</span>
    </label>
    <textarea name="deskripsi"
              id="deskripsi"
              rows="4"
              required
              placeholder="Jelaskan profil dan fokus pendampingan instansi."
              class="w-full bg-[#FAF9F7] text-base text-[#1C1917] placeholder-[#686563] px-4 py-3 rounded-lg border @error('deskripsi') border-[#B42318] @else border-[#DDDCD7] @enderror focus:outline-none focus:border-[#16A34A] transition resize-y">{{ old('deskripsi', $instansi?->deskripsi) }}</textarea>
    @error('deskripsi')
        <p class="text-xs text-[#B42318] mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Situs Web --}}
<div>
    <label for="website" class="block text-[13px] font-medium text-[#1C1917] mb-1.5">
        Situs web (opsional)
    </label>
    <input type="url"
           name="website"
           id="website"
           value="{{ old('website', $instansi?->website) }}"
           placeholder="https://contoh.org"
           class="w-full bg-[#FAF9F7] text-base text-[#1C1917] placeholder-[#686563] px-4 py-3 rounded-lg border @error('website') border-[#B42318] @else border-[#DDDCD7] @enderror focus:outline-none focus:border-[#16A34A] transition">
    <p class="text-[12px] text-[#686563] mt-1">
        Gunakan alamat lengkap dengan https://. Kosongkan jika belum tersedia.
    </p>
    @error('website')
        <p class="text-xs text-[#B42318] mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Status --}}
<div x-data="{ currentStatus: '{{ old('status', $instansi?->status ?? 'aktif') }}' }">
    <span class="block text-[13px] font-medium text-[#1C1917] mb-2">
        Status <span class="text-[#B42318]">*</span>
    </span>
    <div class="flex items-center gap-3">
        <label class="inline-flex items-center justify-center min-w-[164px] px-5 py-2.5 rounded-[6px] text-sm font-medium cursor-pointer transition"
               :class="currentStatus === 'aktif' ? 'bg-[#EDECE9] text-[#1C1917]' : 'bg-[#FAF9F7] border border-[#DDDCD7] text-[#686563]'">
            <input type="radio" name="status" value="aktif" x-model="currentStatus" class="sr-only">
            <span class="mr-2" aria-hidden="true" x-text="currentStatus === 'aktif' ? '◉' : '○'"></span>
            <span>Aktif</span>
        </label>
        <label class="inline-flex items-center justify-center min-w-[164px] px-5 py-2.5 rounded-[6px] text-sm font-medium cursor-pointer transition"
               :class="currentStatus === 'nonaktif' ? 'bg-[#EDECE9] text-[#1C1917]' : 'bg-[#FAF9F7] border border-[#DDDCD7] text-[#686563]'">
            <input type="radio" name="status" value="nonaktif" x-model="currentStatus" class="sr-only">
            <span class="mr-2" aria-hidden="true" x-text="currentStatus === 'nonaktif' ? '◉' : '○'"></span>
            <span>Nonaktif</span>
        </label>
    </div>
    <p class="text-[12px] text-[#686563] mt-2">
        Instansi aktif ditampilkan di direktori publik. Nonaktif hanya terlihat oleh admin.
    </p>
    @error('status')
        <p class="text-xs text-[#B42318] mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Action Buttons --}}
<div class="pt-4 flex items-center gap-4">
    <button type="submit"
            class="bg-[#16A34A] hover:bg-[#15803d] text-[#1C1917] text-sm font-medium rounded-[2px] px-8 py-3 transition shadow-none">
        {{ $submitLabel }}
    </button>
    <a href="{{ route('instansi.index') }}"
       class="bg-[#EDECE9] hover:bg-[#e4e2de] text-[#1C1917] text-sm font-medium rounded-[2px] px-6 py-3 transition">
        Batal
    </a>
</div>
