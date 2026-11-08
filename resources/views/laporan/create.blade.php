@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-slate-900">Buat Laporan Kasus</h1>
            <p class="mt-1 text-sm text-slate-600">Isi dengan tenang. Anda bisa mengaktifkan Mode Anonim di langkah pertama.</p>
        </div>

        <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data"
              x-data="{
                  step: Number({{ old('step', 1) }}),
                  mode_anonim: {{ old('mode_anonim', true) ? 'true' : 'false' }},
                  judul: @json(old('judul') ?? ''),
                  kategori: @json(old('kategori_kekerasan') ?? ''),
                  waktu: @json(old('waktu_kejadian') ?? ''),
                  lokasi: @json(old('lokasi_kejadian') ?? ''),
                  urgensi: @json(old('tingkat_urgensi') ?? ''),
                  kronologi: @json(old('kronologi') ?? ''),
              }">
            @csrf
            <input type="hidden" name="step" :value="step">

            <nav class="mb-8" aria-label="Progress">
                <ol class="flex items-start gap-2 sm:gap-4">
                    @foreach (['Identitas & Anonimitas', 'Detail Kejadian', 'Kronologi & Bukti', 'Konfirmasi'] as $index => $label)
                        @php $stepNumber = $index + 1; @endphp
                        <li class="relative flex-1">
                            <div class="flex flex-col items-center text-center">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold transition"
                                     :class="{
                                         'bg-brand-600 text-white': Number(step) > {{ $stepNumber }},
                                         'bg-brand-100 text-brand-700 ring-2 ring-brand-600': Number(step) == {{ $stepNumber }},
                                         'bg-slate-100 text-slate-500': Number(step) < {{ $stepNumber }}
                                     }">
                                    <template x-if="Number(step) > {{ $stepNumber }}">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                    <span x-show="Number(step) <= {{ $stepNumber }}">{{ $stepNumber }}</span>
                                </div>
                                <span class="mt-1.5 text-xs font-medium transition"
                                      :class="{
                                          'text-brand-700 font-bold': Number(step) == {{ $stepNumber }},
                                          'text-slate-700': Number(step) > {{ $stepNumber }},
                                          'text-slate-500': Number(step) < {{ $stepNumber }}
                                      }">
                                    {{ $label }}
                                </span>
                            </div>
                            @if (! $loop->last)
                                <div class="absolute left-1/2 top-4 hidden h-0.5 w-[calc(100%-1rem)] -translate-y-1/2 sm:block transition"
                                     :class="Number(step) > {{ $stepNumber }} ? 'bg-brand-600' : 'bg-slate-200'"></div>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>

            {{-- Langkah 1 --}}
            <div x-show="Number(step) == 1" x-transition>
                <x-card>
                    <h2 class="text-lg font-semibold text-slate-900">Identitas Pelapor</h2>
                    <p class="text-sm text-slate-600">Gunakan alias untuk melindungi identitas Anda.</p>

                    <div class="mt-5 space-y-4">
                        <x-input name="alias" label="Alias Pelapor" value="{{ $alias }}" helper="Nama ini akan dilihat pihak penangan jika Mode Anonim nonaktif." readonly />

                        <div class="rounded-lg border border-brand-200 bg-brand-50 p-4">
                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="checkbox" name="mode_anonim" value="1" class="mt-1 h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500" x-model="mode_anonim">
                                <div>
                                    <span class="block text-sm font-semibold text-slate-900">Aktifkan Mode Anonim</span>
                                    <span class="block text-sm text-slate-600">Identitas profil Anda disembunyikan dari konselor yang menangani kasus ini.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-button type="button" variant="primary" @click="step = 2">Lanjut</x-button>
                    </div>
                </x-card>
            </div>

            {{-- Langkah 2 --}}
            <div x-show="Number(step) == 2" x-transition x-cloak>
                <x-card>
                    <h2 class="text-lg font-semibold text-slate-900">Detail Kejadian</h2>
                    <p class="text-sm text-slate-600">Ceritakan apa yang terjadi secara ringkas.</p>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <x-input name="judul" label="Judul Laporan" placeholder="Ringkasan singkat kejadian" x-model="judul" :error="$errors->first('judul')" required />
                        </div>
                        <div class="sm:col-span-2">
                            <x-select name="kategori_kekerasan" label="Kategori Kekerasan" :options="$kategoriOptions" x-model="kategori" placeholder="Pilih kategori" :error="$errors->first('kategori_kekerasan')" required />
                        </div>
                        <x-input name="waktu_kejadian" type="datetime-local" label="Waktu Kejadian" x-model="waktu" :error="$errors->first('waktu_kejadian')" required />
                        <x-select name="tingkat_urgensi" label="Tingkat Urgensi" :options="$urgensiOptions" x-model="urgensi" placeholder="Pilih tingkat" :error="$errors->first('tingkat_urgensi')" required />
                        <div class="sm:col-span-2">
                            <x-input name="lokasi_kejadian" label="Lokasi Kejadian" placeholder="Kota, institusi, atau alamat umum" x-model="lokasi" :error="$errors->first('lokasi_kejadian')" required />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <x-button type="button" variant="secondary" @click="step = 1">Kembali</x-button>
                        <x-button type="button" variant="primary" @click="step = 3">Lanjut</x-button>
                    </div>
                </x-card>
            </div>

            {{-- Langkah 3 --}}
            <div x-show="Number(step) == 3" x-transition x-cloak>
                <x-card>
                    <h2 class="text-lg font-semibold text-slate-900">Kronologi & Bukti</h2>
                    <p class="text-sm text-slate-600">Jelaskan kronologi secara lengkap. Lampiran bersifat opsional.</p>

                    <div class="mt-5 space-y-4">
                        <x-textarea name="kronologi" label="Kronologi Kejadian" placeholder="Ceritakan urutan kejadian, pelaku (jika diketahui), dan dampak yang dirasakan..." rows="6" x-model="kronologi" :error="$errors->first('kronologi')" required />

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Lampiran Bukti <span class="text-slate-500 font-normal">(opsional, maks. 5 file, masing-masing 10 MB)</span></label>
                            <input type="file" name="lampiran[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.mp3,.mp4,.mov" class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                            @if ($errors->has('lampiran.*'))
                                <p class="mt-1 text-xs text-safety-rose">{{ $errors->first('lampiran.*') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <x-button type="button" variant="secondary" @click="step = 2">Kembali</x-button>
                        <x-button type="button" variant="primary" @click="step = 4">Lanjut</x-button>
                    </div>
                </x-card>
            </div>

            {{-- Langkah 4 --}}
            <div x-show="Number(step) == 4" x-transition x-cloak>
                <x-card>
                    <h2 class="text-lg font-semibold text-slate-900">Konfirmasi Laporan</h2>
                    <p class="text-sm text-slate-600">Periksa ringkasan sebelum mengirim.</p>

                    <div class="mt-5 space-y-3 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm">
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Mode Anonim</span>
                            <span class="font-medium text-slate-900" x-text="mode_anonim ? 'Aktif' : 'Nonaktif'"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Judul</span>
                            <span class="font-medium text-slate-900" x-text="judul || '-'"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Kategori</span>
                            <span class="font-medium text-slate-900" x-text="kategori || '-'"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Waktu</span>
                            <span class="font-medium text-slate-900" x-text="waktu ? (new Date(waktu).toString() !== 'Invalid Date' ? new Date(waktu).toLocaleString('id-ID') : waktu) : '-'"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Lokasi</span>
                            <span class="font-medium text-slate-900" x-text="lokasi || '-'"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Urgensi</span>
                            <span class="font-medium text-slate-900" x-text="urgensi || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500">Kronologi</span>
                            <p class="mt-1 whitespace-pre-wrap text-slate-900" x-text="kronologi || '-'"></p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label class="flex cursor-pointer items-start gap-3">
                            <input type="checkbox" name="persetujuan" value="1" class="mt-1 h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500" @checked(old('persetujuan'))>
                            <span class="text-sm text-slate-700">Saya menyatakan bahwa informasi yang saya berikan adalah benar dan saya memahami bahwa laporan palsu dapat dikenakan sanksi hukum.</span>
                        </label>
                        @if ($errors->has('persetujuan'))
                            <p class="mt-1 text-xs text-safety-rose">{{ $errors->first('persetujuan') }}</p>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-between">
                        <x-button type="button" variant="secondary" @click="step = 3">Kembali</x-button>
                        <x-button type="submit" variant="primary">Kirim Laporan</x-button>
                    </div>
                </x-card>
            </div>
        </form>
    </div>
@endsection
