<div
    x-data="gallery({{ Js::from($images) }})"
    x-init="init()"
    class="space-y-3"
>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-1">
        <template x-for="(img, i) in images" :key="i">
            <button
                @click="open(i)"
                class="relative aspect-square overflow-hidden border-2 border-surface-200 dark:border-ink-700 bg-surface-100 dark:bg-ink-800 hover:border-brand-500 dark:hover:border-brand-400 transition-colors cursor-pointer"
            >
                <img
                    :src="img.url"
                    :alt="img.alt || ''"
                    class="w-full h-full object-cover"
                    loading="lazy"
                >
            </button>
        </template>
    </div>

    <!-- Lightbox -->
    <div
        x-show="show"
        x-cloak
        @keydown.escape.window="close()"
        @keydown.left.window="prev()"
        @keydown.right.window="next()"
        class="fixed inset-0 z-[9999] bg-black/95 flex items-center justify-center"
        @click.self="close()"
    >
        <button @click="close()" class="absolute top-4 right-4 w-10 h-10 flex items-center justify-center text-white/70 hover:text-white border-2 border-white/20 hover:border-white text-lg font-bold cursor-pointer">&times;</button>

        <button @click="prev()" class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center text-white/70 hover:text-white border-2 border-white/20 hover:border-white text-lg font-bold cursor-pointer">&larr;</button>

        <img :src="images[current].url" :alt="images[current].alt || ''" class="max-w-[90vw] max-h-[85vh] object-contain border-2 border-white/10">

        <button @click="next()" class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center text-white/70 hover:text-white border-2 border-white/20 hover:border-white text-lg font-bold cursor-pointer">&rarr;</button>

        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/50 text-xs">
            <span x-text="current + 1"></span> / <span x-text="images.length"></span>
        </div>
    </div>
</div>
