<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Grunge', 'Alternative Rock', 'Hard Rock', 'Rap Metal',
            'Heavy Metal', 'Punk Rock', 'Indie Rock', 'Pop Rock',
            'Post-Grunge', 'Nu Metal', 'Thrash Metal', 'Death Metal',
            'Black Metal', 'Progressive Rock', 'Psychedelic Rock',
            'Blues Rock', 'Folk Rock', 'Electronic', 'Hip Hop', 'R&B',
            'Country', 'Jazz', 'Classical', 'Reggae', 'Ska', 'Emo',
            'Screamo', 'Metalcore', 'Deathcore', 'Electronicore',
            'Synthwave', 'Dream Pop', 'Shoegaze', 'Noise Rock',
            'Experimental', 'Ambient', 'Post-Rock', 'Post-Punk',
            'New Wave', 'Gothic Rock', 'Doom Metal', 'Stoner Rock',
            'Sludge Metal', 'Southern Rock', 'Funk', 'Soul', 'Disco',
            'Techno', 'House', 'Trance', 'Drum and Bass', 'Dubstep',
        ];

        foreach ($genres as $genre) {
            Genre::create([
                'name' => $genre,
                'slug' => str($genre)->slug(),
            ]);
        }
    }
}
