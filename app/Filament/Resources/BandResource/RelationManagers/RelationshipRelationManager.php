<?php

namespace App\Filament\Resources\BandResource\RelationManagers;

use App\Models\BandRelationship;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RelationshipRelationManager extends RelationManager
{
    protected static string $relationship = 'relatedTo';

    protected static ?string $title = 'Relationships';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('child_band_id')
                ->relationship('childBand', 'name')
                ->searchable()
                ->required()
                ->label('Related Band'),
            Select::make('type')
                ->options(BandRelationship::types())
                ->required(),
            Textarea::make('description'),
            TextInput::make('year')->numeric(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('childBand.name')->searchable()->sortable()->label('Related Band'),
                TextColumn::make('type')
                    ->formatStateUsing(fn ($state) => BandRelationship::types()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('year')->sortable(),
            ]);
    }
}
