<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Preconnect CDNs -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <!-- Font Inter + fallback local -->
    <link rel="preload" href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" as="style">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">

    @yield('preload')

    <link rel="icon" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/favicon.svg">

    @vite(['resources/css/app.css'])
    @fluxAppearance

    <title>{{ config('app.name') }}</title>
    @yield('head')
</head>
<body class="min-h-screen bg-surface-50 dark:bg-surface-900 font-sans antialiased text-surface-900 dark:text-surface-100">
    <header class="sticky top-0 z-50 border-b border-surface-200 dark:border-surface-700 bg-white/95 dark:bg-surface-900/95 backdrop-blur-sm" x-data="{ menu: false }">
        <div class="max-w-7xl mx-auto px-4 h-14 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-bold text-brand-500 shrink-0 hover:text-brand-600 transition-colors">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                {{ config('app.name', 'LISTA') }}
            </a>

            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="{{ route('bands.index') }}" class="px-3 py-2 rounded-lg text-surface-600 dark:text-surface-300 hover:bg-brand-50 dark:hover:bg-brand-900/30 hover:text-brand-600 dark:hover:text-brand-400 transition-colors {{ request()->routeIs('bands.*') ? 'bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400' : '' }}">Bands</a>
                <a href="{{ route('artists.index') }}" class="px-3 py-2 rounded-lg text-surface-600 dark:text-surface-300 hover:bg-accent-50 dark:hover:bg-accent-900/30 hover:text-accent-600 dark:hover:text-accent-400 transition-colors {{ request()->routeIs('artists.*') ? 'bg-accent-50 dark:bg-accent-900/30 text-accent-600 dark:text-accent-400' : '' }}">Artists</a>
                <a href="{{ route('genealogy') }}" class="px-3 py-2 rounded-lg text-surface-600 dark:text-surface-300 hover:bg-warm-50 dark:hover:bg-warm-900/30 hover:text-warm-600 dark:hover:text-warm-400 transition-colors {{ request()->routeIs('genealogy') ? 'bg-warm-50 dark:bg-warm-900/30 text-warm-600 dark:text-warm-400' : '' }}">Genealogy</a>
            </nav>

            <div class="flex items-center gap-2">
                <div class="hidden lg:block relative" x-data="searchBox()">
                    <form @submit.prevent class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input x-model="query" @input.debounce.300ms="search()" @focus="if(query.length>=2) open=true" @keydown.escape="open=false"
                               placeholder="Search..." x-ref="input"
                               class="w-48 lg:w-56 pl-9 pr-3 py-1.5 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 placeholder-surface-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-shadow">
                    </form>
                    <div x-show="open && (results.bands.length || results.artists.length)" @click.away="open = false" x-cloak
                         class="absolute top-full mt-1 right-0 w-72 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-xl shadow-xl z-50 overflow-hidden">
                        <template x-if="results.bands.length">
                            <div>
                                <div class="px-3 pt-2 pb-1 text-[10px] font-semibold text-surface-400 tracking-widest uppercase">Bands</div>
                                <template x-for="band in results.bands" :key="band.id">
                                    <button @click="select('band', band.slug)" class="w-full px-3 py-2 text-left hover:bg-brand-50 dark:hover:bg-brand-900/20 flex items-center justify-between">
                                        <span class="font-medium text-brand-600 dark:text-brand-400" x-text="band.name"></span>
                                        <span x-show="band.genre" class="text-[11px] px-2 py-0.5 rounded-full bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300" x-text="band.genre"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                        <template x-if="results.artists.length">
                            <div>
                                <div class="px-3 pt-2 pb-1 text-[10px] font-semibold text-surface-400 tracking-widest uppercase">Artists</div>
                                <template x-for="artist in results.artists" :key="artist.id">
                                    <button @click="select('artist', artist.slug)" class="w-full px-3 py-2 text-left hover:bg-accent-50 dark:hover:bg-accent-900/20 flex items-center justify-between">
                                        <span class="font-medium text-accent-600 dark:text-accent-400" x-text="artist.name"></span>
                                        <span x-show="artist.origin" class="text-[11px] text-surface-500" x-text="artist.origin"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <a href="/admin" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-surface-500 dark:text-surface-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors" title="Admin" rel="nofollow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </a>

                @auth
                <span class="hidden sm:inline text-xs text-surface-400 mr-2">{{ auth()->user()->name }}</span>
                @else
                <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-surface-500 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Register</a>
                @endauth

                <div x-data="{
                    theme: localStorage.getItem('theme') || 'system',
                    init() {
                        this.applyTheme();
                        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                            if (this.theme === 'system') {
                                let html = document.documentElement;
                                html.style.transition = 'background-color .25s, border-color .2s, color .15s';
                                setTimeout(() => html.style.transition = '', 400);
                                this.applyTheme();
                            }
                        });
                    },
                    applyTheme() {
                        let isDark = this.theme === 'dark' ||
                            (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                        let html = document.documentElement;
                        if (html.classList.contains('dark') !== isDark) {
                            html.style.transition = 'background-color .25s, border-color .2s, color .15s';
                            setTimeout(() => html.style.transition = '', 400);
                        }
                        html.classList.toggle('dark', isDark);
                        localStorage.setItem('theme', this.theme);
                    },
                    setTheme(t) { this.theme = t; this.applyTheme(); }
                }" class="relative" x-init="init()">
                    <button @click="setTheme(theme === 'dark' ? 'light' : 'dark')" class="p-2 rounded-lg text-surface-500 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors" aria-label="Toggle theme">
                        <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                </div>

                <button @click="menu = !menu" class="md:hidden p-2 rounded-lg text-surface-500 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors" aria-label="Menu">
                    <svg class="w-5 h-5" :class="menu ? 'hidden' : 'block'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="w-5 h-5" :class="menu ? 'block' : 'hidden'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div x-show="menu" @click.away="menu = false" x-cloak class="md:hidden border-t border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-900">
            <div class="max-w-7xl mx-auto px-4 py-3 space-y-3">
                <div class="relative" x-data="searchBox()">
                    <form @submit.prevent class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input x-model="query" @input.debounce.300ms="search()" @focus="if(query.length>=2) open=true" @keydown.escape="open=false"
                               placeholder="Search bands & artists..."
                               class="w-full pl-9 pr-3 py-2 text-sm border border-surface-300 dark:border-surface-600 rounded-lg bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 placeholder-surface-400 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </form>
                    <div x-show="open && (results.bands.length || results.artists.length)" @click.away="open = false" x-cloak
                         class="mt-1 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-xl shadow-xl overflow-hidden">
                        <template x-if="results.bands.length">
                            <div>
                                <div class="px-3 pt-2 pb-1 text-[10px] font-semibold text-surface-400 tracking-widest uppercase">Bands</div>
                                <template x-for="band in results.bands" :key="band.id">
                                    <button @click="select('band', band.slug)" class="w-full px-3 py-2 text-left hover:bg-brand-50 dark:hover:bg-brand-900/20 flex items-center justify-between">
                                        <span class="font-medium text-brand-600 dark:text-brand-400" x-text="band.name"></span>
                                        <span x-show="band.genre" class="text-[11px] px-2 py-0.5 rounded-full bg-brand-100 text-brand-700 dark:bg-brand-900/40" x-text="band.genre"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                        <template x-if="results.artists.length">
                            <div>
                                <div class="px-3 pt-2 pb-1 text-[10px] font-semibold text-surface-400 tracking-widest uppercase">Artists</div>
                                <template x-for="artist in results.artists" :key="artist.id">
                                    <button @click="select('artist', artist.slug)" class="w-full px-3 py-2 text-left hover:bg-accent-50 dark:hover:bg-accent-900/20 flex items-center justify-between">
                                        <span class="font-medium text-accent-600 dark:text-accent-400" x-text="artist.name"></span>
                                        <span x-show="artist.origin" class="text-[11px] text-surface-500" x-text="artist.origin"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
                <a href="{{ route('bands.index') }}" class="block px-3 py-2 rounded-lg text-surface-700 dark:text-surface-200 hover:bg-brand-50 dark:hover:bg-brand-900/30 hover:text-brand-600 font-medium">Bands</a>
                <a href="{{ route('artists.index') }}" class="block px-3 py-2 rounded-lg text-surface-700 dark:text-surface-200 hover:bg-accent-50 dark:hover:bg-accent-900/30 hover:text-accent-600 font-medium">Artists</a>
                <a href="{{ route('genealogy') }}" class="block px-3 py-2 rounded-lg text-surface-700 dark:text-surface-200 hover:bg-warm-50 dark:hover:bg-warm-900/30 hover:text-warm-600 font-medium">Genealogy</a>
                <a href="/admin" class="block px-3 py-2 rounded-lg text-surface-500 hover:bg-surface-100 dark:hover:bg-surface-800 text-sm">Admin →</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <footer class="border-t border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-900 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <p class="font-semibold text-brand-600 dark:text-brand-400 mb-2">LISTA 🎸</p>
                    <p class="text-sm text-surface-500 dark:text-surface-400">Connecting bands through the artists who shaped music history.</p>
                </div>
                <div>
                    <p class="font-semibold text-surface-700 dark:text-surface-200 mb-2">Explore</p>
                    <div class="space-y-1 text-sm">
                        <a href="{{ route('bands.index') }}" class="block text-surface-500 hover:text-brand-600 transition-colors">Bands</a>
                        <a href="{{ route('artists.index') }}" class="block text-surface-500 hover:text-accent-600 transition-colors">Artists</a>
                        <a href="{{ route('genealogy') }}" class="block text-surface-500 hover:text-warm-600 transition-colors">Genealogy</a>
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-surface-700 dark:text-surface-200 mb-2">About</p>
                    <div class="space-y-1 text-sm">
                        <p class="text-surface-500">A community-built directory.</p>
                        <x-ad-slot position="footer" />
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-surface-200 dark:border-surface-700 text-center text-xs text-surface-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'LISTA') }}. Built by fans, for fans.
            </div>
        </div>
    </footer>

    <!-- Scroll to top -->
    <button x-data="{ visible: false }" x-on:scroll.window="visible = window.scrollY > 400"
            x-show="visible" @click="window.scrollTo({top:0,behavior:'smooth'})" x-cloak
            class="fixed bottom-6 right-6 w-10 h-10 bg-brand-500 text-white rounded-full shadow-lg hover:bg-brand-600 hover:scale-110 transition-all flex items-center justify-center z-50"
            aria-label="Scroll to top">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
    </button>

    @livewireScripts
    @fluxScripts
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
