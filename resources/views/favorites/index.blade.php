@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: 'My Favorites', description: 'Your favorited bands and artists.'); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="flex items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold text-base-content">My Favorites</h1>
    <div class="divider divider-neutral flex-1 h-px"></div>
</div>

@guest
<p class="text-base-content/50 text-center py-12"><a href="{{ route('register') }}" class="link link-primary">Register</a> or <a href="{{ route('filament.admin.auth.login') }}" class="link link-primary">log in</a> to save favorites.</p>
@else
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
    @forelse($favorites as $fav)
    @php $item = $fav->favoriteable; @endphp
    @if($item)
    <a href="{{ $fav->favoriteable_type === 'App\Models\Band' ? route('bands.show', $item) : route('artists.show', $item) }}" class="group block">
        <div class="card card-compact bg-base-100 border border-base-300 shadow-sm hover:shadow-md hover:border-primary transition-all duration-200 h-full">
            <div class="card-body flex-row gap-2.5 p-3">
                @if(method_exists($item, 'getAttribute') && $item->photo)<img src="{{ Storage::url($item->photo) }}" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0" loading="lazy">@endif
                <div class="min-w-0 flex-1">
                    <h3 class="card-title text-sm text-primary truncate">{{ $item->name }}</h3>
                    <p class="text-xs text-base-content/50 mt-1">{{ class_basename($fav->favoriteable_type) }}</p>
                </div>
            </div>
        </div>
    </a>
    @endif
    @empty
    <div class="col-span-full text-center py-12">
        <p class="text-base-content/50 mb-3">No favorites yet.</p>
        <a href="{{ route('bands.index') }}" class="btn btn-primary btn-sm">Browse Bands</a>
    </div>
    @endforelse
</div>
@endguest
@endsection
