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
<div class="max-w-6xl mx-auto px-4">
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
        <div class="w-full aspect-square bg-surface-100 dark:bg-ink-700 flex items-center justify-center text-surface-300 dark:text-ink-600" style="border:1px solid var(--color-surface-200)">
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
