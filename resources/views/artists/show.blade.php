@extends('layouts.app')

@section('head')
@php
$hero = $artist->hero_image ? Storage::url($artist->hero_image) : null;
$thumb = $artist->photo ? Storage::url($artist->photo) : null;
$slug = md5($artist->slug);
$heroPlaceholder = $hero ?: $thumb ?: '';
$thumbPlaceholder = $thumb ?: "https://picsum.photos/seed/{$slug}/400/400";
$gallery = $artist->gallery && count($artist->gallery) ? $artist->gallery : [
    "https://picsum.photos/seed/{$slug}-1/600/400",
    "https://picsum.photos/seed/{$slug}-2/600/400",
    "https://picsum.photos/seed/{$slug}-3/600/400",
];
$seo = new \App\Values\SeoData(
    title: $artist->name,
    description: Str::limit(strip_tags($artist->bio ?? 'Learn about ' . $artist->name . ' and their musical career.'), 160),
    type: 'profile',
    image: $thumbPlaceholder,
    canonical: route('artists.show', $artist),
    schema: json_encode(['@context'=>'https://schema.org','@type'=>'Person','name'=>$artist->name,'url'=>route('artists.show',$artist)], JSON_UNESCAPED_SLASHES),
);
@endphp
<x-seo-meta :seo="$seo" />
<link rel="preload" href="{{ $heroPlaceholder ?: $thumbPlaceholder }}" as="image" fetchpriority="high">
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="preconnect" href="https://picsum.photos" crossorigin>
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
<link rel="dns-prefetch" href="https://picsum.photos">
<meta name="twitter:card" content="summary">
<meta name="twitter:image" content="{{ $thumbPlaceholder }}">
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<!-- Hero -->
<section class="relative -mx-4 -mt-6 mb-10 overflow-hidden bg-ink-900 dark:bg-ink" style="aspect-ratio:16/5; max-height:55vh;">
    @if($hero)
    <img src="{{ $hero }}" alt="{{ $artist->name }}" class="absolute inset-0 w-full h-full object-cover opacity-50 dark:opacity-35" fetchpriority="high" decoding="async" sizes="100vw" width="1200" height="375">
    @elseif($thumb)
    <img src="{{ $thumb }}" alt="{{ $artist->name }}" class="absolute inset-0 w-full h-full object-cover opacity-50 dark:opacity-35" fetchpriority="high" decoding="async" sizes="100vw" width="1200" height="375">
    @else
    <div class="absolute inset-0 bg-gradient-to-br from-accent-500/30 via-brand-500/20 to-warm-500/10"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/30 to-transparent flex items-end p-6 sm:p-10">
        <h1 class="font-display text-3xl sm:text-5xl md:text-6xl font-bold text-white leading-none tracking-tight">{{ $artist->name }}</h1>
    </div>
</section>

<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-xs text-surface-400 mb-8 uppercase tracking-wider">
    <a href="{{ route('home') }}" class="hover:text-accent-600">Home</a><span>/</span>
    <a href="{{ route('artists.index') }}" class="hover:text-accent-600">Artists</a><span>/</span>
    <span class="text-surface-700 dark:text-ink-200 font-medium">{{ $artist->name }}</span>
</nav>

