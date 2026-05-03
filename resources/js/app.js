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

Alpine.data('gallery', (images) => ({
    images: images,
    show: false,
    current: 0,

    init() {},

    open(i) {
        this.current = i
        this.show = true
        document.body.style.overflow = 'hidden'
    },

    close() {
        this.show = false
        document.body.style.overflow = ''
    },

    prev() {
        this.current = this.current > 0 ? this.current - 1 : this.images.length - 1
    },

    next() {
        this.current = this.current < this.images.length - 1 ? this.current + 1 : 0
    }
}))

Alpine.start()
