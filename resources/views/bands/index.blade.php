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
<div class="breadcrumbs text-sm text-base-content/60 mb-4">
    <ul>
        <li><a href="{{ route('home') }}" class="hover:text-primary">Home</a></li>
        <li><a href="{{ route('bands.index') }}" class="hover:text-primary">Bands</a></li>
        <li class="text-base-content font-medium">{{ $genreName }}</li>
    </ul>
</div>
@endisset
<div class="flex items-center gap-3 mb-4">
    <h1 class="text-2xl font-bold text-base-content">@isset($genreName){{ $genreName }}@else Bands @endisset</h1>
    <div class="divider divider-neutral flex-1 h-px"></div>
</div>

<div class="lg:flex lg:gap-8">
    <div class="flex-1 min-w-0">
        <form method="GET" class="flex flex-wrap gap-2 mb-3">
            <input name="search" value="{{ request('search') }}" placeholder="Search bands..."
                   class="input input-bordered input-sm flex-1 min-w-[160px]">
            <select name="genre" class="select select-bordered select-sm">
                <option value="">All genres</option>
                @foreach($genres as $slug => $name)<option value="{{ $slug }}" {{ (request('genre') ?? '') === $slug ? 'selected' : '' }}>{{ $name }}</option>@endforeach
            </select>
            <input name="year" value="{{ request('year') }}" placeholder="Year" class="input input-bordered input-sm w-20">
            <select name="origin" class="select select-bordered select-sm">
                <option value="">All origins</option>
                @foreach($origins as $o)<option value="{{ $o }}" {{ request('origin') === $o ? 'selected' : '' }}>{{ $o }}</option>@endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        </form>

        <div class="flex items-center justify-between mb-3 text-xs text-base-content/50">
            <span>{{ $bands->total() }} band{{ $bands->total() !== 1 ? 's' : '' }}</span>
            <div class="flex gap-3">
                <a href="?sort=name&dir={{ $sort === 'name' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-primary {{ $sort === 'name' ? 'font-semibold text-primary' : '' }}">Name {{ $sort === 'name' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
                <a href="?sort=formed_year&dir={{ $sort === 'formed_year' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-primary {{ $sort === 'formed_year' ? 'font-semibold text-primary' : '' }}">Year {{ $sort === 'formed_year' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
                <a href="?sort=genre&dir={{ $sort === 'genre' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-primary {{ $sort === 'genre' ? 'font-semibold text-primary' : '' }}">Genre {{ $sort === 'genre' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
            @forelse($bands as $band)
                <a href="{{ route('bands.show', $band) }}" class="group block">
                    <div class="card card-compact bg-base-100 shadow-md hover:shadow-xl transition-shadow duration-200 h-full">
                        <div class="card-body flex-row gap-3 p-4">
                            @if($band->photo)<img src="{{ Storage::url($band->photo) }}" alt="" class="w-12 h-12 rounded-xl object-cover shrink-0" loading="lazy">@endif
                            <div class="min-w-0 flex-1">
                                <h3 class="card-title text-sm text-primary group-hover:text-primary-focus truncate">{{ $band->name }}</h3>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @if($band->formed_year)<span class="badge badge-xs bg-primary/10 text-primary border-none">{{ $band->formed_year }}–{{ $band->dissolved_year ?? 'present' }}</span>@endif
                                    @foreach($band->genres->take(2) as $genre)<span class="badge badge-xs badge-ghost truncate">{{ $genre->name }}</span>@endforeach
                                </div>
                                @if($band->label)<span class="badge badge-xs bg-primary/10 text-primary border-none mt-1">{{ $band->label->name }}</span>@endif
                                <p class="text-xs text-base-content/50 mt-0.5">{{ $band->artists_count }} member{{ $band->artists_count !== 1 ? 's' : '' }}@if($band->origin) · {{ $band->origin }}@endif</p>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-16">
                    <p class="text-base-content/50 mb-4">No bands found.</p>
                    <a href="/admin/bands/create" class="btn btn-primary">Add First Band</a>
                </div>
            @endforelse
        </div>

        <div class="mt-4 flex items-center justify-between text-sm text-base-content/50">
            <span>{{ $bands->firstItem() }}–{{ $bands->lastItem() }} of {{ $bands->total() }}</span>
            <div class="join">{{ $bands->appends(request()->query())->links('pagination::tailwind') }}</div>
        </div>
    </div>

    <aside class="lg:w-64 mt-6 lg:mt-0 shrink-0 lg:sticky lg:top-20 self-start space-y-3">
        <div class="card card-compact bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <h3 class="card-title text-sm">Genres</h3>
                <div class="flex flex-wrap gap-1.5">
                    @foreach(array_slice($genres, 0, 15) as $slug => $name)
                    <a href="?genre={{ $slug }}" class="badge badge-sm badge-ghost hover:bg-primary/10 hover:text-primary transition-colors {{ request('genre') === $slug ? 'badge-primary' : '' }}">{{ $name }}</a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card card-compact bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <h3 class="card-title text-sm">Labels</h3>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($labels as $slug => $name)
                    <a href="?label={{ $slug }}" class="badge badge-sm badge-ghost hover:bg-primary/10 hover:text-primary transition-colors {{ request('label') === $slug ? 'badge-primary' : '' }}">{{ $name }}</a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card card-compact bg-base-100 shadow-sm">
            <div class="card-body p-4">
                <h3 class="card-title text-sm">Origins</h3>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($origins as $o)
                    <a href="?origin={{ urlencode($o) }}" class="badge badge-sm badge-ghost hover:bg-primary/10 hover:text-primary transition-colors {{ request('origin') === $o ? 'badge-primary' : '' }}">{{ $o }}</a>
                    @endforeach
                </div>
            </div>
        </div>
        <x-ad-slot position="sidebar" />
    </aside>
</div>
@endsection
