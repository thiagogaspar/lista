@extends('layouts.app')

@section('head')
@php
$hero = $band->hero_image ? Storage::url($band->hero_image) : null;
$thumb = $band->photo ? Storage::url($band->photo) : null;
$slug = md5($band->slug);
$heroPlaceholder = $hero ?: $thumb ?: '';
$heroGradient = match($band->genres->first()?->slug) {
    'grunge' => 'from-primary to-primary/70',
    'alternative-rock' => 'from-secondary to-secondary/70',
    'hard-rock' => 'from-error to-error/70',
    'rap-metal' => 'from-warning to-warning/70',
    'heavy-metal' => 'from-info to-info/70',
    'punk-rock' => 'from-secondary to-primary/70',
    default => 'from-primary to-info',
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
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
<meta name="twitter:card" content="summary">
<meta name="twitter:image" content="{{ $thumbPlaceholder }}">
@endsection

@section('content')
<div class="relative -mx-4 -mt-8 mb-10 overflow-hidden bg-base-200" style="aspect-ratio:1200/350">
    @if($heroPlaceholder)
    <img src="{{ $heroPlaceholder }}" alt="{{ $band->name }}" class="w-full h-full object-cover" fetchpriority="high" decoding="async" sizes="100vw" width="1200" height="350">
    @else
    <div class="w-full h-full bg-gradient-to-br {{ $heroGradient }} flex items-center justify-center">
        <span class="text-white/30 text-7xl font-bold drop-shadow-lg select-none">{{ $band->name[0] }}</span>
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
                    <h1 class="text-3xl sm:text-4xl font-bold text-base-content">{{ $band->name }}</h1>
                    @auth
                    <button x-data="{ fav: false, count: 0 }"
                        x-init="fetch('{{ route('favorites.toggle-band', $band) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; }).catch(() => {})"
                        @click.prevent="fetch('{{ route('favorites.toggle-band', $band) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(d => { fav = d.favorited; count = d.count; })"
                        class="btn btn-ghost btn-sm btn-square"
                        :class="fav ? 'text-error' : 'text-base-content/40'">
                        <svg class="w-4 h-4" :fill="fav ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span x-text="count" class="font-medium"></span>
                    </button>
                    @endauth
                </div>
                <div class="flex flex-wrap gap-2 mt-2">
                    @if($band->formed_year)
                    <span class="badge badge-sm bg-primary/10 text-primary border-none">{{ $band->formed_year }}–{{ $band->dissolved_year ?? 'present' }}</span>
                    @endif
                    @foreach($band->genres as $genre)
                    <span class="badge badge-sm badge-ghost">{{ $genre->name }}</span>
                    @endforeach
                    @if($band->origin)
                    <span class="badge badge-sm badge-ghost">{{ $band->origin }}</span>
                    @endif
                    @if($band->label)
                    <a href="{{ route('bands.index', ['label' => $band->label->slug]) }}" class="badge badge-sm gap-1.5 bg-primary/10 text-primary border-none hover:bg-primary/20">
                        @if($band->label->logo)<img src="{{ Storage::url($band->label->logo) }}" alt="" class="w-3 h-3 rounded object-cover">@endif
                        {{ $band->label->name }}
                    </a>
                    @endif
                    @foreach($band->tags->where('is_approved', true) as $tag)
                    <span class="badge badge-sm bg-secondary/10 text-secondary border-none">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        @if($band->bio)
        <div class="prose max-w-none mt-4">{!! \Stevebauman\Purify\Facades\Purify::clean(Str::markdown($band->bio)) !!}</div>
        @endif

        <div class="mt-5" x-data="{ lightbox: null }">
            <div class="grid grid-cols-3 gap-2">
                @foreach(collect(is_array($gallery) ? $gallery : $gallery->toArray())->take(3) as $img)
                <div class="aspect-[4/3] rounded-lg overflow-hidden bg-base-200 cursor-pointer hover:opacity-90 transition-opacity" @click="lightbox = '{{ $img }}'">
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

        <h2 class="text-2xl font-bold text-base-content mt-12 mb-4">
            Members <span class="text-base font-normal text-base-content/50">({{ $band->artists->count() }})</span>
        </h2>
        <div class="divide-y divide-base-300 border border-base-300 rounded-xl overflow-hidden bg-base-100 shadow-sm">
            @forelse($band->artists as $artist)
            <div class="flex items-center justify-between px-4 py-3 hover:bg-base-200 transition-colors">
                <div class="flex items-center gap-2">
                    <a href="{{ route('artists.show', $artist) }}" class="font-medium text-sm text-secondary hover:underline">{{ $artist->name }}</a>
                    @if($artist->pivot->role)<span class="badge badge-sm bg-secondary/10 text-secondary border-none">{{ $artist->pivot->role }}</span>@endif
                </div>
                <span class="text-xs text-base-content/50">{{ $artist->pivot->joined_year ?? '?' }}–{{ $artist->pivot->left_year ?? 'present' }}</span>
            </div>
            @empty
            <p class="px-4 py-3 text-base-content/50 text-sm">No members.</p>
            @endforelse
        </div>

        @if($band->albums->count())
        <h2 class="text-2xl font-bold text-base-content mt-12 mb-4">Discography</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($band->albums as $album)
            <div class="card card-compact bg-base-100 shadow-md hover:shadow-xl transition-shadow">
                <figure class="px-3 pt-3">
                    @if($album->cover_art)
                    <img src="{{ Storage::url($album->cover_art) }}" alt="{{ $album->title }}" class="w-full aspect-square object-cover rounded-lg" loading="lazy">
                    @else
                    <div class="w-full aspect-square rounded-lg bg-base-200 flex items-center justify-center text-base-content/30">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    </div>
                    @endif
                </figure>
                <div class="card-body p-3">
                    <h3 class="card-title text-sm">{{ $album->title }}</h3>
                    @if($album->release_year)<p class="text-xs text-base-content/50">{{ $album->release_year }}</p>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if(count($graph['nodes']) > 1)
        <h2 class="text-xl font-bold text-base-content mt-8 mb-3">Connection Graph</h2>
        <x-genealogy-graph :graph="$graph" containerId="band-graph" />
        @endif

        @if(count($related))
        <h2 class="text-xl font-bold text-base-content mt-8 mb-3">Related Bands</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($related as $rel)
            <div class="card card-compact bg-base-100 shadow-sm border border-base-300">
                <div class="card-body p-4">
                    <span class="badge badge-sm bg-accent/10 text-accent border-none uppercase">{{ \App\Models\BandRelationship::types()[$rel->type] ?? $rel->type }}</span>
                    <p class="mt-2 text-sm">
                        @if($rel->parent_band_id === $band->id)
                        <span class="text-base-content">{{ $band->name }}</span> → <a href="{{ route('bands.show', $rel->childBand) }}" class="text-primary hover:underline font-medium">{{ $rel->childBand->name }}</a>
                        @else
                        <a href="{{ route('bands.show', $rel->parentBand) }}" class="text-primary hover:underline font-medium">{{ $rel->parentBand->name }}</a> → <span class="text-base-content">{{ $band->name }}</span>
                        @endif
                    </p>
                    @if($rel->year)<p class="text-xs text-base-content/50 mt-1">{{ $rel->year }}</p>@endif
                    @if($rel->description)<p class="text-xs text-base-content/50 mt-1">{{ $rel->description }}</p>@endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @auth
        <details class="mt-8 group">
            <summary class="text-sm font-semibold text-surface-700 dark:text-surface-200 cursor-pointer hover:text-brand-600">Suggest an edit ✏️</summary>
            <form method="POST" action="{{ route('suggestions.store') }}" class="mt-3 p-4 bg-surface-50 dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700 rounded-lg">
                @csrf
                <input type="hidden" name="suggestable_type" value="band">
                <input type="hidden" name="suggestable_id" value="{{ $band->id }}">
                <div class="flex gap-2 mb-2">
                    <select name="field" class="select select-bordered select-sm w-1/3" required>
                        <option value="">Field</option>
                        <option value="bio">Bio</option>
                        <option value="origin">Origin</option>
                        <option value="formed_year">Formed Year</option>
                        <option value="dissolved_year">Dissolved Year</option>
                    </select>
                    <textarea name="suggested_value" rows="2" placeholder="Your suggestion..." class="textarea textarea-bordered textarea-sm flex-1" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-xs">Submit</button>
            </form>
        </details>
        @endauth

        <h2 class="text-xl font-bold text-base-content mt-8 mb-3">Comments</h2>
        @auth
        <form method="POST" action="{{ route('comments.store') }}" class="mb-4">
            @csrf
            <input type="hidden" name="commentable_type" value="band">
            <input type="hidden" name="commentable_id" value="{{ $band->id }}">
            <textarea name="body" rows="2" placeholder="Share your thoughts..." class="textarea textarea-bordered w-full" required></textarea>
            <button type="submit" class="btn btn-primary btn-sm mt-2">Post Comment</button>
        </form>
        @else
        <p class="text-sm text-base-content/50 mb-4"><a href="{{ route('filament.admin.auth.login') }}" class="link link-primary">Log in</a> to leave a comment.</p>
        @endauth

        <div class="space-y-3">
            @forelse($band->comments->where('is_approved', true) as $comment)
            <div class="card card-compact bg-base-100 shadow-sm">
                <div class="card-body p-4">
                    <p class="text-xs text-base-content/50 mb-1">{{ $comment->user?->name ?? 'Anonymous' }} · {{ $comment->created_at->diffForHumans() }}</p>
                    <p class="text-sm text-base-content">{{ $comment->body }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-base-content/50">No comments yet. Be the first!</p>
            @endforelse
        </div>
    </div>
    <aside class="lg:w-64 mt-6 lg:mt-0 shrink-0 lg:sticky lg:top-20 self-start"><x-ad-slot position="sidebar" /></aside>
</div>
@endsection
