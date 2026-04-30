@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: 'Blog', description: 'Latest news and articles about local music.', canonical: route('blog.index')); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<div class="flex items-center gap-4 mb-8">
    <h1 class="font-display text-2xl sm:text-3xl font-bold text-surface-900 dark:text-ink-200">Blog</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-ink-700"></span>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse($posts as $post)
    <article class="card card-hover bg-white dark:bg-ink-800">
        @if($post->featured_image)
        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover" loading="lazy" style="border-bottom:1px solid var(--color-surface-200)">
        @endif
        <div class="p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[10px] text-surface-400 uppercase tracking-wider">{{ $post->published_at->format('M j, Y') }}</span>
                <span class="text-[10px] text-surface-300">&middot;</span>
                <span class="text-[10px] text-surface-400 uppercase tracking-wider">{{ $post->author ?? 'LISTA' }}</span>
            </div>
            <h2 class="font-display font-bold text-lg text-surface-900 dark:text-ink-200 mb-2">
                <a href="{{ route('blog.show', $post) }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">{{ $post->title }}</a>
            </h2>
            @if($post->excerpt)
            <p class="text-sm text-surface-500 dark:text-surface-400 leading-relaxed mb-4">{{ $post->excerpt }}</p>
            @endif
            <a href="{{ route('blog.show', $post) }}" class="link text-xs uppercase tracking-wider font-semibold">Read more &rarr;</a>
        </div>
    </article>
    @empty
    <div class="col-span-full text-center py-16"><p class="text-surface-500">No posts yet.</p></div>
    @endforelse
</div>

<div class="mt-8">{{ $posts->links('pagination::tailwind') }}</div>
</div>
@endsection
