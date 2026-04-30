<?php

namespace App\Filament\Resources\BandRelationshipResource\Pages;

use App\Filament\Resources\BandRelationshipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBandRelationship extends EditRecord
{
    protected static string $resource = BandRelationshipResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
