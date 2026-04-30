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
    <div class="w-full h-full bg-gradient-to-br from-primary/20 via-secondary/20 to-accent/20"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent flex flex-col justify-end p-6 sm:p-10">
        <p class="text-white/70 text-xs font-medium uppercase tracking-wider mb-1">Featured Band</p>
        <h2 class="text-2xl sm:text-3xl font-bold text-white">{{ $hero->name }}</h2>
        <div class="flex flex-wrap gap-2 mt-2">
            @if($hero->formed_year)<span class="badge badge-sm bg-white/20 text-white border-none backdrop-blur-sm">{{ $hero->formed_year }}–{{ $hero->dissolved_year ?? 'present' }}</span>@endif
            @foreach($hero->genres->take(3) as $genre)<span class="badge badge-sm bg-white/15 text-white border-none backdrop-blur-sm">{{ $genre->name }}</span>@endforeach
        </div>
        <a href="{{ route('bands.show', $hero) }}" class="btn btn-sm bg-white text-base-content hover:bg-primary/10 border-none mt-3 self-start">Explore →</a>
    </div>
</div>
@endif

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-10">
    <div class="stat place-items-center bg-base-100 border border-base-300 rounded-lg">
        <div class="stat-value text-primary">{{ number_format($stats['bands']) }}</div>
        <div class="stat-desc">Bands</div>
    </div>
    <div class="stat place-items-center bg-base-100 border border-base-300 rounded-lg">
        <div class="stat-value text-secondary">{{ number_format($stats['artists']) }}</div>
        <div class="stat-desc">Artists</div>
    </div>
    <div class="stat place-items-center bg-base-100 border border-base-300 rounded-lg">
        <div class="stat-value text-accent">{{ number_format($stats['memberships']) }}</div>
        <div class="stat-desc">Memberships</div>
    </div>
    <div class="stat place-items-center bg-base-100 border border-base-300 rounded-lg">
        <div class="stat-value text-base-content">{{ number_format($stats['relationships']) }}</div>
        <div class="stat-desc">Connections</div>
    </div>
</div>

<div class="text-center mb-8">
    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-base-content mb-2">Band <span class="text-primary">Genealogy</span> Explorer</h1>
    <p class="text-base-content/50 max-w-xl mx-auto">Discover connections between bands through the artists who played in them.</p>
</div>

<div class="mb-12">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-bold text-base-content">Featured Bands</h2>
        <a href="{{ route('bands.index') }}" class="link link-hover text-sm text-primary">View all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
        @foreach($featuredBands as $band)
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
                            @if($band->origin)<p class="text-xs text-base-content/50 mt-1 truncate">{{ $band->origin }}</p>@endif
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>

<div>
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-bold text-base-content">Featured Artists</h2>
        <a href="{{ route('artists.index') }}" class="link link-hover text-sm text-secondary">View all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
        @foreach($featuredArtists as $artist)
            <a href="{{ route('artists.show', $artist) }}" class="group block">
                <div class="card card-compact bg-base-100 shadow-md hover:shadow-xl transition-shadow duration-200 h-full">
                    <div class="card-body flex-row gap-3 p-4">
                        @if($artist->photo)<img src="{{ Storage::url($artist->photo) }}" alt="" class="w-12 h-12 rounded-xl object-cover shrink-0" loading="lazy">@endif
                        <div class="min-w-0 flex-1">
                            <h3 class="card-title text-sm text-secondary group-hover:text-secondary-focus truncate">{{ $artist->name }}</h3>
                            @if($artist->origin)<p class="text-xs text-base-content/50 mt-1 truncate">{{ $artist->origin }}</p>@endif
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
