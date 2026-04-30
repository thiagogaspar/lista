@extends('layouts.app')

@section('head')
@php
$hero = $band->hero_image ? Storage::url($band->hero_image) : null;
$thumb = $band->photo ? Storage::url($band->photo) : null;
$slug = md5($band->slug);
$heroPlaceholder = $hero ?: $thumb ?: '';
$heroGradient = match($band->genres->first()?->slug) {
    'grunge' => 'from-emerald-800 via-emerald-700 to-emerald-900',
    'alternative-rock' => 'from-violet-800 via-violet-700 to-violet-900',
    'hard-rock' => 'from-red-800 via-red-700 to-red-900',
    'rap-metal' => 'from-amber-800 via-amber-700 to-amber-900',
    'heavy-metal' => 'from-indigo-800 via-indigo-700 to-indigo-900',
    'punk-rock' => 'from-pink-800 via-pink-700 to-pink-900',
    default => 'from-brand-800 via-brand-700 to-brand-900',
};
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
<link rel="preconnect" href="https://picsum.photos" crossorigin>
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
<link rel="dns-prefetch" href="https://picsum.photos">
<meta name="twitter:card" content="summary">
<meta name="twitter:image" content="{{ $thumbPlaceholder }}">
@endsection

@section('content')
<div class="relative -mx-4 -mt-8 mb-8 overflow-hidden bg-surface-100 dark:bg-surface-800" style="aspect-ratio:1200/400">
    @if($heroPlaceholder)
    <img src="{{ $heroPlaceholder }}" alt="{{ $band->name }}" class="w-full h-full object-cover" fetchpriority="high" decoding="async" sizes="100vw" width="1200" height="400">
    @else
    <div class="w-full h-full bg-gradient-to-br {{ $heroGradient }} flex items-center justify-center">
        <span class="text-white/30 text-6xl font-bold drop-shadow-lg select-none">{{ $band->name[0] }}</span>
    </div>
    @endif
</div>

<x-breadcrumb :items="[['label' => 'Home', 'url' => route('home')], ['label' => 'Bands', 'url' => route('bands.index')]]" :last="$band->name" />

