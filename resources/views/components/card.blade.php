@props(['header' => null, 'footer' => null])

{{-- Panel datar khas Figma RuangAman: isi #f2f2f2, hairline netral, tanpa shadow. --}}
<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-neutral-200/70 bg-surface']) }}>
    @if ($header)
        <div class="border-b border-neutral-300/60 px-6 py-4 text-sm font-semibold text-neutral-900">
            {{ $header }}
        </div>
    @endif

    <div class="px-6 py-5">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="border-t border-neutral-300/60 px-6 py-4">
            {{ $footer }}
        </div>
    @endif
</div>
