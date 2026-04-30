<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ArtistExporter;
use App\Filament\Imports\ArtistImporter;
use App\Filament\Resources\ArtistResource\Pages;
use App\Filament\Resources\ArtistResource\RelationManagers\BandRelationManager;
use App\Models\Artist;
use BackedEnum;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\DatePicker;
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

class ArtistResource extends Resource
{
    protected static ?string $model = Artist::class;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
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
                    ->directory('artists/hero')
                    ->label('Hero Banner (1920×400)'),
                FileUpload::make('photo')
                    ->image()
                    ->directory('artists')
                    ->label('Thumbnail (400×400)'),
                RichEditor::make('bio'),
                FileUpload::make('gallery')
                    ->multiple()
                    ->image()
                    ->directory('artists/gallery')
                    ->label('Gallery Images'),
                DatePicker::make('birth_date'),
                DatePicker::make('death_date'),
                TextInput::make('origin'),
                Select::make('tags')
                    ->multiple()
                    ->relationship('tags', 'name', fn ($q) => $q->where('is_approved', true))
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(ArtistImporter::class)
                    ->label('Import CSV'),
                ExportAction::make()
                    ->exporter(ArtistExporter::class)
                    ->label('Export CSV'),
            ])
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                ImageColumn::make('photo')->circular()->size(40),
                TextColumn::make('origin')->sortable(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            BandRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArtists::route('/'),
            'create' => Pages\CreateArtist::route('/create'),
            'edit' => Pages\EditArtist::route('/{record}/edit'),
        ];
    }
}
