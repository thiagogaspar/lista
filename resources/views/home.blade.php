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

    <div class="relative z-10 flex flex-col justify-end h-full">
        <div class="max-w-6xl mx-auto px-4 w-full pb-8 sm:pb-12 pt-6">
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
<div class="mb-10 max-w-2xl mx-auto text-center">
    <h1 class="text-3xl sm:text-4xl font-black text-surface-900 dark:text-ink-200 leading-tight tracking-tight">
        {{ __('common.home.local') }} <span class="text-brand-600 dark:text-brand-500">{{ __('common.home.music') }}</span> {{ __('common.home.genealogy') }}
    </h1>
    <p class="mt-3 text-surface-500 dark:text-ink-400 text-sm max-w-md mx-auto">
        {{ __('common.home.subtitle') }}
    </p>
    <div class="flex items-center justify-center gap-3 mt-5">
        <a href="{{ route('bands.index') }}" class="btn btn-brand">{{ __('common.home.browse_bands') }}</a>
        <a href="{{ route('genealogy') }}" class="btn btn-ghost">{{ __('common.home.view_graph') }}</a>
    </div>
</div>

<!-- Featured Bands -->
<section class="mb-10">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-bold uppercase tracking-wider text-surface-700 dark:text-ink-300">{{ __('common.home.featured_bands') }}</h2>
        <a href="{{ route('bands.index') }}" class="text-xs text-surface-400 hover:text-brand-600 dark:hover:text-brand-400">{{ __('common.view_all') }} &rarr;</a>
    </div>
    <div class="divide-y divide-surface-200 dark:divide-ink-700 border-t border-b border-surface-200 dark:border-ink-700">
        @forelse($featuredBands as $band)
            <a href="{{ route('bands.show', $band) }}" class="flex items-start gap-3 py-2.5 px-1 -mx-1 hover:bg-surface-100 dark:hover:bg-ink-800/50 group">
                @if($band->photo)
                <img src="{{ Storage::url($band->photo) }}" alt="{{ $band->name }}" class="w-9 h-9 object-cover shrink-0 mt-0.5" loading="lazy">
                @endif
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-bold text-surface-900 dark:text-ink-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 leading-tight">{{ $band->name }}</h3>
                    <div class="text-[11px] text-surface-500 dark:text-ink-500 mt-0.5">
                        @if($band->formed_year)<span>{{ $band->formed_year }}&ndash;{{ $band->dissolved_year ?? 'present' }}</span>@endif
                        @foreach($band->genres->take(2) as $genre)<span class="ml-1">{{ $genre->name }}</span>@endforeach
                        @if($band->origin)<span class="ml-1">&middot; {{ $band->origin }}</span>@endif
                    </div>
                </div>
                <span class="text-surface-300 dark:text-ink-600 text-xs mt-1 shrink-0 opacity-0 group-hover:opacity-100">&rarr;</span>
            </a>
        @empty
            <p class="text-sm text-surface-400 text-center py-6">{{ __('common.home.no_featured_bands') }}</p>
        @endforelse
    </div>
</section>

<!-- Featured Artists -->
<section class="mb-10">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-bold uppercase tracking-wider text-surface-700 dark:text-ink-300">{{ __('common.home.featured_artists') }}</h2>
        <a href="{{ route('artists.index') }}" class="text-xs text-surface-400 hover:text-brand-600 dark:hover:text-brand-400">{{ __('common.view_all') }} &rarr;</a>
    </div>
    <div class="divide-y divide-surface-200 dark:divide-ink-700 border-t border-b border-surface-200 dark:border-ink-700">
        @forelse($featuredArtists as $artist)
            <a href="{{ route('artists.show', $artist) }}" class="flex items-start gap-3 py-2.5 px-1 -mx-1 hover:bg-surface-100 dark:hover:bg-ink-800/50 group">
                @if($artist->photo)
                <img src="{{ Storage::url($artist->photo) }}" alt="{{ $artist->name }}" class="w-9 h-9 object-cover shrink-0 mt-0.5" loading="lazy">
                @endif
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-bold text-surface-900 dark:text-ink-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 leading-tight">{{ $artist->name }}</h3>
                    <div class="text-[11px] text-surface-500 dark:text-ink-500 mt-0.5">
                        @if($artist->origin)<span>{{ $artist->origin }}</span>@endif
                    </div>
                </div>
                <span class="text-surface-300 dark:text-ink-600 text-xs mt-1 shrink-0 opacity-0 group-hover:opacity-100">&rarr;</span>
            </a>
        @empty
            <p class="text-sm text-surface-400 text-center py-6">{{ __('common.home.no_featured_artists') }}</p>
        @endforelse
    </div>
</section>

<!-- Featured Labels -->
<section class="mb-10">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-bold uppercase tracking-wider text-surface-700 dark:text-ink-300">{{ __('common.home.labels') }}</h2>
        <a href="{{ route('labels.index') }}" class="text-xs text-surface-400 hover:text-brand-600 dark:hover:text-brand-400">{{ __('common.view_all') }} &rarr;</a>
    </div>
    <div class="divide-y divide-surface-200 dark:divide-ink-700 border-t border-b border-surface-200 dark:border-ink-700">
        @forelse($featuredLabels as $label)
        <a href="{{ route('labels.show', $label) }}" class="flex items-start gap-3 py-2.5 px-1 -mx-1 hover:bg-surface-100 dark:hover:bg-ink-800/50 group">
            @if($label->logo)
            <img src="{{ Storage::url($label->logo) }}" alt="{{ $label->name }} logo" class="w-9 h-9 object-contain shrink-0 mt-0.5" loading="lazy">
            @else
            <div class="w-9 h-9 shrink-0 bg-surface-100 dark:bg-ink-900 flex items-center justify-center text-surface-400 dark:text-ink-500 text-xs font-bold mt-0.5">{{ $label->name[0] }}</div>
            @endif
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold text-surface-900 dark:text-ink-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 leading-tight">{{ $label->name }}</h3>
                <div class="text-[11px] text-surface-500 dark:text-ink-500 mt-0.5">
                    <span>{{ $label->bands_count }} band{{ $label->bands_count !== 1 ? 's' : '' }}</span>
                    @if($label->country)<span class="ml-1">&middot; {{ $label->country }}</span>@endif
                </div>
            </div>
            <span class="text-surface-300 dark:text-ink-600 text-xs mt-1 shrink-0 opacity-0 group-hover:opacity-100">&rarr;</span>
        </a>
        @empty
        <p class="text-sm text-surface-400 text-center py-6">{{ __('common.home.no_labels') }}</p>
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
