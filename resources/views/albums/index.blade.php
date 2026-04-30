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
    <h1 class="font-display text-2xl sm:text-3xl font-bold text-surface-900 dark:text-ink-200">Albums</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-ink-700"></span>
</div>

<form method="GET" class="flex flex-wrap gap-2 mb-6">
    <input name="search" value="{{ request('search') }}" placeholder="Search albums..." aria-label="Search albums" class="input flex-1 min-w-[180px]">
    <select name="genre" aria-label="Filter by genre" class="select" style="max-width:160px">
        <option value="">All genres</option>
        @foreach($genres as $slug => $name)
        <option value="{{ $slug }}" {{ request('genre') === $slug ? 'selected' : '' }}>{{ $name }}</option>
        @endforeach
    </select>
    <select name="year" aria-label="Filter by year" class="select" style="max-width:120px">
        <option value="">All years</option>
        @foreach($years as $y)
        <option value="{{ $y }}" {{ (string) request('year') === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-brand">Filter</button>
</form>

<div class="flex items-center justify-between mb-4 text-xs text-surface-500">
    <span class="uppercase tracking-wider font-medium">{{ $albums->total() }} album{{ $albums->total() !== 1 ? 's' : '' }}</span>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
    @forelse($albums as $album)
    <a href="{{ route('albums.show', $album) }}" class="block group">
        <div class="card card-hover h-full bg-white dark:bg-ink-800">
            @if($album->cover_art)
            <img src="{{ Storage::url($album->cover_art) }}" alt="{{ $album->title }} cover" class="w-full aspect-square object-cover" loading="lazy" style="border-bottom:1px solid var(--color-surface-200)">
            @else
            <div class="w-full aspect-square bg-surface-100 dark:bg-ink-700 flex items-center justify-center text-surface-300 dark:text-ink-600" style="border-bottom:1px solid var(--color-surface-200)">
                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
            </div>
            @endif
            <div class="p-3">
                <h3 class="font-display font-bold text-sm text-surface-800 dark:text-ink-200 group-hover:text-brand-600 dark:group-hover:text-brand-400 truncate transition-colors">{{ $album->title }}</h3>
                <p class="text-[11px] text-surface-500 mt-0.5 truncate">{{ $album->band->name }}</p>
                @if($album->release_year)
                <p class="text-[10px] text-surface-400 mt-1 uppercase tracking-wider">{{ $album->release_year }}</p>
                @endif
            </div>
        </div>
    </a>
    @empty
    <div class="col-span-full text-center py-16"><p class="text-surface-500">No albums found.</p></div>
    @endforelse
</div>

<div class="mt-6 flex items-center justify-between text-xs text-surface-500">
    <span class="uppercase tracking-wider">{{ $albums->firstItem() }}&ndash;{{ $albums->lastItem() }} of {{ $albums->total() }}</span>
    {{ $albums->appends(request()->query())->links('pagination::tailwind') }}
</div>
</div>
@endsection
