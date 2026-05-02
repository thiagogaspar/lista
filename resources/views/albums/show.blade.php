@extends('layouts.app')

@section('head')
@php
if (!isset($seo)) {
    $seo = new \App\Values\SeoData(
        title: $album->title . ' — ' . $album->band->name,
        description: $album->description ? Str::limit(strip_tags($album->description), 160) : $album->title . ' by ' . $album->band->name,
        type: 'music.album',
        image: $album->cover_art ? \Illuminate\Support\Facades\Storage::url($album->cover_art) : null,
        canonical: route('albums.show', $album),
    );
}
@endphp
<x-seo-meta :seo="$seo" />
@if($album->cover_art)
<link rel="preload" href="{{ Storage::url($album->cover_art) }}" as="image" fetchpriority="high">
<meta name="twitter:card" content="summary">
<meta name="twitter:image" content="{{ Storage::url($album->cover_art) }}">
@endif
@endsection

@section('content')
@php
$albumGeo = match ($album->band?->genres->first()?->slug) {
    'alternative-rock', 'indie-rock', 'dream-pop', 'shoegaze' => 'sage',
    'grunge', 'punk-rock', 'post-grunge' => 'ocher',
    default => 'terracotta',
};
@endphp
<div class="max-w-6xl mx-auto px-4">
<!-- Hero -->
<section class="relative -mx-4 -mt-6 mb-8 overflow-hidden bg-ink" style="aspect-ratio:16/4; max-height:45vh;">
    @if($album->cover_art)
    <img src="{{ Storage::url($album->cover_art) }}" alt="{{ $album->title }}" class="absolute inset-0 w-full h-full object-cover opacity-30" fetchpriority="high" decoding="async" sizes="100vw">
    @endif
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        @if($albumGeo === 'terracotta')
        <div class="absolute w-[45vmin] h-[45vmin] rounded-full bg-brand-500/15 -top-[12%] -right-[8%]"></div>
        <div class="absolute w-[8vmin] h-[8vmin] bg-accent-500/20 bottom-[30%] left-[15%]"></div>
        @elseif($albumGeo === 'sage')
        <div class="absolute w-[35vmin] h-[35vmin] rounded-full bg-accent-500/15 top-[10%] right-[10%]"></div>
        <div class="absolute w-[30%] h-[12%] bg-brand-500/15 bottom-0 right-0"></div>
        @else
        <div class="absolute w-[30vmin] h-[30vmin] bg-warm-500/15 -top-[5%] -left-[5%] rotate-12"></div>
        <div class="absolute w-[20vmin] h-[20vmin] rounded-full bg-accent-500/15 bottom-[10%] right-[8%]"></div>
        @endif
    </div>
    <div class="absolute inset-0 bg-gradient-to-r from-ink/80 via-ink/50 to-ink/20"></div>
    <div class="relative z-10 flex flex-col justify-end h-full">
        <div class="max-w-6xl mx-auto px-4 w-full pb-8 sm:pb-12 pt-6">
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-black text-white leading-none tracking-tight">{{ $album->title }}</h1>
        </div>
    </div>
</section>

<nav class="flex items-center gap-2 text-xs text-surface-400 mb-8 uppercase tracking-wider">
    <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a><span>/</span>
    <a href="{{ route('albums.index') }}" class="hover:text-brand-600">Albums</a><span>/</span>
    <span class="text-surface-700 dark:text-ink-200 font-medium">{{ $album->title }}</span>
</nav>

<div class="lg:flex lg:gap-10">
    <div class="lg:w-80 shrink-0">
        @if($album->cover_art)
        <img src="{{ Storage::url($album->cover_art) }}" alt="{{ $album->title }} cover" class="w-full aspect-square object-cover" fetchpriority="high" style="border:1px solid var(--color-surface-200)">
        @else
        <div class="w-full aspect-square bg-surface-100 dark:bg-ink-700 flex items-center justify-center text-surface-300 dark:text-ink-400" style="border:1px solid var(--color-surface-200)">
            <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
        </div>
        @endif
    </div>

    <div class="flex-1 min-w-0 mt-6 lg:mt-0">
        <h1 class="font-display text-3xl sm:text-4xl font-bold text-surface-900 dark:text-ink-200 leading-tight tracking-tight">{{ $album->title }}</h1>

        <div class="flex flex-wrap gap-2 mt-3">
            <a href="{{ route('bands.show', $album->band) }}" class="badge badge-brand text-sm py-1 px-3">{{ $album->band->name }}</a>
            @if($album->release_year)
            <span class="badge badge-surface">{{ $album->release_year }}</span>
            @endif
        </div>

        @if($album->description)
        <div class="prose max-w-none mt-6">{{ $album->description }}</div>
        @endif

        @if($album->tracklist)
        <h2 class="font-display text-xl font-bold mt-8 mb-4 text-surface-900 dark:text-ink-200">Tracklist</h2>
        <ol class="border border-surface-200 dark:border-ink-700 divide-y divide-surface-200 dark:divide-ink-700 bg-white dark:bg-ink-800">
            @foreach($album->tracklist as $i => $track)
            <li class="flex items-center gap-3 px-4 py-2.5 text-sm">
                <span class="text-surface-400 font-mono text-xs w-6 text-right">{{ $i + 1 }}.</span>
                <span class="text-surface-700 dark:text-ink-200">{{ is_array($track) ? ($track['title'] ?? $track[0] ?? '') : $track }}</span>
            </li>
            @endforeach
        </ol>
        @endif
    </div>
</div>
</div>
@endsection
