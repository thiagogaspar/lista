<?php

namespace App\Filament\Widgets;

use App\Models\Artist;
use App\Models\Band;
use App\Models\BandArtist;
use App\Models\BandRelationship;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Bands', Band::count())
                ->description('Registered bands')
                ->color('success'),

            Stat::make('Artists', Artist::count())
                ->description('Registered artists')
                ->color('warning'),

            Stat::make('Memberships', BandArtist::count())
                ->description('Artist-band connections')
                ->color('info'),

            Stat::make('Relationships', BandRelationship::count())
                ->description('Band-band connections')
                ->color('danger'),
        ];
    }
}
