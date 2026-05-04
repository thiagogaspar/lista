<?php

namespace App\Filament\Pages;

use Database\Seeders\ProductionMockDataSeeder;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class SystemMaintenance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $title = 'System Maintenance';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runProductionSeeder')
                ->label('Run Production Seeder')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    Artisan::call('db:seed', [
                        '--class' => 'ProductionMockDataSeeder',
                        '--force' => true,
                    ]);

                    Notification::make()
                        ->title('Seeding completed')
                        ->success()
                        ->send();
                }),
        ];
    }
}