<div class="lg:flex lg:gap-10">
    <div class="flex-1 min-w-0">
        <!-- Thumb + Meta -->
        <div class="flex items-start gap-4">
            <img src="{{ $thumbPlaceholder }}" alt="{{ $artist->name }}" class="w-20 h-20 object-cover shrink-0" loading="lazy" width="100" height="100" style="border:1px solid var(--color-surface-200)">
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex flex-wrap gap-2">
                        @if($artist->birth_date)
                        <span class="badge badge-accent">{{ $artist->birth_date->format('Y') }}@if($artist->death_date)&ndash;{{ $artist->death_date->format('Y') }}@endif</span>
                        @endif
                        @if($artist->origin)
                        <span class="badge badge-surface">{{ $artist->origin }}</span>
                        @endif
                        @foreach($artist->approvedTags as $tag)
                        <span class="badge badge-accent">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    @auth
                    <button x-data="{ fav: false, count: 0 }"
                        x-init="fetch('{{ route('favorites.toggle-artist', $artist) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; }).catch(() => {})"
                        @click.prevent="fetch('{{ route('favorites.toggle-artist', $artist) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; })"
                        class="btn btn-icon shrink-0 transition-colors"
                        :class="fav ? 'btn-error' : 'btn-ghost'" title="Favorite">
                        <svg class="w-4 h-4" :fill="fav ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Bio -->
        @if($artist->bio)
        <div class="prose max-w-none mt-6">{!! \Stevebauman\Purify\Facades\Purify::clean(Str::markdown($artist->bio)) !!}</div>
        @endif

        <!-- Gallery -->
        <div class="mt-6" x-data="{ lightbox: null }">
            <div class="grid grid-cols-3 gap-2">
                @foreach(collect(is_array($gallery) ? $gallery : $gallery->toArray())->take(3) as $img)
                <div class="aspect-[4/3] overflow-hidden cursor-pointer group" @click="lightbox = '{{ $img }}'" style="border:1px solid var(--color-surface-200)">
                    <img src="{{ $img }}" alt="Gallery image" class="w-full h-full object-cover transition-transform group-hover:scale-105" loading="lazy" decoding="async" sizes="(max-width:640px) 50vw, 33vw" width="400" height="300">
                </div>
                @endforeach
            </div>
            <template x-teleport="body">
                <div x-show="lightbox" x-cloak class="fixed inset-0 z-[60] bg-ink/95 flex items-center justify-center p-4" @click="lightbox = null" @keydown.escape="lightbox = null">
                    <img :src="lightbox" :alt="'Enlarged image'" class="max-w-full max-h-full object-contain" @click.stop style="border:1px solid var(--color-surface-700)">
                    <button class="absolute top-4 right-4 text-white/70 hover:text-white text-2xl font-bold" @click="lightbox = null">&times;</button>
                </div>
            </template>
        </div>

        <!-- Band History Timeline -->
        <h2 class="font-display text-2xl font-bold mt-12 mb-5 text-surface-900 dark:text-ink-200">Band History</h2>
        <div class="relative pl-10">
            <div class="absolute left-[19px] top-2 bottom-0 w-px bg-surface-200 dark:bg-ink-700"></div>
            @forelse($artist->bands as $band)
            <div class="relative pb-8 last:pb-0">
                <div class="absolute -left-10 top-2 w-5 h-5 bg-accent-500 flex items-center justify-center">
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                </div>
                <div class="absolute -left-[34px] top-7 text-[10px] font-bold text-accent-600 dark:text-accent-400 whitespace-nowrap">{{ $band->pivot->joined_year ?? '?' }}</div>
                <div class="pl-4">
                    <a href="{{ route('bands.show', $band) }}" class="font-display font-bold text-brand-600 dark:text-brand-400 hover:underline text-lg">{{ $band->name }}</a>
                    @if($band->pivot->role)
                    <span class="badge badge-accent ml-2">{{ $band->pivot->role }}</span>
                    @endif
                    @if($band->pivot->is_current)
                    <span class="badge badge-brand ml-1">current</span>
                    @endif
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                        {{ $band->pivot->joined_year ?? '?' }}&ndash;{{ $band->pivot->left_year ?? ($band->pivot->is_current ? 'present' : '?') }}
                        @if($band->genres->count())&middot; {{ $band->genres->pluck('name')->implode(', ') }}@endif
                    </p>
                </div>
            </div>
            @empty
            <p class="text-sm text-surface-400 pl-4">No band history recorded.</p>
            @endforelse
        </div>
    </div>

    <aside class="lg:w-64 mt-8 lg:mt-0 shrink-0 lg:sticky lg:top-16 self-start space-y-4">
        <div class="card bg-white dark:bg-ink-800 p-4">
            <h3 class="font-display text-sm font-bold mb-3 text-surface-700 dark:text-ink-200">Stats</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-surface-400">Bands</span><span class="font-bold text-surface-700 dark:text-ink-200">{{ $artist->bands->count() }}</span></div>
                @if($artist->origin)<div class="flex justify-between"><span class="text-surface-400">From</span><span class="font-bold text-surface-700 dark:text-ink-200">{{ $artist->origin }}</span></div>@endif
            </div>
        </div>
        <div><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
</div>
@endsection
