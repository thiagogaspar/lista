@extends('layouts.app')

@section('head')
@php
$hero = $band->hero_image ? Storage::url($band->hero_image) : null;
$thumb = $band->photo ? Storage::url($band->photo) : null;
$slug = md5($band->slug);
$heroPlaceholder = $hero ?: $thumb ?: '';
$thumbPlaceholder = $thumb ?: "https://picsum.photos/seed/{$slug}/400/400";
$gallery = $band->gallery && count($band->gallery) ? $band->gallery : [
    "https://picsum.photos/seed/{$slug}-1/600/400",
    "https://picsum.photos/seed/{$slug}-2/600/400",
    "https://picsum.photos/seed/{$slug}-3/600/400",
];
$seo = new \App\Values\SeoData(
    title: $band->name,
    description: Str::limit(strip_tags($band->bio ?? 'Learn about ' . $band->name . ' and their musical journey.'), 160),
    type: 'music.group',
    image: $thumbPlaceholder,
    canonical: route('bands.show', $band),
    schema: json_encode(['@context'=>'https://schema.org','@type'=>'MusicGroup','name'=>$band->name,'url'=>route('bands.show',$band)], JSON_UNESCAPED_SLASHES),
);
@endphp
<x-seo-meta :seo="$seo" />
<link rel="preload" href="{{ $heroPlaceholder ?: $thumbPlaceholder }}" as="image" fetchpriority="high">
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
<meta name="twitter:card" content="summary">
<meta name="twitter:image" content="{{ $thumbPlaceholder }}">
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<!-- Hero -->
<section class="relative -mx-4 -mt-6 mb-10 overflow-hidden bg-ink dark:bg-ink-900" style="aspect-ratio:16/5; max-height:55vh;">
    @if($heroPlaceholder)
    <img src="{{ $heroPlaceholder }}" alt="{{ $band->name }}" class="absolute inset-0 w-full h-full object-cover opacity-50 dark:opacity-35" fetchpriority="high" decoding="async" sizes="100vw" width="1200" height="375">
    @else
    <div class="absolute inset-0 bg-gradient-to-br from-brand-500/40 via-accent-500/30 to-warm-500/20"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/30 to-transparent flex items-end p-6 sm:p-10">
        <h1 class="font-display text-3xl sm:text-5xl md:text-6xl font-bold text-white leading-none tracking-tight">{{ $band->name }}</h1>
    </div>
</section>

<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-xs text-surface-400 mb-8 uppercase tracking-wider">
    <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a><span>/</span>
    <a href="{{ route('bands.index') }}" class="hover:text-brand-600">Bands</a><span>/</span>
    <span class="text-surface-700 dark:text-ink-200 font-medium">{{ $band->name }}</span>
</nav>

