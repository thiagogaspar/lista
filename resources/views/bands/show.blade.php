@extends('layouts.app')

@section('head')
@php
$photo = $band->photo;
$bandPhotoUrl = $photo ? (str_starts_with($photo, 'http') ? $photo : Storage::url($photo)) : null;
$heroImg = $band->hero_image ? (str_starts_with($band->hero_image, 'http') ? $band->hero_image : Storage::url($band->hero_image)) : null;
$heroPlaceholder = $heroImg ?: $bandPhotoUrl;
$slugId = md5($band->slug);

$geoTheme = match ($band->genres->first()?->slug) {
    'alternative-rock', 'indie-rock', 'dream-pop', 'shoegaze' => 'sage',
    'grunge', 'punk-rock', 'post-grunge' => 'ocher',
    default => 'terracotta',
};

$seo = new \App\Values\SeoData(
    title: $band->name,
    description: Str::limit(strip_tags($band->bio ?? 'Learn about ' . $band->name . ' and their musical journey.'), 160),
    type: 'music.group',
    image: $bandPhotoUrl,
    canonical: route('bands.show', $band),
    schema: json_encode(['@context'=>'https://schema.org','@type'=>'MusicGroup','name'=>$band->name,'url'=>route('bands.show',$band)], JSON_UNESCAPED_SLASHES),
);
@endphp
<x-seo-meta :seo="$seo" />
@if($heroPlaceholder)
<link rel="preload" href="{{ $heroPlaceholder }}" as="image" fetchpriority="high">
@endif
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<!-- Hero -->
<section class="relative -mx-4 -mt-6 mb-8 overflow-hidden bg-ink" style="aspect-ratio:16/4; max-height:45vh;">
    @if($heroPlaceholder)
    <img src="{{ $heroPlaceholder }}" alt="{{ $band->name }}" class="absolute inset-0 w-full h-full object-cover opacity-30" fetchpriority="high" decoding="async" sizes="100vw">
    @endif
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        @if($geoTheme === 'terracotta')
        <div class="absolute w-[50vmin] h-[50vmin] rounded-full bg-brand-500/15 -top-[15%] -right-[5%]"></div>
        <div class="absolute w-[55%] h-[10%] bg-warm-500/10 bottom-[20%] -left-[8%]"></div>
        <div class="absolute w-[8vmin] h-[8vmin] bg-accent-500/20 bottom-[40%] left-[20%]"></div>
        @elseif($geoTheme === 'sage')
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
    <div class="relative z-10 flex flex-col justify-end h-full p-6 sm:p-8">
        <h1 class="text-3xl sm:text-5xl md:text-6xl font-black text-white leading-none tracking-tight">{{ $band->name }}</h1>
    </div>
</section>

<nav class="flex items-center gap-2 text-xs text-surface-400 mb-6">
    <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a><span>/</span>
    <a href="{{ route('bands.index') }}" class="hover:text-brand-600">Bands</a><span>/</span>
    <span class="text-surface-700 dark:text-ink-200 font-medium">{{ $band->name }}</span>
</nav>

