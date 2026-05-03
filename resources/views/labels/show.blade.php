@extends('layouts.app')

@section('head')
@php
if (!isset($seo)) {
    $seo = new \App\Values\SeoData(
        title: $label->name,
        description: Str::limit(strip_tags($label->description ?? $label->name . ' — record label.'), 160),
        type: 'organization',
        image: $label->logo ? \Illuminate\Support\Facades\Storage::url($label->logo) : null,
        canonical: route('labels.show', $label),
    );
}
@endphp
<x-seo-meta :seo="$seo" />
@if($label->logo)
<link rel="preload" href="{{ Storage::url($label->logo) }}" as="image" fetchpriority="high">
@endif
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<!-- Hero — sem shapes geométricos -->
<section class="relative -mx-4 mb-8 overflow-hidden bg-black" style="min-height:240px">
    @if($label->logo)
    <img src="{{ Storage::url($label->logo) }}" alt="{{ $label->name }} logo" class="absolute inset-0 w-full h-full object-cover opacity-25" fetchpriority="high">
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-black/20 flex items-center">
        <div class="relative z-10 max-w-6xl mx-auto px-4 w-full flex items-center gap-6">
            @if($label->logo)
            <img src="{{ Storage::url($label->logo) }}" alt="{{ $label->name }}" class="w-20 h-20 object-contain shrink-0 border-2 border-white/20">
            @endif
            <div>
                <p class="font-display text-xs font-bold tracking-[0.15em] uppercase text-white/40 mb-2">Gravadora</p>
                <h1 class="font-display text-3xl sm:text-5xl font-black text-white leading-none tracking-tight">{{ $label->name }}</h1>
            </div>
        </div>
    </div>
</section>

<nav class="breadcrumb mb-8">
    <a href="{{ route('home') }}">Home</a><span>/</span>
    <a href="{{ route('labels.index') }}">Labels</a><span>/</span>
    <span>{{ $label->name }}</span>
</nav>

<div class="lg:flex lg:gap-8">
    <div class="flex-1 min-w-0 order-2 lg:order-1">
        @if($label->description)
        <div class="prose max-w-none mb-8">{{ $label->description }}</div>
        @endif

        <x-section-header tag="h2" :count="$label->bands->count()">Bandas</x-section-header>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @forelse($label->bands as $band)
            <a href="{{ route('bands.show', $band) }}" class="group">
                <div class="card p-3 flex gap-2.5 card-hover h-full">
                    @if($band->photo)
                    <img src="{{ Storage::url($band->photo) }}" alt="{{ $band->name }}" class="w-12 h-12 object-cover shrink-0 border-2 border-surface-200 dark:border-ink-600" loading="lazy">
                    @endif
                    <div class="min-w-0 flex-1">
                        <h3 class="font-display font-bold text-sm text-brand-600 dark:text-brand-400 group-hover:text-brand-700 dark:group-hover:text-brand-300 truncate">{{ $band->name }}</h3>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @if($band->formed_year)
                            <span class="badge badge-brand">{{ $band->formed_year }}&ndash;{{ $band->dissolved_year ?? 'present' }}</span>
                            @endif
                            @foreach($band->genres->take(2) as $genre)
                            <span class="badge badge-surface">{{ $genre->name }}</span>
                            @endforeach
                        </div>
                        <span class="font-display text-[10px] font-bold text-surface-400 mt-1 block">{{ $band->artists_count }} membro{{ $band->artists_count !== 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </a>
            @empty
            <p class="col-span-full text-surface-500 text-sm">Nenhuma banda nesta gravadora.</p>
            @endforelse
        </div>
    </div>

    <!-- Infobox -->
    <aside class="lg:w-72 mt-8 lg:mt-0 shrink-0 self-start order-1 lg:order-2 lg:sticky lg:top-16">
        <x-infobox :title="$label->name" :items="[
            'Country' => $label->country ? e($label->country) : null,
            'Founded' => $label->founded_year ? (string) $label->founded_year : null,
            'Bands' => (string) $label->bands->count(),
        ]" />
        @if($label->website)
        <div class="mt-3">
            <a href="{{ $label->website }}" target="_blank" rel="noopener noreferrer" class="btn w-full text-xs bg-black text-white border-black hover:bg-surface-800">Visitar Site &nearr;</a>
        </div>
        @endif
        <div class="mt-4"><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
</div>
@endsection
