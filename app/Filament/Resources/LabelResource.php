<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LabelResource\Pages;
use App\Filament\Resources\LabelResource\RelationManagers\BandRelationManager;
use App\Models\Label;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class LabelResource extends Resource
{
    protected static ?string $model = Label::class;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-building-library';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
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
                        TextInput::make('country'),
                        TextInput::make('founded_year')->numeric()->placeholder('e.g. 1988'),
                        TextInput::make('website')->url()->suffixIcon('heroicon-o-globe-alt')->columnSpanFull(),
                    ]),
                Section::make('Media')
                    ->schema([
                        FileUpload::make('logo')->image()->maxSize(5120)->mimeTypes(['image/jpeg', 'image/png', 'image/webp'])->directory('labels')->label('Label Logo'),
                        RichEditor::make('description'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                ImageColumn::make('logo')->circular()->size(40),
                TextColumn::make('country')->sortable(),
                TextColumn::make('founded_year')->sortable(),
                TextColumn::make('bands_count')->counts('bands')->sortable()->label('Bands'),
                TextColumn::make('created_at')->dateTime('Y-m-d')->sortable()->toggleable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->defaultSort('name', 'asc');
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

    public static function getRelations(): array
    {
        return [
            BandRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLabels::route('/'),
            'create' => Pages\CreateLabel::route('/create'),
            'edit' => Pages\EditLabel::route('/{record}/edit'),
        ];
    }
}
