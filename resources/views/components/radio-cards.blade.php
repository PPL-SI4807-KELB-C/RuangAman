{{-- Grup pilihan berbentuk kartu (radio). options: [nilai => label] atau [nilai => ['label' => ..., 'description' => ...]]. --}}
@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'helper' => null,
    'error' => null,
    'required' => false,
    'columns' => 2,
])

@php
$selected = old($name, $value);
$hasError = $error !== null && $error !== '';
$grid = [1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3'][$columns] ?? 'sm:grid-cols-2';
$groupId = $name . '-group';
@endphp

<fieldset {{ $attributes->only('class') }} @if ($hasError) aria-describedby="{{ $groupId }}-error" @endif>
    @if ($label)
        <legend class="block text-xs font-semibold text-neutral-800">
            {{ $label }}
            @if ($required)
                <span class="text-safety-rose" aria-hidden="true">*</span>
            @endif
        </legend>
    @endif

    @if ($helper)
        <p class="mt-1 text-[11px] leading-relaxed text-neutral-600">{{ $helper }}</p>
    @endif

    <div class="mt-2.5 grid grid-cols-1 gap-3 {{ $grid }}">
        @foreach ($options as $optionValue => $option)
            @php
                $optionLabel = is_array($option) ? $option['label'] : $option;
                $optionDescription = is_array($option) ? ($option['description'] ?? null) : null;
            @endphp
            <label class="flex cursor-pointer items-start gap-3 rounded-lg border bg-white p-3.5 text-sm text-neutral-800 transition-colors hover:bg-neutral-50 has-[:checked]:border-neutral-900 has-[:checked]:bg-surface has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-neutral-900 {{ $hasError ? 'border-safety-rose' : 'border-neutral-300/70' }}">
                <input
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $optionValue }}"
                    class="mt-0.5 h-4 w-4 shrink-0 border-line-strong text-neutral-900 focus:ring-0 focus:ring-offset-0"
                    @checked((string) $selected === (string) $optionValue)
                    @if ($required && $loop->first) required @endif
                >
                <span>
                    <span class="block font-medium">{{ $optionLabel }}</span>
                    @if ($optionDescription)
                        <span class="mt-0.5 block text-[11px] leading-relaxed text-neutral-600">{{ $optionDescription }}</span>
                    @endif
                </span>
            </label>
        @endforeach
    </div>

    @if ($hasError)
        <p id="{{ $groupId }}-error" class="mt-1.5 text-[11px] text-safety-rose">{{ $error }}</p>
    @endif
</fieldset>
