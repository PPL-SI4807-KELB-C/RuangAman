@extends('layouts.app')

@section('title', 'Buat Laporan — RuangAman')

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">

    <x-page-header
        title="Buat laporan"
        description="Ceritakan sebatas yang Anda siap bagikan. Kolom bertanda * wajib diisi."
        :back="url('/')"
        back-label="Kembali ke beranda"
    />

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-start">

        <div class="lg:col-span-8 space-y-6">
            @if ($errors->any())
                <x-alert variant="danger" title="Laporan belum terkirim">
                    Periksa kembali kolom yang ditandai di bawah ini.
                </x-alert>
            @endif

            <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- 1. Identitas & anonimitas (PBI-17) --}}
                <x-card header="Identitas & anonimitas">
                    <div class="space-y-5">
                        <div class="flex items-start gap-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <div>
                                <p class="text-sm text-neutral-700">Dilaporkan sebagai <span class="font-semibold text-neutral-900">{{ $alias }}</span></p>
                                <p class="mt-0.5 text-xs leading-relaxed text-neutral-600">
                                    Alias dapat diubah di <a href="{{ route('profil-pelapor.edit') }}" class="font-medium text-neutral-900 underline">profil &amp; mode anonim</a>.
                                </p>
                            </div>
                        </div>

                        <div>
                            <input type="hidden" name="mode_anonim" value="0">
                            <x-checkbox name="mode_anonim" :checked="(bool) ($defaultAnonim ?? true)" :error="$errors->first('mode_anonim')">
                                <span class="font-semibold text-neutral-900">Aktifkan mode anonim</span>
                                — alias pun disembunyikan dari konselor yang menangani laporan ini.
                            </x-checkbox>
                        </div>
                    </div>
                </x-card>

                {{-- 2. Detail kejadian --}}
                <x-card header="Detail kejadian">
                    <div class="space-y-6">
                        <x-input
                            name="judul"
                            label="Judul singkat"
                            placeholder="Ringkasan satu kalimat"
                            :value="$draf['judul'] ?? null"
                            :error="$errors->first('judul')"
                            required
                        />

                        <x-radio-cards
                            name="kategori_kekerasan"
                            label="Kategori kejadian"
                            helper="Pilih yang paling mendekati. Tidak apa-apa jika belum yakin."
                            :options="$kategoriOptions"
                            :value="$draf['kategori'] ?? null"
                            :error="$errors->first('kategori_kekerasan')"
                            required
                        />

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <x-input
                                type="datetime-local"
                                name="waktu_kejadian"
                                label="Waktu kejadian (perkiraan)"
                                :max="now()->format('Y-m-d\TH:i')"
                                :error="$errors->first('waktu_kejadian')"
                                required
                            />
                            <x-select
                                name="tingkat_urgensi"
                                label="Seberapa mendesak?"
                                :options="$urgensiOptions"
                                placeholder="Pilih tingkat"
                                :error="$errors->first('tingkat_urgensi')"
                                required
                            />
                        </div>

                        <x-input
                            name="lokasi_kejadian"
                            label="Lokasi kejadian"
                            placeholder="Kota, institusi, atau &quot;daring&quot;"
                            helper="Cukup lokasi umum. Hindari alamat rumah lengkap."
                            :error="$errors->first('lokasi_kejadian')"
                            required
                        />
                    </div>
                </x-card>

                {{-- 3. Kronologi & bukti --}}
                <x-card header="Kronologi & bukti">
                    <div class="space-y-6">
                        <x-textarea
                            name="kronologi"
                            label="Kronologi kejadian"
                            rows="6"
                            :value="$draf['kronologi'] ?? ''"
                            placeholder="Ceritakan urutan kejadian dan bantuan yang Anda butuhkan."
                            helper="Anda dapat menyertakan waktu, konteks, dan bantuan yang dibutuhkan. Hindari nama asli, alamat lengkap, atau nomor identitas."
                            :error="$errors->first('kronologi')"
                            required
                        />

                        {{-- Lampiran (opsional) --}}
                        <div x-data="{ files: [] }">
                            <label for="lampiran" class="block text-xs font-semibold text-neutral-800">Lampiran pendukung <span class="font-normal text-neutral-600">(opsional)</span></label>

                            <div class="relative mt-1.5 rounded-lg border border-dashed bg-white p-6 text-center transition-colors hover:bg-neutral-50 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-neutral-900 {{ $errors->has('lampiran') || $errors->has('lampiran.*') ? 'border-safety-rose' : 'border-line-strong' }}">
                                <input
                                    type="file"
                                    id="lampiran"
                                    name="lampiran[]"
                                    multiple
                                    accept=".jpg,.jpeg,.png,.webp,.pdf,.mp3,.mp4,.mov"
                                    aria-describedby="lampiran-help"
                                    @change="files = Array.from($event.target.files).map(f => f.name)"
                                    class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                                >
                                <svg class="mx-auto h-5 w-5 text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="17 8 12 3 7 8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                                <p class="mt-2 text-sm font-medium text-neutral-800" x-text="files.length ? files.length + ' berkas dipilih' : 'Pilih berkas'">Pilih berkas</p>
                                <p id="lampiran-help" class="mt-1 text-[11px] text-neutral-600">Gambar, PDF, audio, atau video · maksimal 5 berkas, 10 MB per berkas</p>
                            </div>

                            <ul x-show="files.length" x-cloak class="mt-2 space-y-1 text-[11px] text-neutral-600">
                                <template x-for="nama in files" :key="nama">
                                    <li class="truncate" x-text="nama"></li>
                                </template>
                            </ul>

                            @if ($errors->has('lampiran') || $errors->has('lampiran.*'))
                                <p class="mt-1.5 text-[11px] text-safety-rose">{{ $errors->first('lampiran') ?: $errors->first('lampiran.*') }}</p>
                            @else
                                <p class="mt-1.5 text-[11px] leading-relaxed text-neutral-600">Hanya unggah jika aman. Berkas disimpan privat dan hanya bisa diunduh oleh Anda.</p>
                            @endif
                        </div>
                    </div>
                </x-card>

                {{-- 4. Persetujuan & kirim --}}
                <x-card>
                    <div class="space-y-5">
                        <x-checkbox name="persetujuan" :error="$errors->first('persetujuan')" required>
                            Saya memahami bahwa informasi ini akan ditinjau oleh tim RuangAman untuk penanganan laporan sesuai
                            <a href="{{ route('halaman.show', 'kebijakan-privasi') }}" target="_blank" class="font-medium text-neutral-900 underline">kebijakan privasi</a>.
                        </x-checkbox>

                        <div class="flex flex-wrap items-center gap-5 border-t border-neutral-300/60 pt-5">
                            <x-button type="submit" variant="primary">Kirim laporan</x-button>
                            <x-button :href="url('/')" variant="ghost">Batalkan</x-button>
                        </div>
                        <p class="text-[11px] text-neutral-600">Setelah dikirim, Anda akan menerima kode laporan. Selama masih berstatus Pending, laporan dapat diubah atau dibatalkan dari <span class="font-medium">Laporan Saya</span>.</p>
                    </div>
                </x-card>
            </form>
        </div>

        {{-- Panduan --}}
        <aside class="lg:col-span-4">
            <x-card header="Sebelum mengirim">
                <dl class="space-y-4 text-xs leading-relaxed">
                    <div>
                        <dt class="font-semibold text-neutral-900">Tidak harus lengkap</dt>
                        <dd class="mt-1 text-neutral-600">Anda tidak harus menceritakan semua detail untuk mulai mendapatkan arahan.</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-neutral-900">Identitas tetap berupa alias</dt>
                        <dd class="mt-1 text-neutral-600">Tidak ada permintaan KTP, nama lengkap, atau alamat rumah.</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-neutral-900">Tidak diteruskan otomatis</dt>
                        <dd class="mt-1 text-neutral-600">Pilihan rujukan dan pendampingan dibahas dengan persetujuan Anda.</dd>
                    </div>
                </dl>
                <x-slot:footer>
                    <p class="text-[11px] leading-relaxed text-neutral-600">Jika belum siap, Anda dapat berhenti kapan saja. Pastikan perangkat tetap aman.</p>
                </x-slot:footer>
            </x-card>
        </aside>

    </div>
</div>
@endsection
