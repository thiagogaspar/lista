import { DataSet, Network } from 'vis-network/standalone'

export function initGenealogy() {
    var status = document.getElementById('graph-status')
    var container = document.getElementById('full-genealogy-graph')
    if (!container) return
    container.innerHTML = ''

    status.textContent = 'Fetching...'

    var bandColor = { bg: '#111111', border: '#ffffff' }
    var artistColor = { bg: '#222222', border: '#cccccc' }

    fetch('/api/genealogy')
        .then(function(r) { return r.json() })
        .then(function(data) {
            status.textContent = 'Rendering ' + data.nodes.length + ' nodes...'

            var nodeColors = {}

            data.nodes.forEach(function(n) {
                if (n.group === 'band') {
                    nodeColors[n.id] = bandColor.border
                    n.color = { background: bandColor.bg, border: bandColor.border }
                    n.borderWidth = 3
                    n.borderWidthSelected = 3
                    n.shapeProperties = { borderRadius: 8 }
                    n.widthConstraint = { minimum: 110, maximum: 200 }
                    n.font = {
                        color: '#e0e0e8',
                        size: 14,
                        face: 'Inter, system-ui, sans-serif',
                        bold: true,
                        multi: 'html',
                        strokeWidth: 0,
                    }
                    n.label = '<b>' + n.label + '</b>\n' + (n.genreName || '')
                    n.margin = { top: 12, bottom: 10, left: 14, right: 14 }
                } else {
                    n.color = { background: artistColor.bg, border: artistColor.border }
                    nodeColors[n.id] = artistColor.border
                    n.shape = 'dot'
                    n.size = 22
                    n.borderWidth = 3
                    n.font = {
                        color: '#c0c0c8',
                        size: 11,
                        face: 'Inter, system-ui, sans-serif',
                        bold: true,
                        strokeWidth: 0,
                    }
                }
                n.cursor = 'pointer'
                n.shadow = { enabled: true, size: 0 }
            })

            data.edges.forEach(function(e) {
                var fromColor = nodeColors[e.from] || '#6b7280'
                e.color = { color: fromColor, highlight: fromColor, hover: fromColor, opacity: 0.6 }
                e.width = 2
                e.hoverWidth = 3
                e.selectionWidth = 3
                e.font = { size: 0, strokeWidth: 0 }
                if (e.dashes) {
                    e.dashes = [5, 4]
                    e.width = 1.2
                    e.color = { color: '#6b7280', highlight: '#9ca3af', hover: '#9ca3af', opacity: 0.4 }
                }
                e.smooth = { type: 'curvedCW', roundness: 0.12 }
            })

            var nodes = new DataSet(data.nodes)
            var edges = new DataSet(data.edges)

            var network = new Network(container, { nodes: nodes, edges: edges }, {
                nodes: {
                    borderWidth: 3,
                    borderWidthSelected: 3,
                    shapeProperties: { borderRadius: 8 },
                },
                edges: {
                    smooth: { type: 'curvedCW', roundness: 0.12 },
                    font: { size: 0, strokeWidth: 0 },
                },
                physics: {
                    solver: 'hierarchicalRepulsion',
                    hierarchicalRepulsion: { nodeDistance: 160, centralGravity: 0.1, springLength: 180, springConstant: 0.01, damping: 0.2 },
                    minVelocity: 0.5,
                    stabilization: { iterations: 150 },
                },
                layout: {
                    hierarchical: {
                        enabled: true,
                        direction: 'LR',
                        sortMethod: 'directed',
                        nodeSpacing: 180,
                        treeSpacing: 240,
                        blockShifting: true,
                        edgeMinimization: true,
                        parentCentralization: true,
                    },
                },
                interaction: {
                    hover: true,
                    tooltipDelay: 200,
                    zoomView: true,
                    dragView: true,
                    hoverConnectedEdges: false,
                    navigationButtons: false,
                    keyboard: true,
                },
            })

            status.textContent = data.nodes.length + ' nodes, ' + data.edges.length + ' connections'

            network.on('doubleClick', function(params) {
                if (params.nodes.length) {
                    var n = nodes.get(params.nodes[0])
                    if (n.url) window.location.href = n.url
                }
            })

            network.on('click', function(params) {
                if (params.nodes.length) {
                    network.focus(params.nodes[0], { scale: 2.0, animation: true })
                } else {
                    network.fit({ animation: true })
                }
            })

            network.once('stabilizationIterationsDone', function() {
                network.fit({ animation: { duration: 400, easingFunction: 'easeInOutQuad' } })
                network.setOptions({ physics: false })
            })

            // Zoom controls
            document.getElementById('graph-zoom-in').addEventListener('click', function() {
                var scale = network.getScale()
                network.moveTo({ scale: scale * 1.3, animation: { duration: 200, easingFunction: 'easeInOutQuad' } })
            })
            document.getElementById('graph-zoom-out').addEventListener('click', function() {
                var scale = network.getScale()
                network.moveTo({ scale: scale / 1.3, animation: { duration: 200, easingFunction: 'easeInOutQuad' } })
            })
        })
        .catch(function(err) {
            status.textContent = 'Error: ' + err.message
            console.error(err)
        })
}

if (document.getElementById('full-genealogy-graph')) {
    initGenealogy()
}
