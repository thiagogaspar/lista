@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: 'Blog', description: 'Latest news and articles about bands and music.', canonical: route('blog.index')); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="flex items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Blog</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-surface-700"></span>
</div>

<div class="max-w-3xl mx-auto space-y-6">
    @forelse($posts as $post)
    <article class="bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
        @if($post->featured_image)
        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover" loading="lazy">
        @endif
        <div class="p-5">
            <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-1">
                <a href="{{ route('blog.show', $post) }}" class="hover:text-brand-600 dark:hover:text-brand-400">{{ $post->title }}</a>
            </h2>
            <p class="text-xs text-surface-400 mb-3">{{ $post->author ?? 'LISTA' }} · {{ $post->published_at->format('M j, Y') }}</p>
            @if($post->excerpt)<p class="text-sm text-surface-600 dark:text-surface-300">{{ $post->excerpt }}</p>@endif
            <a href="{{ route('blog.show', $post) }}" class="inline-block mt-3 text-sm text-brand-600 dark:text-brand-400 hover:underline">Read more →</a>
        </div>
    </article>
    @empty
    <p class="text-center text-surface-500 py-12">No posts yet.</p>
    @endforelse

    <div class="mt-4">{{ $posts->links('pagination::tailwind') }}</div>
</div>
@endsection
