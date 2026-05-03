@extends('layouts.app')

@section('head')
@php
$photo = $artist->photo;
$artistPhotoUrl = $photo ? img_url($photo) : null;
$heroImg = $artist->hero_image ? img_url($artist->hero_image) : null;
$heroPlaceholder = $heroImg ?: $artistPhotoUrl;

$seo = new \App\Values\SeoData(
    title: $artist->name,
    description: Str::limit(strip_tags($artist->bio ?? __('common.learn_about_artist', ['name' => $artist->name])), 160),
    type: 'profile',
    image: $artistPhotoUrl,
    canonical: route('artists.show', $artist),
    schema: json_encode(['@context'=>'https://schema.org','@type'=>'Person','name'=>$artist->name,'url'=>route('artists.show',$artist)], JSON_UNESCAPED_SLASHES),
);
@endphp
<x-seo-meta :seo="$seo" />
@if($heroPlaceholder)
<link rel="preload" href="{{ $heroPlaceholder }}" as="image" fetchpriority="high">
@endif
<meta name="twitter:card" content="summary">
@if($artistPhotoUrl)
<meta name="twitter:image" content="{{ $artistPhotoUrl }}">
@endif
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<!-- Hero — foto sem shapes -->
<section class="relative -mx-4 -mt-6 mb-8 overflow-hidden bg-black" style="aspect-ratio:16/4; max-height:45vh;">
    @if($heroPlaceholder)
    <img src="{{ $heroPlaceholder }}" alt="{{ $artist->name }}" class="absolute inset-0 w-full h-full object-cover opacity-30" fetchpriority="high" decoding="async" sizes="100vw">
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-black/20"></div>
    <div class="relative z-10 flex flex-col justify-end h-full">
        <div class="max-w-6xl mx-auto px-4 w-full pb-8 sm:pb-12 pt-6">
            <h1 class="font-display text-3xl sm:text-5xl md:text-6xl font-black text-white leading-none tracking-tight">{{ $artist->name }}</h1>
        </div>
    </div>
</section>

<nav class="breadcrumb mb-6">
    <a href="{{ route('home') }}">{{ __('common.home') }}</a><span>/</span>
    <a href="{{ route('artists.index') }}">{{ __('common.nav.artists') }}</a><span>/</span>
    <span>{{ $artist->name }}</span>
</nav>

<div class="lg:flex lg:gap-10">
    <div class="flex-1 min-w-0 order-2 lg:order-1">
        <!-- Header compacto -->
        <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-surface-200 dark:border-ink-700">
            @if($artistPhotoUrl)
            <button @click="$dispatch('open-lightbox', { url: '{{ $artistPhotoUrl }}', alt: '{{ $artist->name }}' })" class="shrink-0 cursor-pointer">
            <img src="{{ $artistPhotoUrl }}" alt="{{ $artist->name }}" class="w-14 h-20 object-cover shrink-0 border-2 border-surface-200 dark:border-ink-600 hover:border-brand-500 dark:hover:border-brand-400 transition-colors" loading="lazy">
            </button>
            @endif
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    @if($artist->birth_date)
                    <span class="badge badge-brand">{{ $artist->birth_date->format('Y') }}@if($artist->death_date)&ndash;{{ $artist->death_date->format('Y') }}@endif</span>
                    @endif
                    @if($artist->origin)
                    <span class="badge badge-surface">{{ $artist->origin }}</span>
                    @endif
                    @foreach($artist->approvedTags as $tag)
                    <span class="badge badge-surface">{{ $tag->name }}</span>
                    @endforeach
                </div>
                @auth
                @php $favCount = $artist->favorites()->count(); @endphp
                <button x-data="{ fav: {{ auth()->user() && auth()->user()->hasFavorited($artist) ? 'true' : 'false' }}, count: {{ $favCount }} }"
                    @click.prevent="fetch('{{ route('favorites.toggle-artist', $artist) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; })"
                    class="font-display text-xs font-bold mt-1 hover:text-brand-600 dark:hover:text-brand-400" :class="fav ? 'text-brand-600 dark:text-brand-400' : 'text-surface-400'" title="{{ __('common.artists.favorite_title') }}">
                    <span x-text="fav ? '&hearts;' : '&loz;'"></span> <span x-text="count"></span>
                </button>
                @endauth
            </div>
        </div>

        <!-- Bio -->
        @if($artist->bio)
        <div class="prose max-w-none mb-8">{!! \Stevebauman\Purify\Facades\Purify::clean(Str::markdown($artist->bio)) !!}</div>
        @endif

        <!-- Band History — timeline quadrada -->
        <x-section-header tag="h2" :count="$artist->bands->count()">{{ __('common.artists.band_history') }}</x-section-header>
        <div class="relative pl-8">
            <div class="absolute left-[15px] top-2 bottom-0 w-0.5 bg-surface-300 dark:bg-ink-600"></div>
            @forelse($artist->bands as $band)
            <div class="relative pb-5 last:pb-0">
                <div class="absolute -left-8 top-1.5 w-3.5 h-3.5 bg-accent-500 flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-white"></div>
                </div>
                <div class="pl-4">
                    <a href="{{ route('bands.show', $band) }}" class="font-display text-sm font-bold text-brand-600 dark:text-brand-400 hover:underline">{{ $band->name }}</a>
                    @if($band->pivot->role)
                    <span class="badge badge-surface text-[10px] ml-1">{{ $band->pivot->role }}</span>
                    @endif
                    @if($band->pivot->is_current)
                    <span class="badge badge-brand text-[10px] ml-1">{{ __('common.artists.present') }}</span>
                    @endif
                    <p class="font-display text-[11px] font-bold text-surface-400 mt-0.5">
                        {{ $band->pivot->joined_year ?? '?' }}&ndash;{{ $band->pivot->left_year ?? ($band->pivot->is_current ? __('common.artists.present') : '?') }}
                        @if($band->genres->count()) &middot; {{ $band->genres->pluck('name')->implode(', ') }}@endif
                    </p>
                </div>
            </div>
            @empty
            <p class="text-sm text-surface-400 pl-4">{{ __('common.artists.no_history') }}</p>
            @endforelse
        </div>
    </div>

    <!-- Infobox -->
    <aside class="lg:w-72 mt-8 lg:mt-0 shrink-0 self-start order-1 lg:order-2 lg:sticky lg:top-16">
        <x-infobox :title="$artist->name" :items="[
            __('common.artists.birth') => $artist->birth_date ? $artist->birth_date->format('Y') . ($artist->death_date ? '&ndash;' . $artist->death_date->format('Y') : '') : null,
            __('common.artists.origin') => $artist->origin ? e($artist->origin) : null,
            __('common.artists.bands') => (string) $artist->bands->count(),
        ]" />
        <div class="mt-4"><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
</div>
</div>

<!-- Lightbox -->
<div
    x-data="{ show: false, url: '', alt: '' }"
    @open-lightbox.window="show = true; url = $event.detail.url; alt = $event.detail.alt"
    @keydown.escape.window="show = false"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[9999] bg-black/95 flex items-center justify-center"
    @click.self="show = false"
>
    <button @click="show = false" class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center text-white/70 hover:text-white border-2 border-white/20 hover:border-white text-lg font-bold cursor-pointer">&times;</button>
    <img :src="url" :alt="alt" class="max-w-[90vw] max-h-[85vh] object-contain border-2 border-white/10">
</div>
@endsection
