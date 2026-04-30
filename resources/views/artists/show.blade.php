@extends('layouts.app')

@section('head')
@php
$hero = $artist->hero_image ? Storage::url($artist->hero_image) : null;
$thumb = $artist->photo ? Storage::url($artist->photo) : null;
$slug = md5($artist->slug);
$heroPlaceholder = $hero ?: "https://picsum.photos/seed/{$slug}-hero/1200/400";
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
<link rel="preload" href="{{ $heroPlaceholder }}" as="image" fetchpriority="high">
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="preconnect" href="https://picsum.photos" crossorigin>
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
<link rel="dns-prefetch" href="https://picsum.photos">
<meta name="twitter:card" content="summary">
<meta name="twitter:image" content="{{ $thumbPlaceholder }}">
@endsection

@section('content')
<div class="relative -mx-4 -mt-8 mb-8 overflow-hidden bg-surface-100 dark:bg-surface-800" style="aspect-ratio:1200/400">
    <img src="{{ $heroPlaceholder }}" alt="{{ $artist->name }}" class="w-full h-full object-cover" fetchpriority="high" decoding="async" sizes="100vw" width="1200" height="400">
    @if(!$hero)
    <div class="absolute inset-0 flex items-center justify-center bg-accent-500/20 dark:bg-accent-500/10">
        <span class="text-white/80 dark:text-white/50 text-lg font-semibold drop-shadow-lg">{{ $artist->name }}</span>
    </div>
    @endif
</div>

<nav class="flex items-center gap-2 text-sm text-surface-400 mb-6">
    <a href="{{ route('home') }}" class="hover:text-accent-600">Home</a><span>/</span>
    <a href="{{ route('artists.index') }}" class="hover:text-accent-600">Artists</a><span>/</span>
    <span class="text-surface-700 dark:text-surface-200 font-medium">{{ $artist->name }}</span>
</nav>

<div class="lg:flex lg:gap-8">
    <div class="flex-1 min-w-0">
        <div class="flex items-start gap-3">
            <div class="shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-surface-100 dark:bg-surface-800 shadow-sm">
                <img src="{{ $thumbPlaceholder }}" alt="{{ $artist->name }}" class="w-full h-full object-cover" loading="lazy" width="80" height="80">
            </div>
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-surface-900 dark:text-white">{{ $artist->name }}</h1>
                <div class="flex flex-wrap gap-2 mt-2">
                    @if($artist->birth_date)<span class="inline-flex items-center text-xs px-2.5 py-1 rounded-full bg-accent-50 dark:bg-accent-900/30 text-accent-700 dark:text-accent-300 font-medium">{{ $artist->birth_date->format('Y') }}@if($artist->death_date)–{{ $artist->death_date->format('Y') }}@endif</span>@endif
                    @if($artist->origin)<span class="inline-flex items-center text-xs px-2.5 py-1 rounded-full bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-300">{{ $artist->origin }}</span>@endif
                    @foreach($artist->tags->where('is_approved', true) as $tag)
                    <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full bg-accent-50 dark:bg-accent-900/30 text-accent-600 dark:text-accent-300">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        @if($artist->bio)<div class="prose prose-surface dark:prose-invert max-w-none mt-4">{!! Str::markdown($artist->bio) !!}</div>@endif

        <div class="mt-5">
            <div class="grid grid-cols-3 gap-2">
                @foreach(collect(is_array($gallery) ? $gallery : $gallery->toArray())->take(3) as $img)
                <div class="aspect-[4/3] rounded-lg overflow-hidden bg-surface-100 dark:bg-surface-800">
                    <img src="{{ $img }}" alt="" class="w-full h-full object-cover" loading="lazy" decoding="async" sizes="(max-width:640px) 50vw, 33vw" width="400" height="300">
                </div>
                @endforeach
            </div>
        </div>

        <h2 class="text-xl font-bold text-surface-900 dark:text-white mt-8 mb-4">Band History</h2>
        <div class="relative pl-8">
            <div class="absolute left-[15px] top-1 bottom-0 w-0.5 bg-surface-200 dark:bg-surface-700"></div>
            @forelse($artist->bands as $band)
            <div class="relative pb-8 last:pb-0">
                <div class="absolute -left-8 top-1 w-[30px] h-[30px] rounded-full bg-accent-500 flex items-center justify-center shadow-sm"><div class="w-2 h-2 bg-white rounded-full"></div></div>
                <div class="absolute -left-[26px] top-[32px] text-[10px] font-medium text-accent-600 dark:text-accent-400 whitespace-nowrap">{{ $band->pivot->joined_year ?? '?' }}</div>
                <div class="pl-4">
                    <a href="{{ route('bands.show', $band) }}" class="font-semibold text-brand-600 dark:text-brand-400 hover:underline text-lg">{{ $band->name }}</a>
                    @if($band->pivot->role)<span class="text-xs px-2 py-0.5 rounded bg-accent-50 dark:bg-accent-900/30 text-accent-700 dark:text-accent-300 ml-2 font-medium">{{ $band->pivot->role }}</span>@endif
                    @if($band->pivot->is_current)<span class="text-xs px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 ml-1">current</span>@endif
                    <p class="text-sm text-surface-500 dark:text-surface-400 mt-0.5">{{ $band->pivot->joined_year ?? '?' }}–{{ $band->pivot->left_year ?? ($band->pivot->is_current ? 'present' : '?') }}@if($band->genres->count())· {{ $band->genres->pluck('name')->implode(', ') }}@endif</p>
                </div>
            </div>
            @empty
            <p class="text-surface-500 pl-4">No band history recorded.</p>
            @endforelse
        </div>
    </div>
    <aside class="lg:w-64 mt-6 lg:mt-0 shrink-0 lg:sticky lg:top-20 self-start"><x-ad-slot position="sidebar" /></aside>
</div>
@endsection
