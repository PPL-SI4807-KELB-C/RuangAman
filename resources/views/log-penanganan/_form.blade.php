@php
    $isEdit = $log->exists;
    $waktuValue = old('waktu', $log->waktu ? $log->waktu->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
    $aktivitasValue = old('aktivitas', $log->aktivitas ?? '');
@endphp

<div class="space-y-5" x-data="{ panjangTeks: {{ mb_strlen((string) $aktivitasValue) }} }">
    @if ($isEdit)
        <div class="rounded-lg border border-neutral-200 bg-surface p-4 text-xs">
            <p class="font-semibold uppercase tracking-wider text-neutral-600">Kasus Terkait</p>
            <p class="mt-1 font-medium text-neutral-900">
                {{ $log->penangananKasus?->laporanKasus?->kode_laporan ?? 'Tanpa Kode' }}
                &mdash;
                {{ $log->penangananKasus?->laporanKasus?->judul ?? 'Penanganan #' . $log->penanganan_id }}
            </p>
            <p class="mt-1 text-neutral-600">
                Pendamping: {{ $log->penangananKasus?->petugas?->nama_tampilan ?? 'Belum ditetapkan' }}
            </p>
        </div>
    @else
        <div>
            <label for="penanganan_id" class="mb-1 block text-sm font-medium text-neutral-800">
                Penanganan Kasus Aktif <span class="text-safety-rose" aria-hidden="true">*</span>
            </label>
            <select
                name="penanganan_id"
                id="penanganan_id"
                required
                class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset {{ $errors->has('penanganan_id') ? 'ring-safety-rose focus-visible:ring-safety-rose' : 'ring-line-strong focus-visible:ring-neutral-900' }} focus:outline-none focus-visible:ring-2"
            >
                <option value="" disabled @selected(old('penanganan_id', $selectedPenangananId ?? '') === '' || old('penanganan_id', $selectedPenangananId ?? '') === null)>
                    Pilih kasus yang ditangani...
                </option>
                @foreach ($penangananList as $item)
                    <option
                        value="{{ $item->penanganan_id }}"
                        @selected((string) old('penanganan_id', $selectedPenangananId ?? '') === (string) $item->penanganan_id)
                    >
                        {{ $item->laporanKasus?->kode_laporan ?? '#' . $item->penanganan_id }}
                        — {{ $item->laporanKasus?->judul ?? 'Tanpa Judul' }}
                        ({{ $item->petugas?->nama_tampilan ?? 'Petugas' }})
                    </option>
                @endforeach
            </select>
            @if ($penangananList->isEmpty())
                <p class="mt-1 text-xs text-safety-warning">
                    Belum ada kasus aktif yang tersedia untuk ditambahkan catatannya.
                </p>
            @endif
            @error('penanganan_id')
                <p class="mt-1 text-xs text-safety-rose">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <div>
        <label for="waktu" class="mb-1 block text-sm font-medium text-neutral-800">
            Waktu Aktivitas
        </label>
        <input
            type="datetime-local"
            name="waktu"
            id="waktu"
            value="{{ $waktuValue }}"
            class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset {{ $errors->has('waktu') ? 'ring-safety-rose focus-visible:ring-safety-rose' : 'ring-line-strong focus-visible:ring-neutral-900' }} focus:outline-none focus-visible:ring-2"
        />
        <p class="mt-1 text-xs text-neutral-600">Kosongkan untuk memakai waktu saat ini secara otomatis.</p>
        @error('waktu')
            <p class="mt-1 text-xs text-safety-rose">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <div class="mb-1 flex items-center justify-between">
            <label for="aktivitas" class="block text-sm font-medium text-neutral-800">
                Catatan Aktivitas Penanganan <span class="text-safety-rose" aria-hidden="true">*</span>
            </label>
            <span class="text-xs text-neutral-600"><span x-text="panjangTeks"></span> / 5000 karakter</span>
        </div>
        <textarea
            name="aktivitas"
            id="aktivitas"
            rows="5"
            required
            x-on:input="panjangTeks = $event.target.value.length"
            placeholder="Catat perkembangan asesmen, koordinasi layanan pendampingan, atau tindak lanjut yang netral dan faktual..."
            class="block w-full rounded bg-white px-3 py-2.5 text-sm text-neutral-900 ring-1 ring-inset resize-y {{ $errors->has('aktivitas') ? 'ring-safety-rose focus-visible:ring-safety-rose' : 'ring-line-strong focus-visible:ring-neutral-900' }} focus:outline-none focus-visible:ring-2"
        >{{ $aktivitasValue }}</textarea>
        <p class="mt-1.5 text-xs text-neutral-600">
            Jaga kerahasiaan pelapor: hindari menuliskan alamat email, nomor induk resmi, atau data pribadi sensitif di luar keperluan penanganan.
        </p>
        @error('aktivitas')
            <p class="mt-1 text-xs text-safety-rose">{{ $message }}</p>
        @enderror
    </div>
</div>
