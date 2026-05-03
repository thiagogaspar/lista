@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: __('common.favorites.title'), description: __('common.favorites.seo_description')); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<div class="flex items-center gap-4 mb-8">
    <h1 class="font-display text-2xl sm:text-3xl font-bold text-surface-900 dark:text-ink-200">{{ __('common.favorites.title') }}</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-ink-700"></span>
</div>

@guest
<div class="text-center py-16">
    <p class="text-surface-500 mb-4 text-sm">
        <a href="{{ route('register') }}" class="link font-semibold">Register</a> or
        <a href="{{ route('filament.admin.auth.login') }}" class="link font-semibold">log in</a> to save favorites.
    </p>
</div>
@else
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
    @forelse($favorites as $fav)
    @php $item = $fav->favoriteable; @endphp
    @if($item)
    <a href="{{ $fav->favoriteable_type === 'App\Models\Band' ? route('bands.show', $item) : route('artists.show', $item) }}" class="block group">
        <div class="card card-hover h-full bg-white dark:bg-ink-800 p-3 flex gap-2.5">
            @if(method_exists($item, 'getAttribute') && $item->photo)
            <img src="{{ img_url($item->photo) }}" alt="{{ $item->name }}" class="w-12 h-12 object-cover shrink-0" loading="lazy" style="border:1px solid var(--color-surface-200)">
            @endif
            <div class="min-w-0 flex-1">
                <h3 class="font-display font-bold text-xs text-brand-600 dark:text-brand-400 truncate">{{ $item->name }}</h3>
                <p class="text-[10px] text-surface-400 mt-1.5 uppercase tracking-wider">{{ class_basename($fav->favoriteable_type) }}</p>
            </div>
        </div>
    </a>
    @endif
    @empty
    <div class="col-span-full text-center py-16">
        <p class="text-surface-500 mb-4 text-sm">No favorites yet.</p>
        <a href="{{ route('bands.index') }}" class="btn btn-brand btn-sm">Browse Bands</a>
    </div>
    @endforelse
</div>
@endguest
</div>
@endsection
