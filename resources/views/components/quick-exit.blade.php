{{-- Tombol Keluar Cepat (FR-28). Perilakunya ada di Alpine.data('quickExit') pada resources/js/app.js;
     elemen <body> pada layout memuat x-data="quickExit" dan menangani Esc dua kali. --}}
@props(['label' => 'Keluar cepat'])

<button
    type="button"
    @click="exitNow()"
    {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap rounded border border-neutral-300/70 bg-[#f2f2f2] px-3.5 py-1.5 text-xs font-medium text-neutral-800 transition-colors hover:bg-neutral-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400']) }}
    title="Pindah seketika ke situs netral. Pintasan: tekan Esc dua kali."
    aria-label="{{ $label }} — pindah seketika ke situs netral"
    aria-keyshortcuts="Escape Escape"
>
    <span>{{ $label }}</span>
    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
        <polyline points="16 17 21 12 16 7" />
        <line x1="21" y1="12" x2="9" y2="12" />
    </svg>
</button>
