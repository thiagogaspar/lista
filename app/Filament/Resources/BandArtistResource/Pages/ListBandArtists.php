<?php

namespace App\Filament\Resources\BandArtistResource\Pages;

use App\Filament\Resources\BandArtistResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBandArtists extends ListRecords
{
    protected static string $resource = BandArtistResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
