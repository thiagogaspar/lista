@extends('layouts.app')

@section('head')
@php
$seo = new \App\Values\SeoData(
    title: $post->title,
    description: $post->excerpt ?? Str::limit(strip_tags($post->body), 160),
    canonical: route('blog.show', $post),
);
@endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4">
<nav class="flex items-center gap-2 text-xs text-surface-400 mb-8 uppercase tracking-wider">
    <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a><span>/</span>
    <a href="{{ route('blog.index') }}" class="hover:text-brand-600">Blog</a><span>/</span>
    <span class="text-surface-700 dark:text-ink-200 font-medium">{{ $post->title }}</span>
</nav>

<article class="max-w-3xl mx-auto">
    @if($post->featured_image)
    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-64 object-cover mb-8" loading="lazy" style="border:1px solid var(--color-surface-200)">
    @endif

    <h1 class="font-display text-3xl sm:text-4xl font-bold text-surface-900 dark:text-ink-200 mb-3 leading-tight tracking-tight">{{ $post->title }}</h1>
    <div class="flex items-center gap-2 mb-8">
        <span class="text-xs text-surface-400 uppercase tracking-wider">{{ $post->published_at->format('M j, Y') }}</span>
        <span class="text-xs text-surface-300">&middot;</span>
        <span class="text-xs text-surface-400 uppercase tracking-wider">{{ $post->author ?? 'LISTA' }}</span>
    </div>

    <div class="prose max-w-none">{!! \Stevebauman\Purify\Facades\Purify::clean(Str::markdown($post->body)) !!}</div>
</article>
</div>
@endsection
