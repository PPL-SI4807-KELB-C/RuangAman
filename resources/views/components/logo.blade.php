{{-- Logo RuangAman "Ruang terbuka": ruang dengan satu sudut terbuka dan seseorang di sudut amannya.
     Sumber & panduan: docs/brand/kit/PANDUAN-LOGO.md. Jangan memutar logo atau memindahkan titik ke tengah. --}}
@props(['size' => 'md', 'wordmark' => true])

@php
$sizes = [
    'sm' => ['icon' => 'h-6 w-6', 'text' => 'text-base'],
    'md' => ['icon' => 'h-7 w-7', 'text' => 'text-xl'],
];
$s = $sizes[$size] ?? $sizes['md'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <svg class="{{ $s['icon'] }} shrink-0" viewBox="20 20 216 216" aria-hidden="true" focusable="false">
        <path d="M128 46H106A60 60 0 0 0 46 106V150A60 60 0 0 0 106 210H150A60 60 0 0 0 210 150V128" fill="none" stroke="#15803d" stroke-width="40" stroke-linecap="round" />
        <circle cx="112" cy="144" r="34" fill="#171717" />
    </svg>
    @if ($wordmark)
        <span class="{{ $s['text'] }} font-extrabold tracking-tight text-neutral-900">RuangAman</span>
    @else
        <span class="sr-only">RuangAman</span>
    @endif
</span>
