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

$geo = match ($heroBand?->genres->first()?->slug) {
    'alternative-rock', 'indie-rock', 'dream-pop', 'shoegaze' => 'sage',
    'grunge', 'punk-rock', 'post-grunge' => 'ocher',
    default => 'terracotta',
};
@endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
@php $hero = $heroBand; @endphp

@if($hero)
<section class="relative -mx-4 -mt-6 mb-12 overflow-hidden bg-ink" style="aspect-ratio:16/5; max-height:60vh;">
    @if($hero->hero_image)
    <img src="{{ Storage::url($hero->hero_image) }}" alt="{{ $hero->name }}" class="absolute inset-0 w-full h-full object-cover opacity-25" fetchpriority="high" decoding="async">
    @elseif($hero->photo)
    <img src="{{ Storage::url($hero->photo) }}" alt="{{ $hero->name }}" class="absolute inset-0 w-full h-full object-cover opacity-25" fetchpriority="high" decoding="async">
    @endif

    <!-- Japandi geometric shapes -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        @if($geo === 'terracotta')
        <div class="absolute w-[50vmin] h-[50vmin] rounded-full bg-brand-500/15 -top-[15%] -right-[5%]"></div>
        <div class="absolute w-[55%] h-[10%] bg-warm-500/10 bottom-[20%] -left-[8%]"></div>
        <div class="absolute w-[8vmin] h-[8vmin] bg-accent-500/20 bottom-[40%] left-[20%]"></div>
        @elseif($geo === 'sage')
        <div class="absolute w-[40vmin] h-[40vmin] rounded-full bg-accent-500/15 top-[5%] right-[15%]"></div>
        <div class="absolute w-[35%] h-[14%] bg-brand-500/15 bottom-0 right-0"></div>
        <div class="absolute w-[12vmin] h-[12vmin] rounded-full bg-warm-500/15 bottom-[30%] left-[10%]"></div>
        @else
        <div class="absolute w-[35vmin] h-[35vmin] bg-warm-500/15 -top-[8%] -left-[8%] rotate-12"></div>
        <div class="absolute w-[25vmin] h-[25vmin] rounded-full bg-accent-500/15 bottom-[8%] right-[12%]"></div>
        <div class="absolute w-[20vmin] h-[2vmin] bg-brand-500/20 top-[40%] left-[50%] -translate-x-1/2"></div>
        @endif
    </div>

    <div class="absolute inset-0 bg-gradient-to-r from-ink/80 via-ink/50 to-ink/20"></div>

    <div class="relative z-10 flex flex-col justify-end h-full p-6 sm:p-12">
        <div>
            <p class="text-xs font-bold tracking-[0.15em] uppercase mb-2" style="color:{{ $geo === 'sage' ? 'var(--color-accent-400)' : ($geo === 'ocher' ? 'var(--color-warm-400)' : 'var(--color-brand-400)') }}">{{ __('common.home.featured_band') }}</p>
            <h2 class="text-4xl sm:text-6xl md:text-7xl font-black text-white leading-none tracking-tight">{{ $hero->name }}</h2>
            <div class="flex flex-wrap gap-2 mt-4">
                @if($hero->formed_year)<span class="badge text-white/70" style="border-color:rgba(255,255,255,0.25)">{{ $hero->formed_year }}&ndash;{{ $hero->dissolved_year ?? __('common.bands.present') }}</span>@endif
                @foreach($hero->genres->take(3) as $genre)<span class="badge text-white/50" style="border-color:rgba(255,255,255,0.15)">{{ $genre->name }}</span>@endforeach
            </div>
            <div class="mt-6">
                <a href="{{ route('bands.show', $hero) }}" class="btn btn-brand text-sm tracking-wider">{{ __('common.home.explore', ['name' => $hero->name]) }} &rarr;</a>
            </div>
        </div>
    </div>
</section>
@endif

<div class="flex gap-6 justify-center mb-12 mx-4 max-w-6xl lg:mx-auto">
    <div class="text-center">
        <div class="text-2xl font-black text-brand-600 dark:text-brand-500">{{ number_format($stats['bands']) }}</div>
        <div class="text-[10px] font-semibold uppercase tracking-widest text-surface-400 dark:text-ink-500 mt-0.5">{{ __('common.home.bands') }}</div>
    </div>
    <div class="text-center">
        <div class="text-2xl font-black text-accent-500 dark:text-accent-400">{{ number_format($stats['artists']) }}</div>
        <div class="text-[10px] font-semibold uppercase tracking-widest text-surface-400 dark:text-ink-500 mt-0.5">{{ __('common.home.artists') }}</div>
    </div>
    <div class="text-center">
        <div class="text-2xl font-black text-warm-500 dark:text-warm-400">{{ number_format($stats['memberships']) }}</div>
        <div class="text-[10px] font-semibold uppercase tracking-widest text-surface-400 dark:text-ink-500 mt-0.5">{{ __('common.home.links') }}</div>
    </div>
    <div class="text-center">
        <div class="text-2xl font-black text-surface-700 dark:text-ink-200">{{ number_format($stats['relationships']) }}</div>
        <div class="text-[10px] font-semibold uppercase tracking-widest text-surface-400 dark:text-ink-500 mt-0.5">{{ __('common.home.connections') }}</div>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4">
