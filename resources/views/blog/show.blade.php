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
<div class="breadcrumbs text-sm text-base-content/60 mb-4">
    <ul>
        <li><a href="{{ route('home') }}" class="hover:text-primary">Home</a></li>
        <li><a href="{{ route('blog.index') }}" class="hover:text-primary">Blog</a></li>
        <li class="text-base-content font-medium">{{ $post->title }}</li>
    </ul>
</div>

<article class="max-w-3xl mx-auto">
    @if($post->featured_image)
    <figure class="mb-6"><img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-64 object-cover rounded-lg" loading="lazy"></figure>
    @endif

    <h1 class="text-3xl font-bold text-base-content mb-2">{{ $post->title }}</h1>
    <p class="text-sm text-base-content/50 mb-6">{{ $post->author ?? 'LISTA' }} · {{ $post->published_at->format('M j, Y') }}</p>

    <div class="prose prose-base-content max-w-none">{!! \Stevebauman\Purify\Facades\Purify::clean(Str::markdown($post->body)) !!}</div>
</article>
@endsection
