<nav class="breadcrumbs text-sm text-base-content/60 mb-6">
    <ul>
        @foreach($items as $item)
        <li><a href="{{ $item['url'] }}" class="hover:text-primary">{{ $item['label'] }}</a></li>
        @endforeach
        <li class="text-base-content font-medium">{{ $last }}</li>
    </ul>
</nav>
