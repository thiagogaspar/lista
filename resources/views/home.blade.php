@extends('layouts.app')

@section('head')
@php
$seo = new \App\Values\SeoData(
    title: 'LISTA — Band Genealogy',
    description: 'Explore connections between bands and artists. A community-built directory of local original music.',
    canonical: url('/'),
    schema: json_encode(['@context'=>'https://schema.org','@type'=>'WebSite','name'=>'LISTA','url'=>url('/')], JSON_UNESCAPED_SLASHES),
);
@endphp
<x-seo-meta :seo="$seo" />
<style>
.home-hero {
    min-height: 42vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
</style>
@endsection

@section('content')
<!-- Hero tipográfico — estilo newspaper -->
<section class="home-hero -mx-4 -mt-6 mb-10 bg-black">
    <div class="max-w-6xl mx-auto px-4 w-full py-16 sm:py-20">
        <div class="lg:flex lg:items-center lg:gap-12 xl:gap-16">
            <div class="flex-1 min-w-0">
                <p class="font-display text-xs font-bold tracking-[0.2em] uppercase text-white/40 mb-4">{{ __('common.home.hero_tag') }}</p>
                <h1 class="text-5xl sm:text-7xl md:text-8xl font-black text-white leading-none tracking-tight max-w-4xl">{!! __('common.home.hero_title') !!}</h1>
                <p class="mt-4 text-base sm:text-lg text-white/50 max-w-xl leading-relaxed font-serif">{{ __('common.home.hero_subtitle') }}</p>
                <div class="flex flex-wrap items-center gap-3 mt-8">
                    <a href="{{ route('bands.index') }}" class="btn text-sm bg-white text-black border-white hover:bg-white/90 tracking-wider font-bold">{{ __('common.home.browse_bands') }} &rarr;</a>
                    <a href="{{ route('genealogy') }}" class="btn text-sm text-white/70 border-2 border-white/20 hover:border-white/50 hover:text-white tracking-wider font-bold">{{ __('common.home.view_graph') }}</a>
                </div>
            </div>
            @if($heroBand)
            <div class="shrink-0 mt-8 lg:mt-0 lg:w-72">
                <div class="border border-white/10 bg-white/5 p-4 group hover:bg-white/[7%] transition-colors">
                    <p class="font-display text-[10px] font-bold tracking-[0.15em] uppercase text-white/30 mb-3">{{ __('common.home.featured_band') }}</p>
                    @if($heroBand->photo)
                    <img src="{{ img_url($heroBand->photo) }}" alt="{{ $heroBand->name }}" class="w-full aspect-[3/2] object-cover border border-white/10" loading="lazy">
                    @else
                    <div class="w-full aspect-[3/2] bg-white/5 border border-white/10 flex items-center justify-center text-white/20 font-display text-4xl font-black">{{ $heroBand->name[0] }}</div>
                    @endif
                    <h3 class="font-display font-bold text-lg text-white mt-3 leading-tight">{{ $heroBand->name }}</h3>
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1.5">
                        @foreach($heroBand->genres->take(2) as $genre)
                        <span class="font-display text-[10px] font-bold tracking-wider uppercase text-white/40">{{ $genre->name }}</span>
                        @if(!$loop->last)<span class="text-white/20 text-[8px]">|</span>@endif
                        @endforeach
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        @if($heroBand->formed_year)
                        <span class="font-display text-[10px] text-white/30">{{ $heroBand->formed_year }}{{ $heroBand->dissolved_year ? '&ndash;'.$heroBand->dissolved_year : '&ndash;'.__('common.home.present') }}</span>
                        @endif
                        @if($heroBand->origin)
                        <span class="text-white/15 text-[8px]">/</span>
                        <span class="font-display text-[10px] text-white/30">{{ $heroBand->origin }}</span>
                        @endif
                    </div>
                    <a href="{{ route('bands.show', $heroBand) }}" class="inline-block mt-3 font-display text-[11px] font-bold tracking-wider uppercase text-white/50 hover:text-white transition-colors">{{ __('common.home.explore', ['name' => $heroBand->name]) }} &rarr;</a>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Stats bar — dividers grossos -->
<div class="max-w-6xl mx-auto px-4 mb-12">
    <div class="grid grid-cols-2 sm:grid-cols-4 border-2 border-surface-200 dark:border-ink-700 bg-white dark:bg-ink-900">
        <div class="stat border-r-2 border-surface-200 dark:border-ink-700 last:border-0">
            <div class="stat-value text-brand-600 dark:text-brand-500">{{ number_format($stats['bands']) }}</div>
            <div class="stat-desc">{{ __('common.home.bands') }}</div>
        </div>
        <div class="stat border-r-2 border-surface-200 dark:border-ink-700 last:border-0">
            <div class="stat-value text-accent-500 dark:text-accent-400">{{ number_format($stats['artists']) }}</div>
            <div class="stat-desc">{{ __('common.home.artists') }}</div>
        </div>
        <div class="stat border-r-2 border-surface-200 dark:border-ink-700 last:border-0">
            <div class="stat-value text-warm-500 dark:text-warm-400">{{ number_format($stats['memberships']) }}</div>
            <div class="stat-desc">{{ __('common.home.links') }}</div>
        </div>
        <div class="stat">
            <div class="stat-value text-surface-700 dark:text-ink-200">{{ number_format($stats['relationships']) }}</div>
            <div class="stat-desc">{{ __('common.home.connections') }}</div>
        </div>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4">
<!-- Featured Bands — newspaper grid 3-col -->
<section class="mb-12">
    <div class="flex items-center justify-between mb-5">
        <h2 class="section-header flex-1">{{ __('common.home.featured_bands') }}</h2>
        <a href="{{ route('bands.index') }}" class="font-display text-xs font-bold text-surface-400 hover:text-brand-600 dark:hover:text-brand-400 shrink-0 ml-4">{{ __('common.view_all') }} &rarr;</a>
    </div>
    <div class="newspaper-grid gap-px bg-surface-200 dark:bg-ink-700 border-2 border-surface-200 dark:border-ink-700">
        @forelse($featuredBands as $band)
            <a href="{{ route('bands.show', $band) }}" class="bg-white dark:bg-ink-800 p-4 hover:bg-surface-50 dark:hover:bg-ink-700 group flex flex-col gap-2">
                @if($band->photo)
                <img src="{{ img_url($band->photo) }}" alt="{{ $band->name }}" class="w-full aspect-[3/2] object-cover border-2 border-surface-200 dark:border-ink-600" loading="lazy">
                @endif
                <h3 class="font-display font-bold text-sm text-surface-900 dark:text-ink-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 leading-tight">{{ $band->name }}</h3>
                <div class="text-[11px] text-surface-500 dark:text-ink-500 leading-relaxed">
                    @if($band->formed_year)<span>{{ $band->formed_year }}&ndash;{{ $band->dissolved_year ?? __('common.home.present') }}</span> · @endif
                    @foreach($band->genres->take(2) as $genre){{ $genre->name }}@if(!$loop->last), @endif @endforeach
                    @if($band->origin)· {{ $band->origin }}@endif
                </div>
            </a>
        @empty
            <div class="bg-white dark:bg-ink-800 p-6 text-sm text-surface-400 text-center col-span-3">{{ __('common.home.no_featured_bands') }}</div>
        @endforelse
    </div>
</section>

<!-- Featured Artists — newspaper grid -->
<section class="mb-12">
    <div class="flex items-center justify-between mb-5">
        <h2 class="section-header flex-1">{{ __('common.home.featured_artists') }}</h2>
        <a href="{{ route('artists.index') }}" class="font-display text-xs font-bold text-surface-400 hover:text-brand-600 dark:hover:text-brand-400 shrink-0 ml-4">{{ __('common.view_all') }} &rarr;</a>
    </div>
    <div class="newspaper-grid gap-px bg-surface-200 dark:bg-ink-700 border-2 border-surface-200 dark:border-ink-700">
        @forelse($featuredArtists as $artist)
            <a href="{{ route('artists.show', $artist) }}" class="bg-white dark:bg-ink-800 p-4 hover:bg-surface-50 dark:hover:bg-ink-700 group flex flex-col gap-2">
                @if($artist->photo)
                <img src="{{ img_url($artist->photo) }}" alt="{{ $artist->name }}" class="w-full aspect-[2/3] object-cover border-2 border-surface-200 dark:border-ink-600" loading="lazy">
                @endif
                <h3 class="font-display font-bold text-sm text-surface-900 dark:text-ink-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 leading-tight">{{ $artist->name }}</h3>
                <div class="text-[11px] text-surface-500 dark:text-ink-500">
                    @if($artist->origin){{ $artist->origin }}@endif
                </div>
            </a>
        @empty
            <div class="bg-white dark:bg-ink-800 p-6 text-sm text-surface-400 text-center col-span-3">{{ __('common.home.no_featured_artists') }}</div>
        @endforelse
    </div>
</section>

<!-- Featured Labels -->
<section class="mb-12">
    <div class="flex items-center justify-between mb-5">
        <h2 class="section-header flex-1">{{ __('common.home.labels') }}</h2>
        <a href="{{ route('labels.index') }}" class="font-display text-xs font-bold text-surface-400 hover:text-brand-600 dark:hover:text-brand-400 shrink-0 ml-4">{{ __('common.view_all') }} &rarr;</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-surface-200 dark:bg-ink-700 border-2 border-surface-200 dark:border-ink-700">
        @forelse($featuredLabels as $label)
        <a href="{{ route('labels.show', $label) }}" class="bg-white dark:bg-ink-800 p-4 hover:bg-surface-50 dark:hover:bg-ink-700 group flex items-center gap-3">
            @if($label->logo)
            <img src="{{ img_url($label->logo) }}" alt="{{ $label->name }} logo" class="w-10 h-10 object-contain shrink-0 border-2 border-surface-200 dark:border-ink-600" loading="lazy">
            @else
            <div class="w-10 h-10 shrink-0 bg-surface-100 dark:bg-ink-900 flex items-center justify-center text-surface-400 dark:text-ink-500 font-display text-sm font-bold border-2 border-surface-200 dark:border-ink-600">{{ $label->name[0] }}</div>
            @endif
            <div class="min-w-0 flex-1">
                <h3 class="font-display font-bold text-sm text-surface-900 dark:text-ink-100 group-hover:text-brand-600 dark:group-hover:text-brand-400 truncate">{{ $label->name }}</h3>
                <div class="text-[11px] text-surface-500 dark:text-ink-500">{{ $label->bands_count }} banda(s)@if($label->country) · {{ $label->country }}@endif</div>
            </div>
        </a>
        @empty
        <div class="bg-white dark:bg-ink-800 p-6 text-sm text-surface-400 text-center col-span-4">{{ __('common.home.no_labels') }}</div>
        @endforelse
    </div>
</section>

<!-- CTA -->
<section class="border-t-2 border-surface-200 dark:border-ink-700 pt-12 pb-8">
    <div class="max-w-xl mx-auto text-center">
        <h2 class="font-display text-2xl font-black text-surface-900 dark:text-ink-200 mb-3">{{ __('common.home.cta_title') }}</h2>
        <p class="text-surface-500 dark:text-ink-400 text-sm mb-5 font-serif">{{ __('common.home.cta_desc') }}</p>
        <a href="/admin" class="btn bg-black text-white border-black hover:bg-surface-800">{{ __('common.home.cta_button') }} &rarr;</a>
        <p class="text-xs text-surface-400 dark:text-ink-500 mt-4">{{ __('common.contribute') }} <a href="{{ route('register') }}" class="link">{{ __('common.home.cta_register') }}</a></p>
    </div>
</section>
</div>
@endsection
