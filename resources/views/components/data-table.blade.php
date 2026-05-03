<div class="border-2 border-surface-200 dark:border-ink-700 bg-white dark:bg-ink-800">
    @if(isset($header))
    <div class="border-b-2 border-surface-200 dark:border-ink-700 px-4 py-2 bg-surface-50 dark:bg-ink-700 font-display text-xs font-bold uppercase tracking-wider text-surface-600 dark:text-ink-300">
        {{ $header }}
    </div>
    @endif
    {{ $slot }}
</div>
