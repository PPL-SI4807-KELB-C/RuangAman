@extends('layouts.app')

@section('title', 'RuangAman — Pelaporan Aman Kekerasan Berbasis Gender')

@section('content')
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-50 to-white px-6 py-16 sm:px-12 sm:py-24 lg:py-28">
        <div class="relative mx-auto max-w-3xl text-center">
            <div class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-800">
                <span class="h-2 w-2 rounded-full bg-brand-600" aria-hidden="true"></span>
                Privasi & anonimitas terjaga
            </div>
            <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                Laporkan dengan <span class="text-brand-700">aman</span>, pantau dengan <span class="text-brand-700">tenang</span>.
            </h1>
            <p class="mx-auto mt-6 max-w-xl text-lg text-slate-600">
                RuangAman menyediakan ruang digital yang aman bagi penyintas kekerasan berbasis gender untuk melapor, berkonsultasi, dan memantau penanganan kasus.
            </p>
            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <x-button href="{{ route('laporan.create') }}" variant="primary" size="lg">Buat Laporan</x-button>
                <x-button href="{{ route('lacak.form') }}" variant="secondary" size="lg">Lacak Status Kasus</x-button>
            </div>
        </div>
    </section>

    <section class="mt-12">
        <h2 class="text-center text-2xl font-bold text-slate-900">Cara Kerja</h2>
        <div class="mt-8 grid gap-6 sm:grid-cols-3">
            <x-card class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                    <span class="text-lg font-bold">1</span>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Buat Laporan</h3>
                <p class="mt-2 text-sm text-slate-600">Isi formulir dengan alias/pseudonim. Aktifkan Mode Anonim agar identitas terlindungi.</p>
            </x-card>
            <x-card class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                    <span class="text-lg font-bold">2</span>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Dapatkan Kode</h3>
                <p class="mt-2 text-sm text-slate-600">Simpan kode pelacakan unik untuk memantau status penanganan tanpa perlu login.</p>
            </x-card>
            <x-card class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                    <span class="text-lg font-bold">3</span>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Pantau & Konsultasi</h3>
                <p class="mt-2 text-sm text-slate-600">Admin dan konselor terdaftar akan menangani kasus serta menawarkan jadwal konseling.</p>
            </x-card>
        </div>
    </section>

    <section class="mt-12 rounded-2xl bg-white px-6 py-12 shadow-sm ring-1 ring-slate-200 sm:px-12">
        <h2 class="text-center text-2xl font-bold text-slate-900">Jaminan Keamanan & Privasi</h2>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="flex flex-col items-center text-center">
                <svg class="h-8 w-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <h3 class="mt-3 font-semibold text-slate-900">Mode Anonim</h3>
                <p class="mt-1 text-sm text-slate-600">Identitas disembunyikan per laporan.</p>
            </div>
            <div class="flex flex-col items-center text-center">
                <svg class="h-8 w-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-3 font-semibold text-slate-900">Bukti Privat</h3>
                <p class="mt-1 text-sm text-slate-600">Lampiran disimpan di storage privat dengan otorisasi.</p>
            </div>
            <div class="flex flex-col items-center text-center">
                <svg class="h-8 w-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-3 font-semibold text-slate-900">Audit Trail</h3>
                <p class="mt-1 text-sm text-slate-600">Setiap perubahan status tercatat dan tidak dapat dihapus.</p>
            </div>
            <div class="flex flex-col items-center text-center">
                <svg class="h-8 w-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <h3 class="mt-3 font-semibold text-slate-900">Keluar Cepat</h3>
                <p class="mt-1 text-sm text-slate-600">Tombol darurat untuk meninggalkan halaman sensitif.</p>
            </div>
        </div>
    </section>

    <section class="mt-12 text-center">
        <h2 class="text-2xl font-bold text-slate-900">Siap melapor?</h2>
        <p class="mt-2 text-slate-600">Anda tidak perlu menyebutkan identitas asli untuk memulai.</p>
        <div class="mt-6">
            <x-button href="{{ route('laporan.create') }}" variant="primary" size="lg">Buat Laporan Sekarang</x-button>
        </div>
    </section>
@endsection
