import { DataSet, Network } from 'vis-network/standalone'

window.initBandGraph = function(containerId, graphData) {
    var isDark = document.documentElement.classList.contains('dark')
    var container = document.getElementById(containerId)
    if (!container) return
    container.innerHTML = ''

    var bandBg = '#000000', bandBorder = '#ffffff'
    var artistBg = '#222222', artistBorder = '#cccccc'

    graphData.nodes.forEach(function(n) {
        var isArtist = n.group === 'artist'
        n.color = { background: isArtist ? artistBg : bandBg, border: isArtist ? artistBorder : bandBorder }
        n.borderWidth = isArtist ? 3 : 4
        n.shadow = { enabled: true, size: 8, x: 0, y: 2, color: 'rgba(0,0,0,0.15)' }
        n.cursor = 'pointer'
        if (isArtist) {
            n.shape = 'dot'
            n.size = 22
            n.font = { color: '#ffffff', size: 14, face: 'DM Sans, system-ui', bold: true }
        } else {
            n.shape = 'box'
            n.widthConstraint = { minimum: 120, maximum: 200 }
            n.shapeProperties = { borderRadius: 6 }
            n.font = { color: '#ffffff', size: 16, face: 'DM Sans, system-ui', bold: true }
        }
    })

    graphData.edges.forEach(function(e) {
        if (e.dashes) {
            e.dashes = [6, 4]
            e.color = { color: isDark ? '#d6d3d1' : '#a8a29e' }
            e.width = 1.5
        } else {
            e.color = { color: '#888888' }
            e.width = 3
            e.font = { size: 10, color: isDark ? '#d6d3d1' : '#57534e', strokeWidth: 2, strokeColor: isDark ? '#292524' : '#ffffff' }
        }
    })

    var nodes = new DataSet(graphData.nodes)
    var edges = new DataSet(graphData.edges)

    var network = new Network(container, { nodes: nodes, edges: edges }, {
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
    })

    network.on('doubleClick', function(params) {
        if (params.nodes.length) {
            var n = nodes.get(params.nodes[0])
            if (n.url) window.location.href = n.url
        }
    })

    network.once('stabilizationIterationsDone', function() {
        network.fit({ animation: { duration: 300, easingFunction: 'easeInOutQuad' } })
        network.setOptions({ physics: false })
    })
}
