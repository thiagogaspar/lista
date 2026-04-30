<nav class="flex items-center gap-2 text-xs text-surface-400 mb-8 uppercase tracking-wider">
    @foreach($items as $item)
    <a href="{{ $item['url'] }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">{{ $item['label'] }}</a><span>/</span>
    @endforeach
    <span class="text-surface-700 dark:text-ink-200 font-medium">{{ $last }}</span>
</nav>
