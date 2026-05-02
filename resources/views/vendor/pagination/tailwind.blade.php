@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="opacity-40 cursor-default px-2 py-1 text-xs uppercase tracking-wider border border-surface-200 dark:border-ink-700 text-surface-400">&laquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-2 py-1 text-xs uppercase tracking-wider border border-surface-200 dark:border-ink-700 text-surface-500 hover:text-brand-600 hover:border-brand-500 dark:hover:text-brand-400 dark:hover:border-brand-700 transition-colors">&laquo;</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 py-1 text-xs text-surface-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-2.5 py-1 text-xs font-bold uppercase tracking-wider bg-brand-600 text-white" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-2.5 py-1 text-xs uppercase tracking-wider border border-surface-200 dark:border-ink-700 text-surface-500 hover:text-brand-600 hover:border-brand-500 dark:hover:text-brand-400 dark:hover:border-brand-700 transition-colors">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-2 py-1 text-xs uppercase tracking-wider border border-surface-200 dark:border-ink-700 text-surface-500 hover:text-brand-600 hover:border-brand-500 dark:hover:text-brand-400 dark:hover:border-brand-700 transition-colors">&raquo;</a>
        @else
            <span class="opacity-40 cursor-default px-2 py-1 text-xs uppercase tracking-wider border border-surface-200 dark:border-ink-700 text-surface-400">&raquo;</span>
        @endif
    </nav>
@endif
