@extends('layouts.app')

@section('head')
@php
$seo = new \App\Values\SeoData(
    title: 'LISTA',
    description: 'Explore band genealogies. Discover connections between bands through the artists who played in them.',
    canonical: url('/'),
    image: $heroBand?->photo ? Storage::url($heroBand->photo) : null,
    schema: json_encode(['@context'=>'https://schema.org','@type'=>'WebSite','name'=>'LISTA','url'=>url('/')], JSON_UNESCAPED_SLASHES),
);
@endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
@php $hero = $heroBand; @endphp
<style>
@keyframes draw-line { to { stroke-dashoffset: 0; } }
@keyframes fade-node { to { opacity: 1; } }
.hero-line { animation: draw-line 2.5s ease-out forwards; }
.hero-line:nth-child(2) { animation-delay: .6s; }
.hero-line:nth-child(3) { animation-delay: 1.2s; }
.hero-node { animation: fade-node .4s ease-out forwards; }
.hero-node:nth-child(4) { animation-delay: 1.6s; }
.hero-node:nth-child(5) { animation-delay: 1.9s; }
.hero-node:nth-child(6) { animation-delay: 2.1s; }
.hero-node:nth-child(7) { animation-delay: 2s; }
.hero-node:nth-child(8) { animation-delay: 2.3s; }
</style>
@if($hero)
<div class="relative -mx-4 -mt-8 mb-10 overflow-hidden rounded-none sm:rounded-xl" style="aspect-ratio:1200/300">
    <svg class="absolute inset-0 w-full h-full pointer-events-none select-none text-white dark:text-white/60" viewBox="0 0 1200 300" preserveAspectRatio="none" style="opacity:.07">
        <path d="M30,260 C100,80 200,220 350,140 C420,100 480,170 540,90" stroke="currentColor" stroke-width="2.5" fill="none" class="hero-line" stroke-dasharray="900" stroke-dashoffset="900"/>
        <path d="M350,140 Q420,50 530,70 T700,110" stroke="currentColor" stroke-width="1.5" fill="none" class="hero-line" stroke-dasharray="700" stroke-dashoffset="700"/>
        <path d="M150,280 Q400,180 750,250 T1150,180" stroke="currentColor" stroke-width="2" fill="none" class="hero-line" stroke-dasharray="1100" stroke-dashoffset="1100"/>
        <circle cx="30" cy="260" r="4.5" fill="currentColor" class="hero-node" opacity="0"/>
        <circle cx="350" cy="140" r="3.5" fill="currentColor" class="hero-node" opacity="0"/>
        <circle cx="540" cy="90" r="5" fill="currentColor" class="hero-node" opacity="0"/>
        <circle cx="150" cy="280" r="3" fill="currentColor" class="hero-node" opacity="0"/>
        <circle cx="1150" cy="180" r="4.5" fill="currentColor" class="hero-node" opacity="0"/>
    </svg>
    @if($hero->hero_image)
    <img src="{{ Storage::url($hero->hero_image) }}" alt="" class="w-full h-full object-cover" fetchpriority="high" decoding="async">
    @elseif($hero->photo)
    <img src="{{ Storage::url($hero->photo) }}" alt="" class="w-full h-full object-cover" fetchpriority="high" decoding="async">
    @else
    <div class="w-full h-full bg-gradient-to-br from-brand-500/20 via-accent-500/20 to-warm-500/20 dark:from-brand-900/30 dark:via-accent-900/30 dark:to-warm-900/30"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent flex flex-col justify-end p-6 sm:p-10">
        <p class="text-white/70 text-xs font-medium uppercase tracking-wider mb-1">Featured Band</p>
        <h2 class="text-2xl sm:text-3xl font-bold text-white">{{ $hero->name }}</h2>
        <div class="flex flex-wrap gap-2 mt-2">
            @if($hero->formed_year)<span class="text-xs px-2 py-0.5 rounded-full bg-white/20 text-white font-medium backdrop-blur-sm">{{ $hero->formed_year }}–{{ $hero->dissolved_year ?? 'present' }}</span>@endif
            @foreach($hero->genres->take(3) as $genre)<span class="text-xs px-2 py-0.5 rounded-full bg-white/15 text-white backdrop-blur-sm">{{ $genre->name }}</span>@endforeach
        </div>
        <a href="{{ route('bands.show', $hero) }}" class="mt-3 inline-flex items-center gap-1 self-start px-4 py-1.5 text-sm font-medium bg-white text-surface-900 rounded-lg hover:bg-brand-50 transition-colors">Explore →</a>
    </div>
