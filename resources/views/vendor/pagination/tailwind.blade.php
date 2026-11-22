@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <p class="text-[11px] text-neutral-600">
            Menampilkan
            <span class="font-medium text-neutral-700">{{ $paginator->firstItem() }}</span>
            &ndash;
            <span class="font-medium text-neutral-700">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-medium text-neutral-700">{{ $paginator->total() }}</span>
            data
        </p>

        <ul class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <li aria-hidden="true">
                    <span class="inline-flex items-center rounded px-3 py-1.5 text-xs font-medium text-neutral-400">&larr; Sebelumnya</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center rounded px-3 py-1.5 text-xs font-medium text-neutral-700 ring-1 ring-inset ring-neutral-300/70 transition-colors hover:bg-neutral-50" aria-label="Halaman sebelumnya">
                        &larr; Sebelumnya
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="px-1 text-xs text-neutral-400" aria-hidden="true">{{ $element }}</li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex h-7 min-w-7 items-center justify-center rounded bg-neutral-900 px-2 text-xs font-medium text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="Halaman {{ $page }}" class="inline-flex h-7 min-w-7 items-center justify-center rounded px-2 text-xs font-medium text-neutral-700 ring-1 ring-inset ring-neutral-300/70 transition-colors hover:bg-neutral-50">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center rounded px-3 py-1.5 text-xs font-medium text-neutral-700 ring-1 ring-inset ring-neutral-300/70 transition-colors hover:bg-neutral-50" aria-label="Halaman berikutnya">
                        Berikutnya &rarr;
                    </a>
                </li>
            @else
                <li aria-hidden="true">
                    <span class="inline-flex items-center rounded px-3 py-1.5 text-xs font-medium text-neutral-400">Berikutnya &rarr;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
