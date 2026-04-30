<nav class="flex items-center gap-2 text-sm text-surface-400 mb-6">
    @foreach($items as $item)
        <a href="{{ $item['url'] }}" class="hover:text-brand-600">{{ $item['label'] }}</a><span>/</span>
    @endforeach
    <span class="text-surface-700 dark:text-surface-200 font-medium">{{ $last }}</span>
</nav>
