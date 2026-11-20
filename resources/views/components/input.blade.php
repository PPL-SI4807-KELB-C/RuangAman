@props([
    'name',
    'id' => null,
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'helper' => null,
    'error' => null,
    'required' => false,
])

@php
$id ??= $name;
$hasError = $error !== null && $error !== '';
$field = 'block w-full rounded bg-white px-3.5 py-2.5 text-sm text-neutral-900 ring-1 ring-inset transition placeholder:text-neutral-400 focus:outline-none focus:ring-1 '
    . ($hasError ? 'ring-safety-rose focus:ring-safety-rose' : 'ring-neutral-300/70 focus:ring-neutral-400');
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-xs font-semibold text-neutral-800">
            {{ $label }}
            @if ($required)
                <span class="text-safety-rose" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if ($required) required @endif
        aria-invalid="{{ $hasError ? 'true' : 'false' }}"
        @if ($hasError) aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except('class')->merge(['class' => $field]) }}
    />

    @if ($helper && ! $hasError)
        <p class="mt-1.5 text-[11px] leading-relaxed text-neutral-500">{{ $helper }}</p>
    @endif

    @if ($hasError)
        <p id="{{ $id }}-error" class="mt-1.5 text-[11px] text-safety-rose">{{ $error }}</p>
    @endif
</div>
