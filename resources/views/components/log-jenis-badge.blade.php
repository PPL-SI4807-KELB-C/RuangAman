@props([
    'jenis' => 'manual',
])

@php
    $isSistem = in_array($jenis, [\App\Models\LogPenanganan::JENIS_SISTEM, 'otomatis_status'], true);
@endphp

@if ($isSistem)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full border border-neutral-300 bg-surface px-2.5 py-0.5 text-[11px] font-semibold text-neutral-800']) }}>
        <svg class="h-3 w-3 text-neutral-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        <span>Sistem Otomatis</span>
    </span>
@else
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 px-2.5 py-0.5 text-[11px] font-semibold text-safety-info']) }}>
        <svg class="h-3 w-3 text-safety-info" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        <span>Catatan Manual</span>
    </span>
@endif
