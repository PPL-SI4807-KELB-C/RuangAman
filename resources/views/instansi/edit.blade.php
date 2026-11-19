<x-layouts.app :title="$title">
    <div class="w-full bg-[#FAF9F7] min-h-[calc(100vh-160px)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
            {{-- Breadcrumb / Back --}}
            <div class="mb-5">
                <a href="{{ route('instansi.index') }}" class="text-[13px] text-[#686563] hover:text-[#1C1917] font-normal transition inline-flex items-center gap-1.5">
                    <span>←</span>
                    <span>Kembali ke daftar instansi</span>
                </a>
            </div>

            {{-- Page Header --}}
            <div class="mb-8">
                <h1 class="text-[36px] font-medium text-[#1C1917] leading-tight">
                    Ubah instansi bantuan
                </h1>
                <p class="text-base text-[#686563] font-normal mt-2">
                    Lengkapi informasi instansi. Kolom bertanda * wajib diisi.
                </p>
            </div>

            {{-- Two Column Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                {{-- Form Column --}}
                <div class="lg:col-span-8">
                    <form action="{{ route('instansi.update', $instansi) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Nama Instansi --}}
                        <div>
                            <label for="nama_instansi" class="block text-[13px] font-medium text-[#1C1917] mb-1.5">
                                Nama instansi <span class="text-[#B42318]">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_instansi" 
                                   id="nama_instansi" 
                                   value="{{ old('nama_instansi', $instansi->nama_instansi) }}" 
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
                                      class="w-full bg-[#FAF9F7] text-base text-[#1C1917] placeholder-[#686563] px-4 py-3 rounded-lg border @error('alamat') border-[#B42318] @else border-[#DDDCD7] @enderror focus:outline-none focus:border-[#16A34A] transition resize-y">{{ old('alamat', $instansi->alamat) }}</textarea>
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
                                      class="w-full bg-[#FAF9F7] text-base text-[#1C1917] placeholder-[#686563] px-4 py-3 rounded-lg border @error('deskripsi') border-[#B42318] @else border-[#DDDCD7] @enderror focus:outline-none focus:border-[#16A34A] transition resize-y">{{ old('deskripsi', $instansi->deskripsi) }}</textarea>
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
                                   value="{{ old('website', $instansi->website) }}" 
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
                        <div x-data="{ currentStatus: '{{ old('status', $instansi->status) }}' }">
                            <label class="block text-[13px] font-medium text-[#1C1917] mb-2">
                                Status <span class="text-[#B42318]">*</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center justify-center px-5 py-2.5 rounded-[6px] text-sm font-medium cursor-pointer transition"
                                       :class="currentStatus === 'aktif' ? 'bg-[#EDECE9] text-[#1C1917]' : 'bg-[#FAF9F7] border border-[#DDDCD7] text-[#686563]'">
                                    <input type="radio" name="status" value="aktif" x-model="currentStatus" class="sr-only">
                                    <span class="mr-2" x-text="currentStatus === 'aktif' ? '◉' : '○'"></span>
                                    <span>Aktif</span>
                                </label>
                                <label class="inline-flex items-center justify-center px-5 py-2.5 rounded-[6px] text-sm font-medium cursor-pointer transition"
                                       :class="currentStatus === 'nonaktif' ? 'bg-[#EDECE9] text-[#1C1917]' : 'bg-[#FAF9F7] border border-[#DDDCD7] text-[#686563]'">
                                    <input type="radio" name="status" value="nonaktif" x-model="currentStatus" class="sr-only">
                                    <span class="mr-2" x-text="currentStatus === 'nonaktif' ? '◉' : '○'"></span>
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
                                Simpan perubahan
                            </button>
                            <a href="{{ route('instansi.index') }}" 
                               class="bg-[#EDECE9] hover:bg-[#e4e2de] text-[#1C1917] text-sm font-medium rounded-[2px] px-6 py-3 transition">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Sidebar Column --}}
                <div class="lg:col-span-4 lg:pl-4">
                    <div class="bg-[#EDECE9] rounded-[12px] p-6 text-[#1C1917]">
                        <h2 class="text-[20px] font-medium leading-snug">
                            Informasi yang membantu
                        </h2>
                        <p class="text-base text-[#686563] font-normal mt-2 leading-relaxed">
                            Tulis profil instansi dengan jelas agar pengguna dapat mengenali pilihan bantuan yang sesuai.
                        </p>
                        
                        <div class="w-full h-px bg-[#DDDCD7] my-5"></div>

                        <h3 class="text-sm font-medium text-[#1C1917]">
                            Sebelum menyimpan
                        </h3>
                        <p class="text-sm text-[#686563] font-normal mt-2 leading-relaxed">
                            Periksa nama, alamat, dan situs web. Hindari memasukkan data pribadi penyintas.
                        </p>
                    </div>

                    <p class="text-[12px] text-[#686563] mt-4 px-1">
                        Data contoh · Seluruh informasi instansi pada rancangan ini bersifat fiktif.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
