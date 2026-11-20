<div class="space-y-5">
    <!-- Pilihan Laporan Kasus -->
    <x-select
        name="laporan_id"
        id="laporan_id"
        label="Laporan Kasus"
        :options="$laporanList->mapWithKeys(fn($lap) => [$lap->laporan_id => '[' . $lap->kode_laporan . '] ' . \Illuminate\Support\Str::limit($lap->judul, 60) . ' • Status: ' . ($lap->statusKasus?->nama_status ?? 'N/A')])->all()"
        :value="old('laporan_id', $penanganan->laporan_id)"
        placeholder="-- Pilih Laporan Kasus --"
        helper="Satu laporan kasus hanya dapat memiliki satu penugasan aktif (laporan_id unik)."
        :error="$errors->first('laporan_id')"
        required
    />

    <!-- Pilihan Petugas / Konselor -->
    <x-select
        name="petugas_id"
        id="petugas_id"
        label="Petugas / Konselor Pendamping"
        :options="$daftarPetugas->mapWithKeys(fn($p) => [$p->user_id => $p->nama_tampilan . ' (' . $p->email . ') • Role: ' . ($p->role?->nama_role ?? '-')])->all()"
        :value="old('petugas_id', $penanganan->petugas_id)"
        placeholder="-- Pilih Petugas / Konselor --"
        helper="Pilih tenaga pendamping atau Admin Kasus yang akan menangani kasus ini."
        :error="$errors->first('petugas_id')"
        required
    />

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