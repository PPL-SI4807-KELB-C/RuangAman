@props([
    'variant' => 'info',
    'title' => null,
    'message' => null,
    'dismissible' => false,
    'type' => null,
])

@php
$variant = $type ?? $variant;
$dots = [
    'info' => 'bg-safety-info',
    'success' => 'bg-brand-600',
    'warning' => 'bg-amber-500',
    'danger' => 'bg-safety-rose',
    'brand' => 'bg-brand-600',
];
$dot = $dots[$variant] ?? $dots['info'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-lg border border-neutral-200/70 bg-[#f2f2f2] p-4', 'role' => 'alert']) }} @if ($dismissible) x-data="{ open: true }" x-show="open" @endif>
    <div class="flex items-start gap-2.5">
        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $dot }}" aria-hidden="true"></span>

        <div class="flex-1">
            @if ($title)
                <h3 class="text-xs font-semibold text-neutral-900">{{ $title }}</h3>
            @endif
            <div class="text-xs leading-relaxed text-neutral-600 {{ $title ? 'mt-1' : '' }}">
                {{ $message ?? $slot }}
            </div>
        </div>

        @if ($dismissible)
            <button type="button" @click="open = false" class="text-xs font-medium text-neutral-500 hover:text-neutral-900" aria-label="Tutup">
                &times;
            </button>
        @endif
    </div>
</div>
