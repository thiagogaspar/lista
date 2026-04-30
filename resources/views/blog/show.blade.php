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
<nav class="flex items-center gap-2 text-sm text-surface-400 mb-4">
    <a href="{{ route('home') }}" class="hover:text-brand-600">Home</a><span>/</span>
    <a href="{{ route('blog.index') }}" class="hover:text-brand-600">Blog</a><span>/</span>
    <span class="text-surface-700 dark:text-surface-200 font-medium">{{ $post->title }}</span>
</nav>

<article class="max-w-3xl mx-auto">
    @if($post->featured_image)
    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-64 object-cover rounded-lg mb-6" loading="lazy">
    @endif

    <h1 class="text-3xl font-bold text-surface-900 dark:text-white mb-2">{{ $post->title }}</h1>
    <p class="text-sm text-surface-400 mb-6">{{ $post->author ?? 'LISTA' }} · {{ $post->published_at->format('M j, Y') }}</p>

    <div class="prose prose-surface dark:prose-invert max-w-none">{!! Str::markdown($post->body) !!}</div>
</article>
@endsection
