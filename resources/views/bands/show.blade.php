@extends('layouts.app')

@section('head')
@php
$photo = $band->photo;
$bandPhotoUrl = $photo ? (str_starts_with($photo, 'http') ? $photo : Storage::url($photo)) : null;
$heroImg = $band->hero_image ? (str_starts_with($band->hero_image, 'http') ? $band->hero_image : Storage::url($band->hero_image)) : null;
$heroPlaceholder = $heroImg ?: $bandPhotoUrl;

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
<!-- Hero — foto sem shapes -->
<section class="relative -mx-4 -mt-6 mb-8 overflow-hidden bg-black" style="aspect-ratio:16/4; max-height:45vh;">
    @if($heroPlaceholder)
    <img src="{{ $heroPlaceholder }}" alt="{{ $band->name }}" class="absolute inset-0 w-full h-full object-cover opacity-30" fetchpriority="high" decoding="async" sizes="100vw">
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-black/20"></div>
    <div class="relative z-10 flex flex-col justify-end h-full">
        <div class="max-w-6xl mx-auto px-4 w-full pb-8 sm:pb-12 pt-6">
            <h1 class="font-display text-3xl sm:text-5xl md:text-6xl font-black text-white leading-none tracking-tight">{{ $band->name }}</h1>
            <div class="flex flex-wrap gap-2 mt-3">
                @if($band->formed_year)<span class="badge text-white/70 border-white/20">{{ $band->formed_year }}&ndash;{{ $band->dissolved_year ?? 'present' }}</span>@endif
                @foreach($band->genres->take(3) as $genre)<span class="badge text-white/50 border-white/15">{{ $genre->name }}</span>@endforeach
            </div>
        </div>
    </div>
</section>

<nav class="breadcrumb mb-6">
    <a href="{{ route('home') }}">Home</a><span>/</span>
    <a href="{{ route('bands.index') }}">Bands</a><span>/</span>
    <span>{{ $band->name }}</span>
</nav>

