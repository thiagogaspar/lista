@extends('layouts.app')

@section('head')
@php
$filterLabel = '';
$genreSlug = $genreName ?? request('genre');
if ($genreSlug) { $genreLabel = $genres[$genreSlug] ?? $genreSlug; $filterLabel = "{$genreLabel} — "; }
if (request('label')) { $filterLabel = "Label: " . (request('label')) . " — "; }
if (request('origin')) { $filterLabel = e(request('origin')) . " — "; }
if (request('search')) { $filterLabel = "Search: " . e(request('search')) . " — "; }
$seo = new \App\Values\SeoData(
    title: $filterLabel . (isset($genreName) ? $genreName . ' Bands' : 'All Bands'),
    description: 'Browse ' . (isset($genreName) ? $genreName : 'all') . ' bands in the directory.' . ($filterLabel ? ' Filtered by ' . strip_tags($filterLabel) . '.' : ''),
    canonical: isset($genreName) ? route('genres.show', request()->route('slug') ?? '') : route('bands.index'),
);
@endphp
<x-seo-meta :seo="$seo" />
@endsection

@php $qs = http_build_query(request()->except(['sort','dir'])); @endphp

@section('content')
<div class="max-w-6xl mx-auto px-4">
@isset($genreName)
<nav class="flex items-center gap-2 text-xs text-surface-400 mb-5">
    <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a><span>/</span>
    <a href="{{ route('bands.index') }}" class="hover:text-brand-600">Bands</a><span>/</span>
    <span class="text-surface-700 dark:text-ink-200 font-medium">{{ $genreName }}</span>
</nav>
@endisset

<div class="flex items-center gap-4 mb-6">
    <h1 class="text-xl sm:text-2xl font-bold text-surface-900 dark:text-ink-200">
        @isset($genreName){{ $genreName }}@else Bands @endisset
    </h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-ink-700"></span>
</div>

<!-- Filters -->
<form method="GET" class="flex flex-wrap gap-2 mb-4">
    <input name="search" value="{{ request('search') }}" placeholder="Search bands..." aria-label="Search bands" class="flex-1 min-w-[140px] input">
    <select name="genre" aria-label="Filter by genre" class="select" style="max-width:150px">
        <option value="">All genres</option>
        @foreach($genres as $slug => $name)
        <option value="{{ $slug }}" {{ (request('genre') ?? '') === $slug ? 'selected' : '' }}>{{ $name }}</option>
        @endforeach
    </select>
    <input name="year" value="{{ request('year') }}" placeholder="Year" aria-label="Filter by year" class="input" style="max-width:70px">
    <select name="origin" aria-label="Filter by origin" class="select" style="max-width:130px">
        <option value="">All origins</option>
        @foreach($origins as $o)
        <option value="{{ $o }}" {{ request('origin') === $o ? 'selected' : '' }}>{{ $o }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-brand text-xs">Filter</button>
</form>

<!-- Sort + count -->
<div class="flex items-center justify-between mb-4 text-xs text-surface-500">
    <span class="font-semibold uppercase tracking-wider">{{ $bands->total() }} band{{ $bands->total() !== 1 ? 's' : '' }}</span>
    <div class="flex gap-3">
        <a href="?sort=name&dir={{ $sort === 'name' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'name' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }}">
            Name {{ $sort === 'name' ? ($dir === 'asc' ? '&uarr;' : '&darr;') : '' }}
        </a>
        <a href="?sort=formed_year&dir={{ $sort === 'formed_year' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'formed_year' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }}">
            Year {{ $sort === 'formed_year' ? ($dir === 'asc' ? '&uarr;' : '&darr;') : '' }}
        </a>
        <a href="?sort=genre&dir={{ $sort === 'genre' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'genre' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }}">
            Genre {{ $sort === 'genre' ? ($dir === 'asc' ? '&uarr;' : '&darr;') : '' }}
        </a>
    </div>
</div>

<!-- Bandcamp-style card grid -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    @forelse($bands as $band)
        <a href="{{ route('bands.show', $band) }}" class="block group">
            <div class="card-bandcamp">
                <div class="aspect-square overflow-hidden">
                    @if($band->photo)
                    <img src="{{ Storage::url($band->photo) }}" alt="{{ $band->name }}" class="w-full h-full object-cover" loading="lazy">
                    @else
                    <div class="w-full h-full bg-surface-100 dark:bg-ink-900 flex items-center justify-center text-surface-300 dark:text-ink-600">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.5" d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </div>
                    @endif
                    <div class="card-bandcamp-overlay">
                        <span class="text-white text-2xl font-light opacity-0 group-hover:opacity-100">&rarr;</span>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-sm font-bold text-surface-900 dark:text-ink-100 truncate">{{ $band->name }}</h3>
                <div class="flex flex-wrap gap-1 mt-0.5">
                    @if($band->formed_year)
                    <span class="text-[10px] font-semibold text-surface-400 dark:text-ink-500">{{ $band->formed_year }}&ndash;{{ $band->dissolved_year ?? 'present' }}</span>
                    @endif
                    @foreach($band->genres->take(2) as $genre)
                    <span class="text-[10px] text-surface-400 dark:text-ink-500">{{ $genre->name }}</span>
                    @endforeach
                </div>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[9px] text-surface-400 dark:text-ink-600 font-semibold">{{ $band->artists_count }} member{{ $band->artists_count !== 1 ? 's' : '' }}</span>
                    @if($band->origin)
                    <span class="text-[9px] text-surface-400 dark:text-ink-600 uppercase tracking-wider">{{ $band->origin }}</span>
                    @endif
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full text-center py-16">
            <p class="text-surface-500 mb-4">No bands found.</p>
            <a href="/admin/bands/create" class="btn btn-brand">Add First Band</a>
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-6 flex items-center justify-between text-xs text-surface-500">
    <span class="uppercase tracking-wider font-semibold">{{ $bands->firstItem() }}&ndash;{{ $bands->lastItem() }} of {{ $bands->total() }}</span>
    {{ $bands->appends(request()->query())->links('pagination::tailwind') }}
</div>
</div>
@endsection
