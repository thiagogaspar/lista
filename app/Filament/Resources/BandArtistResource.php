<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BandArtistResource\Pages;
use App\Models\BandArtist;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class BandArtistResource extends Resource
{
    protected static ?string $model = BandArtist::class;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-user-plus';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Relations';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Membership')
                    ->columns(2)
                    ->schema([
                        Select::make('band_id')->relationship('band', 'name')->searchable()->required(),
                        Select::make('artist_id')->relationship('artist', 'name')->searchable()->required(),
                        TextInput::make('role')->placeholder('Vocalist, Guitarist...'),
                        TextInput::make('joined_year')->numeric(),
                        TextInput::make('left_year')->numeric(),
                        Toggle::make('is_current')->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('band.name')->searchable()->sortable()->weight('bold'),
                TextColumn::make('artist.name')->searchable()->sortable(),
                TextColumn::make('role')->badge()->sortable(),
                TextColumn::make('joined_year')->sortable(),
                TextColumn::make('left_year')->sortable()->placeholder('present'),
                IconColumn::make('is_current')->boolean()->sortable(),
            ])
            ->defaultSort('band_id', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBandArtists::route('/'),
            'create' => Pages\CreateBandArtist::route('/create'),
            'edit' => Pages\EditBandArtist::route('/{record}/edit'),
        ];
    }
}
