@props(['header' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden']) }}>
    @if ($header)
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            {{ $header }}
        </div>
    @endif

    <div class="px-5 py-5">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $footer }}
        </div>
    @endif
</div>
