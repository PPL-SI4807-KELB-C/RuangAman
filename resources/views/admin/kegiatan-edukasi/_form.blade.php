@php
    $selectedMetode = old('metode', $kegiatan->metode ?? 'online');
    $tanggalMulaiVal = old('tanggal_mulai', $kegiatan->tanggal_mulai?->format('Y-m-d'));
    $tanggalSelesaiVal = old('tanggal_selesai', $kegiatan->tanggal_selesai?->format('Y-m-d'));
    $waktuMulaiVal = old('waktu_mulai', $kegiatan->waktu_mulai ? substr((string) $kegiatan->waktu_mulai, 0, 5) : '');
    $waktuSelesaiVal = old('waktu_selesai', $kegiatan->waktu_selesai ? substr((string) $kegiatan->waktu_selesai, 0, 5) : '');
@endphp

<div class="space-y-5" x-data="{ metode: '{{ $selectedMetode }}' }">
    <x-input
        name="judul"
        label="Judul Kegiatan / Webinar"
        :value="$kegiatan->judul"
        placeholder="Contoh: Webinar Perlindungan Diri di Ruang Digital"
        :error="$errors->first('judul')"
        required
    />

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-input
            name="narasumber"
            label="Narasumber / Pembicara"
            :value="$kegiatan->narasumber"
            placeholder="Contoh: Dr. Rina Kusuma, M.Psi."
            :error="$errors->first('narasumber')"
        />

        <x-input
            type="number"
            name="kuota"
            label="Kuota Peserta"
            min="1"
            :value="$kegiatan->kuota"
            placeholder="Minimal 1 peserta"
            :error="$errors->first('kuota')"
            required
        />
    </div>

    <x-textarea
        name="deskripsi"
        label="Deskripsi Kegiatan"
        :value="$kegiatan->deskripsi"
        rows="4"
        placeholder="Jelaskan topik, tujuan, sasaran peserta, dan manfaat kegiatan ini..."
        :error="$errors->first('deskripsi')"
    />

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <x-input
            type="date"
            name="tanggal_mulai"
            label="Tanggal Mulai"
            :value="$tanggalMulaiVal"
            :error="$errors->first('tanggal_mulai')"
            required
        />

        <x-input
            type="time"
            name="waktu_mulai"
            label="Waktu Mulai"
            :value="$waktuMulaiVal"
            :error="$errors->first('waktu_mulai')"
            required
        />

        <x-input
            type="date"
            name="tanggal_selesai"
            label="Tanggal Selesai"
            :value="$tanggalSelesaiVal"
            helper="Opsional jika berlangsung pada hari yang sama."
            :error="$errors->first('tanggal_selesai')"
        />

        <x-input
            type="time"
            name="waktu_selesai"
            label="Waktu Selesai"
            :value="$waktuSelesaiVal"
            :error="$errors->first('waktu_selesai')"
        />
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label for="metode" class="block text-sm font-medium text-slate-700 mb-1">
                Metode Pelaksanaan <span class="text-safety-rose" aria-hidden="true">*</span>
            </label>
            <select
                name="metode"
                id="metode"
                x-model="metode"
                required
                class="block w-full rounded-lg border shadow-sm text-sm transition bg-white px-3 py-2.5 {{ $errors->has('metode') ? 'border-safety-rose focus:ring-safety-rose focus:border-safety-rose' : 'border-slate-300 focus:ring-brand-500 focus:border-brand-500' }}"
            >
                @foreach ($metodeOptions as $val => $label)
                    <option value="{{ $val }}" @selected($selectedMetode === $val)>{{ $label }}</option>
                @endforeach
            </select>
            @error('metode')
                <p class="mt-1 text-xs text-safety-rose">{{ $message }}</p>
            @enderror
        </div>

        <x-select
            name="status"
            label="Status Publikasi Kegiatan"
            :options="$statusOptions"
            :value="$kegiatan->status"
            :error="$errors->first('status')"
            required
        />
    </div>

    <div>
        <label for="lokasi_atau_tautan" class="block text-sm font-medium text-slate-700 mb-1">
            <span x-text="metode === 'online' ? 'Tautan Meeting / Webinar' : 'Alamat Lokasi Pelaksanaan'"></span>
        </label>
        <input
            type="text"
            name="lokasi_atau_tautan"
            id="lokasi_atau_tautan"
            value="{{ old('lokasi_atau_tautan', $kegiatan->lokasi_atau_tautan) }}"
            :placeholder="metode === 'online' ? 'Contoh: https://zoom.us/j/123456789' : 'Contoh: Aula Serbaguna Gedung C, Bandung'"
            class="block w-full rounded-lg border shadow-sm text-sm transition px-3 py-2.5 {{ $errors->has('lokasi_atau_tautan') ? 'border-safety-rose text-safety-rose focus:ring-safety-rose focus:border-safety-rose' : 'border-slate-300 focus:ring-brand-500 focus:border-brand-500' }}"
        />
        <p class="mt-1 text-xs text-slate-500">
            <span x-show="metode === 'online'">Tautan meeting hanya akan ditampilkan kepada peserta yang sudah terdaftar aktif.</span>
            <span x-show="metode === 'offline'" x-cloak>Cantumkan alamat lengkap ruangan/gedung tempat kegiatan berlangsung.</span>
        </p>
        @error('lokasi_atau_tautan')
            <p class="mt-1 text-xs text-safety-rose">{{ $message }}</p>
        @enderror
    </div>
</div>

