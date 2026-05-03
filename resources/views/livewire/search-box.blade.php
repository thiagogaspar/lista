<div class="relative" x-data="{ open: false }" x-on:click.away="open = false">
    <form wire:submit.prevent class="relative">
        <flux:input
            wire:model.live.debounce.300ms="query"
            placeholder="Search bands & artists..."
            class="w-full lg:w-72"
            x-on:focus="open = true"
            x-on:keydown.escape="open = false"
        />
        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </form>

    @if(strlen($query) >= 2 && ($bands->count() || $artists->count()))
        <div x-show="open" class="absolute top-full mt-1 left-0 right-0 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-lg z-50 overflow-hidden">
            @if($bands->count())
                <div class="px-3 pt-2 pb-1 text-xs font-semibold text-zinc-400 uppercase tracking-wider">Bands</div>
                @foreach($bands as $band)
                    <button type="button" wire:click="select('band','{{ $band->slug }}')"
                            class="w-full px-3 py-2 text-left hover:bg-zinc-50 dark:hover:bg-zinc-700 flex items-center justify-between">
                        <span class="font-medium text-brand-500">{{ $band->name }}</span>
                        @if($band->genre)
                            <flux:badge size="sm">{{ $band->genre }}</flux:badge>
                        @endif
                    </button>
                @endforeach
            @endif

            @if($artists->count())
                <div class="px-3 pt-2 pb-1 text-xs font-semibold text-zinc-400 uppercase tracking-wider">Artists</div>
                @foreach($artists as $artist)
                    <button type="button" wire:click="select('artist','{{ $artist->slug }}')"
                            class="w-full px-3 py-2 text-left hover:bg-zinc-50 dark:hover:bg-zinc-700 flex items-center justify-between">
                        <span class="font-medium text-purple-600">{{ $artist->name }}</span>
                        @if($artist->origin)
                            <flux:badge size="sm">{{ $artist->origin }}</flux:badge>
                        @endif
                    </button>
                @endforeach
            @endif
        </div>
    @endif
</div>
