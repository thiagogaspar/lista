<div id="{{ $containerId }}" class="border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800 overflow-hidden" style="height:400px">
    <div class="flex items-center justify-center h-full text-surface-400">
        <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vis-network@9.1.9/standalone/umd/vis-network.min.js" defer></script>
<script>
(function() {
    var isDark = document.documentElement.classList.contains('dark');
    var graph = @json($graph);

    var container = document.getElementById('{{ $containerId }}');
    if (!container) return;
    container.innerHTML = '';

    var bandBg = '#059669', bandBorder = '#34d399';
    var artistBg = '#7c3aed', artistBorder = '#a78bfa';

    graph.nodes.forEach(function(n) {
        var isArtist = n.group === 'artist';
        n.color = { background: isArtist ? artistBg : bandBg, border: isArtist ? artistBorder : bandBorder };
        n.borderWidth = isArtist ? 3 : 4;
        n.shadow = { enabled: true, size: 8, x: 0, y: 2, color: 'rgba(0,0,0,0.15)' };
        n.cursor = 'pointer';
        if (isArtist) {
            n.shape = 'dot';
            n.size = 22;
            n.font = { color: '#ffffff', size: 14, face: 'DM Sans, system-ui', bold: true };
        } else {
            n.shape = 'box';
            n.widthConstraint = { minimum: 120, maximum: 200 };
            n.shapeProperties = { borderRadius: 6 };
            n.font = { color: '#ffffff', size: 16, face: 'DM Sans, system-ui', bold: true };
        }
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
            solver: 'barnesHut',
            barnesHut: { gravitationalConstant: -5000, centralGravity: 0.06, springLength: 320, springConstant: 0.02, damping: 0.18 },
            minVelocity: 0.5,
            stabilization: { iterations: 200 }
        },
        layout: { improvedLayout: true },
        interaction: { hover: true, tooltipDelay: 200, hoverConnectedEdges: false },
        edges: { smooth: { type: 'continuous' } },
        nodes: { borderWidth: 0 },
    });

    network.on('doubleClick', function(params) {
        if (params.nodes.length) {
            var n = nodes.get(params.nodes[0]);
            if (n.url) window.location.href = n.url;
        }
    });

    network.once('stabilizationIterationsDone', function() {
        network.fit({ animation: { duration: 300, easingFunction: 'easeInOutQuad' } });
        network.setOptions({ physics: false });
    });
})();
</script>
