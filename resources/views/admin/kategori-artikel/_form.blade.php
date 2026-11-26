<div class="space-y-5">
    <x-input
        name="nama_kategori"
        label="Nama Kategori"
        :value="$kategori->nama_kategori"
        placeholder="Contoh: Pencegahan, Pemulihan, Hukum dan Hak Korban"
        :helper="$kategori->exists && $kategori->isDefault() ? 'Kategori bawaan tidak dapat diganti namanya.' : null"
        :error="$errors->first('nama_kategori')"
        required
    />

    <x-textarea
        name="deskripsi"
        label="Deskripsi"
        :value="$kategori->deskripsi"
        rows="3"
        placeholder="Jelaskan singkat jenis artikel yang masuk kategori ini (opsional)."
        :error="$errors->first('deskripsi')"
    />
</div>
