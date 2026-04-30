<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <link rel="preload" href="https://fonts.bunny.net/css?family=dm-serif-display:400&display=swap" as="style">
    <link rel="preload" href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&display=swap" as="style">
    <link href="https://fonts.bunny.net/css?family=dm-serif-display:400&display=swap" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&display=swap" rel="stylesheet">

    @yield('preload')

    <link rel="icon" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/favicon.svg">

    @vite(['resources/css/app.css'])

    @yield('head')
</head>
<body class="min-h-screen bg-ink-50 dark:bg-ink font-sans antialiased text-surface-900 dark:text-ink-200">
    <!-- Noise texture overlay -->
    <div class="fixed inset-0 pointer-events-none z-[9999] opacity-[0.03] dark:opacity-[0.06]" style="background-image:url('data:image/svg+xml,%3Csvg viewBox=%220 0 512 512%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22n%22%3E%3CfeTurbulence baseFrequency=%220.85%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23n)%22/%3E%3C/svg%3E'); background-size:512px 512px"></div>

    <header class="sticky top-0 z-50 border-b border-surface-200 dark:border-ink-700 bg-ink-50/95 dark:bg-ink/95 backdrop-blur-sm" x-data="{ menu: false }">
        <div class="max-w-7xl mx-auto px-4 h-14 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-lg font-bold text-brand-600 dark:text-brand-500 shrink-0 hover:text-brand-700 dark:hover:text-brand-400 transition-colors group">
                <svg class="w-6 h-6 transition-transform group-hover:scale-110" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                <span class="font-display tracking-tight">{{ config('app.name', 'LISTA') }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-0.5 text-xs font-semibold uppercase tracking-widest">
                <a href="{{ route('bands.index') }}" class="px-3 py-2 text-surface-500 dark:text-surface-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors {{ request()->routeIs('bands.*') ? 'text-brand-600 dark:text-brand-400' : '' }}">Bands</a>
                <a href="{{ route('artists.index') }}" class="px-3 py-2 text-surface-500 dark:text-surface-400 hover:text-accent-600 dark:hover:text-accent-400 transition-colors {{ request()->routeIs('artists.*') ? 'text-accent-600 dark:text-accent-400' : '' }}">Artists</a>
                <a href="{{ route('genealogy') }}" class="px-3 py-2 text-surface-500 dark:text-surface-400 hover:text-warm-600 dark:hover:text-warm-400 transition-colors {{ request()->routeIs('genealogy') ? 'text-warm-600 dark:text-warm-400' : '' }}">Graph</a>
                @auth
                <a href="{{ route('favorites.index') }}" class="px-3 py-2 text-surface-500 dark:text-surface-400 hover:text-brand-600 transition-colors">Favorites</a>
                @endauth
            </nav>

            <div class="flex items-center gap-1.5">
                <div class="hidden lg:block relative" x-data="searchBox()">
                    <form @submit.prevent class="relative">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-surface-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input x-model="query" @input.debounce.300ms="search()" @focus="if(query.length>=2) open=true" @keydown.escape="open=false"
                               placeholder="Search..." x-ref="input"
                               class="w-40 lg:w-48 pl-8 pr-2.5 py-1.5 text-xs font-medium uppercase tracking-wider border border-surface-200 dark:border-ink-600 bg-white dark:bg-ink-800 text-surface-900 dark:text-ink-200 placeholder-surface-400 focus:outline-none focus:ring-1 focus:ring-brand-500 focus:border-brand-500 transition-shadow">
                    </form>
                    <div x-show="open && (results.bands.length || results.artists.length)" @click.away="open = false" x-cloak
                         class="absolute top-full mt-1 right-0 w-72 bg-white dark:bg-ink-800 border border-surface-200 dark:border-ink-700 shadow-2xl z-50 overflow-hidden">
                        <template x-if="results.bands.length">
                            <div>
                                <div class="px-3 pt-2.5 pb-1 text-[9px] font-bold text-surface-400 tracking-[0.15em] uppercase">Bands</div>
                                <template x-for="band in results.bands" :key="band.id">
                                    <button @click="select('band', band.slug)" class="w-full px-3 py-2 text-left hover:bg-brand-50 dark:hover:bg-brand-900/20 flex items-center justify-between">
                                        <span class="font-semibold text-sm text-brand-600 dark:text-brand-400" x-text="band.name"></span>
                                        <span x-show="band.genre" class="text-[10px] px-1.5 py-0.5 rounded-full bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300" x-text="band.genre"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                        <template x-if="results.artists.length">
                            <div>
                                <div class="px-3 pt-2.5 pb-1 text-[9px] font-bold text-surface-400 tracking-[0.15em] uppercase">Artists</div>
                                <template x-for="artist in results.artists" :key="artist.id">
                                    <button @click="select('artist', artist.slug)" class="w-full px-3 py-2 text-left hover:bg-accent-50 dark:hover:bg-accent-900/20 flex items-center justify-between">
                                        <span class="font-semibold text-sm text-accent-600 dark:text-accent-400" x-text="artist.name"></span>
                                        <span x-show="artist.origin" class="text-[10px] text-surface-500" x-text="artist.origin"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <a href="/admin" class="hidden sm:inline-flex items-center gap-1 px-2 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-surface-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors" title="Admin" rel="nofollow">Admin</a>

                @auth
                <span class="hidden sm:inline text-[10px] text-surface-400 font-medium mr-1 tracking-wider uppercase">{{ auth()->user()->name }}</span>
                @else
                <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center gap-1 px-2 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-surface-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Register</a>
                @endauth

                <div x-data="{
                    theme: localStorage.getItem('theme') || 'system',
                    init() {
                        this.applyTheme();
                        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                            if (this.theme === 'system') {
                                let html = document.documentElement;
                                html.style.transition = 'background-color .3s, border-color .25s, color .2s';
                                setTimeout(() => html.style.transition = '', 500);
                                this.applyTheme();
                            }
                        });
                    },
                    applyTheme() {
                        let isDark = this.theme === 'dark' ||
                            (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                        let html = document.documentElement;
                        if (html.classList.contains('dark') !== isDark) {
                            html.style.transition = 'background-color .3s, border-color .25s, color .2s';
                            setTimeout(() => html.style.transition = '', 500);
                        }
                        html.classList.toggle('dark', isDark);
                        localStorage.setItem('theme', this.theme);
                    },
                    setTheme(t) { this.theme = t; this.applyTheme(); }
                }" class="relative" x-init="init()">
                    <button @click="setTheme(theme === 'dark' ? 'light' : 'dark')" class="p-1.5 text-surface-400 hover:text-surface-700 dark:hover:text-surface-300 transition-colors" aria-label="Toggle theme">
                        <svg class="w-4 h-4 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                </div>

                <button @click="menu = !menu" class="md:hidden p-1.5 text-surface-400 hover:text-surface-700 dark:hover:text-surface-300 transition-colors" aria-label="Menu">
                    <svg class="w-5 h-5" :class="menu ? 'hidden' : 'block'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="w-5 h-5" :class="menu ? 'block' : 'hidden'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div x-show="menu" @click.away="menu = false" x-cloak class="md:hidden border-t border-surface-200 dark:border-ink-700 bg-ink-50 dark:bg-ink">
            <div class="max-w-7xl mx-auto px-4 py-4 space-y-4">
                <div class="relative" x-data="searchBox()">
                    <form @submit.prevent class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input x-model="query" @input.debounce.300ms="search()" @focus="if(query.length>=2) open=true" @keydown.escape="open=false"
                               placeholder="Search..." class="w-full pl-9 pr-3 py-2 text-sm input">
                    </form>
                    <div x-show="open && (results.bands.length || results.artists.length)" @click.away="open = false" x-cloak
                         class="mt-1 bg-white dark:bg-ink-800 border border-surface-200 dark:border-ink-700 shadow-xl overflow-hidden">
                        <template x-if="results.bands.length">
                            <div>
                                <div class="px-3 pt-2 pb-1 text-[9px] font-bold text-surface-400 tracking-[0.15em] uppercase">Bands</div>
                                <template x-for="band in results.bands" :key="band.id">
                                    <button @click="select('band', band.slug)" class="w-full px-3 py-2 text-left hover:bg-brand-50 dark:hover:bg-brand-900/20 flex items-center justify-between">
                                        <span class="font-semibold text-sm text-brand-600 dark:text-brand-400" x-text="band.name"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                        <template x-if="results.artists.length">
                            <div>
                                <div class="px-3 pt-2 pb-1 text-[9px] font-bold text-surface-400 tracking-[0.15em] uppercase">Artists</div>
                                <template x-for="artist in results.artists" :key="artist.id">
                                    <button @click="select('artist', artist.slug)" class="w-full px-3 py-2 text-left hover:bg-accent-50 dark:hover:bg-accent-900/20 flex items-center justify-between">
                                        <span class="font-semibold text-sm text-accent-600 dark:text-accent-400" x-text="artist.name"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
                <a href="{{ route('bands.index') }}" class="block px-3 py-2 text-sm font-semibold text-surface-700 dark:text-ink-200 hover:text-brand-600 dark:hover:text-brand-400">Bands</a>
                <a href="{{ route('artists.index') }}" class="block px-3 py-2 text-sm font-semibold text-surface-700 dark:text-ink-200 hover:text-accent-600 dark:hover:text-accent-400">Artists</a>
                <a href="{{ route('genealogy') }}" class="block px-3 py-2 text-sm font-semibold text-surface-700 dark:text-ink-200 hover:text-warm-600 dark:hover:text-warm-400">Genealogy</a>
                @auth
                <a href="{{ route('favorites.index') }}" class="block px-3 py-2 text-sm font-semibold text-surface-700 dark:text-ink-200 hover:text-brand-600">Favorites</a>
                @endauth
                <a href="/admin" class="block px-3 py-2 text-[10px] uppercase tracking-wider text-surface-400">Admin</a>
            </div>
        </div>
    </header>

    <main class="relative">
        @yield('content')
    </main>

    <footer class="border-t border-surface-200 dark:border-ink-700 mt-16">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        <span class="font-display text-lg font-bold text-brand-600 dark:text-brand-500">{{ config('app.name', 'LISTA') }}</span>
                    </div>
                    <p class="text-sm text-surface-500 dark:text-surface-400 max-w-sm leading-relaxed">A local music directory. We map the connections between bands and artists — who played with whom, when, and where.</p>
                </div>
                <div>
                    <p class="font-display text-sm font-bold mb-3 text-surface-700 dark:text-ink-200">Explore</p>
                    <div class="space-y-2 text-sm">
                        <a href="{{ route('bands.index') }}" class="block text-surface-500 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Bands</a>
                        <a href="{{ route('artists.index') }}" class="block text-surface-500 hover:text-accent-600 dark:hover:text-accent-400 transition-colors">Artists</a>
                        <a href="{{ route('genealogy') }}" class="block text-surface-500 hover:text-warm-600 dark:hover:text-warm-400 transition-colors">Genealogy Graph</a>
                        <a href="{{ route('blog.index') }}" class="block text-surface-500 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Blog</a>
                    </div>
                </div>
                <div>
                    <p class="font-display text-sm font-bold mb-3 text-surface-700 dark:text-ink-200">Community</p>
                    <div class="space-y-2 text-sm">
                        <a href="{{ route('register') }}" class="block text-surface-500 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Join</a>
                        <a href="/admin" class="block text-surface-500 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Contribute</a>
                    </div>
                </div>
            </div>
            <div class="mt-10 pt-6 border-t border-surface-200 dark:border-ink-700 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-surface-400">
                <span>&copy; {{ date('Y') }} {{ config('app.name', 'LISTA') }}. Built by the local scene, for the local scene.</span>
                <span>All rights reserved.</span>
            </div>
        </div>
    </footer>

    <!-- Scroll to top -->
    <button x-data="{ visible: false }" x-on:scroll.window="visible = window.scrollY > 500"
            x-show="visible" @click="window.scrollTo({top:0,behavior:'smooth'})" x-cloak
            x-transition.opacity
            class="fixed bottom-6 right-6 w-9 h-9 bg-brand-500 text-white hover:bg-brand-600 shadow-lg transition-colors flex items-center justify-center z-50" aria-label="Top">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M7 17l5-5 5 5M7 11l5-5 5 5"/></svg>
    </button>

    @livewireScripts
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('searchBox', () => ({
                query: '', results: { bands: [], artists: [] }, open: false,
                search() {
                    if (this.query.length < 2) { this.results = {bands:[],artists:[]}; this.open = false; return; }
                    fetch('/api/search?q=' + encodeURIComponent(this.query))
                        .then(r => r.json()).then(data => { this.results = data; this.open = true; });
                },
                select(type, slug) { this.open = false; this.query = ''; window.location.href = '/' + type + 's/' + slug; }
            }));
        });
    </script>
</body>
</html>
