<?php

namespace App\Filament\Resources;

use App\Filament\Exports\BandExporter;
use App\Filament\Imports\BandImporter;
use App\Filament\Resources\BandResource\Pages;
use App\Filament\Resources\BandResource\RelationManagers\AlbumRelationManager;
use App\Filament\Resources\BandResource\RelationManagers\ArtistRelationManager;
use App\Filament\Resources\BandResource\RelationManagers\RelationshipRelationManager;
use App\Models\Band;
use BackedEnum;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use UnitEnum;

class BandResource extends Resource
{
    protected static ?string $model = Band::class;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-musical-note';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                FileUpload::make('hero_image')
                    ->image()
                    ->maxSize(5120)
                    ->mimeTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->directory('bands/hero')
                    ->label('Hero Banner (1920×400)'),
                FileUpload::make('photo')
                    ->image()
                    ->maxSize(5120)
                    ->mimeTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->directory('bands')
                    ->label('Thumbnail (400×400)'),
                RichEditor::make('bio'),
                FileUpload::make('gallery')
                    ->multiple()
                    ->image()
                    ->maxSize(5120)
                    ->mimeTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->directory('bands/gallery')
                    ->label('Gallery Images'),
                Select::make('genres')
                    ->multiple()
                    ->relationship('genres', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('label_id')
                    ->relationship('label', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('tags')
                    ->multiple()
                    ->relationship('tags', 'name', fn ($q) => $q->where('is_approved', true))
                    ->searchable()
                    ->preload(),
                TextInput::make('formed_year')->numeric(),
                TextInput::make('dissolved_year')->numeric(),
                TextInput::make('origin'),
                Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(BandImporter::class)
                    ->label('Import CSV'),
                ExportAction::make()
                    ->exporter(BandExporter::class)
                    ->label('Export CSV'),
            ])
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                ImageColumn::make('photo')->circular()->size(40),
                TextColumn::make('genres.name')->badge()->sortable(),
                TextColumn::make('label.name')->badge()->color('gray')->sortable(),
                TextColumn::make('formed_year')->sortable(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            ArtistRelationManager::class,
            AlbumRelationManager::class,
            RelationshipRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBands::route('/'),
            'create' => Pages\CreateBand::route('/create'),
            'edit' => Pages\EditBand::route('/{record}/edit'),
        ];
    }
}
