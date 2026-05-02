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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
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
                Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                        RichEditor::make('bio')->columnSpanFull(),
                    ]),
                Section::make('Images')
                    ->columns(3)
                    ->collapsible()
                    ->schema([
                        FileUpload::make('hero_image')->image()->maxSize(5120)->mimeTypes(['image/jpeg', 'image/png', 'image/webp'])->directory('artists/hero')->label('Hero Banner'),
                        FileUpload::make('photo')->image()->maxSize(5120)->mimeTypes(['image/jpeg', 'image/png', 'image/webp'])->directory('artists')->label('Thumbnail'),
                        FileUpload::make('gallery')->multiple()->image()->maxSize(5120)->mimeTypes(['image/jpeg', 'image/png', 'image/webp'])->directory('artists/gallery')->label('Gallery'),
                    ]),
                Section::make('Details')
                    ->columns(3)
                    ->schema([
                        DatePicker::make('birth_date'),
                        DatePicker::make('death_date'),
                        TextInput::make('origin'),
                    ]),
                Section::make('Classification')
                    ->schema([
                        Select::make('tags')->multiple()->relationship('tags', 'name', fn ($q) => $q->where('is_approved', true))->searchable()->preload(),
                    ]),
                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make()->importer(ArtistImporter::class)->label('Import CSV'),
                ExportAction::make()->exporter(ArtistExporter::class)->label('Export CSV'),
            ])
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                ImageColumn::make('photo')->circular()->size(40),
                TextColumn::make('origin')->searchable()->sortable(),
                TextColumn::make('bands')->counts('bands')->sortable()->label('Bands')->toggleable(),
                IconColumn::make('is_active')->boolean()->toggleable(),
                TextColumn::make('created_at')->dateTime('Y-m-d')->sortable()->toggleable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('origin')->options(fn () => Artist::whereNotNull('origin')->distinct()->orderBy('origin')->pluck('origin', 'origin')->all())->label('Origin'),
                SelectFilter::make('is_active')->options([true => 'Active', false => 'Inactive'])->label('Status'),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            BandRelationManager::class,
        ];
    }

    public static function canDelete(Model $model): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canForceDelete(Model $model): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canRestore(Model $model): bool
    {
        return auth()->user()->isEditor();
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
