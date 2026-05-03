<div class="flex items-end gap-4 mb-5">
    <{{ $tag ?? 'h2' }} class="section-header flex-1">{{ $slot }}</{{ $tag ?? 'h2' }}>
    @if(isset($count))
    <span class="font-display text-xs font-bold text-surface-400 dark:text-ink-400 shrink-0">({{ $count }})</span>
    @endif
    @if(isset($action))
    {{ $action }}
    @endif
</div>
