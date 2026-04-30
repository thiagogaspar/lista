@extends('layouts.app')

@section('head')
@php
$seo = new \App\Values\SeoData(
    title: 'LISTA — Band Genealogy',
    description: 'Explore connections between bands and artists. A community-built directory of local original music.',
    canonical: url('/'),
    image: $heroBand?->photo ? Storage::url($heroBand->photo) : null,
    schema: json_encode(['@context'=>'https://schema.org','@type'=>'WebSite','name'=>'LISTA','url'=>url('/')], JSON_UNESCAPED_SLASHES),
);
@endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
@php $hero = $heroBand; @endphp

@if($hero)
<section class="relative -mx-4 -mt-6 mb-12 overflow-hidden bg-ink dark:bg-ink-900" style="aspect-ratio:16/6; max-height:70vh;">
    @if($hero->hero_image)
    <img src="{{ Storage::url($hero->hero_image) }}" alt="{{ $hero->name }}" class="absolute inset-0 w-full h-full object-cover opacity-40 dark:opacity-30" fetchpriority="high" decoding="async">
    @elseif($hero->photo)
    <img src="{{ Storage::url($hero->photo) }}" alt="{{ $hero->name }}" class="absolute inset-0 w-full h-full object-cover opacity-40 dark:opacity-30" fetchpriority="high" decoding="async">
    @endif

    <!-- Animated genealogy lines -->
    <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 1200 400" preserveAspectRatio="none">
        <defs>
            <style>
                .hl1 { animation: draw-line 2s ease-out forwards; stroke-dasharray: 900; stroke-dashoffset: 900; }
                .hl2 { animation: draw-line 2.2s ease-out forwards .4s; stroke-dasharray: 800; stroke-dashoffset: 800; }
                .hl3 { animation: draw-line 2.5s ease-out forwards .8s; stroke-dasharray: 1200; stroke-dashoffset: 1200; }
                .hn1 { animation: fade-node .5s ease-out forwards 1.8s; opacity: 0; }
                .hn2 { animation: fade-node .5s ease-out forwards 2s; opacity: 0; }
                .hn3 { animation: fade-node .5s ease-out forwards 2.1s; opacity: 0; }
                .hn4 { animation: fade-node .5s ease-out forwards 2.05s; opacity: 0; }
                .hn5 { animation: fade-node .5s ease-out forwards 2.15s; opacity: 0; }
                .hn6 { animation: fade-node .5s ease-out forwards 2.25s; opacity: 0; }
            </style>
        </defs>
        <path d="M40,300 C150,80 280,200 400,120 C480,72 530,160 620,90 C680,44 750,140 830,100" stroke="currentColor" stroke-width="2" fill="none" class="hl1" style="color:var(--color-brand-500);"/>
        <path d="M420,120 C500,80 550,40 650,60 C700,72 760,110 850,70" stroke="currentColor" stroke-width="1.5" fill="none" class="hl2" style="color:var(--color-accent-500);"/>
        <path d="M120,340 C250,200 600,280 900,200 C1000,170 1100,200 1160,160" stroke="currentColor" stroke-width="1.8" fill="none" class="hl3" style="color:var(--color-warm-500);"/>
        <circle cx="40" cy="300" r="5" fill="currentColor" class="hn1" style="color:var(--color-brand-500);"/>
        <circle cx="420" cy="120" r="4" fill="currentColor" class="hn2" style="color:var(--color-brand-500);"/>
        <circle cx="650" cy="60" r="5" fill="currentColor" class="hn3" style="color:var(--color-accent-500);"/>
        <circle cx="120" cy="340" r="3.5" fill="currentColor" class="hn4" style="color:var(--color-warm-500);"/>
        <circle cx="900" cy="200" r="4" fill="currentColor" class="hn5" style="color:var(--color-warm-500);"/>
        <circle cx="1160" cy="160" r="5" fill="currentColor" class="hn6" style="color:var(--color-brand-500);"/>
    </svg>

    <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/40 to-transparent flex flex-col justify-end p-6 sm:p-12 anim-reveal">
        <div>
            <p class="text-brand-400 text-[10px] font-bold uppercase tracking-[0.2em] mb-3">Featured Band</p>
            <h2 class="font-display text-3xl sm:text-5xl md:text-6xl font-bold text-white leading-none tracking-tight">{{ $hero->name }}</h2>
            <div class="flex flex-wrap gap-2 mt-4">
                @if($hero->formed_year)<span class="badge badge-brand">{{ $hero->formed_year }}&ndash;{{ $hero->dissolved_year ?? 'present' }}</span>@endif
                @foreach($hero->genres->take(3) as $genre)<span class="badge badge-surface">{{ $genre->name }}</span>@endforeach
            </div>
            <div class="mt-6">
                <a href="{{ route('bands.show', $hero) }}" class="btn btn-brand">Explore {{ $hero->name }} &rarr;</a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Stats -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-px bg-surface-200 dark:bg-ink-700 mb-12">
    <div class="stat bg-white dark:bg-ink-800">
        <div class="stat-value text-brand-600 dark:text-brand-500">{{ number_format($stats['bands']) }}</div>
        <div class="stat-desc">Bands</div>
    </div>
    <div class="stat bg-white dark:bg-ink-800">
        <div class="stat-value text-accent-600 dark:text-accent-500">{{ number_format($stats['artists']) }}</div>
        <div class="stat-desc">Artists</div>
    </div>
    <div class="stat bg-white dark:bg-ink-800">
        <div class="stat-value text-warm-600 dark:text-warm-500">{{ number_format($stats['memberships']) }}</div>
        <div class="stat-desc">Links</div>
    </div>
    <div class="stat bg-white dark:bg-ink-800">
        <div class="stat-value text-surface-700 dark:text-ink-200">{{ number_format($stats['relationships']) }}</div>
        <div class="stat-desc">Connections</div>
    </div>
