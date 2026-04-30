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
<div class="max-w-7xl mx-auto px-4">
<div class="flex items-center gap-4 mb-6">
    <h1 class="font-display text-2xl sm:text-3xl font-bold text-surface-900 dark:text-ink-200">Artists</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-ink-700"></span>
</div>

<div class="lg:flex lg:gap-8">
    <div class="flex-1 min-w-0">
        <form method="GET" class="mb-4">
            <input name="search" value="{{ request('search') }}" placeholder="Search artists..." class="input" style="max-width:320px">
        </form>

        <div class="flex items-center justify-between mb-4 text-xs text-surface-500 dark:text-surface-400">
            <span class="font-medium uppercase tracking-wider">{{ $artists->total() }} artist{{ $artists->total() !== 1 ? 's' : '' }}</span>
            <div class="flex gap-4">
                <a href="?sort=name&dir={{ $sort === 'name' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-accent-600 {{ $sort === 'name' ? 'font-bold text-accent-600 dark:text-accent-400' : '' }} uppercase tracking-wider">
                    Name {{ $sort === 'name' ? ($dir === 'asc' ? '&#8593;' : '&#8595;') : '' }}
                </a>
                <a href="?sort=origin&dir={{ $sort === 'origin' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-accent-600 {{ $sort === 'origin' ? 'font-bold text-accent-600 dark:text-accent-400' : '' }} uppercase tracking-wider">
                    Origin {{ $sort === 'origin' ? ($dir === 'asc' ? '&#8593;' : '&#8595;') : '' }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @forelse($artists as $artist)
                <a href="{{ route('artists.show', $artist) }}" class="block group">
                    <div class="card card-hover h-full bg-white dark:bg-ink-800 p-3 flex gap-2.5">
                        @if($artist->photo)
                        <img src="{{ Storage::url($artist->photo) }}" alt="" class="w-12 h-12 object-cover shrink-0" loading="lazy" style="border:1px solid var(--color-surface-200)">
                        @endif
                        <div class="min-w-0 flex-1">
                            <h3 class="font-display font-bold text-xs text-accent-600 dark:text-accent-400 group-hover:text-accent-700 dark:group-hover:text-accent-300 truncate">{{ $artist->name }}</h3>
                            @if($artist->origin)
                            <p class="text-[10px] text-surface-400 mt-1.5 truncate uppercase tracking-wider">{{ $artist->origin }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-16">
                    <p class="text-surface-500 mb-4">No artists found.</p>
                    <a href="/admin/artists/create" class="btn btn-accent">Add First Artist</a>
                </div>
            @endforelse
        </div>

        <div class="mt-6 flex items-center justify-between text-xs text-surface-500">
            <span class="uppercase tracking-wider">{{ $artists->firstItem() }}&ndash;{{ $artists->lastItem() }} of {{ $artists->total() }}</span>
            {{ $artists->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

    <aside class="lg:w-64 mt-6 lg:mt-0 shrink-0 lg:sticky lg:top-16 self-start space-y-4">
        <div class="card bg-white dark:bg-ink-800 p-4">
            <h3 class="font-display text-sm font-bold mb-3 text-surface-700 dark:text-ink-200">Stats</h3>
            <p class="text-xs text-surface-500">{{ $artists->total() }} artists catalogued</p>
        </div>
        <div><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
</div>
@endsection
