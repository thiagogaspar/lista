<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Artisan;

class SetupWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.setup-widget';

    public bool $seeded = false;

    public function seed(): void
    {
        Artisan::call('app:create-admin-user');
        Artisan::call('db:seed', [
            '--class' => 'ProductionMockDataSeeder',
            '--force' => true,
        ]);

        $this->seeded = true;
    }
}
