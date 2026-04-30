<?php

namespace App\Filament\Resources\ArtistResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BandRelationManager extends RelationManager
{
    protected static string $relationship = 'bands';

    protected static ?string $title = 'Band History';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('band_id')
                ->relationship('band', 'name')
                ->searchable()
                ->required()
                ->label('Band'),
            TextInput::make('role')->placeholder('Vocalist, Guitarist...'),
            TextInput::make('joined_year')->numeric(),
            TextInput::make('left_year')->numeric(),
            Toggle::make('is_current')->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('band.name')->searchable()->sortable(),
                TextColumn::make('role')->sortable(),
                TextColumn::make('joined_year')->sortable(),
                IconColumn::make('is_current')->boolean(),
            ]);
    }
}
