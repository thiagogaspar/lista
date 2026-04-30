use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return 'System';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime('M j, Y H:i')->sortable()->label('Time')->weight('bold'),
                TextColumn::make('user.name')->label('User')->searchable()->sortable(),
                TextColumn::make('auditable_type')->label('Type')->formatStateUsing(fn ($s) => class_basename($s))->badge()->color('gray'),
                TextColumn::make('event')->badge()->color(fn ($s) => match ($s) {
                    'created' => 'success',
                    'updated' => 'warning',
                    'deleted' => 'danger',
                    'restored' => 'info',
                    default => 'gray',
                })->sortable(),
                TextColumn::make('auditable_id')->label('ID')->sortable(),
                TextColumn::make('ip_address')->label('IP')->toggleable()->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('event')->options(['created' => 'Created', 'updated' => 'Updated', 'deleted' => 'Deleted', 'restored' => 'Restored']),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