<div class="lg:flex lg:gap-10">
    <div class="flex-1 min-w-0">
        <!-- Thumb + Meta -->
        <div class="flex items-start gap-4">
            <img src="{{ $thumbPlaceholder }}" alt="{{ $band->name }}" class="w-20 h-20 object-cover shrink-0" loading="lazy" width="100" height="100" style="border:1px solid var(--color-surface-200)">
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex flex-wrap gap-2 mt-1">
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
                        <a href="{{ route('bands.index', ['label' => $band->label->slug]) }}" class="badge badge-warm mt-2 inline-flex gap-1.5 hover:opacity-80 transition-opacity">
                            @if($band->label->logo)<img src="{{ Storage::url($band->label->logo) }}" alt="" class="w-3 h-3 rounded-sm object-cover">@endif
                            {{ $band->label->name }}
                        </a>
                        @endif
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach($band->tags->where('is_approved', true) as $tag)
                            <span class="badge badge-accent">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @auth
                    <button x-data="{ fav: false, count: 0 }"
                        x-init="fetch('{{ route('favorites.toggle-band', $band) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; }).catch(() => {})"
                        @click.prevent="fetch('{{ route('favorites.toggle-band', $band) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; })"
                        class="btn btn-icon shrink-0 transition-colors"
                        :class="fav ? 'btn-error' : 'btn-ghost'" title="Favorite">
                        <svg class="w-4 h-4" :fill="fav ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span x-text="count" class="text-[10px] font-bold"></span>
                    </button>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Bio -->
        @if($band->bio)
        <div class="prose max-w-none mt-6">{!! \Stevebauman\Purify\Facades\Purify::clean(Str::markdown($band->bio)) !!}</div>
        @endif

        <!-- Gallery -->
        <div class="mt-6" x-data="{ lightbox: null }">
            <div class="grid grid-cols-3 gap-2">
                @foreach(collect(is_array($gallery) ? $gallery : $gallery->toArray())->take(3) as $img)
                <div class="aspect-[4/3] cursor-pointer group relative overflow-hidden" @click="lightbox = '{{ $img }}'" style="border:1px solid var(--color-surface-200)">
                    <img src="{{ $img }}" alt="" class="w-full h-full object-cover transition-transform group-hover:scale-105" loading="lazy" decoding="async" sizes="(max-width:640px) 50vw, 33vw" width="400" height="300">
                </div>
                @endforeach
            </div>
            <template x-teleport="body">
                <div x-show="lightbox" x-cloak class="fixed inset-0 z-[60] bg-ink/95 flex items-center justify-center p-4" @click="lightbox = null" @keydown.escape="lightbox = null">
                    <img :src="lightbox" class="max-w-full max-h-full object-contain" @click.stop style="border:1px solid var(--color-surface-700)">
                    <button class="absolute top-4 right-4 text-white/70 hover:text-white text-2xl font-bold" @click="lightbox = null">&times;</button>
                </div>
            </template>
        </div>

        <!-- Members -->
        <h2 class="font-display text-2xl font-bold mt-12 mb-5 text-surface-900 dark:text-ink-200">
            Members <span class="text-base font-sans font-normal text-surface-400">({{ $band->artists->count() }})</span>
        </h2>
        <div class="border border-surface-200 dark:border-ink-700 overflow-hidden bg-white dark:bg-ink-800">
            @forelse($band->artists as $artist)
            <div class="flex items-center justify-between px-4 py-3 hover:bg-surface-50 dark:hover:bg-ink-700/50 transition-colors border-b border-surface-200 dark:border-ink-700 last:border-0">
                <div class="flex items-center gap-3">
                    <a href="{{ route('artists.show', $artist) }}" class="font-display font-bold text-sm text-accent-600 dark:text-accent-400 hover:underline">{{ $artist->name }}</a>
                    @if($artist->pivot->role)<span class="badge badge-accent">{{ $artist->pivot->role }}</span>@endif
                </div>
                <span class="text-[10px] text-surface-400 uppercase tracking-wider">{{ $artist->pivot->joined_year ?? '?' }}&ndash;{{ $artist->pivot->left_year ?? 'present' }}</span>
            </div>
            @empty
            <p class="px-4 py-3 text-sm text-surface-400">No members recorded.</p>
            @endforelse
        </div>

        <!-- Discography -->
        @if($band->albums->count())
        <h2 class="font-display text-2xl font-bold mt-12 mb-5 text-surface-900 dark:text-ink-200">Discography</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($band->albums as $album)
            <div class="card card-hover bg-white dark:bg-ink-800">
                <div class="p-3">
                    @if($album->cover_art)
                    <img src="{{ Storage::url($album->cover_art) }}" alt="{{ $album->title }}" class="w-full aspect-square object-cover mb-3" loading="lazy" style="border:1px solid var(--color-surface-200)">
                    @else
                    <div class="w-full aspect-square mb-3 bg-surface-100 dark:bg-ink-700 flex items-center justify-center text-surface-300 dark:text-ink-600">
                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </div>
                    @endif
                    <h3 class="font-display font-bold text-sm text-surface-800 dark:text-ink-200">{{ $album->title }}</h3>
                    @if($album->release_year)<p class="text-[10px] text-surface-400 uppercase tracking-wider mt-0.5">{{ $album->release_year }}</p>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Connection Graph -->
        @if(count($graph['nodes']) > 1)
        <h2 class="font-display text-2xl font-bold mt-12 mb-5 text-surface-900 dark:text-ink-200">Connection Graph</h2>
        <div class="border border-surface-200 dark:border-ink-700 bg-surface-50 dark:bg-ink-800 overflow-hidden" style="height:400px">
            <x-genealogy-graph :graph="$graph" containerId="band-graph" />
        </div>
        @endif

        <!-- Related Bands -->
        @if(count($related))
        <h2 class="font-display text-2xl font-bold mt-12 mb-5 text-surface-900 dark:text-ink-200">Related Bands</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($related as $rel)
            <div class="card bg-white dark:bg-ink-800 p-4">
                <span class="badge badge-warm uppercase text-[9px] font-bold">{{ \App\Models\BandRelationship::types()[$rel->type] ?? $rel->type }}</span>
                <p class="mt-2 text-sm text-surface-700 dark:text-ink-200">
                    @if($rel->parent_band_id === $band->id)
                    <span class="font-display font-bold text-brand-600 dark:text-brand-400">{{ $band->name }}</span>
                    {{ $rel->description ?? 'is related to' }}
                    <a href="{{ route('bands.show', $rel->childBand) }}" class="font-display font-bold text-brand-600 dark:text-brand-400 hover:underline">{{ $rel->childBand->name }}</a>
                    @else
                    <a href="{{ route('bands.show', $rel->parentBand) }}" class="font-display font-bold text-brand-600 dark:text-brand-400 hover:underline">{{ $rel->parentBand->name }}</a>
                    {{ $rel->description ?? 'is related to' }}
                    <span class="font-display font-bold text-brand-600 dark:text-brand-400">{{ $band->name }}</span>
                    @endif
                </p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <aside class="lg:w-64 mt-8 lg:mt-0 shrink-0 lg:sticky lg:top-16 self-start space-y-4">
        <div class="card bg-white dark:bg-ink-800 p-4">
            <h3 class="font-display text-sm font-bold mb-3 text-surface-700 dark:text-ink-200">Stats</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-surface-400">Members</span><span class="font-bold text-surface-700 dark:text-ink-200">{{ $band->artists->count() }}</span></div>
                @if($band->albums->count())<div class="flex justify-between"><span class="text-surface-400">Albums</span><span class="font-bold text-surface-700 dark:text-ink-200">{{ $band->albums->count() }}</span></div>@endif
                @if(count($related))<div class="flex justify-between"><span class="text-surface-400">Related</span><span class="font-bold text-surface-700 dark:text-ink-200">{{ count($related) }}</span></div>@endif
            </div>
        </div>
        <div><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
</div>
@endsection
