@extends('layouts.app')

@section('head')
@php
$photo = $artist->photo;
$artistPhotoUrl = $photo ? (str_starts_with($photo, 'http') ? $photo : Storage::url($photo)) : null;
$heroImg = $artist->hero_image ? (str_starts_with($artist->hero_image, 'http') ? $artist->hero_image : Storage::url($artist->hero_image)) : null;
$heroPlaceholder = $heroImg ?: $artistPhotoUrl;
$slugId = md5($artist->slug);

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
<!-- Hero -->
<section class="relative -mx-4 -mt-6 mb-8 overflow-hidden bg-ink" style="aspect-ratio:16/4; max-height:45vh;">
    @if($heroPlaceholder)
    <img src="{{ $heroPlaceholder }}" alt="{{ $artist->name }}" class="absolute inset-0 w-full h-full object-cover opacity-30" fetchpriority="high" decoding="async" sizes="100vw">
    @endif
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute w-[40vmin] h-[40vmin] rounded-full bg-accent-500/15 top-[5%] right-[15%]"></div>
        <div class="absolute w-[35%] h-[14%] bg-brand-500/15 bottom-0 right-0"></div>
        <div class="absolute w-[12vmin] h-[12vmin] rounded-full bg-warm-500/15 bottom-[30%] left-[10%]"></div>
    </div>
    <div class="absolute inset-0 bg-gradient-to-r from-ink/80 via-ink/50 to-ink/20"></div>
    <div class="relative z-10 flex flex-col justify-end h-full">
        <div class="max-w-6xl mx-auto px-4 w-full pb-8 sm:pb-12 pt-6">
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-black text-white leading-none tracking-tight">{{ $artist->name }}</h1>
        </div>
    </div>
</section>

<nav class="flex items-center gap-2 text-xs text-surface-400 mb-6">
    <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a><span>/</span>
    <a href="{{ route('artists.index') }}" class="hover:text-brand-600">Artists</a><span>/</span>
    <span class="text-surface-700 dark:text-ink-200 font-medium">{{ $artist->name }}</span>
</nav>

<div class="lg:flex lg:gap-10">
    <div class="flex-1 min-w-0">
        <!-- Header -->
        <div class="flex items-start gap-4 mb-6">
            @if($artistPhotoUrl)
            <img src="{{ $artistPhotoUrl }}" alt="{{ $artist->name }}" class="w-20 h-20 object-cover shrink-0" loading="lazy">
            @endif
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap gap-2">
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
                <button x-data="{ fav: false, count: 0 }"
                    x-init="fetch('{{ route('favorites.toggle-artist', $artist) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; }).catch(() => {})"
                    @click.prevent="fetch('{{ route('favorites.toggle-artist', $artist) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; })"
                    class="btn btn-ghost btn-sm mt-2" :class="fav ? 'btn-brand' : 'btn-ghost'" title="Favorite">
                    <svg class="w-3.5 h-3.5" :fill="fav ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span x-text="count" class="text-[10px] font-bold"></span>
                </button>
                @endauth
            </div>
        </div>

        <!-- Bio -->
        @if($artist->bio)
        <div class="prose max-w-none">{!! \Stevebauman\Purify\Facades\Purify::clean(Str::markdown($artist->bio)) !!}</div>
        @endif

        <!-- Band History Timeline -->
        <h2 class="text-base font-bold mt-10 mb-4 text-surface-900 dark:text-ink-200">Band History</h2>
        <div class="relative pl-8">
            <div class="absolute left-[15px] top-2 bottom-0 w-px bg-surface-200 dark:bg-ink-700"></div>
            @forelse($artist->bands as $band)
            <div class="relative pb-6 last:pb-0">
                <div class="absolute -left-8 top-1.5 w-[14px] h-[14px] bg-accent-500 flex items-center justify-center">
                    <div class="w-1.5 h-1.5 bg-white"></div>
                </div>
                <div class="pl-4">
                    <a href="{{ route('bands.show', $band) }}" class="text-sm font-bold text-brand-600 dark:text-brand-400 hover:underline">{{ $band->name }}</a>
                    @if($band->pivot->role)
                    <span class="badge badge-surface text-[10px] ml-1">{{ $band->pivot->role }}</span>
                    @endif
                    @if($band->pivot->is_current)
                    <span class="badge badge-brand text-[10px] ml-1">current</span>
                    @endif
                    <p class="text-[11px] text-surface-400 mt-0.5">
                        {{ $band->pivot->joined_year ?? '?' }}&ndash;{{ $band->pivot->left_year ?? ($band->pivot->is_current ? 'present' : '?') }}
                        @if($band->genres->count()) &middot; {{ $band->genres->pluck('name')->implode(', ') }}@endif
                    </p>
                </div>
            </div>
            @empty
            <p class="text-sm text-surface-400 pl-4">No band history recorded.</p>
            @endforelse
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="lg:w-64 mt-8 lg:mt-0 shrink-0 lg:sticky lg:top-16 self-start space-y-4">
        <div class="card bg-white dark:bg-ink-800 p-4">
            <div class="space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-surface-400">Bands</span><span class="font-bold text-surface-900 dark:text-ink-200">{{ $artist->bands->count() }}</span></div>
                @if($artist->origin)<div class="flex justify-between"><span class="text-surface-400">From</span><span class="font-bold text-surface-900 dark:text-ink-200">{{ $artist->origin }}</span></div>@endif
            </div>
        </div>
        <div><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
</div>
@endsection
