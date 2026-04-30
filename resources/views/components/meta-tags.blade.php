<x-layouts.app>
    <x-slot name="head">
        <title>{{ $fullTitle() }}</title>
        <meta name="description" content="{{ $description }}">
        <meta property="og:title" content="{{ $fullTitle() }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:type" content="{{ $type }}">
        <meta property="og:url" content="{{ $canonical }}">
        @if($image)
            <meta property="og:image" content="{{ $image }}">
        @endif
        <meta name="twitter:card" content="summary">
        @if($canonical)
            <link rel="canonical" href="{{ $canonical }}">
        @endif
    </x-slot>

    {{ $slot }}
</x-layouts.app>
