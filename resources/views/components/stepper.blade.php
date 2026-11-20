@props([
    'steps' => [],
    'current' => 1,
])

@php
$total = count($steps);
@endphp

<nav aria-label="Progress">
    <ol class="flex items-start gap-2 sm:gap-4">
        @foreach ($steps as $index => $label)
            @php
                $step = $index + 1;
                $isCompleted = $step < $current;
                $isCurrent = $step === $current;
            @endphp
            <li class="relative flex-1">
                <div class="flex flex-col items-center text-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold
                        {{ $isCompleted ? 'bg-brand-600 text-white' : ($isCurrent ? 'bg-brand-100 text-brand-700 ring-2 ring-brand-600' : 'bg-neutral-100 text-neutral-500') }}">
                        @if ($isCompleted)
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            {{ $step }}
                        @endif
                    </div>
                    <span class="mt-1.5 text-xs font-medium
                        {{ $isCurrent ? 'text-brand-700' : ($isCompleted ? 'text-neutral-700' : 'text-neutral-500') }}">
                        {{ $label }}
                    </span>
                </div>
                @if (! $loop->last)
                    <div class="absolute left-1/2 top-4 hidden h-0.5 w-[calc(100%-1rem)] -translate-y-1/2 sm:block
                        {{ $isCompleted ? 'bg-brand-600' : 'bg-neutral-200' }}">
                    </div>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
