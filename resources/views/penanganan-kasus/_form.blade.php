<div class="space-y-5">
    <!-- Pilihan Laporan Kasus -->
    <div>
        <label for="laporan_id" class="block text-sm font-medium text-slate-700 mb-1">
            Laporan Kasus <span class="text-safety-rose" aria-hidden="true">*</span>
        </label>
        <select
            name="laporan_id"
            id="laporan_id"
            required
            class="block w-full rounded-lg border shadow-sm text-sm transition bg-white px-3 py-2.5 {{ $errors->has('laporan_id') ? 'border-safety-rose focus:ring-safety-rose focus:border-safety-rose' : 'border-slate-300 focus:ring-brand-500 focus:border-brand-500' }}"
        >
            <option value="" disabled {{ old('laporan_id', $penanganan->laporan_id) ? '' : 'selected' }}>
                -- Pilih Laporan Kasus --
            </option>
            @foreach ($laporanList as $lap)
                <option
                    value="{{ $lap->laporan_id }}"
                    {{ old('laporan_id', $penanganan->laporan_id) == $lap->laporan_id ? 'selected' : '' }}
                >
                    [{{ $lap->kode_laporan }}] {{ Str::limit($lap->judul, 60) }} &bull; Status: {{ $lap->statusKasus?->nama_status ?? 'N/A' }}
                </option>
            @endforeach
        </select>
        @if ($errors->has('laporan_id'))
            <p class="mt-1 text-xs text-safety-rose">{{ $errors->first('laporan_id') }}</p>
        @else
            <p class="mt-1 text-xs text-slate-500">Satu laporan kasus hanya dapat memiliki satu penugasan aktif (laporan_id unik).</p>
        @endif
    </div>

    <!-- Pilihan Petugas / Konselor -->
    <div>
        <label for="petugas_id" class="block text-sm font-medium text-slate-700 mb-1">
            Petugas / Konselor Pendamping <span class="text-safety-rose" aria-hidden="true">*</span>
        </label>
        <select
            name="petugas_id"
            id="petugas_id"
            required
            class="block w-full rounded-lg border shadow-sm text-sm transition bg-white px-3 py-2.5 {{ $errors->has('petugas_id') ? 'border-safety-rose focus:ring-safety-rose focus:border-safety-rose' : 'border-slate-300 focus:ring-brand-500 focus:border-brand-500' }}"
        >
            <option value="" disabled {{ old('petugas_id', $penanganan->petugas_id) ? '' : 'selected' }}>
                -- Pilih Petugas / Konselor --
            </option>
            @foreach ($daftarPetugas as $p)
                <option
                    value="{{ $p->user_id }}"
                    {{ old('petugas_id', $penanganan->petugas_id) == $p->user_id ? 'selected' : '' }}
                >
                    {{ $p->nama_tampilan }} ({{ $p->email }}) &bull; Role: {{ $p->role?->nama_role }}
                </option>
            @endforeach
        </select>
        @if ($errors->has('petugas_id'))
            <p class="mt-1 text-xs text-safety-rose">{{ $errors->first('petugas_id') }}</p>
        @else
            <p class="mt-1 text-xs text-slate-500">Pilih tenaga pendamping atau Admin Kasus yang akan menangani kasus ini.</p>
        @endif
    </div>

    <!-- Tanggal Mulai & Tanggal Selesai -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-input
            type="date"
            name="tanggal_mulai"
            id="tanggal_mulai"
            label="Tanggal Mulai Penanganan"
            :value="old('tanggal_mulai', optional($penanganan->tanggal_mulai)->format('Y-m-d') ?? date('Y-m-d'))"
            :required="true"
            :error="$errors->first('tanggal_mulai')"
            helper="Waktu penugasan resmi dimulai."
        />

        <x-input
            type="date"
            name="tanggal_selesai"
            id="tanggal_selesai"
            label="Tanggal Selesai Penanganan"
            :value="old('tanggal_selesai', optional($penanganan->tanggal_selesai)->format('Y-m-d'))"
            :required="false"
            :error="$errors->first('tanggal_selesai')"
            helper="Opsional. Kosongkan jika penanganan masih berlangsung."
        />
    </div>

    <!-- Catatan Penanganan -->
    <x-textarea
        name="catatan"
        id="catatan"
        label="Catatan Arahan Penugasan"
        :value="old('catatan', $penanganan->catatan)"
        placeholder="Instruksi awal, poin perhatian khusus dari Admin Kasus untuk petugas pendamping..."
        rows="4"
        :error="$errors->first('catatan')"
        helper="Catatan internal yang dapat dibaca oleh konselor/petugas yang ditugaskan."
    />
</div>