<div class="lg:flex lg:gap-10">
    <div class="flex-1 min-w-0 order-2 lg:order-1">
        <!-- Header compacto -->
        <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-surface-200 dark:border-ink-700">
            @if($bandPhotoUrl)
            <img src="{{ $bandPhotoUrl }}" alt="{{ $band->name }}" class="w-14 h-14 object-cover shrink-0 border-2 border-surface-200 dark:border-ink-600" loading="lazy">
            @endif
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    @if($band->label)
                    <a href="{{ route('labels.show', $band->label) }}" class="font-display text-xs font-bold uppercase tracking-wider text-surface-400 hover:text-brand-600 dark:hover:text-brand-400">{{ $band->label->name }}</a>
                    @endif
                    @if($band->origin)
                    <span class="text-xs text-surface-400">&middot; {{ $band->origin }}</span>
                    @endif
                </div>
                @auth
                @php $favCount = $band->favorites()->count(); @endphp
                <button x-data="{ fav: {{ auth()->user() && auth()->user()->hasFavorited($band) ? 'true' : 'false' }}, count: {{ $favCount }} }"
                    @click.prevent="fetch('{{ route('favorites.toggle-band', $band) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; })"
                    class="font-display text-xs font-bold mt-1 hover:text-brand-600 dark:hover:text-brand-400" :class="fav ? 'text-brand-600 dark:text-brand-400' : 'text-surface-400'" title="Favorite">
                    <span x-text="fav ? '&hearts;' : '&loz;'"></span> <span x-text="count"></span>
                </button>
                @endauth
            </div>
        </div>

        <!-- Bio -->
        @if($band->bio)
        <div class="prose max-w-none mb-8">{!! \Stevebauman\Purify\Facades\Purify::clean(Str::markdown($band->bio)) !!}</div>
        @endif

        <!-- Members -->
        <x-section-header tag="h2" :count="$band->artists->count()">Members</x-section-header>
        <x-data-table>
            @forelse($band->artists as $artist)
            <div class="flex items-center justify-between px-4 py-2.5 border-b-2 border-surface-200 dark:border-ink-700 last:border-0 hover:bg-surface-50 dark:hover:bg-ink-700/50">
                <div class="flex items-center gap-2 min-w-0">
                    <a href="{{ route('artists.show', $artist) }}" class="font-display text-sm font-bold text-brand-600 dark:text-brand-400 hover:underline truncate">{{ $artist->name }}</a>
                    @if($artist->pivot->role)<span class="badge badge-surface text-[10px] shrink-0">{{ $artist->pivot->role }}</span>@endif
                </div>
                <span class="font-display text-[10px] font-bold text-surface-400 shrink-0 ml-3">{{ $artist->pivot->joined_year ?? '?' }}&ndash;{{ $artist->pivot->left_year ?? 'present' }}</span>
            </div>
            @empty
            <p class="px-4 py-3 text-sm text-surface-400">Nenhum membro registrado.</p>
            @endforelse
        </x-data-table>

        <!-- Discography -->
        @if($band->albums->count())
        <x-section-header tag="h2" :count="$band->albums->count()" class="mt-10">Discografia</x-section-header>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($band->albums as $album)
            <a href="{{ route('albums.show', $album) }}" class="group">
                <div class="border-2 border-surface-200 dark:border-ink-700 bg-white dark:bg-ink-800 hover:border-brand-500 dark:hover:border-brand-400 transition-colors">
                    @php $cover = $album->cover_art ? (str_starts_with($album->cover_art, 'http') ? $album->cover_art : Storage::url($album->cover_art)) : null; @endphp
                    @if($cover)
                    <img src="{{ $cover }}" alt="{{ $album->title }}" class="w-full aspect-square object-cover" loading="lazy">
                    @else
                    <div class="w-full aspect-square bg-surface-100 dark:bg-ink-900 flex items-center justify-center text-surface-300 dark:text-ink-400">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.5" d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </div>
                    @endif
                </div>
                <div class="mt-2">
                    <h3 class="font-display text-sm font-bold text-surface-900 dark:text-ink-100 truncate group-hover:text-brand-600 dark:group-hover:text-brand-400">{{ $album->title }}</h3>
                    @if($album->release_year)<p class="font-display text-[10px] font-bold text-surface-400 dark:text-ink-400">{{ $album->release_year }}</p>@endif
                </div>
            </a>
            @endforeach
        </div>
        @endif

        <!-- Connection Graph -->
        @if(count($graph['nodes']) > 1)
        <x-section-header tag="h2" class="mt-10">Conexões</x-section-header>
        <div class="border-2 border-surface-200 dark:border-ink-700 bg-surface-50 dark:bg-ink-800 overflow-hidden" style="height:350px">
            <x-genealogy-graph :graph="$graph" containerId="band-graph" />
        </div>
        @endif

    </div>

    <!-- Infobox — Wikipedia style -->
    <aside class="lg:w-72 mt-8 lg:mt-0 shrink-0 self-start order-1 lg:order-2 lg:sticky lg:top-16">
        <x-infobox :title="$band->name" :items="[
            'Members' => (string) $band->artists->count(),
            'Albums' => $band->albums->count() ? (string) $band->albums->count() : null,
            'Formed' => $band->formed_year ? (string) $band->formed_year : null,
            'Dissolved' => $band->dissolved_year ? (string) $band->dissolved_year : null,
            'Origin' => $band->origin ? e($band->origin) : null,
            'Label' => $band->label ? '<a href=\"' . route('labels.show', $band->label) . '\" class=\"link text-xs\">' . e($band->label->name) . '</a>' : null,
            'Genres' => $band->genres->count() ? $band->genres->pluck('name')->implode(', ') : null,
        ]" />
        <div class="mt-4"><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
</div>
@endsection
