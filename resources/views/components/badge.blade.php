@props([
    'variant' => 'slate',
    'type' => null,
    'label' => null,
])

@php
$variant = $type ?? $variant;
// Figma RuangAman menandai status dengan titik berwarna + teks, bukan pil berlatar.
$variants = [
    'brand' => ['dot' => 'bg-brand-600', 'text' => 'text-brand-700'],
    'success' => ['dot' => 'bg-brand-600', 'text' => 'text-neutral-800'],
    'warning' => ['dot' => 'bg-amber-500', 'text' => 'text-neutral-800'],
    'danger' => ['dot' => 'bg-safety-rose', 'text' => 'text-safety-rose'],
    'info' => ['dot' => 'bg-safety-info', 'text' => 'text-neutral-800'],
    'slate' => ['dot' => 'bg-neutral-400', 'text' => 'text-neutral-700'],
    'rose' => ['dot' => 'bg-safety-rose', 'text' => 'text-safety-rose'],
];

$style = $variants[$variant] ?? $variants['slate'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 text-xs font-medium ' . $style['text']]) }}>
    <span class="h-2 w-2 shrink-0 rounded-full {{ $style['dot'] }}" aria-hidden="true"></span>
    {{ $label ?? $slot }}
</span>
