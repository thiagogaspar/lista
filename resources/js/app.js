import Alpine from 'alpinejs'

Alpine.data('searchBox', () => ({
    query: '',
    results: { bands: [], artists: [] },
    open: false,

    search() {
        if (this.query.length < 2) {
            this.results = { bands: [], artists: [] }
            this.open = false
            return
        }
        fetch('/api/search?q=' + encodeURIComponent(this.query))
            .then(r => r.json())
            .then(data => {
                this.results = data
                this.open = true
            })
    },

    select(type, slug) {
        this.open = false
        this.query = ''
        window.location.href = '/' + type + 's/' + slug
    }
}))

Alpine.start()
