{{-- Kepala halaman standar: tautan kembali (opsional), eyebrow, judul, deskripsi, dan slot aksi di kanan. --}}
@props([
    'title',
    'description' => null,
    'back' => null,
    'backLabel' => 'Kembali',
    'eyebrow' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        @if ($back)
            <a href="{{ $back }}" class="mb-5 inline-flex items-center gap-1.5 text-xs text-neutral-600 transition-colors hover:text-neutral-900">
                <span aria-hidden="true">&larr;</span>
                <span>{{ $backLabel }}</span>
            </a>
        @endif

        @if ($eyebrow)
            <p class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">{{ $eyebrow }}</p>
        @endif

        <h1 class="{{ $eyebrow ? 'mt-1.5' : '' }} text-2xl font-extrabold tracking-tight text-neutral-900">{{ $title }}</h1>

        @if ($description)
            <p class="mt-1.5 max-w-2xl text-xs leading-relaxed text-neutral-600">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
