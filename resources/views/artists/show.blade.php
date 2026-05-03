@extends('layouts.app')

@section('head')
@php
$photo = $artist->photo;
$artistPhotoUrl = $photo ? img_url($photo) : null;
$heroImg = $artist->hero_image ? img_url($artist->hero_image) : null;
$heroPlaceholder = $heroImg ?: $artistPhotoUrl;

$seo = new \App\Values\SeoData(
    title: $artist->name,
    description: Str::limit(strip_tags($artist->bio ?? 'Learn about ' . $artist->name . ' and their musical career.'), 160),
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
    <a href="{{ route('home') }}">Home</a><span>/</span>
    <a href="{{ route('artists.index') }}">Artists</a><span>/</span>
    <span>{{ $artist->name }}</span>
</nav>

<div class="lg:flex lg:gap-10">
    <div class="flex-1 min-w-0 order-2 lg:order-1">
        <!-- Header compacto -->
        <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-surface-200 dark:border-ink-700">
            @if($artistPhotoUrl)
            <img src="{{ $artistPhotoUrl }}" alt="{{ $artist->name }}" class="w-14 h-14 object-cover shrink-0 border-2 border-surface-200 dark:border-ink-600" loading="lazy">
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
                    class="font-display text-xs font-bold mt-1 hover:text-brand-600 dark:hover:text-brand-400" :class="fav ? 'text-brand-600 dark:text-brand-400' : 'text-surface-400'" title="Favorite">
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
        <x-section-header tag="h2" :count="$artist->bands->count()">Histórico de Bandas</x-section-header>
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
                    <span class="badge badge-brand text-[10px] ml-1">atual</span>
                    @endif
                    <p class="font-display text-[11px] font-bold text-surface-400 mt-0.5">
                        {{ $band->pivot->joined_year ?? '?' }}&ndash;{{ $band->pivot->left_year ?? ($band->pivot->is_current ? 'presente' : '?') }}
                        @if($band->genres->count()) &middot; {{ $band->genres->pluck('name')->implode(', ') }}@endif
                    </p>
                </div>
            </div>
            @empty
            <p class="text-sm text-surface-400 pl-4">Nenhum histórico registrado.</p>
            @endforelse
        </div>
    </div>

    <!-- Infobox -->
    <aside class="lg:w-72 mt-8 lg:mt-0 shrink-0 self-start order-1 lg:order-2 lg:sticky lg:top-16">
        <x-infobox :title="$artist->name" :items="[
            'Birth' => $artist->birth_date ? $artist->birth_date->format('Y') . ($artist->death_date ? '&ndash;' . $artist->death_date->format('Y') : '') : null,
            'Origin' => $artist->origin ? e($artist->origin) : null,
            'Bands' => (string) $artist->bands->count(),
        ]" />
        <div class="mt-4"><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
</div>
@endsection
