{{-- Kolom pencarian dengan ikon, dipakai di daftar admin dan direktori publik. --}}
@props([
    'name' => 'search',
    'id' => null,
    'value' => null,
    'label' => 'Cari',
    'placeholder' => 'Cari…',
])

@php $id ??= $name; @endphp

<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="sr-only">{{ $label }}</label>
    <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-4 w-4 text-neutral-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
        </div>
        <input
            type="search"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->except('class')->merge(['class' => 'block w-full rounded bg-white py-2.5 pl-9 pr-3 text-sm text-neutral-900 ring-1 ring-inset ring-line-strong placeholder:text-neutral-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900']) }}
        />
    </div>
</div>
