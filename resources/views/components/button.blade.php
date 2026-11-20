@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
])

@php
$base = 'inline-flex items-center justify-center rounded font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400 disabled:cursor-not-allowed disabled:opacity-50';

$variants = [
    'primary' => 'bg-brand-600 text-white hover:bg-brand-700',
    'secondary' => 'border border-neutral-300/70 bg-white text-neutral-800 hover:bg-neutral-50',
    'danger' => 'bg-safety-rose text-white hover:bg-safety-alert',
    'outline-danger' => 'border border-neutral-300/70 bg-white text-safety-rose hover:bg-red-50',
    'ghost' => 'bg-transparent text-neutral-700 hover:text-neutral-900 hover:underline',
    'white' => 'bg-white text-neutral-900 hover:bg-neutral-100',
];

$sizes = [
    'sm' => 'px-4 py-2 text-xs gap-1.5',
    'md' => 'px-5 py-2.5 text-xs gap-2',
    'lg' => 'px-6 py-3 text-sm gap-2',
];

$classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href && ! $disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
