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
<div class="flex items-center gap-3 mb-4">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Artists</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-surface-700"></span>
</div>

<div class="lg:flex lg:gap-8">
    <div class="flex-1 min-w-0">
        <form method="GET" class="mb-3">
            <input name="search" value="{{ request('search') }}" placeholder="Search artists..." class="w-full max-w-sm px-3 py-1.5 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 placeholder-surface-400 focus:outline-none focus:ring-2 focus:ring-accent-500">
        </form>

        <div class="flex items-center justify-between mb-3 text-xs text-surface-500 dark:text-surface-400">
            <span>{{ $artists->total() }} artist{{ $artists->total() !== 1 ? 's' : '' }}</span>
            <div class="flex gap-3">
                <a href="?sort=name&dir={{ $sort === 'name' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-accent-600 {{ $sort === 'name' ? 'font-semibold text-accent-600 dark:text-accent-400' : '' }}">Name {{ $sort === 'name' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
                <a href="?sort=origin&dir={{ $sort === 'origin' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-accent-600 {{ $sort === 'origin' ? 'font-semibold text-accent-600 dark:text-accent-400' : '' }}">Origin {{ $sort === 'origin' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
            @forelse($artists as $artist)
                <a href="{{ route('artists.show', $artist) }}" class="group block">
                    <div class="p-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg shadow-sm hover:shadow-md hover:border-accent-300 dark:hover:border-accent-700 transition-all duration-200 h-full flex gap-2.5">
                        @if($artist->photo)<img src="{{ Storage::url($artist->photo) }}" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0" loading="lazy">@endif
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-sm text-accent-600 dark:text-accent-400 group-hover:text-accent-700 truncate">{{ $artist->name }}</h3>
                            @if($artist->origin)<p class="text-xs text-surface-500 dark:text-surface-400 mt-1 truncate">{{ $artist->origin }}</p>@endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12"><p class="text-surface-500 mb-3">No artists found.</p><a href="/admin/artists/create" class="inline-flex px-4 py-1.5 bg-accent-500 text-white rounded-lg hover:bg-accent-600 text-sm font-medium">Add First Artist</a></div>
            @endforelse
        </div>

        <div class="mt-4 flex items-center justify-between text-sm text-surface-500">
            <span>{{ $artists->firstItem() }}–{{ $artists->lastItem() }} of {{ $artists->total() }}</span>
            {{ $artists->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

    <aside class="lg:w-64 mt-6 lg:mt-0 shrink-0 lg:sticky lg:top-20 self-start">
        <div class="bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-200 mb-3">Quick Stats</h3>
            <p class="text-xs text-surface-500">{{ $artists->total() }} artists cataloged</p>
        </div>
        <div class="mt-3"><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
@endsection
