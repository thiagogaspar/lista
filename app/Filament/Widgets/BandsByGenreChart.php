<?php

namespace App\Filament\Widgets;

use App\Models\Genre;
use Filament\Widgets\ChartWidget;

class BandsByGenreChart extends ChartWidget
{
    protected ?string $heading = 'Bands by Genre';

    protected function getData(): array
    {
        $data = Genre::withCount('bands')
            ->orderByDesc('bands_count')
            ->limit(15)
            ->pluck('bands_count', 'name');

        return [
            'datasets' => [
                [
                    'label' => 'Bands',
                    'data' => $data->values(),
                    'backgroundColor' => ['#059669', '#7c3aed', '#d97706', '#2563eb', '#dc2626', '#0891b2', '#ca8a04', '#be185d', '#65a30d', '#0d9488', '#4f46e5', '#b45309', '#9333ea', '#15803d', '#1d4ed8'],
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getColumnSpan(): int|string|array
    {
        return 'half';
    }
}
