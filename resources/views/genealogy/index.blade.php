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
<link rel="preload" href="https://cdn.jsdelivr.net/npm/vis-network@9.1.9/standalone/umd/vis-network.min.js" as="script" crossorigin>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="font-display text-2xl sm:text-3xl font-bold text-surface-900 dark:text-ink-200">Genealogy Graph</h1>
        <p class="text-xs text-surface-400 mt-1 uppercase tracking-wider">Click to focus &middot; Double-click to navigate</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <input type="text" id="graph-filter" placeholder="Filter..." class="input text-xs uppercase tracking-wider" style="max-width:160px">
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

<script src="https://cdn.jsdelivr.net/npm/vis-network@9.1.9/standalone/umd/vis-network.min.js" defer></script>
<script>
(function() {
    var isDark = document.documentElement.classList.contains('dark');
    var genreColors = {
        'grunge': '#059669', 'alternative-rock': '#7c3aed', 'hard-rock': '#dc2626',
        'rap-metal': '#d97706', 'heavy-metal': '#4f46e5', 'punk-rock': '#be185d',
        'indie-rock': '#0891b2', 'pop-rock': '#ca8a04', 'post-grunge': '#059669',
        'nu-metal': '#9333ea', 'thrash-metal': '#1d4ed8', 'death-metal': '#6b7280',
    };
    var defaultBandColor = '#059669';
    var defaultBandBorder = '#16a34a';
    var artistBg = '#a855f7';
    var artistBorder = '#9333ea';

    function getBandColor(genre) { return genreColors[genre] || defaultBandColor; }

    var status = document.getElementById('graph-status');
    var container = document.getElementById('full-genealogy-graph');
    container.innerHTML = '';

    status.textContent = 'Fetching data...';

    fetch('/api/genealogy')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            status.textContent = 'Rendering ' + data.nodes.length + ' nodes...';

            data.nodes.forEach(function(n) {
                if (n.group === 'band') {
                    var c = getBandColor(n.genre);
                    n.color = { background: c, border: c };
                } else {
                    n.color = { background: artistBg, border: artistBorder };
                }
                n.font = { color: '#ffffff', size: n.group === 'artist' ? 11 : 14, face: 'DM Sans, system-ui, sans-serif', multi: 'html' };
                n.borderWidth = n.group === 'band' ? 3 : 2;
                n.shadow = { enabled: true, size: 6, x: 0, y: 2, color: 'rgba(0,0,0,0.12)' };
                if (n.group === 'artist') {
                    n.shape = 'dot';
                    n.size = 8;
                } else {
                    n.shape = 'box';
                    n.widthConstraint = { minimum: 80, maximum: 160 };
                    n.shapeProperties = { borderRadius: 4 };
                }
            });

            data.edges.forEach(function(e) {
                e.font = { size: 0, strokeWidth: 0, align: 'middle' };
                e.hoverFont = { size: 11, color: isDark ? '#d6d3d1' : '#57534e', strokeWidth: 0 };
                if (e.dashes) {
                    e.dashes = [6, 4];
                    e.width = 1.2;
                } else {
                    e.width = 2.5;
                }
            });

            var nodes = new vis.DataSet(data.nodes);
            var edges = new vis.DataSet(data.edges);

            var network = new vis.Network(container, { nodes: nodes, edges: edges }, {
                physics: {
                    solver: 'hierarchicalRepulsion',
                    hierarchicalRepulsion: { nodeDistance: 160, centralGravity: 0.2, springLength: 180, springConstant: 0.01, damping: 0.09 },
                    minVelocity: 0.5,
                    stabilization: { iterations: 100 }
                },
                layout: {
                    hierarchical: {
                        enabled: true,
                        direction: 'LR',
                        sortMethod: 'directed',
                        levelSeparation: 200,
                        nodeSpacing: 180,
                        treeSpacing: 200,
                        blockShifting: true,
                        edgeMinimization: true,
                    }
                },
                interaction: { hover: true, tooltipDelay: 300, zoomView: true, dragView: true },
                edges: {
                    smooth: { type: 'curvedCW', roundness: 0.15 },
                    font: { size: 0, strokeWidth: 0 },
                    color: { color: isDark ? '#57534e' : '#a8a29e', hover: '#f59e0b', highlight: '#f59e0b' },
                },
                nodes: {
                    borderWidth: 2,
                    shadow: { enabled: true, size: 4, x: 0, y: 1, color: 'rgba(0,0,0,0.1)' },
                },
            });

            status.textContent = data.nodes.length + ' nodes, ' + data.edges.length + ' connections';

            network.on('hoverEdge', function(params) {
                edges.update({ id: params.edgeId, font: { size: 11, strokeWidth: 0, color: isDark ? '#d6d3d1' : '#57534e' } });
            });
            network.on('blurEdge', function(params) {
                edges.update({ id: params.edgeId, font: { size: 0, strokeWidth: 0 } });
            });

            network.on('doubleClick', function(params) {
                if (params.nodes.length) {
                    var n = nodes.get(params.nodes[0]);
                    if (n.url) window.location.href = n.url;
                }
            });

            network.once('stabilizationIterationsDone', function() {
                network.fit({ animation: true });
                network.setOptions({ physics: false });
            });

            var focusActive = false;
            network.on('click', function(params) {
                if (params.nodes.length && params.event.srcEvent.type === 'click') {
                    network.focus(params.nodes[0], { scale: 1.8, animation: true });
                    focusActive = true;
                } else if (!params.nodes.length) {
                    if (focusActive) {
                        network.fit({ animation: true });
                        focusActive = false;
                    }
                }
            });

            document.getElementById('graph-filter').addEventListener('input', function(e) {
                var q = e.target.value.toLowerCase();
                if (!q) { document.getElementById('graph-reset').click(); return; }
                nodes.forEach(function(n) {
                    var match = n.label.toLowerCase().includes(q);
                    nodes.update({ id: n.id, hidden: !match });
                });
            });

            document.getElementById('graph-reset').addEventListener('click', function() {
                document.getElementById('graph-filter').value = '';
                network.storePositions();
                network.setOptions({ physics: true });
                nodes.forEach(function(n) { nodes.update({ id: n.id, hidden: false }); });
                setTimeout(function() {
                    network.fit({ animation: { duration: 500, easingFunction: 'easeInOutQuad' } });
                    network.once('stabilizationIterationsDone', function() { network.setOptions({ physics: false }); });
                }, 100);
            });

            var clustered = false;
            document.getElementById('graph-cluster-toggle').addEventListener('click', function() {
                if (!clustered) {
                    var genreCounts = {};
                    nodes.forEach(function(n) { if (n.group === 'band' && n.genre) { genreCounts[n.genre] = (genreCounts[n.genre] || 0) + 1; } });
                    Object.keys(genreCounts).forEach(function(genre) {
                        if (genreCounts[genre] > 1) {
                            network.cluster({
                                joinCondition: function(n) { return n.genre === genre && n.group === 'band'; },
                                clusterNode: {
                                    id: 'cluster_' + genre,
                                    label: genre.charAt(0).toUpperCase() + genre.slice(1).replace('-', ' ') + ' (' + genreCounts[genre] + ')',
                                    shape: 'box', color: { background: genreColors[genre] || defaultBandColor, border: '#ffffff' },
                                    font: { color: '#ffffff', size: 18, face: 'DM Sans, system-ui', bold: true },
                                    borderWidth: 3, shapeProperties: { borderRadius: 6 },
                                    widthConstraint: { minimum: 140, maximum: 200 },
                                },
                                clusterEdge: { color: { color: isDark ? '#57534e' : '#a8a29e' }, width: 2, dashes: true },
                            });
                        }
                    });
                    clustered = true;
                    document.getElementById('graph-cluster-toggle').textContent = 'Uncluster';
                    network.fit({ animation: true });
                } else {
                    network.openClusters();
                    clustered = false;
                    document.getElementById('graph-cluster-toggle').textContent = 'Cluster';
                    network.fit({ animation: true });
                }
            });

            document.getElementById('graph-fullscreen').addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    container.requestFullscreen();
                    container.style.height = '100vh';
                } else {
                    document.exitFullscreen();
                    container.style.height = '85vh';
                }
                setTimeout(function() { network.fit({ animation: true }); }, 200);
            });
        })
        .catch(function(err) {
            status.textContent = 'Error: ' + err.message;
            console.error(err);
        });
})();
</script>
</div>
@endsection
