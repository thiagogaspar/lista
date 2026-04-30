<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BandRelationshipResource\Pages;
use App\Models\BandRelationship;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class BandRelationshipResource extends Resource
{
    protected static ?string $model = BandRelationship::class;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-arrow-right-circle';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Relations';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_band_id')
                    ->relationship('parentBand', 'name')
                    ->searchable()
                    ->required()
                    ->label('Parent Band'),
                Select::make('child_band_id')
                    ->relationship('childBand', 'name')
                    ->searchable()
                    ->required()
                    ->label('Child Band'),
                Select::make('type')
                    ->options(BandRelationship::types())
                    ->required(),
                Textarea::make('description'),
                TextInput::make('year')->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parentBand.name')->searchable()->sortable(),
                TextColumn::make('type')
                    ->formatStateUsing(fn ($state) => BandRelationship::types()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('childBand.name')->searchable()->sortable(),
                TextColumn::make('year')->sortable(),
            ])
            ->defaultSort('year', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBandRelationships::route('/'),
            'create' => Pages\CreateBandRelationship::route('/create'),
            'edit' => Pages\EditBandRelationship::route('/{record}/edit'),
        ];
    }
}
