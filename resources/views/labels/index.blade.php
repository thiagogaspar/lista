@extends('layouts.app')

@section('head')
@php
$seo = new \App\Values\SeoData(
    title: 'Labels',
    description: 'Browse record labels in the directory.',
    canonical: route('labels.index'),
);
@endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<div class="flex items-center gap-4 mb-8">
    <h1 class="text-xl sm:text-2xl font-bold text-surface-900 dark:text-ink-200">Labels</h1>
    <span class="h-px flex-1 bg-surface-200 dark:bg-ink-700"></span>
</div>

@if($labels->isEmpty())
<div class="text-center py-16"><p class="text-surface-500">No labels yet.</p></div>
@else
<!-- Alphabet nav -->
<div class="flex flex-wrap gap-1.5 mb-8" aria-label="Alphabetical index">
    @foreach($alphabet as $letter)
    <a href="#letter-{{ $letter }}"
       class="w-8 h-8 flex items-center justify-center text-xs font-bold uppercase border border-surface-200 dark:border-ink-700 text-surface-500 dark:text-surface-400 hover:border-brand-500 hover:text-brand-600 dark:hover:border-brand-700 dark:hover:text-brand-400 {{ isset($labels[$letter]) ? '' : 'opacity-40 pointer-events-none' }}">
        {{ $letter }}
    </a>
    @endforeach
</div>

@foreach($alphabet as $letter)
@if(isset($labels[$letter]))
<section class="mb-10" id="letter-{{ $letter }}">
    <h2 class="text-base font-bold text-surface-900 dark:text-ink-200 border-b border-surface-200 dark:border-ink-700 pb-2 mb-4">{{ $letter }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($labels[$letter] as $label)
        <a href="{{ route('labels.show', $label) }}" class="block group">
            <div class="card p-4 flex items-center gap-4">
                @if($label->logo)
                <img src="{{ Storage::url($label->logo) }}" alt="{{ $label->name }} logo" class="w-14 h-14 object-contain shrink-0" loading="lazy">
                @else
                <div class="w-14 h-14 shrink-0 bg-surface-100 dark:bg-ink-900 flex items-center justify-center text-surface-400 dark:text-ink-500 text-base font-bold">
                    {{ $label->name[0] }}
                </div>
                @endif
                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-sm text-surface-900 dark:text-ink-100 truncate">{{ $label->name }}</h3>
                    <div class="flex flex-wrap gap-2 mt-1 text-xs text-surface-500 dark:text-ink-500">
                        @if($label->country)<span>{{ $label->country }}</span>@endif
                        <span>{{ $label->bands_count }} band{{ $label->bands_count !== 1 ? 's' : '' }}</span>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif
@endforeach
@endif
</div>
@endsection
