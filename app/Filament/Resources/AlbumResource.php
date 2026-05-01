<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlbumResource\Pages;
use App\Models\Album;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-rectangle-stack';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 7;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Album Info')
                    ->columns(2)
                    ->schema([
                        Select::make('band_id')->relationship('band', 'name')->searchable()->required(),
                        TextInput::make('release_year')->numeric()->placeholder('e.g. 1991'),
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->columnSpanFull()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true),
                        FileUpload::make('cover_art')->image()->maxSize(5120)->mimeTypes(['image/jpeg', 'image/png', 'image/webp'])->directory('albums')->label('Cover Art'),
                        RichEditor::make('description')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('band.name')->searchable()->sortable()->label('Band'),
                TextColumn::make('title')->searchable()->sortable()->weight('bold'),
                TextColumn::make('release_year')->sortable(),
                ImageColumn::make('cover_art')->circular()->size(40),
                TextColumn::make('created_at')->dateTime('Y-m-d')->sortable()->toggleable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('release_year')
                    ->options(fn () => Album::whereNotNull('release_year')->distinct()->orderBy('release_year', 'desc')->pluck('release_year', 'release_year')->all()),
            ])
            ->defaultSort('release_year', 'desc');
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
            'index' => Pages\ListAlbums::route('/'),
            'create' => Pages\CreateAlbum::route('/create'),
            'edit' => Pages\EditAlbum::route('/{record}/edit'),
        ];
    }
}
