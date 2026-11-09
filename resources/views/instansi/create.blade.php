<x-layouts.app :title="$title">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Page Header --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Tambah Instansi Bantuan
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Masukkan informasi lengkap untuk menambahkan instansi baru
            </p>
        </div>

        {{-- Form --}}
        <div class="bg-white shadow sm:rounded-lg">
            <form action="{{ route('instansi.store') }}" method="POST">
                @csrf
                
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    {{-- Nama Instansi --}}
                    <div>
                        <label for="nama_instansi" class="block text-sm font-medium text-gray-700">
                            Nama Instansi <span class="text-red-500">*</span>
                        </label>
                        <x-input id="nama_instansi" 
                                 name="nama_instansi" 
                                 type="text" 
                                 value="{{ old('nama_instansi') }}" 
                                 placeholder="Contoh: Dinas Sosial Provinsi Jawa Barat"
                                 required />
                        @error('nama_instansi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700">
                            Alamat <span class="text-red-500">*</span>
                        </label>
                        <x-textarea id="alamat" 
                                    name="alamat" 
                                    rows="4"
                                    placeholder="Masukkan alamat lengkap instansi..."
                                    required>{{ old('alamat') }}</x-textarea>
                        @error('alamat')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">
                            Deskripsi
                        </label>
                        <x-textarea id="deskripsi" 
                                    name="deskripsi" 
                                    rows="3"
                                    placeholder="Deskripsi singkat tentang instansi...">{{ old('deskripsi') }}</x-textarea>
                        @error('deskripsi')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Website --}}
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700">
                            Website
                        </label>
                        <x-input id="website" 
                                 name="website" 
                                 type="url" 
                                 value="{{ old('website') }}" 
                                 placeholder="https://example.com" />
                        @error('website')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <x-select id="status" 
                                  name="status" 
                                  required>
                            <option value="">Pilih Status</option>
                            <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </x-select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Footer / Actions --}}
                <div class="bg-gray-50 px-4 py-4 sm:px-7 sm:py-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <a href="{{ route('instansi.index') }}" 
                       class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