<div class="mb-12 max-w-2xl mx-auto text-center">
    <h1 class="text-4xl sm:text-5xl md:text-6xl font-black text-surface-900 dark:text-ink-200 leading-none tracking-tight">
        {{ __('common.home.local') }} <span class="text-brand-600 dark:text-brand-500">{{ __('common.home.music') }}</span> {{ __('common.home.genealogy') }}
    </h1>
    <p class="mt-4 text-surface-500 dark:text-ink-400 text-base leading-relaxed max-w-lg mx-auto">
        {{ __('common.home.subtitle') }}
    </p>
    <div class="flex items-center justify-center gap-4 mt-6">
        <a href="{{ route('bands.index') }}" class="btn btn-brand">{{ __('common.home.browse_bands') }}</a>
        <a href="{{ route('genealogy') }}" class="btn btn-ghost">{{ __('common.home.view_graph') }}</a>
    </div>
</div>

<!-- Featured Bands -->
<section class="mb-14">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-surface-900 dark:text-ink-200">{{ __('common.home.featured_bands') }}</h2>
        <a href="{{ route('bands.index') }}" class="text-xs font-semibold text-surface-400 hover:text-brand-600 dark:hover:text-brand-400">{{ __('common.view_all') }} &rarr;</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($featuredBands as $band)
            <a href="{{ route('bands.show', $band) }}" class="block group">
                <div class="card-bandcamp">
                    <div class="aspect-square overflow-hidden">
                        @if($band->photo)
                        <img src="{{ Storage::url($band->photo) }}" alt="{{ $band->name }}" class="w-full h-full object-cover" loading="lazy">
                        @else
                        <div class="w-full h-full bg-surface-100 dark:bg-ink-900 flex items-center justify-center text-surface-300 dark:text-ink-600">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.5" d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        </div>
                        @endif
                        <div class="card-bandcamp-overlay">
                            <span class="text-white text-2xl font-light opacity-0 group-hover:opacity-100 transition-opacity">&rarr;</span>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-sm font-bold text-surface-900 dark:text-ink-100 truncate">{{ $band->name }}</h3>
                    <div class="flex flex-wrap gap-1 mt-0.5">
                        @if($band->formed_year)
                        <span class="text-[10px] font-semibold text-surface-400 dark:text-ink-500">{{ $band->formed_year }}&ndash;{{ $band->dissolved_year ?? 'present' }}</span>
                        @endif
                        @foreach($band->genres->take(2) as $genre)
                        <span class="text-[10px] text-surface-400 dark:text-ink-500">{{ $genre->name }}</span>
                        @endforeach
                    </div>
                    @if($band->origin)
                    <p class="text-[9px] text-surface-400 dark:text-ink-600 mt-0.5 uppercase tracking-wider font-semibold">{{ $band->origin }}</p>
                    @endif
                </div>
            </a>
        @empty
            <p class="col-span-full text-sm text-surface-400 text-center py-8">{{ __('common.home.no_featured_bands') }}</p>
        @endforelse
    </div>
</section>

<!-- Featured Artists -->
<section class="mb-14">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-surface-900 dark:text-ink-200">{{ __('common.home.featured_artists') }}</h2>
        <a href="{{ route('artists.index') }}" class="text-xs font-semibold text-surface-400 hover:text-brand-600 dark:hover:text-brand-400">{{ __('common.view_all') }} &rarr;</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($featuredArtists as $artist)
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
                            <span class="text-white text-2xl font-light opacity-0 group-hover:opacity-100 transition-opacity">&rarr;</span>
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
            <p class="col-span-full text-sm text-surface-400 text-center py-8">{{ __('common.home.no_featured_artists') }}</p>
        @endforelse
    </div>
</section>

<!-- Featured Labels -->
<section class="mb-14">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-surface-900 dark:text-ink-200">{{ __('common.home.labels') }}</h2>
        <a href="{{ route('labels.index') }}" class="text-xs font-semibold text-surface-400 hover:text-brand-600 dark:hover:text-brand-400">{{ __('common.view_all') }} &rarr;</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @forelse($featuredLabels as $label)
        <a href="{{ route('labels.show', $label) }}" class="block group">
            <div class="card p-5 text-center">
                @if($label->logo)
                <img src="{{ Storage::url($label->logo) }}" alt="{{ $label->name }} logo" class="h-16 mx-auto object-contain mb-3" loading="lazy">
                @else
                <div class="w-16 h-16 mx-auto bg-surface-100 dark:bg-ink-900 flex items-center justify-center text-surface-400 dark:text-ink-400 text-lg font-bold mb-3">
                    {{ $label->name[0] }}
                </div>
                @endif
                <h3 class="text-sm font-bold text-surface-900 dark:text-ink-100 truncate">{{ $label->name }}</h3>
                <p class="text-[10px] text-surface-400 dark:text-ink-500 mt-1 font-semibold">{{ $label->bands_count }} band{{ $label->bands_count !== 1 ? 's' : '' }}</p>
            </div>
        </a>
        @empty
        <p class="col-span-full text-sm text-surface-400 text-center py-8">{{ __('common.home.no_labels') }}</p>
        @endforelse
    </div>
</section>

<!-- CTA -->
<section class="border-t-2 border-surface-200 dark:border-ink-700 pt-10">
    <div class="max-w-xl mx-auto text-center">
        <h2 class="text-2xl font-black text-surface-900 dark:text-ink-200 mb-3">{{ __('common.home.cta_title') }}</h2>
        <p class="text-surface-500 dark:text-ink-400 text-sm mb-5">{{ __('common.home.cta_desc') }}</p>
        <a href="/admin" class="btn btn-brand">{{ __('common.home.cta_button') }} &rarr;</a>
        <p class="text-xs text-surface-400 dark:text-ink-500 mt-4">{{ __('common.contribute') }} <a href="{{ route('register') }}" class="link">{{ __('common.home.cta_register') }}</a></p>
    </div>
</section>
</div>
@endsection
