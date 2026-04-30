<?php

namespace App\Filament\Resources\BandArtistResource\Pages;

use App\Filament\Resources\BandArtistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBandArtist extends EditRecord
{
    protected static string $resource = BandArtistResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
