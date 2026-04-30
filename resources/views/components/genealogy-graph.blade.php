<div id="{{ $containerId }}" class="border border-surface-200 dark:border-surface-700 rounded-xl bg-surface-50 dark:bg-surface-800 overflow-hidden shadow-sm" style="height:500px">
    <div class="flex items-center justify-center h-full text-surface-400">
        <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vis-network@9.1.9/standalone/umd/vis-network.min.js"></script>
<script>
(function() {
    var isDark = document.documentElement.classList.contains('dark');
    var graph = @json($graph);

    var container = document.getElementById('{{ $containerId }}');
    container.innerHTML = '';

    var bandBg = '#059669', bandBorder = '#34d399';
    var artistBg = '#7c3aed', artistBorder = '#a78bfa';

    graph.nodes.forEach(function(n) {
        var isArtist = n.group === 'artist';
        n.color = { background: isArtist ? artistBg : bandBg, border: isArtist ? artistBorder : bandBorder };
        n.font = { color: '#ffffff', size: isArtist ? 14 : 16, face: 'Inter, system-ui' };
        n.borderWidth = 3;
        n.shadow = { enabled: true, size: 6, x: 0, y: 2, color: 'rgba(0,0,0,0.12)' };
        if (isArtist) { n.shape = 'dot'; n.size = 28; }
        else { n.shape = 'box'; n.widthConstraint = { minimum: 120, maximum: 200 }; n.shapeProperties = { borderRadius: 8 }; }
    });

    graph.edges.forEach(function(e) {
        if (e.dashes) {
            e.dashes = [6, 4];
            e.color = { color: isDark ? '#d6d3d1' : '#a8a29e' };
            e.width = 1.5;
        } else {
            e.color = { color: '#f59e0b' };
            e.width = 3;
            e.font = { size: 10, color: isDark ? '#d6d3d1' : '#57534e', strokeWidth: 2, strokeColor: isDark ? '#292524' : '#ffffff' };
        }
    });

    var nodes = new vis.DataSet(graph.nodes);
    var edges = new vis.DataSet(graph.edges);

    var network = new vis.Network(container, { nodes: nodes, edges: edges }, {
        physics: {
            solver: 'repulsion',
            repulsion: { nodeDistance: 250, centralGravity: 0.3, springLength: 180, damping: 0.09 },
            minVelocity: 0.5,
            stabilization: { iterations: 150 }
        },
        layout: { improvedLayout: true },
        interaction: { hover: true, tooltipDelay: 200 },
        edges: { smooth: { type: 'continuous' } },
    });

    network.on('doubleClick', function(params) {
        if (params.nodes.length) {
            var n = nodes.get(params.nodes[0]);
            if (n.url) window.location.href = n.url;
        }
    });

    network.once('stabilizationIterationsDone', function() {
        network.fit({ animation: { duration: 300, easingFunction: 'easeInOutQuad' } });
    });
})();
</script>