<div class="lg:flex lg:gap-10">
    <div class="flex-1 min-w-0">
        <!-- Header -->
        <div class="flex items-start gap-4 mb-6">
            @if($bandPhotoUrl)
            <img src="{{ $bandPhotoUrl }}" alt="{{ $band->name }}" class="w-20 h-20 object-cover shrink-0" loading="lazy">
            @endif
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap gap-2 mb-1">
                    @if($band->formed_year)
                    <span class="badge badge-brand">{{ $band->formed_year }}&ndash;{{ $band->dissolved_year ?? 'present' }}</span>
                    @endif
                    @foreach($band->genres as $genre)
                    <span class="badge badge-surface">{{ $genre->name }}</span>
                    @endforeach
                    @if($band->origin)
                    <span class="badge badge-surface">{{ $band->origin }}</span>
                    @endif
                </div>
                @if($band->label)
                <a href="{{ route('labels.show', $band->label) }}" class="text-xs text-surface-500 hover:text-brand-600 dark:hover:text-brand-400">{{ $band->label->name }}</a>
                @endif
                @auth
                <button x-data="{ fav: false, count: 0 }"
                    x-init="fetch('{{ route('favorites.toggle-band', $band) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; }).catch(() => {})"
                    @click.prevent="fetch('{{ route('favorites.toggle-band', $band) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; })"
                    class="btn btn-ghost btn-sm ml-auto mt-1" :class="fav ? 'btn-brand' : 'btn-ghost'" title="Favorite">
                    <svg class="w-3.5 h-3.5" :fill="fav ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span x-text="count" class="text-[10px] font-bold"></span>
                </button>
                @endauth
            </div>
        </div>

        <!-- Bio -->
        @if($band->bio)
        <div class="prose max-w-none">{!! \Stevebauman\Purify\Facades\Purify::clean(Str::markdown($band->bio)) !!}</div>
        @endif

        <!-- Members -->
        <h2 class="text-base font-bold mt-10 mb-4 text-surface-900 dark:text-ink-200">Members <span class="font-normal text-surface-400">({{ $band->artists->count() }})</span></h2>
        <div class="border border-surface-200 dark:border-ink-700 bg-white dark:bg-ink-800">
            @forelse($band->artists as $artist)
            <div class="flex items-center justify-between px-4 py-2.5 border-b border-surface-200 dark:border-ink-700 last:border-0 hover:bg-surface-50 dark:hover:bg-ink-700/50">
                <div class="flex items-center gap-2">
                    <a href="{{ route('artists.show', $artist) }}" class="text-sm font-bold text-brand-600 dark:text-brand-400 hover:underline">{{ $artist->name }}</a>
                    @if($artist->pivot->role)<span class="badge badge-surface text-[10px]">{{ $artist->pivot->role }}</span>@endif
                </div>
                <span class="text-[10px] text-surface-400">{{ $artist->pivot->joined_year ?? '?' }}&ndash;{{ $artist->pivot->left_year ?? 'present' }}</span>
            </div>
            @empty
            <p class="px-4 py-3 text-sm text-surface-400">No members recorded.</p>
            @endforelse
        </div>

        <!-- Discography -->
        @if($band->albums->count())
        <h2 class="text-base font-bold mt-10 mb-4 text-surface-900 dark:text-ink-200">Discography</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($band->albums as $album)
            <a href="{{ route('albums.show', $album) }}" class="block group">
                <div class="card-bandcamp">
                    <div class="aspect-square overflow-hidden">
                        @php $cover = $album->cover_art ? (str_starts_with($album->cover_art, 'http') ? $album->cover_art : Storage::url($album->cover_art)) : null; @endphp
                        @if($cover)
                        <img src="{{ $cover }}" alt="{{ $album->title }}" class="w-full h-full object-cover" loading="lazy">
                        @else
                        <div class="w-full h-full bg-surface-100 dark:bg-ink-900 flex items-center justify-center text-surface-300 dark:text-ink-600">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.5" d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        </div>
                        @endif
                        <div class="card-bandcamp-overlay">
                            <span class="text-white text-2xl font-light opacity-0 group-hover:opacity-100">&rarr;</span>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <h3 class="text-sm font-bold text-surface-900 dark:text-ink-100 truncate">{{ $album->title }}</h3>
                    @if($album->release_year)<p class="text-[10px] text-surface-400 dark:text-ink-600">{{ $album->release_year }}</p>@endif
                </div>
            </a>
            @endforeach
        </div>
        @endif

        <!-- Connection Graph -->
        @if(count($graph['nodes']) > 1)
        <h2 class="text-base font-bold mt-10 mb-4 text-surface-900 dark:text-ink-200">Connections</h2>
        <div class="border border-surface-200 dark:border-ink-700 bg-surface-50 dark:bg-ink-800 overflow-hidden" style="height:350px">
            <x-genealogy-graph :graph="$graph" containerId="band-graph" />
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <aside class="lg:w-64 mt-8 lg:mt-0 shrink-0 lg:sticky lg:top-16 self-start space-y-4">
        <div class="card bg-white dark:bg-ink-800 p-4">
            <div class="space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-surface-400">Members</span><span class="font-bold text-surface-900 dark:text-ink-200">{{ $band->artists->count() }}</span></div>
                @if($band->albums->count())<div class="flex justify-between"><span class="text-surface-400">Albums</span><span class="font-bold text-surface-900 dark:text-ink-200">{{ $band->albums->count() }}</span></div>@endif
                @if(count($related))<div class="flex justify-between"><span class="text-surface-400">Related</span><span class="font-bold text-surface-900 dark:text-ink-200">{{ count($related) }}</span></div>@endif
                @if($band->origin)<div class="flex justify-between"><span class="text-surface-400">Origin</span><span class="font-bold text-surface-900 dark:text-ink-200">{{ $band->origin }}</span></div>@endif
            </div>
        </div>
        <div><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
</div>
@endsection
