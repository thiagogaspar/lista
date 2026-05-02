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
<style>
    body { background-color: #1a1d23 !important; }
    .dark body { background-color: #1a1d23 !important; }
</style>
@endsection

@section('content')
<div id="full-genealogy-graph" class="graph-container" style="position:fixed;inset:0;top:0;height:100vh;z-index:0">
    <div class="flex items-center justify-center h-full text-surface-400">
        <svg class="w-10 h-10 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>
</div>

<!-- Legend -->
<div class="graph-legend">
    <div class="text-[10px] font-bold uppercase tracking-wider text-white/50 mb-2">Types</div>
    <div class="graph-legend-item"><span class="graph-legend-dot" style="background:#ec4899"></span> Grunge</div>
    <div class="graph-legend-item"><span class="graph-legend-dot" style="background:#a855f7"></span> Alternative Rock</div>
    <div class="graph-legend-item"><span class="graph-legend-dot" style="background:#3b82f6"></span> Hard Rock</div>
    <div class="graph-legend-item"><span class="graph-legend-dot" style="background:#22c55e"></span> Indie Rock</div>
    <div class="graph-legend-item"><span class="graph-legend-dot" style="background:#eab308"></span> Rap Metal</div>
    <div class="graph-legend-item"><span class="graph-legend-dot" style="background:#f97316"></span> Punk Rock</div>
    <div class="graph-legend-item"><span class="graph-legend-dot" style="background:#8b5cf6"></span> Artist</div>
    <div class="border-t border-white/10 mt-2 pt-2">
        <div class="graph-legend-item"><span class="w-[18px]" style="border-top:2px solid #ec4899"></span> Relationship</div>
        <div class="graph-legend-item"><span class="w-[18px]" style="border-top:2px dashed #6b7280"></span> Membership</div>
    </div>
</div>

<!-- Zoom -->
<div class="graph-zoom">
    <button class="graph-zoom-btn" id="graph-zoom-in" title="Zoom in">+</button>
    <button class="graph-zoom-btn" id="graph-zoom-out" title="Zoom out">&minus;</button>
</div>

<!-- Status -->
<div id="graph-status" style="position:fixed;bottom:20px;right:20px;z-index:20;font-size:11px;color:#6b7280;font-family:monospace">Loading...</div>

@vite(['resources/js/genealogy.js'])
@endsection
