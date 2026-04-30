<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EditSuggestionResource\Pages;
use App\Models\EditSuggestion;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class EditSuggestionResource extends Resource
{
    protected static ?string $model = EditSuggestion::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-pencil-square';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                    ->required(),
                Textarea::make('field')->disabled(),
                Textarea::make('current_value')->disabled(),
                Textarea::make('suggested_value')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('By')->sortable(),
                TextColumn::make('suggestable_type')->label('On')->formatStateUsing(fn ($s) => class_basename($s)),
                TextColumn::make('field')->sortable(),
                TextColumn::make('status')->badge()->color(fn ($s) => match ($s) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'gray',
                })->sortable(),
                TextColumn::make('created_at')->dateTime('Y-m-d')->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEditSuggestions::route('/'),
            'edit' => Pages\EditEditSuggestion::route('/{record}/edit'),
        ];
    }
}
