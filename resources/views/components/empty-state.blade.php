{{-- Keadaan kosong standar: pesan utama, keterangan opsional, dan slot aksi. --}}
@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-dashed border-neutral-300 bg-white px-6 py-12 text-center']) }}>
    <p class="text-sm font-semibold text-neutral-900">{{ $title }}</p>
    @if ($description)
        <p class="mx-auto mt-1.5 max-w-md text-xs leading-relaxed text-neutral-600">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-5 flex flex-wrap items-center justify-center gap-3">{{ $slot }}</div>
    @endif
</div>
