@extends('layouts.app')

@section('head')
@php
$seo = new \App\Values\SeoData(
    title: __('common.genealogy.title'),
    description: __('common.genealogy.seo_description'),
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
    <div class="text-[10px] font-bold uppercase tracking-wider text-white/50 mb-2">{{ __('common.genealogy.legend') }}</div>
    <div class="graph-legend-item"><span class="graph-legend-dot" style="background:#ffffff;border:1px solid #fff"></span> {{ __('common.genealogy.legend_band') }}</div>
    <div class="graph-legend-item"><span class="graph-legend-dot" style="background:#cccccc;border:1px solid #cccccc"></span> {{ __('common.genealogy.legend_artist') }}</div>
    <div class="border-t border-white/10 mt-2 pt-2">
        <div class="graph-legend-item"><span class="w-[18px]" style="border-top:2px solid #fff"></span> {{ __('common.genealogy.legend_relationship') }}</div>
        <div class="graph-legend-item"><span class="w-[18px]" style="border-top:2px dashed #666"></span> {{ __('common.genealogy.legend_membership') }}</div>
    </div>
</div>

<!-- Zoom -->
<div class="graph-zoom">
    <button class="graph-zoom-btn" id="graph-zoom-in" title="{{ __('common.genealogy.zoom_in') }}">+</button>
    <button class="graph-zoom-btn" id="graph-zoom-out" title="{{ __('common.genealogy.zoom_out') }}">&minus;</button>
</div>

<!-- Status -->
<div id="graph-status" style="position:fixed;bottom:20px;right:20px;z-index:20;font-size:11px;color:#6b7280;font-family:monospace">{{ __('common.genealogy.fetching') }}</div>

@vite(['resources/js/genealogy.js'])
@endsection
