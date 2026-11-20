@php
    $isEdit = $konselor->exists;
@endphp

<div class="space-y-5">
    @if ($isEdit)
        <div class="rounded bg-white p-4 ring-1 ring-inset ring-neutral-300/70">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-neutral-500">Akun Pengguna Terhubung</p>
            <p class="mt-1 text-sm font-semibold text-neutral-900">
                {{ $konselor->user?->nama_tampilan ?? 'Akun tidak ditemukan' }}
            </p>
            <p class="mt-0.5 text-xs text-neutral-500">
                Akun #{{ $konselor->user_id }} &middot; Role Konselor
            </p>
            <p class="mt-2 text-[11px] text-neutral-500">
                Akun pengguna tidak dapat diubah setelah profil dibuat agar relasi jadwal konseling tetap konsisten.
            </p>
        </div>
    @elseif (empty($calonPenggunaOptions))
        <x-alert variant="warning" title="Belum ada akun yang dapat didaftarkan">
            Seluruh akun ber-role Konselor sudah memiliki profil, atau belum ada akun Konselor yang terdaftar.
            Buat/ubah role akun terlebih dahulu melalui modul manajemen pengguna.
        </x-alert>
    @else
        <x-select
            name="user_id"
            label="Akun Pengguna (Role Konselor)"
            :options="$calonPenggunaOptions"
            :value="$konselor->user_id"
            placeholder="Pilih akun konselor"
            helper="Hanya akun ber-role Konselor yang belum memiliki profil yang ditampilkan. Email akun tidak ditampilkan demi privasi."
            :error="$errors->first('user_id')"
            required
        />
    @endif

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-input
            name="nama"
            label="Nama Lengkap & Gelar"
            :value="$konselor->nama"
            placeholder="Contoh: Rina Kusuma, M.Psi."
            :error="$errors->first('nama')"
            required
        />

        <x-input
            name="spesialisasi"
            label="Spesialisasi / Kualifikasi"
            :value="$konselor->spesialisasi"
            placeholder="Contoh: Psikologi Klinis Dewasa"
            :error="$errors->first('spesialisasi')"
            required
        />
    </div>

    <x-textarea
        name="bio"
        label="Bio Profesional"
        :value="$konselor->bio"
        rows="4"
        placeholder="Ringkasan latar belakang profesional, pendekatan konseling, dan area pendampingan..."
        helper="Bio ditampilkan pada halaman profil konselor untuk membantu pelapor memilih pendamping."
        :error="$errors->first('bio')"
    />

    <x-select
        name="status"
        label="Status Ketersediaan"
        :options="$statusOptions"
        :value="$konselor->status"
        helper="Konselor berstatus cuti/nonaktif tidak dapat menerima slot jadwal konseling baru."
        :error="$errors->first('status')"
        required
    />
</div>
