import { DataSet, Network } from 'vis-network/standalone'

export function initGenealogy() {
    var status = document.getElementById('graph-status')
    var container = document.getElementById('full-genealogy-graph')
    if (!container) return
    container.innerHTML = ''

    status.textContent = 'Fetching...'

    var genreNodeColors = {
        'grunge':            { bg: '#1a0f1a', border: '#ec4899', label: 'Grunge' },
        'alternative-rock':  { bg: '#0f0f1a', border: '#a855f7', label: 'Alt Rock' },
        'hard-rock':         { bg: '#0a0f1a', border: '#3b82f6', label: 'Hard Rock' },
        'rap-metal':         { bg: '#1a1a0a', border: '#eab308', label: 'Rap Metal' },
        'heavy-metal':       { bg: '#0f0a1a', border: '#6366f1', label: 'Heavy Metal' },
        'punk-rock':         { bg: '#1a0f0a', border: '#f97316', label: 'Punk Rock' },
        'indie-rock':        { bg: '#0a1a0f', border: '#22c55e', label: 'Indie Rock' },
        'pop-rock':          { bg: '#1a1a0a', border: '#eab308', label: 'Pop Rock' },
        'post-grunge':       { bg: '#1a0a1a', border: '#d946ef', label: 'Post-Grunge' },
        'nu-metal':          { bg: '#0f0f1a', border: '#8b5cf6', label: 'Nu Metal' },
        'thrash-metal':      { bg: '#0a0a1a', border: '#60a5fa', label: 'Thrash Metal' },
        'death-metal':       { bg: '#0a0a0a', border: '#6b7280', label: 'Death Metal' },
    }

    var defaultColor = { bg: '#111118', border: '#6366f1', label: 'Other' }
    var artistColor = { bg: '#0f0a1a', border: '#8b5cf6' }

    function getGenreColors(genre) {
        return genreNodeColors[genre] || defaultColor
    }

    fetch('/api/genealogy')
        .then(function(r) { return r.json() })
        .then(function(data) {
            status.textContent = 'Rendering ' + data.nodes.length + ' nodes...'

            var nodeColors = {}

            data.nodes.forEach(function(n) {
                if (n.group === 'band') {
                    var c = getGenreColors(n.genre)
                    nodeColors[n.id] = c.border
                    n.color = { background: c.bg, border: c.border }
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
