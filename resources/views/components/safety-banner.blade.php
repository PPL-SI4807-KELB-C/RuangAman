{{-- Banner keselamatan + kontak darurat yang bisa langsung ditekan di ponsel.
     Nomor: 112 (panggilan darurat nasional), SAPA 129 (layanan pengaduan KemenPPPA). Verifikasi ulang sebelum rilis. --}}
<div {{ $attributes->merge(['class' => 'w-full border-b border-neutral-200 bg-surface px-4 py-2 text-xs text-neutral-700 sm:px-6']) }}>
    <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-4 gap-y-1 text-center">
        <span class="inline-flex items-center gap-2">
            <svg class="hidden h-3.5 w-3.5 shrink-0 text-neutral-600 sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
            <span class="hidden sm:inline">Utamakan keselamatan. Keluar cepat tidak menghapus riwayat peramban.</span>
        </span>
        <span class="inline-flex items-center gap-2">
            <span class="font-semibold text-neutral-900">Dalam bahaya?</span>
            <a href="tel:112" class="rounded px-1 font-semibold text-neutral-900 underline underline-offset-2 hover:text-neutral-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900">112</a>
            <span aria-hidden="true">·</span>
            <a href="tel:129" class="rounded px-1 font-semibold text-neutral-900 underline underline-offset-2 hover:text-neutral-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900">SAPA 129</a>
        </span>
    </div>
</div>
