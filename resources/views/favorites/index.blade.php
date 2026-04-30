@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: 'My Favorites', description: 'Your favorited bands and artists.'); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="flex items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">My Favorites</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-surface-700"></span>
</div>

@guest
<p class="text-surface-500 text-center py-12"><a href="{{ route('register') }}" class="text-brand-600 hover:underline">Register</a> or <a href="{{ route('filament.admin.auth.login') }}" class="text-brand-600 hover:underline">log in</a> to save favorites.</p>
@else
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
    @forelse($favorites as $fav)
    @php $item = $fav->favoriteable; @endphp
    @if($item)
    <a href="{{ $fav->favoriteable_type === 'App\Models\Band' ? route('bands.show', $item) : route('artists.show', $item) }}" class="group block">
        <div class="p-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg shadow-sm hover:shadow-md hover:border-brand-300 dark:hover:border-brand-700 transition-all duration-200 h-full flex gap-2.5">
            @if(method_exists($item, 'getAttribute') && $item->photo)<img src="{{ Storage::url($item->photo) }}" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0" loading="lazy">@endif
            <div class="min-w-0 flex-1">
                <h3 class="font-semibold text-sm text-brand-600 dark:text-brand-400 truncate">{{ $item->name }}</h3>
                <p class="text-xs text-surface-400 mt-1">{{ class_basename($fav->favoriteable_type) }}</p>
            </div>
        </div>
    </a>
    @endif
    @empty
    <div class="col-span-full text-center py-12">
        <p class="text-surface-500 mb-3">No favorites yet.</p>
        <a href="{{ route('bands.index') }}" class="inline-flex px-4 py-1.5 bg-brand-500 text-white rounded-lg hover:bg-brand-600 text-sm font-medium">Browse Bands</a>
    </div>
    @endforelse
</div>
@endguest
@endsection
