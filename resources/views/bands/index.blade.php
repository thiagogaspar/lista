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

<!-- Classifieds-style list -->
<div class="divide-y divide-surface-200 dark:divide-ink-700 border-t border-surface-200 dark:border-ink-700">
    @forelse($bands as $band)
        <a href="{{ route('bands.show', $band) }}" class="flex items-start gap-3 py-3 px-2 -mx-2 hover:bg-surface-100 dark:hover:bg-ink-800/50 transition-colors group">
            @if($band->photo)
            <img src="{{ Storage::url($band->photo) }}" alt="{{ $band->name }}" class="w-10 h-10 object-cover shrink-0 mt-0.5" loading="lazy">
            @endif
            <div class="min-w-0 flex-1">
                <h2 class="text-base font-bold text-surface-900 dark:text-ink-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 leading-tight">{{ $band->name }}</h2>
                <div class="text-xs text-surface-500 dark:text-ink-500 mt-0.5 leading-relaxed">
                    @if($band->formed_year)
                    <span>{{ $band->formed_year }}&ndash;{{ $band->dissolved_year ?? 'present' }}</span><span class="mx-1">&middot;</span>
                    @endif
                    @foreach($band->genres->take(3) as $genre)
                    <span>{{ $genre->name }}</span>@if(!$loop->last), @endif
                    @endforeach
                    @if($band->origin)
                    <span class="mx-1">&middot;</span><span>{{ $band->origin }}</span>
                    @endif
                </div>
                <div class="text-[11px] text-surface-400 dark:text-ink-600 mt-0.5">
                    <span>{{ $band->artists_count }} member{{ $band->artists_count !== 1 ? 's' : '' }}</span>
                    @if($band->label)
                    <span class="mx-1.5">&middot;</span><span>{{ $band->label->name }}</span>
                    @endif
                </div>
            </div>
            <span class="text-surface-300 dark:text-ink-600 text-xs mt-1.5 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">&rarr;</span>
        </a>
    @empty
        <div class="text-center py-16">
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
