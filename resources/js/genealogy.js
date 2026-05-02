import { DataSet, Network } from 'vis-network/standalone'

export function initGenealogy() {
    var isDark = document.documentElement.classList.contains('dark')
    var genreColors = {
        'grunge': '#059669', 'alternative-rock': '#7c3aed', 'hard-rock': '#dc2626',
        'rap-metal': '#d97706', 'heavy-metal': '#4f46e5', 'punk-rock': '#be185d',
        'indie-rock': '#0891b2', 'pop-rock': '#ca8a04', 'post-grunge': '#059669',
        'nu-metal': '#9333ea', 'thrash-metal': '#1d4ed8', 'death-metal': '#6b7280',
    }
    var defaultBandColor = '#059669'
    var artistBg = '#a855f7'

    function getBandColor(genre) { return genreColors[genre] || defaultBandColor }

    var status = document.getElementById('graph-status')
    var container = document.getElementById('full-genealogy-graph')
    if (!container) return
    container.innerHTML = ''

    status.textContent = 'Fetching data...'

    fetch('/api/genealogy')
        .then(function(r) { return r.json() })
        .then(function(data) {
            status.textContent = 'Rendering ' + data.nodes.length + ' nodes...'

            data.nodes.forEach(function(n) {
                if (n.group === 'band') {
                    var c = getBandColor(n.genre)
                    n.color = { background: c, border: c }
                } else {
                    n.color = { background: artistBg, border: '#9333ea' }
                }
                n.font = { color: '#ffffff', size: n.group === 'artist' ? 11 : 14, face: 'DM Sans, system-ui, sans-serif', multi: 'html' }
                n.borderWidth = n.group === 'band' ? 3 : 2
                n.shadow = { enabled: true, size: 6, x: 0, y: 2, color: 'rgba(0,0,0,0.12)' }
                if (n.group === 'artist') {
                    n.shape = 'dot'
                    n.size = 16
                    n.borderWidth = 3
                } else {
                    n.shape = 'box'
                    n.widthConstraint = { minimum: 120, maximum: 220 }
                    n.shapeProperties = { borderRadius: 6 }
                    n.borderWidth = 4
                    n.font = { color: '#ffffff', size: 18, face: 'DM Sans, system-ui', bold: true, multi: 'html' }
                }
                n.cursor = 'pointer'
                n.shadow = { enabled: true, size: 10, x: 0, y: 2, color: 'rgba(0,0,0,0.2)' }
            })

            data.edges.forEach(function(e) {
                e.font = { size: 0, strokeWidth: 0, align: 'middle' }
                e.hoverFont = { size: 11, color: isDark ? '#d6d3d1' : '#57534e', strokeWidth: 0 }
                if (e.dashes) {
                    e.dashes = [6, 4]
                    e.width = 1.2
                } else {
                    e.width = 2.5
                }
            })

            var nodes = new DataSet(data.nodes)
            var edges = new DataSet(data.edges)

            var network = new Network(container, { nodes: nodes, edges: edges }, {
                physics: {
                    solver: 'barnesHut',
                    barnesHut: { gravitationalConstant: -6000, centralGravity: 0.06, springLength: 380, springConstant: 0.025, damping: 0.2 },
                    minVelocity: 0.5,
                    stabilization: { iterations: 200 }
                },
                layout: { improvedLayout: true },
                interaction: { hover: true, tooltipDelay: 300, zoomView: true, dragView: true, hoverConnectedEdges: false },
                edges: {
                    smooth: { type: 'curvedCW', roundness: 0.15 },
                    font: { size: 0, strokeWidth: 0 },
                    color: { color: isDark ? '#57534e' : '#a8a29e', hover: '#f59e0b', highlight: '#f59e0b' },
                },
                nodes: {
                    borderWidth: 0,
                    shadow: { enabled: true, size: 4, x: 0, y: 1, color: 'rgba(0,0,0,0.1)' },
                },
            })

            status.textContent = data.nodes.length + ' nodes, ' + data.edges.length + ' connections'

            network.on('hoverEdge', function(params) {
                edges.update({ id: params.edgeId, font: { size: 11, strokeWidth: 0, color: isDark ? '#d6d3d1' : '#57534e' } })
            })
            network.on('blurEdge', function(params) {
                edges.update({ id: params.edgeId, font: { size: 0, strokeWidth: 0 } })
            })

            network.on('doubleClick', function(params) {
                if (params.nodes.length) {
                    var n = nodes.get(params.nodes[0])
                    if (n.url) window.location.href = n.url
                }
            })

            network.once('stabilizationIterationsDone', function() {
                network.fit({ animation: true })
                network.setOptions({ physics: false })
            })

            var focusActive = false
            network.on('click', function(params) {
                if (params.nodes.length && params.event.srcEvent.type === 'click') {
                    network.focus(params.nodes[0], { scale: 1.8, animation: true })
                    focusActive = true
                } else if (!params.nodes.length) {
                    if (focusActive) {
                        network.fit({ animation: true })
                        focusActive = false
                    }
                }
            })

            document.getElementById('graph-filter').addEventListener('input', function(e) {
                var q = e.target.value.toLowerCase()
                if (!q) { document.getElementById('graph-reset').click(); return }
                var visible = []
                nodes.forEach(function(n) {
                    var match = n.label.toLowerCase().includes(q)
                    nodes.update({ id: n.id, hidden: !match })
                    if (match) visible.push(n.id)
                })
                if (visible.length) {
                    network.fit({ nodes: visible, animation: { duration: 400, easingFunction: 'easeInOutQuad' } })
                }
            })

            document.getElementById('graph-reset').addEventListener('click', function() {
                document.getElementById('graph-filter').value = ''
                network.storePositions()
                network.setOptions({ physics: true })
                nodes.forEach(function(n) { nodes.update({ id: n.id, hidden: false }) })
                setTimeout(function() {
                    network.fit({ animation: { duration: 500, easingFunction: 'easeInOutQuad' } })
                    network.once('stabilizationIterationsDone', function() { network.setOptions({ physics: false }) })
                }, 100)
            })

            var clustered = false
            document.getElementById('graph-cluster-toggle').addEventListener('click', function() {
                if (!clustered) {
                    var genreCounts = {}
                    nodes.forEach(function(n) { if (n.group === 'band' && n.genre) { genreCounts[n.genre] = (genreCounts[n.genre] || 0) + 1 } })
                    Object.keys(genreCounts).forEach(function(genre) {
                        if (genreCounts[genre] > 1) {
                            network.cluster({
                                joinCondition: function(n) { return n.genre === genre && n.group === 'band' },
                                clusterNode: {
                                    id: 'cluster_' + genre,
                                    label: genre.charAt(0).toUpperCase() + genre.slice(1).replace('-', ' ') + ' (' + genreCounts[genre] + ')',
                                    shape: 'box', color: { background: genreColors[genre] || defaultBandColor, border: '#ffffff' },
                                    font: { color: '#ffffff', size: 18, face: 'DM Sans, system-ui', bold: true },
                                    borderWidth: 3, shapeProperties: { borderRadius: 6 },
                                    widthConstraint: { minimum: 140, maximum: 200 },
                                },
                                clusterEdge: { color: { color: isDark ? '#57534e' : '#a8a29e' }, width: 2, dashes: true },
                            })
                        }
                    })
                    clustered = true
                    document.getElementById('graph-cluster-toggle').textContent = 'Uncluster'
                    network.fit({ animation: true })
                } else {
                    network.openClusters()
                    clustered = false
                    document.getElementById('graph-cluster-toggle').textContent = 'Cluster'
                    network.fit({ animation: true })
                }
            })

            document.getElementById('graph-fullscreen').addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    container.requestFullscreen()
                    container.style.height = '100vh'
                } else {
                    document.exitFullscreen()
                    container.style.height = '85vh'
                }
                setTimeout(function() { network.fit({ animation: true }) }, 200)
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
