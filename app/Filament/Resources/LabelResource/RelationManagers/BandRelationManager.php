<?php

namespace App\Filament\Resources\LabelResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BandRelationManager extends RelationManager
{
    protected static string $relationship = 'bands';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    ]),
                Section::make('Classification')
                    ->schema([
                        Select::make('genres')->multiple()->relationship('genres', 'name'),
                        Toggle::make('is_active')->default(true),
                    ]),
                Section::make('Timeline')
                    ->schema([
                        TextInput::make('formed_year')->numeric(),
                        TextInput::make('dissolved_year')->numeric(),
                        TextInput::make('origin'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('formed_year')->sortable(),
                TextColumn::make('origin')->sortable(),
            ]);
    }
}
