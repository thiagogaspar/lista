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
@isset($genreName)
<nav class="flex items-center gap-2 text-sm text-surface-400 mb-4">
    <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a><span>/</span>
    <a href="{{ route('bands.index') }}" class="hover:text-brand-600">Bands</a><span>/</span>
    <span class="text-surface-700 dark:text-surface-200 font-medium">{{ $genreName }}</span>
</nav>
@endisset
<div class="flex items-center gap-3 mb-4">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">@isset($genreName){{ $genreName }}@else Bands @endisset</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-surface-700"></span>
</div>

<div class="lg:flex lg:gap-8">
    <div class="flex-1 min-w-0">
        <form method="GET" class="flex flex-wrap gap-2 mb-3">
            <input name="search" value="{{ request('search') }}" placeholder="Search bands..."
                   class="flex-1 min-w-[160px] px-3 py-1.5 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 placeholder-surface-400 focus:outline-none focus:ring-2 focus:ring-brand-500">
            <select name="genre" class="px-3 py-1.5 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-brand-500">
                <option value="">All genres</option>
                @foreach($genres as $slug => $name)<option value="{{ $slug }}" {{ (request('genre') ?? '') === $slug ? 'selected' : '' }}>{{ $name }}</option>@endforeach
            </select>
            <input name="year" value="{{ request('year') }}" placeholder="Year" class="w-20 px-3 py-1.5 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-brand-500">
            <select name="origin" class="px-3 py-1.5 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 focus:ring-2 focus:ring-brand-500">
                <option value="">All origins</option>
                @foreach($origins as $o)<option value="{{ $o }}" {{ request('origin') === $o ? 'selected' : '' }}>{{ $o }}</option>@endforeach
            </select>
            <button type="submit" class="px-4 py-1.5 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors text-sm font-medium">Filter</button>
        </form>

        <div class="flex items-center justify-between mb-3 text-xs text-surface-500 dark:text-surface-400">
            <span>{{ $bands->total() }} band{{ $bands->total() !== 1 ? 's' : '' }}</span>
            <div class="flex gap-3">
                <a href="?sort=name&dir={{ $sort === 'name' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'name' ? 'font-semibold text-brand-600 dark:text-brand-400' : '' }}">Name {{ $sort === 'name' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
                <a href="?sort=formed_year&dir={{ $sort === 'formed_year' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'formed_year' ? 'font-semibold text-brand-600 dark:text-brand-400' : '' }}">Year {{ $sort === 'formed_year' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
                <a href="?sort=genre&dir={{ $sort === 'genre' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'genre' ? 'font-semibold text-brand-600 dark:text-brand-400' : '' }}">Genre {{ $sort === 'genre' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
            @forelse($bands as $band)
                <a href="{{ route('bands.show', $band) }}" class="group block">
                    <div class="p-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg shadow-sm hover:shadow-md hover:border-brand-300 dark:hover:border-brand-700 transition-all duration-200 h-full flex gap-2.5">
                        @if($band->photo)<img src="{{ Storage::url($band->photo) }}" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0" loading="lazy">@endif
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-sm text-brand-600 dark:text-brand-400 group-hover:text-brand-700 truncate">{{ $band->name }}</h3>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @if($band->formed_year)<span class="text-[11px] px-1.5 py-0.5 rounded bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 font-medium">{{ $band->formed_year }}–{{ $band->dissolved_year ?? 'present' }}</span>@endif
                                @foreach($band->genres->take(2) as $genre)<span class="text-[11px] px-1.5 py-0.5 rounded bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-300 truncate">{{ $genre->name }}</span>@endforeach
                            </div>
                            @if($band->label)<span class="text-[11px] px-1.5 py-0.5 rounded bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300">{{ $band->label->name }}</span>@endif
                            <span class="text-[11px] text-surface-400">{{ $band->artists_count }} member{{ $band->artists_count !== 1 ? 's' : '' }}</span>
                            @if($band->origin)<p class="text-[11px] text-surface-400 mt-0.5 truncate">{{ $band->origin }}</p>@endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12"><p class="text-surface-500 mb-3">No bands found.</p><a href="/admin/bands/create" class="inline-flex px-4 py-1.5 bg-brand-500 text-white rounded-lg hover:bg-brand-600 text-sm font-medium">Add First Band</a></div>
            @endforelse
        </div>

        <div class="mt-4 flex items-center justify-between text-sm text-surface-500">
            <span>{{ $bands->firstItem() }}–{{ $bands->lastItem() }} of {{ $bands->total() }}</span>
            {{ $bands->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

    <aside class="lg:w-64 mt-6 lg:mt-0 shrink-0 lg:sticky lg:top-20 self-start">
        <div class="bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-200 mb-3">Genres</h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach(array_slice($genres, 0, 15) as $slug => $name)
                <a href="?genre={{ $slug }}" class="text-[11px] px-2 py-0.5 rounded bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-300 hover:bg-brand-50 dark:hover:bg-brand-900/30 hover:text-brand-600 transition-colors">{{ $name }}</a>
                @endforeach
            </div>
        </div>
        <div class="mt-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-200 mb-3">Labels</h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach($labels as $slug => $name)
                <a href="?label={{ $slug }}" class="text-[11px] px-2 py-0.5 rounded bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-300 hover:bg-brand-50 dark:hover:bg-brand-900/30 hover:text-brand-600 transition-colors {{ request('label') === $slug ? 'bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 font-semibold' : '' }}">{{ $name }}</a>
                @endforeach
            </div>
        </div>
        <div class="mt-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-200 mb-3">Origins</h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach($origins as $o)
                <a href="?origin={{ urlencode($o) }}" class="text-[11px] px-2 py-0.5 rounded bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-300 hover:bg-brand-50 dark:hover:bg-brand-900/30 hover:text-brand-600 transition-colors {{ request('origin') === $o ? 'bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 font-semibold' : '' }}">{{ $o }}</a>
                @endforeach
            </div>
        </div>
        <div class="mt-3"><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
@endsection
