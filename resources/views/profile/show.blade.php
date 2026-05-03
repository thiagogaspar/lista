@extends('layouts.app')

@section('head')
@php
$seo = new \App\Values\SeoData(
    title: $user->name,
    description: __('common.profile.seo_public', ['name' => $user->name]),
);
@endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<nav class="flex items-center gap-2 text-xs text-surface-400 mb-8 uppercase tracking-wider">
    <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a><span>/</span>
    <span class="text-surface-700 dark:text-ink-200 font-medium">{{ $user->name }}</span>
</nav>

<div class="lg:flex lg:gap-8">
    <div class="lg:w-72 shrink-0">
        <div class="card bg-white dark:bg-ink-800 p-5 text-center">
            <div class="w-20 h-20 bg-brand-500 flex items-center justify-center text-white font-display text-3xl font-bold mx-auto border-2 border-brand-700">
                {{ strtoupper($user->name[0] ?? '?') }}
            </div>
            <h1 class="font-display font-bold text-xl mt-3 text-surface-900 dark:text-ink-200">{{ $user->name }}</h1>
            <p class="text-xs text-surface-400 mt-1">Member since {{ $user->created_at->format('M Y') }}</p>
            <div class="mt-3">
                @if($user->isAdmin())<span class="badge badge-brand">Admin</span>
                @elseif($user->isEditor())<span class="badge badge-accent">Editor</span>
                @else<span class="badge badge-surface">Viewer</span>@endif
            </div>
            <div class="mt-4 pt-4 border-t border-surface-200 dark:border-ink-700 space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-surface-400">Favorites</span><span class="font-bold text-surface-700 dark:text-ink-200">{{ $user->favorites->count() }}</span></div>
                <div class="flex justify-between"><span class="text-surface-400">Comments</span><span class="font-bold text-surface-700 dark:text-ink-200">{{ $user->comments->count() }}</span></div>
            </div>
        </div>
    </div>

    <div class="flex-1 min-w-0 mt-6 lg:mt-0">
        <!-- Favorites -->
        <section class="mb-10">
            <h2 class="font-display text-xl font-bold mb-4 text-surface-900 dark:text-ink-200">Favorites</h2>
            @if($user->favorites->isEmpty())
            <p class="text-sm text-surface-500">No public favorites.</p>
            @else
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($user->favorites as $fav)
                @php $item = $fav->favoriteable; @endphp
                @if($item)
                <a href="{{ $fav->favoriteable_type === 'App\Models\Band' ? route('bands.show', $item) : route('artists.show', $item) }}" class="block group">
                    <div class="card card-hover h-full bg-white dark:bg-ink-800 p-3 flex gap-2.5">
                        @if(method_exists($item, 'getAttribute') && $item->photo)
                        <img src="{{ img_url($item->photo) }}" alt="{{ $item->name }}" class="w-10 h-10 object-cover shrink-0" loading="lazy" style="border:1px solid var(--color-surface-200)">
                        @endif
                        <div class="min-w-0 flex-1">
                            <h3 class="font-display font-bold text-xs text-brand-600 dark:text-brand-400 truncate">{{ $item->name }}</h3>
                            <p class="text-[10px] text-surface-400 mt-1 uppercase tracking-wider">{{ class_basename($fav->favoriteable_type) }}</p>
                        </div>
                    </div>
                </a>
                @endif
                @endforeach
            </div>
            @endif
        </section>

        @if($user->comments->isNotEmpty())
        <section class="mb-10">
            <h2 class="font-display text-xl font-bold mb-4 text-surface-900 dark:text-ink-200">Comments</h2>
            <div class="border border-surface-200 dark:border-ink-700 divide-y divide-surface-200 dark:divide-ink-700 bg-white dark:bg-ink-800">
                @foreach($user->comments as $c)
                <div class="px-4 py-3">
                    <p class="text-sm text-surface-700 dark:text-ink-200">{{ Str::limit($c->body, 200) }}</p>
                    <p class="text-[10px] text-surface-400 mt-1">
                        on
                        @if($c->commentable)
                        <a href="{{ $c->commentable_type === 'App\Models\Band' ? route('bands.show', $c->commentable) : route('artists.show', $c->commentable) }}" class="link font-medium">{{ $c->commentable->name }}</a>
                        @else
                        <span class="text-surface-400">[deleted]</span>
                        @endif
                        &middot; {{ $c->created_at->diffForHumans() }}
                    </p>
                </div>
                @endforeach
            </div>
        </section>
        @else
        <p class="text-sm text-surface-500">No public comments.</p>
        @endif
    </div>
</div>
</div>
@endsection