<div class="lg:flex lg:gap-8">
    <div class="flex-1 min-w-0">
        <div class="flex items-start gap-3">
            <div class="shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-surface-100 dark:bg-surface-800 shadow-sm">
                <img src="{{ $thumbPlaceholder }}" alt="{{ $band->name }}" class="w-full h-full object-cover" loading="lazy" width="80" height="80">
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <h1 class="text-3xl sm:text-4xl font-bold text-surface-900 dark:text-white">{{ $band->name }}</h1>
                    @auth
                    <button x-data="{ fav: false, count: 0 }"
                        x-init="fetch('{{ route('favorites.toggle-band', $band) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; }).catch(() => {})"
                        @click.prevent="fetch('{{ route('favorites.toggle-band', $band) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; })"
                        class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg border border-surface-300 dark:border-surface-600 hover:bg-surface-100 dark:hover:bg-surface-700 transition-colors"
                        :class="fav ? 'text-red-500 border-red-300 dark:border-red-700' : 'text-surface-500 dark:text-surface-400'">
                        <svg class="w-4 h-4" :fill="fav ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span x-text="count"></span>
                    </button>
                    @endauth
                </div>
                <div class="flex flex-wrap gap-2 mt-2">
                    @if($band->formed_year)
                    <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 font-medium">{{ $band->formed_year }}–{{ $band->dissolved_year ?? 'present' }}</span>
                    @endif
                    @foreach($band->genres as $genre)
                    <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-full bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-300">{{ $genre->name }}</span>
                    @endforeach
                    @if($band->origin)
                    <span class="inline-flex items-center text-xs px-2.5 py-1 rounded-full bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-300">{{ $band->origin }}</span>
                    @endif
                    @if($band->label)
                    <a href="{{ route('bands.index', ['label' => $band->label->slug]) }}" class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full bg-brand-100 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 font-medium hover:underline">
                        @if($band->label->logo)<img src="{{ Storage::url($band->label->logo) }}" alt="" class="w-4 h-4 rounded object-cover">@endif
                        {{ $band->label->name }}
                    </a>
                    @endif
                    @foreach($band->tags->where('is_approved', true) as $tag)
                    <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full bg-accent-50 dark:bg-accent-900/30 text-accent-600 dark:text-accent-300">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        @if($band->bio)
        <div class="prose prose-surface dark:prose-invert max-w-none mt-4">{!! Str::markdown($band->bio) !!}</div>
        @endif

        <div class="mt-5" x-data="{ lightbox: null }">
            <div class="grid grid-cols-3 gap-2">
                @foreach(collect(is_array($gallery) ? $gallery : $gallery->toArray())->take(3) as $img)
                <div class="aspect-[4/3] rounded-lg overflow-hidden bg-surface-100 dark:bg-surface-800 cursor-pointer hover:opacity-90 transition-opacity" @click="lightbox = '{{ $img }}'">
                    <img src="{{ $img }}" alt="" class="w-full h-full object-cover" loading="lazy" decoding="async" sizes="(max-width:640px) 50vw, 33vw" width="400" height="300">
                </div>
                @endforeach
            </div>
            <template x-teleport="body">
                <div x-show="lightbox" x-cloak class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4" @click="lightbox = null" @keydown.escape="lightbox = null">
                    <img :src="lightbox" class="max-w-full max-h-full object-contain rounded-lg" @click.stop>
                    <button class="absolute top-4 right-4 text-white/80 hover:text-white text-2xl" @click="lightbox = null">&times;</button>
                </div>
            </template>
        </div>

        <h2 class="text-2xl font-bold text-surface-900 dark:text-white mt-12 mb-4">
            Members <span class="text-xs font-normal text-surface-400">({{ $band->artists->count() }})</span>
        </h2>
        <div class="divide-y divide-surface-100 dark:divide-surface-700 border border-surface-200 dark:border-surface-700 rounded-lg overflow-hidden bg-white dark:bg-surface-800">
            @forelse($band->artists as $artist)
            <div class="flex items-center justify-between px-3 py-2.5 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('artists.show', $artist) }}" class="font-medium text-sm text-accent-600 dark:text-accent-400 hover:underline">{{ $artist->name }}</a>
                    @if($artist->pivot->role)<span class="text-xs text-surface-500 dark:text-surface-400 bg-surface-100 dark:bg-surface-700 px-1.5 py-0.5 rounded">{{ $artist->pivot->role }}</span>@endif
                </div>
                <span class="text-xs text-surface-400">{{ $artist->pivot->joined_year ?? '?' }}–{{ $artist->pivot->left_year ?? 'present' }}</span>
            </div>
            @empty
            <p class="px-3 py-2 text-surface-500 text-sm">No members.</p>
            @endforelse
        </div>

        @if($band->albums->count())
        <h2 class="text-2xl font-bold text-surface-900 dark:text-white mt-12 mb-4">Discography</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($band->albums as $album)
            <div class="p-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg">
                @if($album->cover_art)
                <img src="{{ Storage::url($album->cover_art) }}" alt="{{ $album->title }}" class="w-full aspect-square object-cover rounded-lg mb-2" loading="lazy">
                @else
                <div class="w-full aspect-square rounded-lg mb-2 bg-surface-100 dark:bg-surface-700 flex items-center justify-center text-surface-400">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                </div>
                @endif
                <h3 class="font-semibold text-sm text-surface-900 dark:text-white truncate">{{ $album->title }}</h3>
                @if($album->release_year)<p class="text-xs text-surface-400">{{ $album->release_year }}</p>@endif
            </div>
            @endforeach
        </div>
        @endif

        @if(count($graph['nodes']) > 1)
        <h2 class="text-xl font-bold text-surface-900 dark:text-white mt-8 mb-3">Connection Graph</h2>
        <x-genealogy-graph :graph="$graph" containerId="band-graph" />
        @endif

        @if(count($related))
        <h2 class="text-xl font-bold text-surface-900 dark:text-white mt-8 mb-3">Related Bands</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            @foreach($related as $rel)
            <div class="p-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg">
                <span class="inline-flex text-xs px-2 py-0.5 rounded-full bg-warm-100 dark:bg-warm-900/30 text-warm-700 dark:text-warm-300 font-medium uppercase">{{ \App\Models\BandRelationship::types()[$rel->type] ?? $rel->type }}</span>
                <p class="mt-2 text-sm">
                    @if($rel->parent_band_id === $band->id)
                    <span class="text-surface-700 dark:text-surface-200">{{ $band->name }}</span> → <a href="{{ route('bands.show', $rel->childBand) }}" class="text-brand-600 dark:text-brand-400 hover:underline font-medium">{{ $rel->childBand->name }}</a>
                    @else
                    <a href="{{ route('bands.show', $rel->parentBand) }}" class="text-brand-600 dark:text-brand-400 hover:underline font-medium">{{ $rel->parentBand->name }}</a> → <span class="text-surface-700 dark:text-surface-200">{{ $band->name }}</span>
                    @endif
                </p>
                @if($rel->year)<p class="text-xs text-surface-400 mt-1">{{ $rel->year }}</p>@endif
                @if($rel->description)<p class="text-xs text-surface-500 mt-1">{{ $rel->description }}</p>@endif
            </div>
            @endforeach
        </div>
        @endif

        <h2 class="text-xl font-bold text-surface-900 dark:text-white mt-8 mb-3">Comments</h2>
        @auth
        <form method="POST" action="{{ route('comments.store') }}" class="mb-4">
            @csrf
            <input type="hidden" name="commentable_type" value="band">
            <input type="hidden" name="commentable_id" value="{{ $band->id }}">
            <textarea name="body" rows="2" placeholder="Share your thoughts..." class="w-full px-3 py-2 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 placeholder-surface-400 focus:outline-none focus:ring-2 focus:ring-brand-500" required></textarea>
            <button type="submit" class="mt-1 px-4 py-1.5 text-sm bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors font-medium">Post Comment</button>
        </form>
        @else
        <p class="text-sm text-surface-500 mb-4"><a href="{{ route('filament.admin.auth.login') }}" class="text-brand-600 hover:underline">Log in</a> to leave a comment.</p>
        @endauth

        <div class="space-y-3">
            @forelse($band->comments->where('is_approved', true) as $comment)
            <div class="p-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg">
                <p class="text-xs text-surface-400 mb-1">{{ $comment->user?->name ?? 'Anonymous' }} · {{ $comment->created_at->diffForHumans() }}</p>
                <p class="text-sm text-surface-700 dark:text-surface-200">{{ $comment->body }}</p>
            </div>
            @empty
            <p class="text-sm text-surface-500">No comments yet. Be the first!</p>
            @endforelse
        </div>
    </div>
    <aside class="lg:w-64 mt-6 lg:mt-0 shrink-0 lg:sticky lg:top-20 self-start"><x-ad-slot position="sidebar" /></aside>
</div>
@endsection
