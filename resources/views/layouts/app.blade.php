<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preload" href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" as="style">
    <link rel="preload" href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700,800,900&display=swap" as="style">
    <link rel="preload" href="https://fonts.bunny.net/css?family=jetbrains-mono:400,500,600,700&display=swap" as="style">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,500,600,700&display=swap" rel="stylesheet">

    @yield('preload')

    <link rel="icon" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/favicon.svg">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('head')

    <style>
        :focus-visible { outline: 2px solid var(--color-brand-500); outline-offset: 2px; }
        .skip-link { position: absolute; top: -100%; left: 0; z-index: 10000; padding: 0.5rem 1rem; background: var(--color-brand-500); color: white; font-weight: 600; font-size: 0.875rem; }
        .skip-link:focus { top: 0; }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-white dark:bg-black text-black dark:text-white">
    <a href="#main-content" class="skip-link">{{ __('common.skip_to_content') }}</a>

    <header class="sticky top-0 z-50 border-b border-surface-200 dark:border-ink-700 bg-white dark:bg-ink-900" x-data="{ menu: false }" role="banner">
        <div class="max-w-6xl mx-auto px-4 h-12 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-sm font-bold text-brand-600 dark:text-brand-500 shrink-0 hover:text-brand-700 dark:hover:text-brand-400">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                <span class="tracking-tight">{{ config('app.name', 'LISTA') }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-0 text-sm" aria-label="Main navigation">
                <a href="{{ route('bands.index') }}" class="px-3 py-1.5 font-medium text-surface-500 dark:text-ink-400 hover:text-brand-600 dark:hover:text-brand-400 {{ request()->routeIs('bands.*') ? 'text-brand-600 dark:text-brand-400' : '' }}">{{ __('common.nav.bands') }}</a>
                <a href="{{ route('artists.index') }}" class="px-3 py-1.5 font-medium text-surface-500 dark:text-ink-400 hover:text-brand-600 dark:hover:text-brand-400 {{ request()->routeIs('artists.*') ? 'text-brand-600 dark:text-brand-400' : '' }}">{{ __('common.nav.artists') }}</a>
                <a href="{{ route('genealogy') }}" class="px-3 py-1.5 font-medium text-surface-500 dark:text-ink-400 hover:text-brand-600 dark:hover:text-brand-400 {{ request()->routeIs('genealogy') ? 'text-brand-600 dark:text-brand-400' : '' }}">{{ __('common.nav.genealogy') }}</a>
                @auth
                <a href="{{ route('favorites.index') }}" class="px-3 py-1.5 font-medium text-surface-500 dark:text-ink-400 hover:text-brand-600 dark:hover:text-brand-400">{{ __('common.nav.favorites') }}</a>
                @endauth
            </nav>

            <div class="flex items-center gap-0">
                <div class="hidden lg:block relative" x-data="searchBox()">
                    <form @submit.prevent class="relative">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-surface-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input x-model="query" @input.debounce.300ms="search()" @focus="if(query.length>=2) open=true" @keydown.escape="open=false"
                                placeholder="{{ __('common.search') }}..." x-ref="input" aria-label="{{ __('common.search') }}"
                               class="w-36 lg:w-44 pl-8 pr-2 py-1.5 text-xs bg-surface-50 dark:bg-ink-800 border border-surface-200 dark:border-ink-700 text-surface-900 dark:text-ink-200 placeholder-surface-400 focus:outline-none focus:border-brand-500">
                    </form>
                    <div x-show="open && (results.bands.length || results.artists.length)" @click.away="open = false" x-cloak
                         class="absolute top-full mt-0 right-0 w-72 bg-white dark:bg-ink-800 border border-surface-200 dark:border-ink-700 z-50">
                        <template x-if="results.bands.length">
                            <div>
                                <div class="px-3 pt-2 pb-1 text-[9px] font-bold text-surface-400 tracking-wider uppercase">{{ __('common.nav.bands') }}</div>
                                <template x-for="band in results.bands" :key="band.id">
                                    <button @click="select('band', band.slug)" class="w-full px-3 py-2 text-left hover:bg-brand-50 dark:hover:bg-brand-900/30 flex items-center justify-between">
                                        <span class="font-semibold text-sm text-brand-600 dark:text-brand-400" x-text="band.name"></span>
                                        <span x-show="band.genre" class="text-[10px] px-1.5 py-0.5 text-surface-500 dark:text-ink-400 border border-surface-200 dark:border-ink-700" x-text="band.genre"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                        <template x-if="results.artists.length">
                            <div>
                                <div class="px-3 pt-2 pb-1 text-[9px] font-bold text-surface-400 tracking-wider uppercase">{{ __('common.nav.artists') }}</div>
                                <template x-for="artist in results.artists" :key="artist.id">
                                    <button @click="select('artist', artist.slug)" class="w-full px-3 py-2 text-left hover:bg-brand-50 dark:hover:bg-brand-900/30 flex items-center justify-between">
                                        <span class="font-semibold text-sm text-brand-600 dark:text-brand-400" x-text="artist.name"></span>
                                        <span x-show="artist.origin" class="text-[10px] text-surface-500 dark:text-ink-400" x-text="artist.origin"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <a href="/admin" class="hidden sm:inline px-2 py-1 text-xs text-surface-400 hover:text-brand-600 dark:hover:text-brand-400" title="{{ __('common.admin') }}" rel="nofollow">{{ __('common.admin') }}</a>

                @auth
                <span class="hidden sm:inline text-xs text-surface-400 mr-1">{{ auth()->user()->name }}</span>
                @else
                <a href="{{ route('register') }}" class="hidden sm:inline px-2 py-1 text-xs text-surface-400 hover:text-brand-600 dark:hover:text-brand-400">{{ __('common.register') }}</a>
                @endauth

                <button x-data="{ theme: localStorage.getItem('theme') || 'system' }"
                    @click="theme = theme === 'dark' ? 'light' : 'dark'; let isDark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches); document.documentElement.classList.toggle('dark', isDark); localStorage.setItem('theme', theme)"
                    class="p-1.5 text-surface-400 hover:text-surface-700 dark:hover:text-ink-300" aria-label="Toggle theme">
                    <svg class="w-4 h-4 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                <button @click="menu = !menu" class="md:hidden p-1.5 text-surface-400 hover:text-surface-700 dark:hover:text-ink-300" aria-label="Menu">
                    <svg class="w-5 h-5" :class="menu ? 'hidden' : 'block'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="w-5 h-5" :class="menu ? 'block' : 'hidden'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div x-show="menu" @click.away="menu = false" x-cloak class="md:hidden border-t border-surface-200 dark:border-ink-700 bg-white dark:bg-ink-900">
            <div class="max-w-6xl mx-auto px-4 py-4 space-y-3">
                <div class="relative" x-data="searchBox()">
                    <form @submit.prevent class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input x-model="query" @input.debounce.300ms="search()" @focus="if(query.length>=2) open=true" @keydown.escape="open=false" placeholder="Search..." aria-label="Search" class="w-full pl-9 pr-3 py-2 text-sm input">
                    </form>
                    <div x-show="open && (results.bands.length || results.artists.length)" @click.away="open = false" x-cloak class="mt-0 bg-white dark:bg-ink-800 border border-surface-200 dark:border-ink-700">
                        <template x-if="results.bands.length">
                            <div>
                                <div class="px-3 pt-2 pb-1 text-[9px] font-bold text-surface-400 tracking-wider uppercase">Bands</div>
                                <template x-for="band in results.bands" :key="band.id">
                                    <button @click="select('band', band.slug)" class="w-full px-3 py-2 text-left hover:bg-brand-50 dark:hover:bg-brand-900/30 flex items-center justify-between">
                                        <span class="font-semibold text-sm text-brand-600 dark:text-brand-400" x-text="band.name"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                        <template x-if="results.artists.length">
                            <div>
                                <div class="px-3 pt-2 pb-1 text-[9px] font-bold text-surface-400 tracking-wider uppercase">Artists</div>
                                <template x-for="artist in results.artists" :key="artist.id">
                                    <button @click="select('artist', artist.slug)" class="w-full px-3 py-2 text-left hover:bg-brand-50 dark:hover:bg-brand-900/30 flex items-center justify-between">
                                        <span class="font-semibold text-sm text-brand-600 dark:text-brand-400" x-text="artist.name"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
                <a href="{{ route('bands.index') }}" class="block px-3 py-2 text-sm text-surface-700 dark:text-ink-200 hover:text-brand-600 dark:hover:text-brand-400 font-medium">{{ __('common.nav.bands') }}</a>
                <a href="{{ route('artists.index') }}" class="block px-3 py-2 text-sm text-surface-700 dark:text-ink-200 hover:text-brand-600 dark:hover:text-brand-400 font-medium">{{ __('common.nav.artists') }}</a>
                <a href="{{ route('genealogy') }}" class="block px-3 py-2 text-sm text-surface-700 dark:text-ink-200 hover:text-brand-600 dark:hover:text-brand-400 font-medium">{{ __('common.nav.genealogy') }}</a>
                @auth
                <a href="{{ route('favorites.index') }}" class="block px-3 py-2 text-sm text-surface-700 dark:text-ink-200 hover:text-brand-600 font-medium">{{ __('common.nav.favorites') }}</a>
                @endauth
                <a href="/admin" class="block px-3 py-2 text-xs text-surface-400">{{ __('common.admin') }}</a>
            </div>
        </div>
    </header>

    <main id="main-content" role="main" class="relative">
        @yield('content')
    </main>

    <footer class="border-t border-surface-200 dark:border-ink-700 mt-12 bg-white dark:bg-ink-900">
        <div class="max-w-6xl mx-auto px-4 py-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        <span class="text-base font-bold text-brand-600 dark:text-brand-500">{{ config('app.name', 'LISTA') }}</span>
                    </div>
                    <p class="text-sm text-surface-500 dark:text-ink-400 max-w-xs leading-relaxed">{{ __('common.footer.tagline') }}</p>
                    <div class="flex gap-3 mt-4">
                        <a href="{{ route('bands.index') }}" class="text-xs font-bold uppercase tracking-wider text-surface-400 hover:text-brand-600 dark:hover:text-brand-400">Bands</a>
                        <a href="{{ route('artists.index') }}" class="text-xs font-bold uppercase tracking-wider text-surface-400 hover:text-brand-600 dark:hover:text-brand-400">Artists</a>
                        <a href="{{ route('labels.index') }}" class="text-xs font-bold uppercase tracking-wider text-surface-400 hover:text-brand-600 dark:hover:text-brand-400">Labels</a>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-surface-600 dark:text-ink-400 mb-3">{{ __('common.footer.explore') }}</p>
                    <div class="space-y-2 text-sm">
                        <a href="{{ route('bands.index') }}" class="block text-surface-500 hover:text-surface-900 dark:hover:text-ink-100 hover:bg-surface-100 dark:hover:bg-ink-800 px-1 -mx-1 rounded-sm">{{ __('common.nav.bands') }}</a>
                        <a href="{{ route('artists.index') }}" class="block text-surface-500 hover:text-surface-900 dark:hover:text-ink-100 hover:bg-surface-100 dark:hover:bg-ink-800 px-1 -mx-1 rounded-sm">{{ __('common.nav.artists') }}</a>
                        <a href="{{ route('albums.index') }}" class="block text-surface-500 hover:text-surface-900 dark:hover:text-ink-100 hover:bg-surface-100 dark:hover:bg-ink-800 px-1 -mx-1 rounded-sm">{{ __('common.nav.albums') }}</a>
                        <a href="{{ route('genealogy') }}" class="block text-surface-500 hover:text-surface-900 dark:hover:text-ink-100 hover:bg-surface-100 dark:hover:bg-ink-800 px-1 -mx-1 rounded-sm">{{ __('common.nav.genealogy') }}</a>
                        <a href="{{ route('blog.index') }}" class="block text-surface-500 hover:text-surface-900 dark:hover:text-ink-100 hover:bg-surface-100 dark:hover:bg-ink-800 px-1 -mx-1 rounded-sm">{{ __('common.nav.blog') }}</a>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-surface-600 dark:text-ink-400 mb-3">{{ __('common.footer.community') }}</p>
                    <div class="space-y-2 text-sm">
                        <a href="{{ route('register') }}" class="block text-surface-500 hover:text-surface-900 dark:hover:text-ink-100 hover:bg-surface-100 dark:hover:bg-ink-800 px-1 -mx-1 rounded-sm">{{ __('common.join') }}</a>
                        <a href="/admin" class="block text-surface-500 hover:text-surface-900 dark:hover:text-ink-100 hover:bg-surface-100 dark:hover:bg-ink-800 px-1 -mx-1 rounded-sm">{{ __('common.contribute') }}</a>
                        <a href="{{ route('labels.index') }}" class="block text-surface-500 hover:text-surface-900 dark:hover:text-ink-100 hover:bg-surface-100 dark:hover:bg-ink-800 px-1 -mx-1 rounded-sm">Labels A–Z</a>
                        <a href="/sitemap.xml" class="block text-surface-500 hover:text-surface-900 dark:hover:text-ink-100 hover:bg-surface-100 dark:hover:bg-ink-800 px-1 -mx-1 rounded-sm">Sitemap</a>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-surface-600 dark:text-ink-400 mb-3">Genres</p>
                    <div class="space-y-2 text-sm">
                        @php $footerGenres = app(\App\Services\BandService::class)->getGenres(); @endphp
                        @foreach(array_slice($footerGenres, 0, 8) as $slug => $name)
                        <a href="{{ route('genres.show', $slug) }}" class="block text-surface-500 hover:text-surface-900 dark:hover:text-ink-100 hover:bg-surface-100 dark:hover:bg-ink-800 px-1 -mx-1 rounded-sm">{{ $name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-10 pt-6 border-t border-surface-200 dark:border-ink-700 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-surface-400">
                <span>&copy; {{ date('Y') }} {{ config('app.name', 'LISTA') }}. {{ __('common.footer.copyright') }}</span>
                <div class="flex gap-3">
                    <a href="/sitemap.xml" class="hover:text-surface-600 dark:hover:text-ink-300">Sitemap</a>
                    <a href="/admin" class="hover:text-surface-600 dark:hover:text-ink-300">Admin</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    <style>[x-cloak] { display: none !important; }</style>
</body>
</html>
