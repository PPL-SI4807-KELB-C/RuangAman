<div class="space-y-5">
    <x-input
        name="nama_status"
        id="nama_status"
        label="Nama Status"
        :value="old('nama_status', $statusKasus->nama_status)"
        placeholder="Contoh: Menunggu Dokumen Tambahan"
        :required="true"
        :error="$errors->first('nama_status')"
        helper="Maksimal 50 karakter. Nama status harus unik."
    />

    <x-textarea
        name="deskripsi"
        id="deskripsi"
        label="Deskripsi Status"
        :value="old('deskripsi', $statusKasus->deskripsi)"
        placeholder="Penjelasan ringkas mengenai kondisi kasus saat berada pada status ini..."
        rows="4"
        :error="$errors->first('deskripsi')"
        helper="Opsional. Jelaskan makna status agar mempermudah alur penanganan."
    />
</div>

