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
    <h1 class="font-display text-xl sm:text-2xl font-bold text-surface-900 dark:text-ink-200">Albums</h1>
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
    <button type="submit" class="btn bg-black text-white border-black hover:bg-surface-800 text-xs">Filter</button>
</form>

<div class="flex items-center justify-between mb-4 font-display text-xs text-surface-500">
    <span class="font-bold uppercase tracking-wider">{{ $albums->total() }} album{{ $albums->total() !== 1 ? 's' : '' }}</span>
</div>

<!-- Album grid — capas com borda 2px -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($albums as $album)
    <a href="{{ route('albums.show', $album) }}" class="group">
        <div class="border-2 border-surface-200 dark:border-ink-700 bg-white dark:bg-ink-800 hover:border-brand-500 dark:hover:border-brand-400 transition-colors">
            @if($album->cover_art)
            <img src="{{ Storage::url($album->cover_art) }}" alt="{{ $album->title }} cover" class="w-full aspect-square object-cover" loading="lazy">
            @else
            <div class="w-full aspect-square bg-surface-100 dark:bg-ink-900 flex items-center justify-center text-surface-300 dark:text-ink-400">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.5" d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
            </div>
            @endif
        </div>
        <div class="mt-2">
            <h2 class="font-display text-sm font-bold text-surface-900 dark:text-ink-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 truncate leading-tight">{{ $album->title }}</h2>
            <div class="font-display text-[11px] font-bold text-surface-500 dark:text-ink-500 mt-0.5">
                <span>{{ $album->band->name }}</span>
                @if($album->release_year)
                <span class="mx-1">&middot;</span><span>{{ $album->release_year }}</span>
                @endif
            </div>
        </div>
    </a>
    @empty
    <div class="col-span-full text-center py-16 border-2 border-surface-200 dark:border-ink-700"><p class="text-surface-500">Nenhum álbum encontrado.</p></div>
    @endforelse
</div>

<div class="mt-8 flex items-center justify-between font-display text-xs text-surface-500">
    <span class="font-bold uppercase tracking-wider">{{ $albums->firstItem() }}&ndash;{{ $albums->lastItem() }} of {{ $albums->total() }}</span>
    {{ $albums->appends(request()->query())->links('pagination::tailwind') }}
</div>
</div>
@endsection
