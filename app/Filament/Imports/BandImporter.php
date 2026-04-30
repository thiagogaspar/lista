<?php

namespace App\Filament\Imports;

use App\Models\Band;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class BandImporter extends Importer
{
    protected static ?string $model = Band::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->required(),
            ImportColumn::make('bio'),
            ImportColumn::make('formed_year'),
            ImportColumn::make('dissolved_year'),
            ImportColumn::make('origin'),
            ImportColumn::make('genre'),
        ];
    }

    public function resolveRecord(): ?Band
    {
        $band = Band::firstOrNew(['slug' => Str::slug($this->data['name'])]);
        $band->name = $this->data['name'];
        $band->bio = $this->data['bio'] ?? null;
        $band->formed_year = $this->data['formed_year'] ?? null;
        $band->dissolved_year = $this->data['dissolved_year'] ?? null;
        $band->origin = $this->data['origin'] ?? null;
        $band->genre = $this->data['genre'] ?? null;

        return $band;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return "Imported {$import->successful_rows} bands.";
    }
}
