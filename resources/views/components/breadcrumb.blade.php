<nav class="breadcrumb">
    @forelse($items as $item)
    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a><span>/</span>
    @empty
    @endforelse
    <span>{{ $last }}</span>
</nav>
