@extends('layouts.app')

@section('head')
@php
$seo = new \App\Values\SeoData(
    title: request('search') ? 'Search: ' . e(request('search')) . ' — All Albums' : 'All Albums',
    description: 'Browse album discography.',
    canonical: route('albums.index'),
);
@endphp
<x-seo-meta :seo="$seo" />
@endsection

@php $qs = http_build_query(request()->except('page')); @endphp

@section('content')
<div class="max-w-6xl mx-auto px-4">
<div class="flex items-center gap-4 mb-6">
    <h1 class="text-xl sm:text-2xl font-bold text-surface-900 dark:text-ink-200">Albums</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-ink-700"></span>
</div>

<form method="GET" class="flex flex-wrap gap-2 mb-6">
    <input name="search" value="{{ request('search') }}" placeholder="Search albums..." aria-label="Search albums" class="input flex-1 min-w-[160px]">
    <select name="genre" aria-label="Filter by genre" class="select" style="max-width:150px">
        <option value="">All genres</option>
        @foreach($genres as $slug => $name)
        <option value="{{ $slug }}" {{ request('genre') === $slug ? 'selected' : '' }}>{{ $name }}</option>
        @endforeach
    </select>
    <select name="year" aria-label="Filter by year" class="select" style="max-width:110px">
        <option value="">All years</option>
        @foreach($years as $y)
        <option value="{{ $y }}" {{ (string) request('year') === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-brand text-xs">Filter</button>
</form>

<div class="flex items-center justify-between mb-4 text-xs text-surface-500">
    <span class="font-semibold uppercase tracking-wider">{{ $albums->total() }} album{{ $albums->total() !== 1 ? 's' : '' }}</span>
</div>

<!-- Classifieds-style list -->
<div class="divide-y divide-surface-200 dark:divide-ink-700 border-t border-surface-200 dark:border-ink-700">
    @forelse($albums as $album)
    <a href="{{ route('albums.show', $album) }}" class="flex items-start gap-3 py-3 px-2 -mx-2 hover:bg-surface-100 dark:hover:bg-ink-800/50 transition-colors group">
        @if($album->cover_art)
        <img src="{{ Storage::url($album->cover_art) }}" alt="{{ $album->title }} cover" class="w-10 h-10 object-cover shrink-0 mt-0.5" loading="lazy">
        @endif
        <div class="min-w-0 flex-1">
            <h2 class="text-base font-bold text-surface-900 dark:text-ink-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 leading-tight">{{ $album->title }}</h2>
            <div class="text-xs text-surface-500 dark:text-ink-500 mt-0.5 leading-relaxed">
                <span>{{ $album->band->name }}</span>
                @if($album->release_year)
                <span class="mx-1">&middot;</span><span>{{ $album->release_year }}</span>
                @endif
            </div>
        </div>
        <span class="text-surface-300 dark:text-ink-400 text-xs mt-1.5 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">&rarr;</span>
    </a>
    @empty
    <div class="col-span-full text-center py-16"><p class="text-surface-500">No albums found.</p></div>
    @endforelse
</div>

<div class="mt-6 flex items-center justify-between text-xs text-surface-500">
    <span class="uppercase tracking-wider font-semibold">{{ $albums->firstItem() }}&ndash;{{ $albums->lastItem() }} of {{ $albums->total() }}</span>
    {{ $albums->appends(request()->query())->links('pagination::tailwind') }}
</div>
</div>
@endsection
