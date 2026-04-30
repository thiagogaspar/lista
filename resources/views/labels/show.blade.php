@extends('layouts.app')

@section('head')
<x-seo-meta :seo="$seo" />
<link rel="preload" href="{{ $label->logo ? Storage::url($label->logo) : '' }}" as="image" fetchpriority="high">
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<!-- Hero -->
<section class="relative -mx-4 mb-8 overflow-hidden bg-ink dark:bg-ink-900" style="min-height:260px">
    @if($label->logo)
    <img src="{{ Storage::url($label->logo) }}" alt="{{ $label->name }} logo" class="absolute inset-0 w-full h-full object-cover opacity-20 dark:opacity-15" fetchpriority="high">
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-ink/90 to-ink/70 flex items-center p-6 sm:p-10">
        <div class="flex items-center gap-6">
            @if($label->logo)
            <img src="{{ Storage::url($label->logo) }}" alt="{{ $label->name }}" class="w-24 h-24 object-contain shrink-0" style="border:1px solid var(--color-surface-700)">
            @endif
            <div>
                <p class="text-brand-400 text-[10px] font-bold uppercase tracking-[0.2em] mb-2">Record Label</p>
                <h1 class="font-display text-3xl sm:text-5xl font-bold text-white leading-none tracking-tight">{{ $label->name }}</h1>
            </div>
        </div>
    </div>
</section>

<nav class="flex items-center gap-2 text-xs text-surface-400 mb-8 uppercase tracking-wider">
    <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a><span>/</span>
    <a href="{{ route('labels.index') }}" class="hover:text-brand-600">Labels</a><span>/</span>
    <span class="text-surface-700 dark:text-ink-200 font-medium">{{ $label->name }}</span>
</nav>

<div class="lg:flex lg:gap-8">
    <div class="flex-1 min-w-0">
        @if($label->description)
        <div class="prose max-w-none mb-8">{{ $label->description }}</div>
        @endif

        <h2 class="font-display text-2xl font-bold mb-5 text-surface-900 dark:text-ink-200">
            Bands <span class="text-base font-sans font-normal text-surface-400">({{ $label->bands->count() }})</span>
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @forelse($label->bands as $band)
            <a href="{{ route('bands.show', $band) }}" class="block group">
                <div class="card card-hover h-full bg-white dark:bg-ink-800 p-3 flex gap-2.5">
                    @if($band->photo)
                    <img src="{{ Storage::url($band->photo) }}" alt="{{ $band->name }}" class="w-12 h-12 object-cover shrink-0" loading="lazy" style="border:1px solid var(--color-surface-200)">
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
                        <span class="text-[10px] text-surface-400 mt-1 block">{{ $band->artists_count }} member{{ $band->artists_count !== 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </a>
            @empty
            <p class="col-span-full text-surface-500 text-sm">No bands on this label yet.</p>
            @endforelse
        </div>
    </div>

    <aside class="lg:w-64 mt-8 lg:mt-0 shrink-0 lg:sticky lg:top-16 self-start space-y-4">
        <div class="card bg-white dark:bg-ink-800 p-4">
            <h3 class="font-display text-sm font-bold mb-3 text-surface-700 dark:text-ink-200">Info</h3>
            <div class="space-y-2 text-xs">
                @if($label->country)
                <div class="flex justify-between"><span class="text-surface-400">Country</span><span class="font-bold text-surface-700 dark:text-ink-200">{{ $label->country }}</span></div>
                @endif
                @if($label->founded_year)
                <div class="flex justify-between"><span class="text-surface-400">Founded</span><span class="font-bold text-surface-700 dark:text-ink-200">{{ $label->founded_year }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-surface-400">Bands</span><span class="font-bold text-surface-700 dark:text-ink-200">{{ $label->bands->count() }}</span></div>
                @if($label->website)
                <div class="pt-2 border-t border-surface-200 dark:border-ink-700">
                    <a href="{{ $label->website }}" target="_blank" rel="noopener noreferrer" class="link text-xs font-semibold">Visit Website &nearr;</a>
                </div>
                @endif
            </div>
        </div>
        <div><x-ad-slot position="sidebar" /></div>
    </aside>
</div>
</div>
@endsection
