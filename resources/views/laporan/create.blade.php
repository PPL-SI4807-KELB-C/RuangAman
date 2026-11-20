@extends('layouts.app')

@section('title', $title ?? 'Buat Laporan Kasus - RuangAman')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-slate-900">Buat Laporan Kasus</h1>
            <p class="mt-1 text-sm text-slate-600">Isi dengan tenang. Anda bisa mengaktifkan Mode Anonim di langkah pertama.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold mb-1">Mohon periksa kembali isian formulir Anda:</p>
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="form-laporan" method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="step" id="input-step" value="{{ old('step', 1) }}">

            {{-- Stepper Navigasi --}}
            <nav class="mb-8" aria-label="Progress">
                <ol class="flex items-start gap-2 sm:gap-4">
                    @foreach (['Identitas & Anonimitas', 'Detail Kejadian', 'Kronologi & Bukti', 'Konfirmasi'] as $index => $label)
                        @php $stepNumber = $index + 1; @endphp
                        <li class="relative flex-1">
                            <button type="button" onclick="goToStep({{ $stepNumber }})" class="relative z-10 flex w-full flex-col items-center text-center focus:outline-none">
                                <div id="step-circle-{{ $stepNumber }}"
                                     class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold transition {{ $stepNumber == 1 ? 'bg-brand-100 text-brand-700 ring-2 ring-brand-600' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $stepNumber }}
                                </div>
                                <span id="step-label-{{ $stepNumber }}"
                                      class="mt-1.5 text-xs font-medium transition {{ $stepNumber == 1 ? 'text-brand-700 font-bold' : 'text-slate-500' }}">
                                    {{ $label }}
                                </span>
                            </button>
                            @if (! $loop->last)
                                <div id="step-line-{{ $stepNumber }}"
                                     class="pointer-events-none absolute left-[calc(50%+1.25rem)] top-4 hidden h-0.5 w-[calc(100%-2.5rem)] -translate-y-1/2 sm:block transition bg-slate-200"></div>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>

            {{-- Langkah 1: Identitas & Anonimitas --}}
            <div id="step-panel-1" class="step-panel">
                <x-card>
                    <h2 class="text-lg font-semibold text-slate-900">Identitas Pelapor</h2>
                    <p class="text-sm text-slate-600">Gunakan alias untuk melindungi identitas Anda.</p>

                    <div class="mt-5 space-y-4">
                        <x-input name="alias" label="Alias Pelapor" value="{{ $alias ?? Auth::user()->nama_tampilan }}" helper="Nama ini akan dilihat pihak penangan jika Mode Anonim nonaktif." readonly />

                        <div class="rounded-lg border border-brand-200 bg-brand-50 p-4">
                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="hidden" name="mode_anonim" value="0">
                                <input type="checkbox"
                                       id="mode_anonim"
                                       name="mode_anonim"
                                       value="1"
                                       class="mt-1 h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                       @checked(old('mode_anonim', $preferensiAnonim ?? true))>
                                <div>
                                    <span class="block text-sm font-semibold text-slate-900">Aktifkan Mode Anonim</span>
                                    <span class="block text-sm text-slate-600">Identitas profil Anda disembunyikan dari konselor yang menangani kasus ini.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-button type="button" variant="primary" onclick="goToStep(2)">Lanjut</x-button>
                    </div>
                </x-card>
            </div>

            {{-- Langkah 2: Detail Kejadian --}}
            <div id="step-panel-2" class="step-panel hidden">
                <x-card>
                    <h2 class="text-lg font-semibold text-slate-900">Detail Kejadian</h2>
                    <p class="text-sm text-slate-600">Ceritakan apa yang terjadi secara ringkas.</p>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <x-input id="judul" name="judul" label="Judul Laporan" placeholder="Ringkasan singkat kejadian" value="{{ old('judul') }}" :error="$errors->first('judul')" required />
                        </div>
                        <div class="sm:col-span-2">
                            <x-select id="kategori_kekerasan" name="kategori_kekerasan" label="Kategori Kekerasan" :options="$kategoriOptions" :selected="old('kategori_kekerasan')" placeholder="Pilih kategori" :error="$errors->first('kategori_kekerasan')" required />
                        </div>
                        <x-input id="waktu_kejadian" name="waktu_kejadian" type="datetime-local" label="Waktu Kejadian" value="{{ old('waktu_kejadian') }}" :error="$errors->first('waktu_kejadian')" required />
                        <x-select id="tingkat_urgensi" name="tingkat_urgensi" label="Tingkat Urgensi" :options="$urgensiOptions" :selected="old('tingkat_urgensi')" placeholder="Pilih tingkat" :error="$errors->first('tingkat_urgensi')" required />
                        <div class="sm:col-span-2">
                            <x-input id="lokasi_kejadian" name="lokasi_kejadian" label="Lokasi Kejadian" placeholder="Kota, institusi, atau alamat umum" value="{{ old('lokasi_kejadian') }}" :error="$errors->first('lokasi_kejadian')" required />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <x-button type="button" variant="secondary" onclick="goToStep(1)">Kembali</x-button>
                        <x-button type="button" variant="primary" onclick="goToStep(3)">Lanjut</x-button>
                    </div>
                </x-card>
            </div>

            {{-- Langkah 3: Kronologi & Bukti --}}
            <div id="step-panel-3" class="step-panel hidden">
                <x-card>
                    <h2 class="text-lg font-semibold text-slate-900">Kronologi & Bukti</h2>
                    <p class="text-sm text-slate-600">Jelaskan kronologi secara lengkap. Lampiran bersifat opsional.</p>

                    <div class="mt-5 space-y-4">
                        <x-textarea id="kronologi" name="kronologi" label="Kronologi Kejadian" placeholder="Ceritakan urutan kejadian, pelaku (jika diketahui), dan dampak yang dirasakan..." rows="6" :error="$errors->first('kronologi')" required>{{ old('kronologi') }}</x-textarea>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Lampiran Bukti <span class="text-slate-500 font-normal">(opsional, maks. 5 file, masing-masing 10 MB)</span></label>
                            <input type="file" name="lampiran[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.mp3,.mp4,.mov" class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                            @if ($errors->has('lampiran.*'))
                                <p class="mt-1 text-xs text-safety-rose">{{ $errors->first('lampiran.*') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <x-button type="button" variant="secondary" onclick="goToStep(2)">Kembali</x-button>
                        <x-button type="button" variant="primary" onclick="goToStep(4)">Lanjut</x-button>
                    </div>
                </x-card>
            </div>

            {{-- Langkah 4: Konfirmasi Laporan --}}
            <div id="step-panel-4" class="step-panel hidden">
                <x-card>
                    <h2 class="text-lg font-semibold text-slate-900">Konfirmasi Laporan</h2>
                    <p class="text-sm text-slate-600">Periksa ringkasan sebelum mengirim.</p>

                    <div class="mt-5 space-y-3 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm">
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Mode Anonim</span>
                            <span id="summary-anonim" class="font-medium text-slate-900">-</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Judul</span>
                            <span id="summary-judul" class="font-medium text-slate-900">-</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Kategori</span>
                            <span id="summary-kategori" class="font-medium text-slate-900">-</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Waktu</span>
                            <span id="summary-waktu" class="font-medium text-slate-900">-</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Lokasi</span>
                            <span id="summary-lokasi" class="font-medium text-slate-900">-</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Urgensi</span>
                            <span id="summary-urgensi" class="font-medium text-slate-900">-</span>
                        </div>
                        <div>
                            <span class="text-slate-500">Kronologi</span>
                            <p id="summary-kronologi" class="mt-1 whitespace-pre-wrap text-slate-900">-</p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label class="flex cursor-pointer items-start gap-3">
                            <input type="checkbox" name="persetujuan" value="1" class="mt-1 h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500" @checked(old('persetujuan')) required>
                            <span class="text-sm text-slate-700">Saya menyatakan bahwa informasi yang saya berikan adalah benar dan saya memahami bahwa laporan palsu dapat dikenakan sanksi hukum.</span>
                        </label>
                        @if ($errors->has('persetujuan'))
                            <p class="mt-1 text-xs text-safety-rose">{{ $errors->first('persetujuan') }}</p>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-between">
                        <x-button type="button" variant="secondary" onclick="goToStep(3)">Kembali</x-button>
                        <x-button type="submit" variant="primary">Kirim Laporan</x-button>
                    </div>
                </x-card>
            </div>
        </form>
    </div>

    <script>
        function goToStep(targetStep) {
            document.getElementById('input-step').value = targetStep;

            // Tampilkan panel sesuai langkah
            for (let i = 1; i <= 4; i++) {
                const panel = document.getElementById('step-panel-' + i);
                const circle = document.getElementById('step-circle-' + i);
                const label = document.getElementById('step-label-' + i);
                const line = document.getElementById('step-line-' + i);

                if (i === targetStep) {
                    panel.classList.remove('hidden');
                } else {
                    panel.classList.add('hidden');
                }

                // Update indikator stepper
                circle.className = 'flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold transition ';
                label.className = 'mt-1.5 text-xs font-medium transition ';

                if (targetStep > i) {
                    circle.className += 'bg-brand-600 text-white';
                    circle.innerHTML = '✓';
                    label.className += 'text-slate-700';
                    if (line) line.className = 'pointer-events-none absolute left-[calc(50%+1.25rem)] top-4 hidden h-0.5 w-[calc(100%-2.5rem)] -translate-y-1/2 sm:block transition bg-brand-600';
                } else if (targetStep === i) {
                    circle.className += 'bg-brand-100 text-brand-700 ring-2 ring-brand-600';
                    circle.innerHTML = i;
                    label.className += 'text-brand-700 font-bold';
                    if (line) line.className = 'pointer-events-none absolute left-[calc(50%+1.25rem)] top-4 hidden h-0.5 w-[calc(100%-2.5rem)] -translate-y-1/2 sm:block transition bg-slate-200';
                } else {
                    circle.className += 'bg-slate-100 text-slate-500';
                    circle.innerHTML = i;
                    label.className += 'text-slate-500';
                    if (line) line.className = 'pointer-events-none absolute left-[calc(50%+1.25rem)] top-4 hidden h-0.5 w-[calc(100%-2.5rem)] -translate-y-1/2 sm:block transition bg-slate-200';
                }
            }

            // Isi ringkasan di Langkah 4
            if (targetStep === 4) {
                const isAnonim = document.getElementById('mode_anonim')?.checked;
                const judul = document.querySelector('[name="judul"]')?.value || '-';
                const kategoriEl = document.querySelector('[name="kategori_kekerasan"]');
                const kategori = kategoriEl?.options[kategoriEl.selectedIndex]?.text || kategoriEl?.value || '-';
                const waktu = document.querySelector('[name="waktu_kejadian"]')?.value || '-';
                const lokasi = document.querySelector('[name="lokasi_kejadian"]')?.value || '-';
                const urgensiEl = document.querySelector('[name="tingkat_urgensi"]');
                const urgensi = urgensiEl?.options[urgensiEl.selectedIndex]?.text || urgensiEl?.value || '-';
                const kronologi = document.querySelector('[name="kronologi"]')?.value || '-';

                document.getElementById('summary-anonim').textContent = isAnonim ? 'Aktif' : 'Nonaktif';
                document.getElementById('summary-judul').textContent = judul;
                document.getElementById('summary-kategori').textContent = kategori;
                document.getElementById('summary-waktu').textContent = waktu.replace('T', ' ');
                document.getElementById('summary-lokasi').textContent = lokasi;
                document.getElementById('summary-urgensi').textContent = urgensi;
                document.getElementById('summary-kronologi').textContent = kronologi;
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const initialStep = Number(document.getElementById('input-step').value) || 1;
            goToStep(initialStep);
        });
    </script>
@endsection