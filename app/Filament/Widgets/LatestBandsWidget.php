<?php

namespace App\Filament\Widgets;

use App\Models\Band;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestBandsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'half';

    public function table(Table $table): Table
    {
        return $table
            ->query(Band::latest()->limit(5))
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('genre')->sortable(),
                TextColumn::make('formed_year')->label('Year')->sortable(),
            ]);
    }
}
