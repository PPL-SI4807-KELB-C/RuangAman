<div class="space-y-5">
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-input
            name="istilah"
            label="Istilah"
            :value="$glosarium->istilah"
            placeholder="Contoh: Kekerasan Berbasis Gender Online"
            :error="$errors->first('istilah')"
            required
        />

        <x-input
            name="kategori"
            label="Kategori"
            :value="$glosarium->kategori"
            placeholder="Contoh: Bentuk Kekerasan, Hukum, Pendampingan"
            helper="Membantu pengelompokan istilah pada daftar glosarium."
            :error="$errors->first('kategori')"
        />
    </div>

    <x-textarea
        name="definisi"
        label="Definisi"
        :value="$glosarium->definisi"
        rows="6"
        placeholder="Tulis definisi dengan bahasa yang mudah dipahami penyintas."
        :error="$errors->first('definisi')"
        required
    />

    <x-select
        name="status_publikasi"
        label="Status Publikasi"
        :options="$statusOptions"
        :value="$glosarium->status_publikasi"
        helper="Hanya istilah berstatus Published yang tampil di halaman glosarium publik."
        :error="$errors->first('status_publikasi')"
        required
    />
</div>
