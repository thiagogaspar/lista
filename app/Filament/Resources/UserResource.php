<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationIcon(): \BackedEnum|string|Htmlable|null
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'System';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $model): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                Select::make('role')
                    ->options([
                        'viewer' => 'Viewer',
                        'editor' => 'Editor',
                        'admin' => 'Admin',
                    ])
                    ->required(),
                TextInput::make('created_at')->disabled()->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'success',
                        'editor' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
                TextColumn::make('favorites_count')->counts('favorites')->sortable()->label('Favs'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
