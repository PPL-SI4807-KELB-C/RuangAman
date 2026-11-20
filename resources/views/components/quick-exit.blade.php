{{-- Tombol Keluar Cepat (FR-28). Perilakunya ada di Alpine.data('quickExit') pada resources/js/app.js;
     elemen <body> pada layout memuat x-data="quickExit" dan menangani Esc dua kali.
     Sengaja dibuat paling kontras di header (hitam, min 44px) agar mudah ditemukan saat panik. --}}
@props(['label' => 'Keluar cepat'])

<button
    type="button"
    @click="exitNow()"
    {{ $attributes->merge(['class' => 'inline-flex min-h-11 shrink-0 items-center gap-2 whitespace-nowrap rounded-lg bg-neutral-900 px-4 text-sm font-semibold text-white transition-colors hover:bg-neutral-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2']) }}
    title="Pindah seketika ke situs netral. Pintasan: tekan Esc dua kali."
    aria-label="{{ $label }} — pindah seketika ke situs netral"
    aria-keyshortcuts="Escape Escape"
>
    <span>{{ $label }}</span>
    <kbd class="hidden rounded bg-white/15 px-1.5 py-0.5 font-sans text-[12px] font-medium text-white md:inline" aria-hidden="true">Esc×2</kbd>
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
        <polyline points="16 17 21 12 16 7" />
        <line x1="21" y1="12" x2="9" y2="12" />
    </svg>
</button>
