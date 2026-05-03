@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: 'Blog', description: 'Latest news and articles about local music.', canonical: route('blog.index')); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<!-- Hero tipográfico -->
<section class="-mx-4 -mt-6 mb-10 bg-black">
    <div class="max-w-6xl mx-auto px-4 w-full py-14 sm:py-16">
        <p class="font-display text-xs font-bold tracking-[0.2em] uppercase text-white/40 mb-3">LISTA</p>
        <h1 class="font-display text-4xl sm:text-6xl md:text-7xl font-black text-white leading-none tracking-tight">Blog</h1>
        <p class="mt-2 text-white/50 font-serif text-base max-w-lg">Notícias, artigos e histórias da cena musical local.</p>
    </div>
</section>

<div class="max-w-6xl mx-auto px-4">
<div class="divide-y-2 divide-surface-300 dark:divide-ink-600 border-t-2 border-black dark:border-white">
    @forelse($posts as $post)
    <a href="{{ route('blog.show', $post) }}" class="flex items-start gap-4 py-4 px-2 -mx-2 hover:bg-surface-100 dark:hover:bg-ink-800 group">
        @if($post->featured_image)
        <img src="{{ Storage::url($post->featured_image) }}" alt="" class="w-16 h-16 object-cover shrink-0 mt-1 border-2 border-surface-200 dark:border-ink-600" loading="lazy">
        @endif
        <div class="min-w-0 flex-1">
            <h2 class="font-display text-base font-bold text-black dark:text-white group-hover:underline leading-tight">{{ $post->title }}</h2>
            <div class="font-display text-[13px] text-surface-600 dark:text-ink-400 mt-1 font-bold tracking-wider">
                <span>{{ $post->published_at->format('M j, Y') }}</span>
                <span class="mx-1.5">/</span>
                <span>{{ $post->author ?? 'LISTA' }}</span>
            </div>
            @if($post->excerpt)
            <p class="font-serif text-sm text-surface-700 dark:text-ink-300 mt-2 leading-snug">{{ $post->excerpt }}</p>
            @endif
        </div>
        <span class="text-surface-500 dark:text-ink-500 text-lg shrink-0 mt-3 font-bold opacity-0 group-hover:opacity-100">&rarr;</span>
    </a>
    @empty
    <div class="text-center py-16 text-surface-500 dark:text-ink-400 font-bold">Nenhum post ainda.</div>
    @endforelse
</div>

<div class="mt-8 border-t-2 border-surface-200 dark:border-ink-700 pt-6">{{ $posts->links('pagination::tailwind') }}</div>
</div>
@endsection
