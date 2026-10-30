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
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 mb-1">
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
        {{ $attributes->except('class')->merge(['class' => 'block w-full rounded-lg border shadow-sm text-sm transition px-3 py-2.5 ' . ($hasError ? 'border-safety-rose text-safety-rose placeholder-safety-rose/70 focus:ring-safety-rose focus:border-safety-rose' : 'border-slate-300 focus:ring-brand-500 focus:border-brand-500')]) }}
    />

    @if ($helper && ! $hasError)
        <p class="mt-1 text-xs text-slate-500">{{ $helper }}</p>
    @endif

    @if ($hasError)
        <p id="{{ $id }}-error" class="mt-1 text-xs text-safety-rose">{{ $error }}</p>
    @endif
</div>
