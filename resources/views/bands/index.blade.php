@extends('layouts.app')

@section('head')
@php
$filterLabel = '';
$genreSlug = $genreName ?? request('genre');
if ($genreSlug) { $genreLabel = $genres[$genreSlug] ?? $genreSlug; $filterLabel = "{$genreLabel} — "; }
if (request('label')) { $filterLabel = __('common.bands.label').': '.e(request('label')).' — '; }
if (request('origin')) { $filterLabel = e(request('origin')) . " — "; }
if (request('search')) { $filterLabel = __('common.bands.seo_search', ['query' => e(request('search'))]) . " — "; }
$seo = new \App\Values\SeoData(
    title: $filterLabel . (isset($genreName) ? $genreName . ' Bands' : __('common.bands.all')),
    description: isset($genreName) ? __('common.bands.seo_description') : __('common.bands.seo_description'),
    canonical: isset($genreName) ? route('genres.show', request()->route('slug') ?? '') : route('bands.index'),
);
@endphp
<x-seo-meta :seo="$seo" />
@endsection

@php $qs = http_build_query(request()->except(['sort','dir'])); @endphp

@section('content')
<div class="max-w-6xl mx-auto px-4">
@isset($genreName)
<nav class="breadcrumb mb-5">
    <a href="{{ route('home') }}">{{ __('common.home') }}</a><span>/</span>
    <a href="{{ route('bands.index') }}">{{ __('common.nav.bands') }}</a><span>/</span>
    <span>{{ $genreName }}</span>
</nav>
@endisset

<div class="flex items-center gap-4 mb-6">
    <h1 class="font-display text-xl sm:text-2xl font-bold text-surface-900 dark:text-ink-200">
        @isset($genreName){{ $genreName }}@else{{ __('common.nav.bands') }}@endisset
    </h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-ink-700"></span>
</div>

<!-- Filters -->
<form method="GET" class="flex flex-wrap gap-2 mb-4">
    <input name="search" value="{{ request('search') }}" placeholder="{{ __('common.bands.search') }}" aria-label="{{ __('common.bands.search_aria') }}" class="flex-1 min-w-[140px] input">
    <select name="genre" aria-label="{{ __('common.bands.filter_genre_aria') }}" class="select" style="max-width:150px">
        <option value="">{{ __('common.bands.all_genres') }}</option>
        @foreach($genres as $slug => $name)
        <option value="{{ $slug }}" {{ (request('genre') ?? '') === $slug ? 'selected' : '' }}>{{ $name }}</option>
        @endforeach
    </select>
    <input name="year" value="{{ request('year') }}" placeholder="{{ __('common.bands.year') }}" aria-label="{{ __('common.bands.filter_year_aria') }}" class="input" style="max-width:70px">
    <select name="origin" aria-label="{{ __('common.bands.filter_origin_aria') }}" class="select" style="max-width:130px">
        <option value="">{{ __('common.bands.all_origins') }}</option>
        @foreach($origins as $o)
        <option value="{{ $o }}" {{ request('origin') === $o ? 'selected' : '' }}>{{ $o }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn bg-black text-white border-black hover:bg-surface-800 text-xs">{{ __('common.filter') }}</button>
</form>

<!-- Sort + count -->
<div class="flex items-center justify-between mb-4 font-display text-xs text-surface-500">
    <span class="font-bold uppercase tracking-wider">{{ trans_choice('common.bands.count', $bands->total()) }}</span>
    <div class="flex gap-3">
        <a href="?sort=name&dir={{ $sort === 'name' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'name' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }}">
            {{ __('common.bands.sort_name') }} {{ $sort === 'name' ? ($dir === 'asc' ? '&uarr;' : '&darr;') : '' }}
        </a>
        <a href="?sort=formed_year&dir={{ $sort === 'formed_year' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'formed_year' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }}">
            {{ __('common.bands.sort_year') }} {{ $sort === 'formed_year' ? ($dir === 'asc' ? '&uarr;' : '&darr;') : '' }}
        </a>
        <a href="?sort=genre&dir={{ $sort === 'genre' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'genre' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }}">
            {{ __('common.bands.sort_genre') }} {{ $sort === 'genre' ? ($dir === 'asc' ? '&uarr;' : '&darr;') : '' }}
        </a>
    </div>
</div>

<!-- Newspaper grid -->
@forelse($bands as $band)
    <a href="{{ route('bands.show', $band) }}" class="flex items-start gap-4 py-3 px-3 -mx-3 border-b-2 border-surface-200 dark:border-ink-700 hover:bg-surface-100 dark:hover:bg-ink-800/50 transition-colors group">
        @if($band->photo)
        <img src="{{ img_url($band->photo) }}" alt="{{ $band->name }}" class="w-12 h-12 object-cover shrink-0 mt-0.5 border-2 border-surface-200 dark:border-ink-600" loading="lazy">
        @endif
        <div class="min-w-0 flex-1">
            <h2 class="font-display text-base font-bold text-surface-900 dark:text-ink-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 leading-tight">{{ $band->name }}</h2>
            <div class="text-xs text-surface-500 dark:text-ink-500 mt-0.5 leading-relaxed">
                @if($band->formed_year)
                <span>{{ $band->formed_year }}&ndash;{{ $band->dissolved_year ?? __('common.bands.present') }}</span><span class="mx-1">&middot;</span>
                @endif
                @foreach($band->genres->take(3) as $genre)
                <span>{{ $genre->name }}</span>@if(!$loop->last), @endif
                @endforeach
                @if($band->origin)
                <span class="mx-1">&middot;</span><span>{{ $band->origin }}</span>
                @endif
            </div>
            <div class="font-display text-[11px] font-bold text-surface-400 dark:text-ink-400 mt-0.5">
                <span>{{ $band->artists_count }} {{ $band->artists_count !== 1 ? __('common.bands.members') : __('common.bands.member') }}</span>
                @if($band->label)
                <span class="mx-1.5">&middot;</span><span>{{ $band->label->name }}</span>
                @endif
            </div>
        </div>
        <span class="text-surface-300 dark:text-ink-400 text-xs mt-1.5 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">&rarr;</span>
    </a>
@empty
    <div class="text-center py-16 border-2 border-surface-200 dark:border-ink-700">
        <p class="text-surface-500 mb-4">{{ __('common.bands.no_bands') }}</p>
        <a href="/admin/bands/create" class="btn bg-black text-white border-black hover:bg-surface-800">{{ __('common.bands.add_first') }}</a>
    </div>
@endforelse

<!-- Pagination -->
<div class="mt-6 flex items-center justify-between font-display text-xs text-surface-500">
    <span class="font-bold uppercase tracking-wider">{{ $bands->firstItem() }}&ndash;{{ $bands->lastItem() }} {{ __('common.pagination.of') }} {{ $bands->total() }}</span>
    {{ $bands->appends(request()->query())->links('pagination::tailwind') }}
</div>
</div>
@endsection
