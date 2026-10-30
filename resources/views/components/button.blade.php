@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
])

@php
$base = 'inline-flex items-center justify-center font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-brand-600 text-white hover:bg-brand-700 focus:ring-brand-500 shadow-sm',
    'secondary' => 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 focus:ring-slate-400 shadow-sm',
    'danger' => 'bg-safety-rose text-white hover:bg-safety-alert focus:ring-safety-rose shadow-sm',
    'outline-danger' => 'bg-white text-safety-rose border border-safety-rose hover:bg-safety-rose/5 focus:ring-safety-rose',
    'ghost' => 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:ring-slate-400',
    'white' => 'bg-white text-slate-900 hover:bg-slate-50 focus:ring-white shadow-sm',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-sm gap-1.5',
    'md' => 'px-4 py-2.5 text-sm gap-2',
    'lg' => 'px-6 py-3 text-base gap-2',
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
