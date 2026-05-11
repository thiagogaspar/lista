<x-filament::section>
    <x-slot name="heading">Quick Setup</x-slot>
    <x-slot name="description">One-click data seeding for initial setup. Safe to run multiple times.</x-slot>

    @if ($seeded)
        <div class="text-sm text-green-600 dark:text-green-400 font-medium">
            Done! Bands, artists, albums, labels, and genealogy data have been seeded.
        </div>
    @else
        <div class="flex items-center gap-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Populate the database with demo bands, artists, albums, labels, and genealogy relationships.
            </p>
            <x-filament::button wire:click="seed" color="success" icon="heroicon-o-rocket-launch">
                Run Seeder
            </x-filament::button>
        </div>
    @endif
</x-filament::section>