</div>

<!-- Hero text -->
<div class="max-w-6xl mx-auto px-4">
<div class="mb-12 text-center max-w-2xl mx-auto">
    <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold text-surface-900 dark:text-ink-200 leading-tight tracking-tight">
        Local <span class="text-brand-600 dark:text-brand-500">Music</span> Genealogy
    </h1>
    <p class="mt-4 text-surface-500 dark:text-surface-400 text-base leading-relaxed">
        A community-built directory mapping the connections between bands and artists.
        Who played with whom, when, and where — the living history of your local scene.
    </p>
    <div class="flex items-center justify-center gap-3 mt-6">
        <a href="{{ route('bands.index') }}" class="btn btn-brand">Browse Bands</a>
        <a href="{{ route('genealogy') }}" class="btn btn-ghost">View Graph</a>
    </div>
</div>

<!-- Featured Bands -->
<section class="mb-12">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="font-display text-xl font-bold text-surface-900 dark:text-ink-200">Featured Bands</h2>
            <p class="text-xs text-surface-400 uppercase tracking-widest mt-0.5">The scene's heavy hitters</p>
        </div>
        <a href="{{ route('bands.index') }}" class="link text-xs uppercase tracking-wider font-semibold">View all &rarr;</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
        @foreach($featuredBands as $band)
            <a href="{{ route('bands.show', $band) }}" class="block group">
                <div class="card card-hover h-full bg-white dark:bg-ink-800">
                    <div class="p-4 flex gap-3">
                        @if($band->photo)
                        <img src="{{ Storage::url($band->photo) }}" alt="" class="w-14 h-14 object-cover shrink-0" loading="lazy" style="border:1px solid var(--color-surface-200)">
                        @endif
                        <div class="min-w-0 flex-1">
                            <h3 class="font-display font-bold text-sm text-brand-600 dark:text-brand-400 group-hover:text-brand-700 dark:group-hover:text-brand-300 truncate">{{ $band->name }}</h3>
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                @if($band->formed_year)
                                <span class="badge badge-brand">{{ $band->formed_year }}&ndash;{{ $band->dissolved_year ?? 'present' }}</span>
                                @endif
                                @foreach($band->genres->take(2) as $genre)
                                <span class="badge badge-surface">{{ $genre->name }}</span>
                                @endforeach
                            </div>
                            @if($band->origin)
                            <p class="text-[11px] text-surface-400 mt-1.5 truncate uppercase tracking-wider">{{ $band->origin }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>

<!-- Featured Artists -->
<section class="mb-12">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="font-display text-xl font-bold text-surface-900 dark:text-ink-200">Featured Artists</h2>
            <p class="text-xs text-surface-400 uppercase tracking-widest mt-0.5">The people behind the sound</p>
        </div>
        <a href="{{ route('artists.index') }}" class="link text-xs uppercase tracking-wider font-semibold">View all &rarr;</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
        @foreach($featuredArtists as $artist)
            <a href="{{ route('artists.show', $artist) }}" class="block group">
                <div class="card card-hover h-full bg-white dark:bg-ink-800">
                    <div class="p-4 flex gap-3">
                        @if($artist->photo)
                        <img src="{{ Storage::url($artist->photo) }}" alt="" class="w-14 h-14 object-cover shrink-0" loading="lazy" style="border:1px solid var(--color-surface-200)">
                        @endif
                        <div class="min-w-0 flex-1">
                            <h3 class="font-display font-bold text-sm text-accent-600 dark:text-accent-400 group-hover:text-accent-700 dark:group-hover:text-accent-300 truncate">{{ $artist->name }}</h3>
                            @if($artist->origin)
                            <p class="text-[11px] text-surface-400 mt-1.5 truncate uppercase tracking-wider">{{ $artist->origin }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>

<!-- CTA -->
<section class="border-t border-surface-200 dark:border-ink-700 pt-10">
    <div class="max-w-xl mx-auto text-center">
        <h2 class="font-display text-2xl font-bold text-surface-900 dark:text-ink-200 mb-3">Help build the directory</h2>
        <p class="text-surface-500 dark:text-surface-400 text-sm leading-relaxed mb-5">Know a band that's missing? An artist who played in more groups than people remember? Contribute and help map the local scene.</p>
        <a href="/admin" class="btn btn-brand">Contribute &rarr;</a>
        <p class="text-[10px] text-surface-400 mt-4 uppercase tracking-wider">Or <a href="{{ route('register') }}" class="link">create an account</a></p>
    </div>
</section>
</div>
@endsection
