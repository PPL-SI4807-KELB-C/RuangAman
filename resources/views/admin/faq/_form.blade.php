{{-- Pertanyaan --}}
<div>
    <label for="pertanyaan" class="block text-[13px] font-medium text-[#1C1917] mb-1.5">
        Pertanyaan <span class="text-[#B42318]">*</span>
    </label>
    <textarea name="pertanyaan"
              id="pertanyaan"
              rows="2"
              required
              placeholder="Tuliskan pertanyaan yang sering ditanyakan pengguna."
              class="w-full min-h-[72px] bg-[#FAF9F7] text-base text-[#1C1917] placeholder-[#686563] px-4 py-3 rounded-lg border @error('pertanyaan') border-[#B42318] @else border-[#DDDCD7] @enderror focus:outline-none focus:border-[#16A34A] transition resize-y">{{ old('pertanyaan', $faq->pertanyaan) }}</textarea>
    @error('pertanyaan')
        <p class="text-xs text-[#B42318] mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Jawaban --}}
<div>
    <label for="jawaban" class="block text-[13px] font-medium text-[#1C1917] mb-1.5">
        Jawaban <span class="text-[#B42318]">*</span>
    </label>
    <textarea name="jawaban"
              id="jawaban"
              rows="6"
              required
              placeholder="Tuliskan jawaban yang jelas, ringkas, dan tidak menghakimi."
              class="w-full min-h-[176px] bg-[#FAF9F7] text-base text-[#1C1917] placeholder-[#686563] px-4 py-3 rounded-lg border @error('jawaban') border-[#B42318] @else border-[#DDDCD7] @enderror focus:outline-none focus:border-[#16A34A] transition resize-y">{{ old('jawaban', $faq->jawaban) }}</textarea>
    @error('jawaban')
        <p class="text-xs text-[#B42318] mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Urutan tampil --}}
<div>
    <label for="urutan" class="block text-[13px] font-medium text-[#1C1917] mb-1.5">
        Urutan tampil <span class="text-[#B42318]">*</span>
    </label>
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6">
        <input type="number"
               name="urutan"
               id="urutan"
               min="1"
               step="1"
               required
               value="{{ old('urutan', $faq->urutan ?? 1) }}"
               class="w-36 bg-[#FAF9F7] text-base text-[#1C1917] px-4 py-2.5 rounded-lg border @error('urutan') border-[#B42318] @else border-[#DDDCD7] @enderror focus:outline-none focus:border-[#16A34A] transition">
        <span class="text-[14px] text-[#686563]">
            Angka lebih kecil ditampilkan lebih dahulu.
        </span>
    </div>
    <p class="text-[12px] text-[#686563] mt-2">
        Gunakan bilangan bulat mulai dari 1.
    </p>
    @error('urutan')
        <p class="text-xs text-[#B42318] mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Status publikasi --}}
<div x-data="{ currentStatus: '{{ old('status_publikasi', $faq->status_publikasi ?? 'draft') }}' }">
    <span class="block text-[13px] font-medium text-[#1C1917] mb-2">
        Status publikasi <span class="text-[#B42318]">*</span>
    </span>
    <div class="flex flex-wrap items-center gap-4">
        <label class="inline-flex items-center justify-start min-w-[164px] px-4 py-2.5 rounded-[6px] text-sm font-medium border border-[#DDDCD7] cursor-pointer transition"
               :class="currentStatus === 'draft' ? 'bg-[#EDECE9] text-[#1C1917]' : 'bg-[#FAF9F7] text-[#686563]'">
            <input type="radio" name="status_publikasi" value="draft" x-model="currentStatus" class="sr-only">
            <span class="mr-2" aria-hidden="true" x-text="currentStatus === 'draft' ? '◉' : '○'"></span>
            <span>Draf</span>
        </label>
        <label class="inline-flex items-center justify-start min-w-[240px] px-4 py-2.5 rounded-[6px] text-sm font-medium border border-[#DDDCD7] cursor-pointer transition"
               :class="currentStatus === 'published' ? 'bg-[#EDECE9] text-[#1C1917]' : 'bg-[#FAF9F7] text-[#686563]'">
            <input type="radio" name="status_publikasi" value="published" x-model="currentStatus" class="sr-only">
            <span class="mr-2" aria-hidden="true" x-text="currentStatus === 'published' ? '◉' : '○'"></span>
            <span>Dipublikasikan</span>
        </label>
    </div>
    <p class="text-[12px] text-[#686563] mt-2">
        Draf hanya terlihat oleh admin. FAQ yang dipublikasikan tampil kepada publik.
    </p>
    @error('status_publikasi')
        <p class="text-xs text-[#B42318] mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- Action Buttons --}}
<div class="pt-4 flex items-center gap-4">
    <button type="submit"
            class="bg-[#16A34A] hover:bg-[#15803d] text-[#1C1917] text-sm font-medium rounded-[2px] px-8 py-3 transition shadow-none">
        {{ $submitLabel }}
    </button>
    <a href="{{ route('admin.faq.index') }}"
       class="bg-[#EDECE9] hover:bg-[#e4e2de] text-[#1C1917] text-sm font-medium rounded-[2px] px-6 py-3 transition">
        Batal
    </a>
</div>
