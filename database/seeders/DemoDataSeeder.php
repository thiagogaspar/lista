<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Band;
use App\Models\BandArtist;
use App\Models\BandRelationship;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $artist1 = Artist::create([
            'name' => 'Dave Grohl',
            'slug' => 'dave-grohl',
            'bio' => 'David Eric Grohl is an American musician. He is the founder of Foo Fighters and was the drummer for Nirvana.',
            'origin' => 'Warren, Ohio, USA',
        ]);
        $artist2 = Artist::create([
            'name' => 'Kurt Cobain',
            'slug' => 'kurt-cobain',
            'bio' => 'Kurt Donald Cobain was an American musician. He was the lead vocalist, guitarist, and primary songwriter of the rock band Nirvana.',
            'origin' => 'Aberdeen, Washington, USA',
        ]);
        $artist3 = Artist::create([
            'name' => 'Krist Novoselic',
            'slug' => 'krist-novoselic',
            'bio' => 'Krist Anthony Novoselic is an American musician. He was the bassist and co-founder of the band Nirvana.',
            'origin' => 'Compton, California, USA',
        ]);
        $artist4 = Artist::create([
            'name' => 'Eddie Vedder',
            'slug' => 'eddie-vedder',
            'bio' => 'Eddie Jerome Vedder is an American singer. He is the lead vocalist and one of four guitarists for Pearl Jam.',
            'origin' => 'Evanston, Illinois, USA',
        ]);
        $artist5 = Artist::create([
            'name' => 'Chris Cornell',
            'slug' => 'chris-cornell',
            'bio' => 'Christopher John Cornell was an American singer and musician. He was the lead vocalist and rhythm guitarist for Soundgarden and Audioslave.',
            'origin' => 'Seattle, Washington, USA',
        ]);
        $artist6 = Artist::create([
            'name' => 'Tom Morello',
            'slug' => 'tom-morello',
            'bio' => 'Thomas Baptist Morello is an American guitarist. He is best known for his work with Rage Against the Machine and Audioslave.',
            'origin' => 'New York City, USA',
        ]);
        $artist7 = Artist::create([
            'name' => 'Jeff Ament',
            'slug' => 'jeff-ament',
            'bio' => 'Jeffrey Allen Ament is an American bassist and songwriter. He is a founding member of Pearl Jam.',
            'origin' => 'Havre, Montana, USA',
        ]);
        $artist8 = Artist::create([
            'name' => 'Mike McCready',
            'slug' => 'mike-mccready',
            'bio' => 'Michael Thomas McCready is an American guitarist. He has been the lead guitarist of Pearl Jam since 1990.',
            'origin' => 'Seattle, Washington, USA',
        ]);
        $artist9 = Artist::create([
            'name' => 'Stone Gossard',
            'slug' => 'stone-gossard',
            'bio' => 'Stone Carpenter Gossard is an American musician and songwriter. He is the rhythm guitarist of Pearl Jam.',
            'origin' => 'Seattle, Washington, USA',
        ]);
        $artist10 = Artist::create([
            'name' => 'Matt Cameron',
            'slug' => 'matt-cameron',
            'bio' => 'Matthew Cameron is an American drummer. He played for Soundgarden and Pearl Jam.',
            'origin' => 'San Diego, California, USA',
        ]);
        $artist11 = Artist::create([
            'name' => 'Kim Thayil',
            'slug' => 'kim-thayil',
            'bio' => 'Kim Thayil is an American musician. He is the lead guitarist of Soundgarden.',
            'origin' => 'Seattle, Washington, USA',
        ]);
        $artist12 = Artist::create([
            'name' => 'Ben Shepherd',
            'slug' => 'ben-shepherd',
            'bio' => 'Ben Shepherd is an American musician. He was the bassist of Soundgarden.',
            'origin' => 'Seattle, Washington, USA',
        ]);
        $artist13 = Artist::create([
            'name' => 'Zack de la Rocha',
            'slug' => 'zack-de-la-rocha',
            'bio' => 'Zacharias Manuel de la Rocha is an American rapper and activist. He is the lead vocalist of Rage Against the Machine.',
            'origin' => 'Long Beach, California, USA',
        ]);
        $artist14 = Artist::create([
            'name' => 'Tim Commerford',
            'slug' => 'tim-commerford',
            'bio' => 'Timothy Robert Commerford is an American musician. He is the bassist of Rage Against the Machine.',
            'origin' => 'Irvine, California, USA',
        ]);
        $artist15 = Artist::create([
            'name' => 'Brad Wilk',
            'slug' => 'brad-wilk',
            'bio' => 'Brad Wilk is an American drummer. He played for Rage Against the Machine and Audioslave.',
            'origin' => 'Portland, Oregon, USA',
        ]);

        $nirvana = Band::create(['name' => 'Nirvana', 'slug' => 'nirvana', 'bio' => 'Nirvana was an American rock band formed in Aberdeen, Washington, in 1987. The band pioneered the grunge movement.', 'formed_year' => 1987, 'dissolved_year' => 1994, 'origin' => 'Aberdeen, Washington, USA', 'genre' => 'Grunge']);
        $fooFighters = Band::create(['name' => 'Foo Fighters', 'slug' => 'foo-fighters', 'bio' => 'Foo Fighters is an American rock band formed in Seattle in 1994 by Dave Grohl.', 'formed_year' => 1994, 'origin' => 'Seattle, Washington, USA', 'genre' => 'Alternative Rock']);
        $pearlJam = Band::create(['name' => 'Pearl Jam', 'slug' => 'pearl-jam', 'bio' => 'Pearl Jam is an American rock band formed in Seattle, Washington, in 1990.', 'formed_year' => 1990, 'origin' => 'Seattle, Washington, USA', 'genre' => 'Grunge']);
        $soundgarden = Band::create(['name' => 'Soundgarden', 'slug' => 'soundgarden', 'bio' => 'Soundgarden was an American rock band formed in Seattle, Washington, in 1984.', 'formed_year' => 1984, 'dissolved_year' => 2019, 'origin' => 'Seattle, Washington, USA', 'genre' => 'Grunge']);
        $audioslave = Band::create(['name' => 'Audioslave', 'slug' => 'audioslave', 'bio' => 'Audioslave was an American rock supergroup formed in 2001 by members of Rage Against the Machine and Soundgarden.', 'formed_year' => 2001, 'dissolved_year' => 2007, 'origin' => 'Los Angeles, California, USA', 'genre' => 'Hard Rock']);
        $rageMachine = Band::create(['name' => 'Rage Against the Machine', 'slug' => 'rage-against-the-machine', 'bio' => 'Rage Against the Machine is an American rap metal band formed in Los Angeles, California, in 1991.', 'formed_year' => 1991, 'dissolved_year' => 2011, 'origin' => 'Los Angeles, California, USA', 'genre' => 'Rap Metal']);
        $greenRiver = Band::create(['name' => 'Green River', 'slug' => 'green-river', 'bio' => 'Green River was an American rock band formed in Seattle, Washington in 1985. They are considered pioneers of the grunge genre.', 'formed_year' => 1985, 'dissolved_year' => 1987, 'origin' => 'Seattle, Washington, USA', 'genre' => 'Grunge']);
        $motherLoveBone = Band::create(['name' => 'Mother Love Bone', 'slug' => 'mother-love-bone', 'bio' => 'Mother Love Bone was an American rock band formed in Seattle in 1988 by ex-members of Green River.', 'formed_year' => 1988, 'dissolved_year' => 1990, 'origin' => 'Seattle, Washington, USA', 'genre' => 'Grunge']);
        $templeDog = Band::create(['name' => 'Temple of the Dog', 'slug' => 'temple-of-the-dog', 'bio' => 'Temple of the Dog was an American rock supergroup formed in Seattle in 1990 by members of Soundgarden and Pearl Jam.', 'formed_year' => 1990, 'dissolved_year' => 1991, 'origin' => 'Seattle, Washington, USA', 'genre' => 'Grunge']);

        BandArtist::create(['band_id' => $nirvana->id, 'artist_id' => $artist1->id, 'role' => 'Drummer', 'joined_year' => 1990, 'left_year' => 1994]);
        BandArtist::create(['band_id' => $nirvana->id, 'artist_id' => $artist2->id, 'role' => 'Vocalist / Guitarist', 'joined_year' => 1987, 'left_year' => 1994]);
        BandArtist::create(['band_id' => $nirvana->id, 'artist_id' => $artist3->id, 'role' => 'Bassist', 'joined_year' => 1987, 'left_year' => 1994]);
        BandArtist::create(['band_id' => $fooFighters->id, 'artist_id' => $artist1->id, 'role' => 'Vocalist / Guitarist', 'joined_year' => 1994, 'is_current' => true]);
        BandArtist::create(['band_id' => $pearlJam->id, 'artist_id' => $artist4->id, 'role' => 'Vocalist', 'joined_year' => 1990, 'is_current' => true]);
        BandArtist::create(['band_id' => $pearlJam->id, 'artist_id' => $artist7->id, 'role' => 'Bassist', 'joined_year' => 1990, 'is_current' => true]);
        BandArtist::create(['band_id' => $pearlJam->id, 'artist_id' => $artist8->id, 'role' => 'Lead Guitarist', 'joined_year' => 1990, 'is_current' => true]);
        BandArtist::create(['band_id' => $pearlJam->id, 'artist_id' => $artist9->id, 'role' => 'Rhythm Guitarist', 'joined_year' => 1990, 'is_current' => true]);
        BandArtist::create(['band_id' => $pearlJam->id, 'artist_id' => $artist10->id, 'role' => 'Drummer', 'joined_year' => 1998, 'is_current' => true]);
        BandArtist::create(['band_id' => $soundgarden->id, 'artist_id' => $artist5->id, 'role' => 'Vocalist / Guitarist', 'joined_year' => 1984, 'left_year' => 2019]);
        BandArtist::create(['band_id' => $soundgarden->id, 'artist_id' => $artist10->id, 'role' => 'Drummer', 'joined_year' => 1986, 'left_year' => 1997]);
        BandArtist::create(['band_id' => $soundgarden->id, 'artist_id' => $artist11->id, 'role' => 'Lead Guitarist', 'joined_year' => 1984, 'left_year' => 2019]);
        BandArtist::create(['band_id' => $soundgarden->id, 'artist_id' => $artist12->id, 'role' => 'Bassist', 'joined_year' => 1990, 'left_year' => 2019]);
        BandArtist::create(['band_id' => $audioslave->id, 'artist_id' => $artist5->id, 'role' => 'Vocalist', 'joined_year' => 2001, 'left_year' => 2007]);
        BandArtist::create(['band_id' => $audioslave->id, 'artist_id' => $artist6->id, 'role' => 'Guitarist', 'joined_year' => 2001, 'left_year' => 2007]);
        BandArtist::create(['band_id' => $audioslave->id, 'artist_id' => $artist14->id, 'role' => 'Bassist', 'joined_year' => 2001, 'left_year' => 2007]);
        BandArtist::create(['band_id' => $audioslave->id, 'artist_id' => $artist15->id, 'role' => 'Drummer', 'joined_year' => 2001, 'left_year' => 2007]);
        BandArtist::create(['band_id' => $rageMachine->id, 'artist_id' => $artist6->id, 'role' => 'Guitarist', 'joined_year' => 1991, 'left_year' => 2011]);
        BandArtist::create(['band_id' => $rageMachine->id, 'artist_id' => $artist13->id, 'role' => 'Vocalist', 'joined_year' => 1991, 'left_year' => 2011]);
        BandArtist::create(['band_id' => $rageMachine->id, 'artist_id' => $artist14->id, 'role' => 'Bassist', 'joined_year' => 1991, 'left_year' => 2011]);
        BandArtist::create(['band_id' => $rageMachine->id, 'artist_id' => $artist15->id, 'role' => 'Drummer', 'joined_year' => 1991, 'left_year' => 2011]);
        BandArtist::create(['band_id' => $greenRiver->id, 'artist_id' => $artist7->id, 'role' => 'Bassist', 'joined_year' => 1985, 'left_year' => 1987]);
        BandArtist::create(['band_id' => $greenRiver->id, 'artist_id' => $artist9->id, 'role' => 'Guitarist', 'joined_year' => 1985, 'left_year' => 1987]);
        BandArtist::create(['band_id' => $motherLoveBone->id, 'artist_id' => $artist7->id, 'role' => 'Bassist', 'joined_year' => 1988, 'left_year' => 1990]);
        BandArtist::create(['band_id' => $motherLoveBone->id, 'artist_id' => $artist9->id, 'role' => 'Guitarist', 'joined_year' => 1988, 'left_year' => 1990]);
        BandArtist::create(['band_id' => $templeDog->id, 'artist_id' => $artist4->id, 'role' => 'Vocalist', 'joined_year' => 1990, 'left_year' => 1991]);
        BandArtist::create(['band_id' => $templeDog->id, 'artist_id' => $artist5->id, 'role' => 'Vocalist / Guitarist', 'joined_year' => 1990, 'left_year' => 1991]);
        BandArtist::create(['band_id' => $templeDog->id, 'artist_id' => $artist7->id, 'role' => 'Bassist', 'joined_year' => 1990, 'left_year' => 1991]);
        BandArtist::create(['band_id' => $templeDog->id, 'artist_id' => $artist9->id, 'role' => 'Guitarist', 'joined_year' => 1990, 'left_year' => 1991]);
        BandArtist::create(['band_id' => $templeDog->id, 'artist_id' => $artist10->id, 'role' => 'Drummer', 'joined_year' => 1990, 'left_year' => 1991]);

        BandRelationship::create(['parent_band_id' => $nirvana->id, 'child_band_id' => $fooFighters->id, 'type' => 'members_formed', 'description' => 'After Kurt Cobain\'s death, drummer Dave Grohl formed Foo Fighters.', 'year' => 1994]);
        BandRelationship::create(['parent_band_id' => $soundgarden->id, 'child_band_id' => $audioslave->id, 'type' => 'members_formed', 'description' => 'Chris Cornell joined with members of Rage Against the Machine to form Audioslave.', 'year' => 2001]);
        BandRelationship::create(['parent_band_id' => $rageMachine->id, 'child_band_id' => $audioslave->id, 'type' => 'members_formed', 'description' => 'Three members of Rage Against the Machine (Morello, Commerford, Wilk) joined Chris Cornell to form Audioslave.', 'year' => 2001]);
        BandRelationship::create(['parent_band_id' => $greenRiver->id, 'child_band_id' => $motherLoveBone->id, 'type' => 'evolved_into', 'description' => 'After Green River dissolved, members formed Mother Love Bone.', 'year' => 1988]);
        BandRelationship::create(['parent_band_id' => $motherLoveBone->id, 'child_band_id' => $pearlJam->id, 'type' => 'members_formed', 'description' => 'After the death of Andrew Wood, remaining Mother Love Bone members formed Pearl Jam with Eddie Vedder.', 'year' => 1990]);
        BandRelationship::create(['parent_band_id' => $soundgarden->id, 'child_band_id' => $templeDog->id, 'type' => 'side_project', 'description' => 'Soundgarden and Pearl Jam members formed Temple of the Dog as a tribute to Andrew Wood.', 'year' => 1990]);
        BandRelationship::create(['parent_band_id' => $pearlJam->id, 'child_band_id' => $templeDog->id, 'type' => 'side_project', 'description' => 'Pearl Jam and Soundgarden members formed Temple of the Dog.', 'year' => 1990]);
        BandRelationship::create(['parent_band_id' => $soundgarden->id, 'child_band_id' => $pearlJam->id, 'type' => 'members_formed', 'description' => 'Matt Cameron moved from Soundgarden to Pearl Jam as drummer.', 'year' => 1998]);

        // Attach genres
        $grunge = Genre::whereSlug('grunge')->first();
        $altRock = Genre::whereSlug('alternative-rock')->first();
        $hardRock = Genre::whereSlug('hard-rock')->first();
        $rapMetal = Genre::whereSlug('rap-metal')->first();

        $nirvana->genres()->attach([$grunge->id]);
        $fooFighters->genres()->attach([$altRock->id]);
        $pearlJam->genres()->attach([$grunge->id]);
        $soundgarden->genres()->attach([$grunge->id]);
        $audioslave->genres()->attach([$hardRock->id]);
        $rageMachine->genres()->attach([$rapMetal->id]);
        $greenRiver->genres()->attach([$grunge->id]);
        $motherLoveBone->genres()->attach([$grunge->id]);
        $templeDog->genres()->attach([$grunge->id]);
    }
}
