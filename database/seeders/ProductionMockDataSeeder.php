<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Band;
use App\Models\BandArtist;
use App\Models\BandRelationship;
use App\Models\Genre;
use App\Models\Label;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionMockDataSeeder extends Seeder
{
    private const WIKI = 'https://upload.wikimedia.org/wikipedia/commons';

    private const PICSUM = 'https://picsum.photos/seed';

    private function photoUrl(?string $commons, string $fallbackSeed): string
    {
        return $commons ?? (self::PICSUM."/{$fallbackSeed}/400/400");
    }

    private function photoUrlPortrait(?string $commons, string $fallbackSeed): string
    {
        return $commons ?? (self::PICSUM."/{$fallbackSeed}/400/600");
    }

    private function photoHero(?string $commons, string $fallbackSeed): string
    {
        return $commons ?? (self::PICSUM."/{$fallbackSeed}-hero/1200/400");
    }

    private function coverUrl(string $seed): string
    {
        return self::PICSUM."/{$seed}-cover/400/400";
    }

    public function run(): void
    {
        // 1. Admin user
        User::firstOrCreate(
            ['email' => 'admin@lista.site'],
            ['name' => 'Admin', 'password' => Hash::make('1234'), 'role' => User::ROLE_ADMIN]
        );

        // 2. Genres
        $genreNames = [
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

        foreach ($genreNames as $genreName) {
            Genre::firstOrCreate(
                ['slug' => str($genreName)->slug()],
                ['name' => $genreName]
            );
        }

        $this->command->info('Genres seeded.');

        // 3. Artists (idempotent)
        $artists = [
            'dave-grohl' => ['name' => 'Dave Grohl', 'bio' => 'David Eric Grohl is an American musician. He is the founder of Foo Fighters and was the drummer for Nirvana.', 'origin' => 'Warren, Ohio, USA'],
            'kurt-cobain' => ['name' => 'Kurt Cobain', 'bio' => 'Kurt Donald Cobain was an American musician. He was the lead vocalist, guitarist, and primary songwriter of the rock band Nirvana.', 'origin' => 'Aberdeen, Washington, USA'],
            'krist-novoselic' => ['name' => 'Krist Novoselic', 'bio' => 'Krist Anthony Novoselic is an American musician. He was the bassist and co-founder of the band Nirvana.', 'origin' => 'Compton, California, USA'],
            'eddie-vedder' => ['name' => 'Eddie Vedder', 'bio' => 'Eddie Jerome Vedder is an American singer. He is the lead vocalist and one of four guitarists for Pearl Jam.', 'origin' => 'Evanston, Illinois, USA'],
            'chris-cornell' => ['name' => 'Chris Cornell', 'bio' => 'Christopher John Cornell was an American singer and musician. He was the lead vocalist and rhythm guitarist for Soundgarden and Audioslave.', 'origin' => 'Seattle, Washington, USA'],
            'tom-morello' => ['name' => 'Tom Morello', 'bio' => 'Thomas Baptist Morello is an American guitarist. He is best known for his work with Rage Against the Machine and Audioslave.', 'origin' => 'New York City, USA'],
            'jeff-ament' => ['name' => 'Jeff Ament', 'bio' => 'Jeffrey Allen Ament is an American bassist and songwriter. He is a founding member of Pearl Jam.', 'origin' => 'Havre, Montana, USA'],
            'mike-mccready' => ['name' => 'Mike McCready', 'bio' => 'Michael Thomas McCready is an American guitarist. He has been the lead guitarist of Pearl Jam since 1990.', 'origin' => 'Seattle, Washington, USA'],
            'stone-gossard' => ['name' => 'Stone Gossard', 'bio' => 'Stone Carpenter Gossard is an American musician and songwriter. He is the rhythm guitarist of Pearl Jam.', 'origin' => 'Seattle, Washington, USA'],
            'matt-cameron' => ['name' => 'Matt Cameron', 'bio' => 'Matthew Cameron is an American drummer. He played for Soundgarden and Pearl Jam.', 'origin' => 'San Diego, California, USA'],
            'kim-thayil' => ['name' => 'Kim Thayil', 'bio' => 'Kim Thayil is an American musician. He is the lead guitarist of Soundgarden.', 'origin' => 'Seattle, Washington, USA'],
            'ben-shepherd' => ['name' => 'Ben Shepherd', 'bio' => 'Ben Shepherd is an American musician. He was the bassist of Soundgarden.', 'origin' => 'Seattle, Washington, USA'],
            'zack-de-la-rocha' => ['name' => 'Zack de la Rocha', 'bio' => 'Zacharias Manuel de la Rocha is an American rapper and activist. He is the lead vocalist of Rage Against the Machine.', 'origin' => 'Long Beach, California, USA'],
            'tim-commerford' => ['name' => 'Tim Commerford', 'bio' => 'Timothy Robert Commerford is an American musician. He is the bassist of Rage Against the Machine.', 'origin' => 'Irvine, California, USA'],
            'brad-wilk' => ['name' => 'Brad Wilk', 'bio' => 'Brad Wilk is an American drummer. He played for Rage Against the Machine and Audioslave.', 'origin' => 'Portland, Oregon, USA'],
        ];

        $artistModels = [];
        foreach ($artists as $slug => $data) {
            $artistModels[$slug] = Artist::firstOrCreate(['slug' => $slug], $data);
        }

        $this->command->info('Artists seeded.');

        // 4. Bands (idempotent)
        $bands = [
            'nirvana' => ['name' => 'Nirvana', 'bio' => 'Nirvana was an American rock band formed in Aberdeen, Washington, in 1987. The band pioneered the grunge movement.', 'formed_year' => 1987, 'dissolved_year' => 1994, 'origin' => 'Aberdeen, Washington, USA'],
            'foo-fighters' => ['name' => 'Foo Fighters', 'bio' => 'Foo Fighters is an American rock band formed in Seattle in 1994 by Dave Grohl.', 'formed_year' => 1994, 'origin' => 'Seattle, Washington, USA'],
            'pearl-jam' => ['name' => 'Pearl Jam', 'bio' => 'Pearl Jam is an American rock band formed in Seattle, Washington, in 1990.', 'formed_year' => 1990, 'origin' => 'Seattle, Washington, USA'],
            'soundgarden' => ['name' => 'Soundgarden', 'bio' => 'Soundgarden was an American rock band formed in Seattle, Washington, in 1984.', 'formed_year' => 1984, 'dissolved_year' => 2019, 'origin' => 'Seattle, Washington, USA'],
            'audioslave' => ['name' => 'Audioslave', 'bio' => 'Audioslave was an American rock supergroup formed in 2001 by members of Rage Against the Machine and Soundgarden.', 'formed_year' => 2001, 'dissolved_year' => 2007, 'origin' => 'Los Angeles, California, USA'],
            'rage-against-the-machine' => ['name' => 'Rage Against the Machine', 'bio' => 'Rage Against the Machine is an American rap metal band formed in Los Angeles, California, in 1991.', 'formed_year' => 1991, 'dissolved_year' => 2011, 'origin' => 'Los Angeles, California, USA'],
            'green-river' => ['name' => 'Green River', 'bio' => 'Green River was an American rock band formed in Seattle, Washington in 1985. They are considered pioneers of the grunge genre.', 'formed_year' => 1985, 'dissolved_year' => 1987, 'origin' => 'Seattle, Washington, USA'],
            'mother-love-bone' => ['name' => 'Mother Love Bone', 'bio' => 'Mother Love Bone was an American rock band formed in Seattle in 1988 by ex-members of Green River.', 'formed_year' => 1988, 'dissolved_year' => 1990, 'origin' => 'Seattle, Washington, USA'],
            'temple-of-the-dog' => ['name' => 'Temple of the Dog', 'bio' => 'Temple of the Dog was an American rock supergroup formed in Seattle in 1990 by members of Soundgarden and Pearl Jam.', 'formed_year' => 1990, 'dissolved_year' => 1991, 'origin' => 'Seattle, Washington, USA'],
        ];

        $bandModels = [];
        foreach ($bands as $slug => $data) {
            $bandModels[$slug] = Band::firstOrCreate(['slug' => $slug], $data);
        }

        $this->command->info('Bands seeded.');

        // 5. Band-Artist memberships (idempotent)
        $memberships = [
            ['nirvana', 'dave-grohl', 'Drummer', 1990, 1994],
            ['nirvana', 'kurt-cobain', 'Vocalist / Guitarist', 1987, 1994],
            ['nirvana', 'krist-novoselic', 'Bassist', 1987, 1994],
            ['foo-fighters', 'dave-grohl', 'Vocalist / Guitarist', 1994, null, true],
            ['pearl-jam', 'eddie-vedder', 'Vocalist', 1990, null, true],
            ['pearl-jam', 'jeff-ament', 'Bassist', 1990, null, true],
            ['pearl-jam', 'mike-mccready', 'Lead Guitarist', 1990, null, true],
            ['pearl-jam', 'stone-gossard', 'Rhythm Guitarist', 1990, null, true],
            ['pearl-jam', 'matt-cameron', 'Drummer', 1998, null, true],
            ['soundgarden', 'chris-cornell', 'Vocalist / Guitarist', 1984, 2019],
            ['soundgarden', 'matt-cameron', 'Drummer', 1986, 1997],
            ['soundgarden', 'kim-thayil', 'Lead Guitarist', 1984, 2019],
            ['soundgarden', 'ben-shepherd', 'Bassist', 1990, 2019],
            ['audioslave', 'chris-cornell', 'Vocalist', 2001, 2007],
            ['audioslave', 'tom-morello', 'Guitarist', 2001, 2007],
            ['audioslave', 'tim-commerford', 'Bassist', 2001, 2007],
            ['audioslave', 'brad-wilk', 'Drummer', 2001, 2007],
            ['rage-against-the-machine', 'tom-morello', 'Guitarist', 1991, 2011],
            ['rage-against-the-machine', 'zack-de-la-rocha', 'Vocalist', 1991, 2011],
            ['rage-against-the-machine', 'tim-commerford', 'Bassist', 1991, 2011],
            ['rage-against-the-machine', 'brad-wilk', 'Drummer', 1991, 2011],
            ['green-river', 'jeff-ament', 'Bassist', 1985, 1987],
            ['green-river', 'stone-gossard', 'Guitarist', 1985, 1987],
            ['mother-love-bone', 'jeff-ament', 'Bassist', 1988, 1990],
            ['mother-love-bone', 'stone-gossard', 'Guitarist', 1988, 1990],
            ['temple-of-the-dog', 'eddie-vedder', 'Vocalist', 1990, 1991],
            ['temple-of-the-dog', 'chris-cornell', 'Vocalist / Guitarist', 1990, 1991],
            ['temple-of-the-dog', 'jeff-ament', 'Bassist', 1990, 1991],
            ['temple-of-the-dog', 'stone-gossard', 'Guitarist', 1990, 1991],
            ['temple-of-the-dog', 'matt-cameron', 'Drummer', 1990, 1991],
        ];

        foreach ($memberships as $m) {
            $bandSlug = $m[0];
            $artistSlug = $m[1];
            $role = $m[2];
            $joinedYear = $m[3];
            $leftYear = $m[4] ?? null;
            $isCurrent = $m[5] ?? false;
            $bandId = $bandModels[$bandSlug]->id;
            $artistId = $artistModels[$artistSlug]->id;

            BandArtist::firstOrCreate(
                [
                    'band_id' => $bandId,
                    'artist_id' => $artistId,
                ],
                [
                    'role' => $role,
                    'joined_year' => $joinedYear,
                    'left_year' => $leftYear,
                    'is_current' => $isCurrent,
                ]
            );
        }

        $this->command->info('Memberships seeded.');

        // 6. Band Relationships (idempotent)
        $relationships = [
            ['nirvana', 'foo-fighters', 'members_formed', "After Kurt Cobain's death, drummer Dave Grohl formed Foo Fighters.", 1994],
            ['soundgarden', 'audioslave', 'members_formed', 'Chris Cornell joined with members of Rage Against the Machine to form Audioslave.', 2001],
            ['rage-against-the-machine', 'audioslave', 'members_formed', 'Three members of Rage Against the Machine (Morello, Commerford, Wilk) joined Chris Cornell to form Audioslave.', 2001],
            ['green-river', 'mother-love-bone', 'evolved_into', 'After Green River dissolved, members formed Mother Love Bone.', 1988],
            ['mother-love-bone', 'pearl-jam', 'members_formed', 'After the death of Andrew Wood, remaining Mother Love Bone members formed Pearl Jam with Eddie Vedder.', 1990],
            ['soundgarden', 'temple-of-the-dog', 'side_project', 'Soundgarden and Pearl Jam members formed Temple of the Dog as a tribute to Andrew Wood.', 1990],
            ['pearl-jam', 'temple-of-the-dog', 'side_project', 'Pearl Jam and Soundgarden members formed Temple of the Dog.', 1990],
            ['soundgarden', 'pearl-jam', 'members_formed', 'Matt Cameron moved from Soundgarden to Pearl Jam as drummer.', 1998],
        ];

        foreach ($relationships as [$parentSlug, $childSlug, $type, $description, $year]) {
            BandRelationship::firstOrCreate(
                [
                    'parent_band_id' => $bandModels[$parentSlug]->id,
                    'child_band_id' => $bandModels[$childSlug]->id,
                ],
                [
                    'type' => $type,
                    'description' => $description,
                    'year' => $year,
                ]
            );
        }

        $this->command->info('Relationships seeded.');

        // 7. Attach genres to bands
        $genreMap = ['grunge', 'alternative-rock', 'hard-rock', 'rap-metal'];
        $genreModels = [];
        foreach ($genreMap as $g) {
            $genreModels[$g] = Genre::whereSlug($g)->first();
        }

        $bandGenres = [
            'nirvana' => ['grunge'],
            'foo-fighters' => ['alternative-rock'],
            'pearl-jam' => ['grunge'],
            'soundgarden' => ['grunge'],
            'audioslave' => ['hard-rock'],
            'rage-against-the-machine' => ['rap-metal'],
            'green-river' => ['grunge'],
            'mother-love-bone' => ['grunge'],
            'temple-of-the-dog' => ['grunge'],
        ];

        foreach ($bandGenres as $bandSlug => $genreSlugs) {
            $band = Band::whereSlug($bandSlug)->first();
            if ($band) {
                $ids = array_map(fn ($s) => $genreModels[$s]->id, $genreSlugs);
                $band->genres()->syncWithoutDetaching($ids);
            }
        }

        $this->command->info('Genre associations seeded.');

        // 8. Labels
        Label::firstOrCreate(['slug' => 'sub-pop'], ['name' => 'Sub Pop', 'country' => 'USA', 'founded_year' => 1988]);
        Label::firstOrCreate(['slug' => 'dgc-records'], ['name' => 'DGC Records', 'country' => 'USA', 'founded_year' => 1990]);
        Label::firstOrCreate(['slug' => 'epic-records'], ['name' => 'Epic Records', 'country' => 'USA', 'founded_year' => 1953]);
        Label::firstOrCreate(['slug' => 'interscope-records'], ['name' => 'Interscope Records', 'country' => 'USA', 'founded_year' => 1990]);
        Label::firstOrCreate(['slug' => 'a-m-records'], ['name' => 'A&M Records', 'country' => 'USA', 'founded_year' => 1962]);

        $this->command->info('Labels seeded.');

        // 9. Tags
        $seattleSound = Tag::firstOrCreate(['slug' => 'seattle-sound'], ['name' => 'Seattle Sound', 'is_approved' => true]);
        $supergroup = Tag::firstOrCreate(['slug' => 'supergroup'], ['name' => 'Supergroup', 'is_approved' => true]);
        $nineties = Tag::firstOrCreate(['slug' => '90s-icons'], ['name' => '90s Icons', 'is_approved' => true]);
        Tag::firstOrCreate(['slug' => 'one-hit-wonder'], ['name' => 'One Hit Wonder', 'is_approved' => false]);
        Tag::firstOrCreate(['slug' => 'hall-of-fame'], ['name' => 'Hall of Fame', 'is_approved' => true]);

        $this->command->info('Tags seeded.');

        // 10. Photos
        $bandImages = [
            'nirvana' => self::WIKI.'/1/19/Nirvana_around_1992.jpg',
            'pearl-jam' => self::WIKI.'/b/bf/Pearl_Jam_1991.jpg',
            'soundgarden' => self::WIKI.'/9/9b/Soundgarden_Chicago.jpg',
            'foo-fighters' => self::WIKI.'/thumb/c/cb/Glasto2023.jpg/500px-Glasto2023.jpg',
            'rage-against-the-machine' => self::WIKI.'/thumb/b/b8/Rage_Against_The_Machine_%28cropped%29.jpg/500px-Rage_Against_The_Machine_%28cropped%29.jpg',
            'audioslave' => self::WIKI.'/thumb/5/51/Audioslave_2005_%28cropped%29.jpg/500px-Audioslave_2005_%28cropped%29.jpg',
            'green-river' => self::WIKI.'/thumb/2/2c/Green_River_%281980s_Sub_Pop_promo_photo%29.jpg/500px-Green_River_%281980s_Sub_Pop_promo_photo%29.jpg',
        ];

        $artistImages = [
            'dave-grohl' => self::WIKI.'/thumb/0/03/FoosDublin210819-6_%2848620330261%29_%28cropped%29.jpg/500px-FoosDublin210819-6_%2848620330261%29_%28cropped%29.jpg',
            'kurt-cobain' => self::WIKI.'/3/37/Nirvana_around_1992_%28cropped%29.jpg',
            'krist-novoselic' => self::WIKI.'/thumb/9/93/Krist_Novoselic%27s_Bona_Fide_Band_2024-07-23_-_13.jpg/500px-Krist_Novoselic%27s_Bona_Fide_Band_2024-07-23_-_13.jpg',
            'eddie-vedder' => self::WIKI.'/thumb/4/49/Eddie_Vedder_2018_-2.jpg/500px-Eddie_Vedder_2018_-2.jpg',
            'chris-cornell' => self::WIKI.'/thumb/5/58/ChrisCornellTIFFSept2011.jpg/500px-ChrisCornellTIFFSept2011.jpg',
            'tom-morello' => self::WIKI.'/thumb/f/fa/Tom_Morello.jpg/500px-Tom_Morello.jpg',
            'jeff-ament' => self::WIKI.'/thumb/f/f8/Jeff-Ament.jpg/500px-Jeff-Ament.jpg',
            'mike-mccready' => self::WIKI.'/thumb/3/34/Mike_McCready_2009.jpg/500px-Mike_McCready_2009.jpg',
            'stone-gossard' => self::WIKI.'/thumb/6/6a/Stone_Gossard_2014_%28cropped%29.jpg/500px-Stone_Gossard_2014_%28cropped%29.jpg',
            'matt-cameron' => self::WIKI.'/thumb/e/e6/Matt_Cameron_SG_2013.jpg/500px-Matt_Cameron_SG_2013.jpg',
        ];

        foreach (Band::all() as $band) {
            if ($band->photo === null || $band->hero_image === null) {
                $band->update([
                    'photo' => $this->photoUrl($bandImages[$band->slug] ?? null, $band->slug),
                    'hero_image' => $this->photoHero($bandImages[$band->slug] ?? null, $band->slug),
                ]);
            }
        }

        foreach (Artist::all() as $artist) {
            if ($artist->photo === null) {
                $artist->update([
                    'photo' => $this->photoUrlPortrait($artistImages[$artist->slug] ?? null, $artist->slug),
                ]);
            }
        }

        $this->command->info('Photos seeded.');

        // 11. Label assignments
        $labelAssignments = [
            'nirvana' => 'dgc-records',
            'foo-fighters' => 'interscope-records',
            'pearl-jam' => 'epic-records',
            'soundgarden' => 'a-m-records',
            'audioslave' => 'epic-records',
            'rage-against-the-machine' => 'epic-records',
        ];

        foreach ($labelAssignments as $bandSlug => $labelSlug) {
            $band = Band::whereSlug($bandSlug)->first();
            $label = Label::whereSlug($labelSlug)->first();
            if ($band && $label && $band->label_id === null) {
                $band->label()->associate($label);
                $band->save();
            }
        }

        $this->command->info('Label assignments seeded.');

        // 12. Tag attachments
        $bandTagAssignments = [
            'nirvana' => ['seattle-sound', '90s-icons'],
            'foo-fighters' => ['90s-icons'],
            'pearl-jam' => ['seattle-sound', '90s-icons'],
            'soundgarden' => ['seattle-sound', '90s-icons'],
            'audioslave' => ['supergroup'],
            'rage-against-the-machine' => ['90s-icons', 'supergroup'],
        ];

        foreach ($bandTagAssignments as $bandSlug => $tagSlugs) {
            $band = Band::whereSlug($bandSlug)->first();
            if ($band) {
                $ids = Tag::whereIn('slug', $tagSlugs)->pluck('id')->toArray();
                $band->tags()->syncWithoutDetaching($ids);
            }
        }

        $artistTagAssignments = [
            'dave-grohl' => ['90s-icons'],
            'kurt-cobain' => ['90s-icons', 'seattle-sound'],
        ];

        foreach ($artistTagAssignments as $artistSlug => $tagSlugs) {
            $artist = Artist::whereSlug($artistSlug)->first();
            if ($artist) {
                $ids = Tag::whereIn('slug', $tagSlugs)->pluck('id')->toArray();
                $artist->tags()->syncWithoutDetaching($ids);
            }
        }

        $this->command->info('Tag associations seeded.');

        // 13. Albums (idempotent)
        $albums = [
            ['nirvana', 'Bleach', 'bleach', 1989],
            ['nirvana', 'Nevermind', 'nevermind', 1991, ['Smells Like Teen Spirit', 'Come as You Are', 'Lithium']],
            ['nirvana', 'In Utero', 'in-utero', 1993],
            ['pearl-jam', 'Ten', 'ten', 1991, ['Alive', 'Even Flow', 'Jeremy']],
            ['pearl-jam', 'Vs.', 'vs', 1993],
            ['pearl-jam', 'Vitalogy', 'vitalogy', 1994],
            ['pearl-jam', 'Yield', 'yield', 1998],
            ['soundgarden', 'Badmotorfinger', 'badmotorfinger', 1991],
            ['soundgarden', 'Superunknown', 'superunknown', 1994, ['Black Hole Sun', 'Spoonman', 'Fell on Black Days']],
            ['soundgarden', 'Down on the Upside', 'down-on-the-upside', 1996],
            ['foo-fighters', 'Foo Fighters', 'foo-fighters', 1995],
            ['foo-fighters', 'The Colour and the Shape', 'the-colour-and-the-shape', 1997],
            ['audioslave', 'Audioslave', 'audioslave', 2002],
            ['audioslave', 'Out of Exile', 'out-of-exile', 2005],
            ['rage-against-the-machine', 'Rage Against the Machine', 'rage-against-the-machine', 1992, ['Killing in the Name', 'Bombtrack', 'Bullet in the Head']],
            ['rage-against-the-machine', 'Evil Empire', 'evil-empire', 1996],
        ];

        foreach ($albums as $a) {
            $bandSlug = $a[0];
            $title = $a[1];
            $slug = $a[2];
            $year = $a[3];
            $tracklist = $a[4] ?? null;
            $band = Band::whereSlug($bandSlug)->first();
            if ($band) {
                $data = [
                    'band_id' => $band->id,
                    'title' => $title,
                    'release_year' => $year,
                ];
                if ($tracklist !== null) {
                    $data['tracklist'] = $tracklist;
                }
                $album = Album::firstOrCreate(['slug' => $slug], $data);
                if ($album->wasRecentlyCreated || $album->cover_art === null) {
                    $album->update(['cover_art' => $this->coverUrl($slug)]);
                }
            }
        }

        $this->command->info('Albums seeded.');

        // 14. Blog posts (idempotent)
        Post::firstOrCreate(
            ['slug' => 'seattle-sound-grunge'],
            [
                'title' => 'The Seattle Sound: A Grunge Explosion',
                'excerpt' => 'How a rainy city in the Pacific Northwest changed rock music forever.',
                'body' => "## The Birth of a Movement\n\nIn the late 1980s, Seattle's underground music scene gave birth to a genre that would dominate the early 1990s. **Grunge** was more than just music — it was a cultural shift.\n\nBands like **Nirvana**, **Pearl Jam**, **Soundgarden**, and **Alice in Chains** created a raw, unpolished sound that resonated with a generation.\n\n### Key Albums\n\n- **Nevermind** (Nirvana, 1991)\n- **Ten** (Pearl Jam, 1991)\n- **Superunknown** (Soundgarden, 1994)\n\n> \"Grunge was about stripping away the excess and playing from the heart.\"\n\nThe influence of the Seattle scene continues to be felt today, with new bands citing these pioneers as major influences.",
                'author' => 'LISTA',
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'featured_image' => $this->photoHero(self::WIKI.'/b/bf/Pearl_Jam_1991.jpg', 'seattle-sound'),
            ]
        );

        Post::firstOrCreate(
            ['slug' => 'band-genealogy-families'],
            [
                'title' => 'Band Genealogy: Tracing Musical Families',
                'excerpt' => 'Explore how bands are connected through shared members across decades of music history.',
                'body' => "## The Web of Connections\n\nOne of the most fascinating aspects of music history is how bands are connected through their members. **Supergroups**, **side projects**, and **lineup changes** create a rich web of relationships.\n\n### The Pearl Jam Family Tree\n\nPearl Jam emerged from the ashes of **Mother Love Bone**, which itself formed from **Green River** — one of the first grunge bands.\n\nSoundgarden's Matt Cameron later joined Pearl Jam, connecting the two iconic bands.\n\n### The Dave Grohl Effect\n\nFrom Nirvana to Foo Fighters, Dave Grohl's career spans multiple genres and decades. He also played in **Them Crooked Vultures** with Josh Homme and John Paul Jones.\n\n> \"Every band is connected by six degrees of separation.\"\n\nUse our interactive [genealogy graph](/genealogy) to explore these connections yourself.",
                'author' => 'LISTA',
                'is_published' => true,
                'published_at' => now()->subDays(1),
                'featured_image' => $this->photoHero(self::WIKI.'/thumb/c/cb/Glasto2023.jpg/500px-Glasto2023.jpg', 'band-genealogy'),
            ]
        );

        $this->command->info('Blog posts seeded.');
        $this->command->info('Production mock data seeded successfully.');
    }
}
