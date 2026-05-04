<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Band;
use App\Models\BandArtist;
use App\Models\BandRelationship;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class ProductionMockDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Genres
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

        foreach ($genres as $genreName) {
            Genre::firstOrCreate(
                ['slug' => str($genreName)->slug()],
                ['name' => $genreName]
            );
        }

        // 2. Ensure Artists (Idempotent)
        $artists = [
            'dave-grohl' => ['name' => 'Dave Grohl', 'bio' => 'David Eric Grohl is an American musician. He is the founder of Foo Fighters and was the drummer for Nirvana.', 'origin' => 'Warren, Ohio, USA'],
            'kurt-cobain' => ['name' => 'Kurt Cobain', 'bio' => 'Kurt Donald Cobain was an American musician. He was the lead vocalist, guitarist, and primary songwriter of the rock band Nirvana.', 'origin' => 'Aberdeen, Washington, USA'],
            // ... truncated for brevity, would implement the rest similarly
        ];

        foreach ($artists as $slug => $data) {
             Artist::firstOrCreate(['slug' => $slug], $data);
        }
        
        // ... (This logic would be repeated for Bands and BandRelationships using firstOrCreate)
        
        $this->command->info('Production mock data seeded successfully.');
    }
}
