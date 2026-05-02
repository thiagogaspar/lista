@extends('layouts.app')

@section('head')
@php
$seo = new \App\Values\SeoData(
    title: 'Genealogy Graph',
    description: 'Interactive band genealogy. Explore connections between bands and artists through an interactive network graph.',
    canonical: route('genealogy'),
);
@endphp
<x-seo-meta :seo="$seo" />
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="font-display text-2xl sm:text-3xl font-bold text-surface-900 dark:text-ink-200">Genealogy Graph</h1>
        <p class="text-xs text-surface-400 mt-1 uppercase tracking-wider">Click to focus &middot; Double-click to navigate</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <input type="text" id="graph-filter" placeholder="Filter..." aria-label="Filter graph nodes" class="input text-xs uppercase tracking-wider" style="max-width:160px">
        <button id="graph-reset" class="btn btn-ghost btn-sm">Reset</button>
        <button id="graph-cluster-toggle" class="btn btn-ghost btn-sm">Cluster</button>
        <button id="graph-fullscreen" class="btn btn-ghost btn-sm" title="Fullscreen">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
        </button>
    </div>
</div>

<div id="full-genealogy-graph" class="border border-surface-200 dark:border-ink-700 bg-surface-50 dark:bg-ink-800 overflow-hidden" style="height:85vh">
    <div class="flex items-center justify-center h-full text-surface-400">
        <svg class="w-10 h-10 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>
</div>

<div class="flex flex-wrap gap-5 mt-4 text-xs text-surface-500 items-center">
    <span class="flex items-center gap-1.5"><span class="w-4 h-4 bg-brand-600" style="border:2px solid var(--color-brand-400)"></span> Band</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-accent-600" style="border:2px solid var(--color-accent-400)"></span> Artist</span>
    <span class="flex items-center gap-1.5"><span class="block w-8 h-0.5 bg-warm-500"></span> Relationship</span>
    <span class="flex items-center gap-1.5"><span class="block w-8" style="border-top:1px dashed var(--color-surface-400)"></span> Membership</span>
    <span id="graph-status" class="text-surface-400 ml-auto">Loading...</span>
</div>

@vite(['resources/js/genealogy.js'])
</div>
@endsection
