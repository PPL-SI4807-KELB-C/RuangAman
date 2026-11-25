<div class="space-y-5">
    <x-input
        name="nama_instansi"
        label="Nama Instansi"
        :value="$instansi->nama_instansi"
        placeholder="Contoh: Pusat Bantuan Harmoni"
        :error="$errors->first('nama_instansi')"
        required
    />

    <x-textarea
        name="alamat"
        label="Alamat Lengkap"
        :value="$instansi->alamat"
        rows="3"
        placeholder="Tuliskan alamat lengkap instansi"
        helper="Isi sebagai teks bebas, termasuk kota/kabupaten dan provinsi."
        :error="$errors->first('alamat')"
        required
    />

    <x-textarea
        name="deskripsi"
        label="Deskripsi"
        :value="$instansi->deskripsi"
        rows="4"
        placeholder="Jelaskan profil dan fokus pendampingan instansi."
        :error="$errors->first('deskripsi')"
        required
    />

    <x-input
        name="website"
        label="Situs Web"
        type="url"
        :value="$instansi->website"
        placeholder="https://contoh.org"
        helper="Opsional. Gunakan alamat lengkap dengan https://."
        :error="$errors->first('website')"
    />

    <x-radio-cards
        name="status"
        label="Status"
        :value="$instansi->status ?? 'aktif'"
        :options="[
            'aktif' => ['label' => 'Aktif', 'description' => 'Tampil di direktori publik.'],
            'nonaktif' => ['label' => 'Nonaktif', 'description' => 'Hanya terlihat oleh admin.'],
        ]"
        :error="$errors->first('status')"
        required
    />
</div>
