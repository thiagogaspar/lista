<?php

namespace App\Filament\Widgets;

use App\Models\Artist;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestArtistsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'half';

    public function table(Table $table): Table
    {
        return $table
            ->query(Artist::latest()->limit(5))
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('origin')->sortable(),
            ]);
    }
}
