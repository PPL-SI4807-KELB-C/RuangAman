@props([
    'variant' => 'slate',
    'type' => null,
    'label' => null,
])

@php
$variant = $type ?? $variant;
$variants = [
    'brand' => 'bg-brand-100 text-brand-800 ring-brand-500/20',
    'success' => 'bg-green-100 text-green-800 ring-green-500/20',
    'warning' => 'bg-amber-100 text-amber-800 ring-amber-500/20',
    'danger' => 'bg-red-100 text-red-800 ring-red-500/20',
    'info' => 'bg-blue-100 text-blue-800 ring-blue-500/20',
    'slate' => 'bg-slate-100 text-slate-700 ring-slate-500/20',
    'rose' => 'bg-safety-rose/10 text-safety-rose ring-safety-rose/20',
];

$classes = 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ' . ($variants[$variant] ?? $variants['slate']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $label ?? $slot }}
</span>
