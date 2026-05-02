@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: 'Blog', description: 'Latest news and articles about local music.', canonical: route('blog.index')); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<div class="flex items-center gap-4 mb-8 border-b-2 border-black dark:border-white pb-4">
    <h1 class="text-2xl sm:text-3xl font-black text-black dark:text-white tracking-tight">Blog</h1>
</div>

<div class="divide-y-2 divide-surface-300 dark:divide-ink-600 border-t-2 border-black dark:border-white">
    @forelse($posts as $post)
    <a href="{{ route('blog.show', $post) }}" class="flex items-start gap-4 py-4 px-2 -mx-2 hover:bg-surface-100 dark:hover:bg-ink-800 group">
        @if($post->featured_image)
        <img src="{{ Storage::url($post->featured_image) }}" alt="" class="w-14 h-14 object-cover shrink-0 mt-1" loading="lazy">
        @endif
        <div class="min-w-0 flex-1">
            <h2 class="text-base font-bold text-black dark:text-white group-hover:underline leading-tight">{{ $post->title }}</h2>
            <div class="text-[13px] text-surface-600 dark:text-ink-400 mt-1 font-semibold tracking-wider">
                <span>{{ $post->published_at->format('M j, Y') }}</span>
                <span class="mx-1.5">/</span>
                <span>{{ $post->author ?? 'LISTA' }}</span>
            </div>
            @if($post->excerpt)
            <p class="text-sm text-surface-700 dark:text-ink-300 mt-2 leading-snug">{{ $post->excerpt }}</p>
            @endif
        </div>
        <span class="text-surface-500 dark:text-ink-500 text-lg shrink-0 mt-3 font-bold opacity-0 group-hover:opacity-100">&rarr;</span>
    </a>
    @empty
    <div class="text-center py-16 text-surface-500 dark:text-ink-400 font-bold">No posts yet.</div>
    @endforelse
</div>

<div class="mt-8 border-t-2 border-surface-200 dark:border-ink-700 pt-6">{{ $posts->links('pagination::tailwind') }}</div>
</div>
@endsection
