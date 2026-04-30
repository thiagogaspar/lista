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
<nav class="flex items-center gap-2 text-xs text-surface-400 mb-5 uppercase tracking-wider">
    <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a><span>/</span>
    <a href="{{ route('bands.index') }}" class="hover:text-brand-600">Bands</a><span>/</span>
    <span class="text-surface-700 dark:text-ink-200 font-medium">{{ $genreName }}</span>
</nav>
@endisset

<div class="flex items-center gap-4 mb-6">
    <h1 class="font-display text-2xl sm:text-3xl font-bold text-surface-900 dark:text-ink-200">
        @isset($genreName){{ $genreName }}@else Bands @endisset
    </h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-ink-700"></span>
</div>

<div class="lg:flex lg:gap-8">
    <div class="flex-1 min-w-0">
        <!-- Filters -->
        <form method="GET" class="flex flex-wrap gap-2 mb-4">
            <input name="search" value="{{ request('search') }}" placeholder="Search bands..." class="flex-1 min-w-[160px] input">
            <select name="genre" class="select" style="max-width:160px">
                <option value="">All genres</option>
                @foreach($genres as $slug => $name)
                <option value="{{ $slug }}" {{ (request('genre') ?? '') === $slug ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <input name="year" value="{{ request('year') }}" placeholder="Year" class="input" style="max-width:80px">
            <select name="origin" class="select" style="max-width:140px">
                <option value="">All origins</option>
                @foreach($origins as $o)
                <option value="{{ $o }}" {{ request('origin') === $o ? 'selected' : '' }}>{{ $o }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-brand">Filter</button>
        </form>

        <!-- Sort + count -->
        <div class="flex items-center justify-between mb-4 text-xs text-surface-500 dark:text-surface-400">
            <span class="font-medium uppercase tracking-wider">{{ $bands->total() }} band{{ $bands->total() !== 1 ? 's' : '' }}</span>
            <div class="flex gap-4">
                <a href="?sort=name&dir={{ $sort === 'name' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'name' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }} uppercase tracking-wider">
                    Name {{ $sort === 'name' ? ($dir === 'asc' ? '&#8593;' : '&#8595;') : '' }}
                </a>
                <a href="?sort=formed_year&dir={{ $sort === 'formed_year' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'formed_year' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }} uppercase tracking-wider">
                    Year {{ $sort === 'formed_year' ? ($dir === 'asc' ? '&#8593;' : '&#8595;') : '' }}
                </a>
                <a href="?sort=genre&dir={{ $sort === 'genre' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'genre' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }} uppercase tracking-wider">
                    Genre {{ $sort === 'genre' ? ($dir === 'asc' ? '&#8593;' : '&#8595;') : '' }}
                </a>
            </div>
        </div>

        <!-- Card grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @forelse($bands as $band)
                <a href="{{ route('bands.show', $band) }}" class="block group">
                    <div class="card card-hover h-full bg-white dark:bg-ink-800 p-3 flex gap-2.5">
                        @if($band->photo)
                        <img src="{{ Storage::url($band->photo) }}" alt="" class="w-12 h-12 object-cover shrink-0" loading="lazy" style="border:1px solid var(--color-surface-200)">
                        @endif
                        <div class="min-w-0 flex-1">
                            <h3 class="font-display font-bold text-xs text-brand-600 dark:text-brand-400 group-hover:text-brand-700 dark:group-hover:text-brand-300 truncate">{{ $band->name }}</h3>
                            <div class="mt-1.5 flex flex-wrap gap-1">
                                @if($band->formed_year)
                                <span class="badge badge-brand">{{ $band->formed_year }}&ndash;{{ $band->dissolved_year ?? 'present' }}</span>
                                @endif
                                @foreach($band->genres->take(2) as $genre)
                                <span class="badge badge-surface">{{ $genre->name }}</span>
                                @endforeach
                            </div>
                            @if($band->label)
                            <div class="mt-1"><span class="badge badge-warm">{{ $band->label->name }}</span></div>
                            @endif
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-[10px] text-surface-400">{{ $band->artists_count }} member{{ $band->artists_count !== 1 ? 's' : '' }}</span>
                                @if($band->origin)
                                <span class="text-[10px] text-surface-400 truncate uppercase tracking-wider">{{ $band->origin }}</span>
                                @endif
                            </div>
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
            <span class="uppercase tracking-wider">{{ $bands->firstItem() }}&ndash;{{ $bands->lastItem() }} of {{ $bands->total() }}</span>
            {{ $bands->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="lg:w-64 mt-6 lg:mt-0 shrink-0 lg:sticky lg:top-16 self-start space-y-4">
        <div class="card bg-white dark:bg-ink-800 p-4">
            <h3 class="font-display text-sm font-bold mb-3 text-surface-700 dark:text-ink-200">Genres</h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach(array_slice($genres, 0, 20) as $slug => $name)
                <a href="?genre={{ $slug }}" class="text-[10px] uppercase tracking-wider px-2 py-1 border border-surface-200 dark:border-ink-700 text-surface-500 dark:text-surface-400 hover:border-brand-300 hover:text-brand-600 dark:hover:border-brand-700 dark:hover:text-brand-400 transition-colors {{ request('genre') === $slug ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : '' }}">{{ $name }}</a>
                @endforeach
            </div>
        </div>
        <div class="card bg-white dark:bg-ink-800 p-4">
            <h3 class="font-display text-sm font-bold mb-3 text-surface-700 dark:text-ink-200">Labels</h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach($labels as $slug => $name)
                <a href="?label={{ $slug }}" class="text-[10px] uppercase tracking-wider px-2 py-1 border border-surface-200 dark:border-ink-700 text-surface-500 dark:text-surface-400 hover:border-warm-300 hover:text-warm-600 dark:hover:border-warm-700 dark:hover:text-warm-400 transition-colors {{ request('label') === $slug ? 'border-warm-500 text-warm-600 dark:text-warm-400 font-bold' : '' }}">{{ $name }}</a>
                @endforeach
            </div>
        </div>
        <div class="card bg-white dark:bg-ink-800 p-4">
            <h3 class="font-display text-sm font-bold mb-3 text-surface-700 dark:text-ink-200">Origins</h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach($origins as $o)
                <a href="?origin={{ urlencode($o) }}" class="text-[10px] uppercase tracking-wider px-2 py-1 border border-surface-200 dark:border-ink-700 text-surface-500 dark:text-surface-400 hover:border-brand-300 hover:text-brand-600 dark:hover:border-brand-700 dark:hover:text-brand-400 transition-colors {{ request('origin') === $o ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : '' }}">{{ $o }}</a>
                @endforeach
            </div>
        </div>
        <div><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
</div>
@endsection
