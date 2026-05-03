<nav class="breadcrumb">
    @foreach($items as $item)
    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a><span>/</span>
    @endforeach
    <span>{{ $last }}</span>
</nav>
