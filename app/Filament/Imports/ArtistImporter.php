<?php

namespace App\Filament\Imports;

use App\Models\Artist;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class ArtistImporter extends Importer
{
    protected static ?string $model = Artist::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->required(),
            ImportColumn::make('bio'),
            ImportColumn::make('birth_date'),
            ImportColumn::make('death_date'),
            ImportColumn::make('origin'),
        ];
    }

    public function resolveRecord(): ?Artist
    {
        $artist = Artist::firstOrNew(['slug' => Str::slug($this->data['name'])]);
        $artist->name = $this->data['name'];
        $artist->bio = $this->data['bio'] ?? null;
        $artist->birth_date = $this->data['birth_date'] ?? null;
        $artist->death_date = $this->data['death_date'] ?? null;
        $artist->origin = $this->data['origin'] ?? null;

        return $artist;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return "Imported {$import->successful_rows} artists.";
    }
}