</div>
@endif

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-10">
    <div class="p-4 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg text-center">
        <p class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ number_format($stats['bands']) }}</p>
        <p class="text-xs text-surface-500 mt-0.5">Bands</p>
    </div>
    <div class="p-4 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg text-center">
        <p class="text-2xl font-bold text-accent-600 dark:text-accent-400">{{ number_format($stats['artists']) }}</p>
        <p class="text-xs text-surface-500 mt-0.5">Artists</p>
    </div>
    <div class="p-4 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg text-center">
        <p class="text-2xl font-bold text-warm-600 dark:text-warm-400">{{ number_format($stats['memberships']) }}</p>
        <p class="text-xs text-surface-500 mt-0.5">Memberships</p>
    </div>
    <div class="p-4 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg text-center">
        <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ number_format($stats['relationships']) }}</p>
        <p class="text-xs text-surface-500 mt-0.5">Connections</p>
    </div>
</div>

<div class="text-center mb-8">
    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-surface-900 dark:text-white mb-2">Band <span class="text-brand-500">Genealogy</span> Explorer</h1>
    <p class="text-surface-500 dark:text-surface-400 max-w-xl mx-auto">Discover connections between bands through the artists who played in them.</p>
</div>

<div class="mb-12">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-bold text-surface-900 dark:text-white">Featured Bands</h2>
        <a href="{{ route('bands.index') }}" class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400">View all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
        @foreach($featuredBands as $band)
            <a href="{{ route('bands.show', $band) }}" class="group block">
                <div class="p-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg shadow-sm hover:shadow-md hover:border-brand-300 dark:hover:border-brand-700 transition-all duration-200 h-full flex gap-2.5">
                    @if($band->photo)<img src="{{ Storage::url($band->photo) }}" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0" loading="lazy">@endif
                    <div class="min-w-0 flex-1">
                        <h3 class="font-semibold text-sm text-brand-600 dark:text-brand-400 group-hover:text-brand-700 dark:group-hover:text-brand-300 truncate">{{ $band->name }}</h3>
                        <div class="mt-1.5 flex flex-wrap gap-1">
                            @if($band->formed_year)<span class="text-[11px] px-1.5 py-0.5 rounded bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 font-medium">{{ $band->formed_year }}–{{ $band->dissolved_year ?? 'present' }}</span>@endif
                            @foreach($band->genres->take(2) as $genre)<span class="text-[11px] px-1.5 py-0.5 rounded bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-300 truncate">{{ $genre->name }}</span>@endforeach
                        </div>
                        @if($band->origin)<p class="text-[11px] text-surface-400 mt-1 truncate">{{ $band->origin }}</p>@endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>

<div>
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-bold text-surface-900 dark:text-white">Featured Artists</h2>
        <a href="{{ route('artists.index') }}" class="text-sm text-accent-600 hover:text-accent-700 dark:text-accent-400">View all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
        @foreach($featuredArtists as $artist)
            <a href="{{ route('artists.show', $artist) }}" class="group block">
                <div class="p-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg shadow-sm hover:shadow-md hover:border-accent-300 dark:hover:border-accent-700 transition-all duration-200 h-full flex gap-2.5">
                    @if($artist->photo)<img src="{{ Storage::url($artist->photo) }}" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0" loading="lazy">@endif
                    <div class="min-w-0 flex-1">
                        <h3 class="font-semibold text-sm text-accent-600 dark:text-accent-400 group-hover:text-accent-700 dark:group-hover:text-accent-300 truncate">{{ $artist->name }}</h3>
                        @if($artist->origin)<p class="text-xs text-surface-500 dark:text-surface-400 mt-1 truncate">{{ $artist->origin }}</p>@endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
