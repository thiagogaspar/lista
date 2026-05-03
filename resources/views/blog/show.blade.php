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
<style>
.dark .prose a:hover { background: #fff; color: #000; }
</style>
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<nav class="breadcrumb mb-6">
    <a href="{{ route('home') }}">Home</a><span>/</span>
    <a href="{{ route('blog.index') }}">Blog</a><span>/</span>
    <span>{{ $post->title }}</span>
</nav>

<article class="max-w-3xl mx-auto">
    @if($post->featured_image)
    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-72 object-cover mb-8 border-2 border-black dark:border-white" loading="lazy">
    @endif

    <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-black text-black dark:text-white leading-tight tracking-tight mb-4">{{ $post->title }}</h1>

    <div class="flex items-center gap-2 mb-8 font-display text-sm font-bold text-surface-600 dark:text-ink-400 uppercase tracking-wider">
        <span>{{ $post->published_at->format('M j, Y') }}</span>
        <span class="mx-1">/</span>
        <span>{{ $post->author ?? 'LISTA' }}</span>
    </div>

    <div class="border-t-4 border-black dark:border-white pt-8">
        <div class="prose-serif max-w-none">{!! \Stevebauman\Purify\Facades\Purify::clean(Str::markdown($post->body)) !!}</div>
    </div>

    <div class="mt-12 pt-6 border-t-2 border-surface-200 dark:border-ink-700">
        <a href="{{ route('blog.index') }}" class="font-display font-bold text-black dark:text-white hover:underline">&larr; Voltar ao Blog</a>
    </div>
</article>
</div>
@endsection
