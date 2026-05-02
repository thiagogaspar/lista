@extends('layouts.app')

@section('head')
@php
$seo = new \App\Values\SeoData(
    title: request('search') ? 'Search: ' . e(request('search')) . ' — All Artists' : 'All Artists',
    description: 'Browse all artists in the directory.',
    canonical: route('artists.index'),
);
@endphp
<x-seo-meta :seo="$seo" />
@endsection

@php $qs = http_build_query(request()->except(['sort','dir'])); @endphp

@section('content')
<div class="max-w-6xl mx-auto px-4">
<div class="flex items-center gap-4 mb-6">
    <h1 class="text-xl sm:text-2xl font-bold text-surface-900 dark:text-ink-200">Artists</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-ink-700"></span>
</div>

<!-- Search -->
<form method="GET" class="mb-4">
    <input name="search" value="{{ request('search') }}" placeholder="Search artists..." aria-label="Search artists" class="input" style="max-width:280px">
</form>

<!-- Sort + count -->
<div class="flex items-center justify-between mb-4 text-xs text-surface-500">
    <span class="font-semibold uppercase tracking-wider">{{ $artists->total() }} artist{{ $artists->total() !== 1 ? 's' : '' }}</span>
    <div class="flex gap-3">
        <a href="?sort=name&dir={{ $sort === 'name' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'name' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }}">
            Name {{ $sort === 'name' ? ($dir === 'asc' ? '&uarr;' : '&darr;') : '' }}
        </a>
        <a href="?sort=origin&dir={{ $sort === 'origin' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'origin' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }}">
            Origin {{ $sort === 'origin' ? ($dir === 'asc' ? '&uarr;' : '&darr;') : '' }}
        </a>
    </div>
</div>

<!-- Bandcamp-style card grid -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    @forelse($artists as $artist)
        <a href="{{ route('artists.show', $artist) }}" class="block group">
            <div class="card-bandcamp">
                <div class="aspect-square overflow-hidden">
                    @if($artist->photo)
                    <img src="{{ Storage::url($artist->photo) }}" alt="{{ $artist->name }}" class="w-full h-full object-cover" loading="lazy">
                    @else
                    <div class="w-full h-full bg-surface-100 dark:bg-ink-900 flex items-center justify-center text-surface-300 dark:text-ink-600">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    @endif
                    <div class="card-bandcamp-overlay">
                        <span class="text-white text-2xl font-light opacity-0 group-hover:opacity-100">&rarr;</span>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <h3 class="text-sm font-bold text-surface-900 dark:text-ink-100 truncate">{{ $artist->name }}</h3>
                @if($artist->origin)
                <p class="text-[9px] text-surface-400 dark:text-ink-600 mt-0.5 uppercase tracking-wider font-semibold">{{ $artist->origin }}</p>
                @endif
            </div>
        </a>
    @empty
        <div class="col-span-full text-center py-16">
            <p class="text-surface-500 mb-4">No artists found.</p>
            <a href="/admin/artists/create" class="btn btn-brand">Add First Artist</a>
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-6 flex items-center justify-between text-xs text-surface-500">
    <span class="uppercase tracking-wider font-semibold">{{ $artists->firstItem() }}&ndash;{{ $artists->lastItem() }} of {{ $artists->total() }}</span>
    {{ $artists->appends(request()->query())->links('pagination::tailwind') }}
</div>
</div>
@endsection
