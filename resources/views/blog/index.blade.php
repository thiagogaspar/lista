@extends('layouts.app')

@section('head')
@php $seo = new \App\Values\SeoData(title: 'Blog', description: 'Latest news and articles about bands and music.', canonical: route('blog.index')); @endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="flex items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold text-base-content">Blog</h1>
    <div class="divider divider-neutral flex-1 h-px"></div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @forelse($posts as $post)
    <article class="card card-compact bg-base-100 shadow-md hover:shadow-xl transition-shadow">
        @if($post->featured_image)
        <figure><img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover" loading="lazy"></figure>
        @endif
        <div class="card-body">
            <h2 class="card-title">
                <a href="{{ route('blog.show', $post) }}" class="hover:text-primary">{{ $post->title }}</a>
            </h2>
            <p class="text-xs text-base-content/50">{{ $post->author ?? 'LISTA' }} · {{ $post->published_at->format('M j, Y') }}</p>
            @if($post->excerpt)<p class="text-sm text-base-content/70">{{ $post->excerpt }}</p>@endif
            <div class="card-actions"><a href="{{ route('blog.show', $post) }}" class="link link-primary text-sm">Read more →</a></div>
        </div>
    </article>
    @empty
    <div class="col-span-full text-center py-12"><p class="text-base-content/50">No posts yet.</p></div>
    @endforelse
</div>

<div class="mt-4">{{ $posts->links('pagination::tailwind') }}</div>
@endsection
