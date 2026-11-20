{{-- Kotak centang dengan label di sebelah kanan. Isi slot = teks label. Tidak pernah tercentang otomatis kecuali diminta. --}}
@props([
    'name',
    'id' => null,
    'value' => '1',
    'checked' => false,
    'error' => null,
    'required' => false,
])

@php
$id ??= $name;
$hasError = $error !== null && $error !== '';
$isChecked = old() ? (bool) old($name) : $checked;
@endphp

<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="flex cursor-pointer items-start gap-2.5">
        <input
            type="checkbox"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            @checked($isChecked)
            @if ($required) required @endif
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            @if ($hasError) aria-describedby="{{ $id }}-error" @endif
            class="mt-0.5 h-4 w-4 shrink-0 rounded border-line-strong text-neutral-900 focus:ring-2 focus:ring-neutral-900 focus:ring-offset-2"
        >
        <span class="text-xs leading-relaxed text-neutral-700">{{ $slot }}</span>
    </label>

    @if ($hasError)
        <p id="{{ $id }}-error" class="mt-1.5 text-[11px] text-safety-rose">{{ $error }}</p>
    @endif
</div>
