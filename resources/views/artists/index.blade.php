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
    <h1 class="font-display text-xl sm:text-2xl font-bold text-surface-900 dark:text-ink-200">Artists</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-ink-700"></span>
</div>

<!-- Search -->
<form method="GET" class="mb-4">
    <input name="search" value="{{ request('search') }}" placeholder="Search artists..." aria-label="Search artists" class="input" style="max-width:280px">
</form>

<!-- Sort + count -->
<div class="flex items-center justify-between mb-4 font-display text-xs text-surface-500">
    <span class="font-bold uppercase tracking-wider">{{ $artists->total() }} artist{{ $artists->total() !== 1 ? 's' : '' }}</span>
    <div class="flex gap-3">
        <a href="?sort=name&dir={{ $sort === 'name' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'name' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }}">
            Nome {{ $sort === 'name' ? ($dir === 'asc' ? '&uarr;' : '&darr;') : '' }}
        </a>
        <a href="?sort=origin&dir={{ $sort === 'origin' && $dir === 'asc' ? 'desc' : 'asc' }}&{{ $qs }}" class="hover:text-brand-600 {{ $sort === 'origin' ? 'font-bold text-brand-600 dark:text-brand-400' : '' }}">
            Origem {{ $sort === 'origin' ? ($dir === 'asc' ? '&uarr;' : '&darr;') : '' }}
        </a>
    </div>
</div>

<!-- Newspaper list -->
@forelse($artists as $artist)
    <a href="{{ route('artists.show', $artist) }}" class="flex items-start gap-4 py-3 px-3 -mx-3 border-b-2 border-surface-200 dark:border-ink-700 hover:bg-surface-100 dark:hover:bg-ink-800/50 transition-colors group">
        @if($artist->photo)
        <img src="{{ Storage::url($artist->photo) }}" alt="{{ $artist->name }}" class="w-12 h-12 object-cover shrink-0 mt-0.5 border-2 border-surface-200 dark:border-ink-600" loading="lazy">
        @endif
        <div class="min-w-0 flex-1">
            <h2 class="font-display text-base font-bold text-surface-900 dark:text-ink-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 leading-tight">{{ $artist->name }}</h2>
            <div class="text-xs text-surface-500 dark:text-ink-500 mt-0.5 leading-relaxed">
                @if($artist->origin)
                <span>{{ $artist->origin }}</span>
                @endif
                @if($artist->birth_date)
                <span class="mx-1">&middot;</span><span>{{ $artist->birth_date->format('Y') }}@if($artist->death_date)&ndash;{{ $artist->death_date->format('Y') }}@endif</span>
                @endif
            </div>
            <div class="font-display text-[11px] font-bold text-surface-400 dark:text-ink-400 mt-0.5">
                <span>{{ $artist->bands->count() }} banda{{ $artist->bands->count() !== 1 ? 's' : '' }}</span>
            </div>
        </div>
        <span class="text-surface-300 dark:text-ink-400 text-xs mt-1.5 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">&rarr;</span>
    </a>
@empty
    <div class="text-center py-16 border-2 border-surface-200 dark:border-ink-700">
        <p class="text-surface-500 mb-4">Nenhum artista encontrado.</p>
        <a href="/admin/artists/create" class="btn bg-black text-white border-black hover:bg-surface-800">Adicionar Primeiro Artista</a>
    </div>
@endforelse

<!-- Pagination -->
<div class="mt-6 flex items-center justify-between font-display text-xs text-surface-500">
    <span class="font-bold uppercase tracking-wider">{{ $artists->firstItem() }}&ndash;{{ $artists->lastItem() }} of {{ $artists->total() }}</span>
    {{ $artists->appends(request()->query())->links('pagination::tailwind') }}
</div>
</div>
@endsection
