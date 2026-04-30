<?php

namespace App\Filament\Resources\EditSuggestionResource\Pages;

use App\Filament\Resources\EditSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEditSuggestion extends EditRecord
{
    protected static string $resource = EditSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
