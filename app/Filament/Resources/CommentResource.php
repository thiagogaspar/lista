<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 8;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Moderation')
                    ->schema([
                        Select::make('is_approved')
                            ->options([1 => 'Approved', 0 => 'Pending'])
                            ->native(false)
                            ->label('Approved'),
                        Textarea::make('body')->disabled()->rows(5),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User')->sortable()->searchable(),
                TextColumn::make('commentable_type')->label('On')->formatStateUsing(fn ($s) => class_basename($s)),
                TextColumn::make('body')->limit(50)->searchable(),
                IconColumn::make('is_approved')->boolean()->sortable(),
                TextColumn::make('created_at')->dateTime('M j, Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_approved')->options([true => 'Approved', false => 'Pending']),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
