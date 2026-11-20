@props([
    'variant' => 'info',
    'type' => null,
    'message' => null,
    'title' => null,
    'dismissible' => false,
])

@php
$variant = $type ?? $variant;
$variants = [
    'info' => 'bg-blue-50 border-blue-400 text-blue-800',
    'success' => 'bg-green-50 border-green-400 text-green-800',
    'warning' => 'bg-amber-50 border-amber-400 text-amber-800',
    'danger' => 'bg-red-50 border-safety-rose text-red-800',
    'brand' => 'bg-brand-50 border-brand-400 text-brand-800',
];

$classes = 'rounded-lg border-l-4 p-4 ' . ($variants[$variant] ?? $variants['info']);
@endphp

<div {{ $attributes->merge(['class' => $classes, 'role' => 'alert']) }}>
    <div class="flex">
        <div class="flex-1">
            @if ($title)
                <h3 class="text-sm font-semibold">{{ $title }}</h3>
            @endif
            <div class="text-sm {{ $title ? 'mt-1' : '' }}">
                {{ $message ?? $slot }}
            </div>
        </div>
        @if ($dismissible)
            <button type="button" class="ml-4 text-sm font-medium opacity-70 hover:opacity-100" aria-label="Tutup">
                ×
            </button>
        @endif
    </div>
</div>
