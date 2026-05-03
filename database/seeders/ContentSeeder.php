<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Band;
use App\Models\Label;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ContentSeeder extends Seeder
{
    private const WIKI = 'https://upload.wikimedia.org/wikipedia/commons';

    private const PICSUM = 'https://picsum.photos/seed';

    private function photoUrl(?string $commons, string $fallbackSeed): string
    {
        return $commons ?? (self::PICSUM."/{$fallbackSeed}/400/400");
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
        User::firstOrCreate(
            ['email' => 'admin@lista.site'],
            ['name' => 'Admin', 'password' => Hash::make('1234'), 'role' => User::ROLE_ADMIN]
        );

        // Labels
        Label::create(['name' => 'Sub Pop', 'slug' => 'sub-pop', 'country' => 'USA', 'founded_year' => 1988]);
        Label::create(['name' => 'DGC Records', 'slug' => 'dgc-records', 'country' => 'USA', 'founded_year' => 1990]);
        Label::create(['name' => 'Epic Records', 'slug' => 'epic-records', 'country' => 'USA', 'founded_year' => 1953]);
        Label::create(['name' => 'Interscope Records', 'slug' => 'interscope-records', 'country' => 'USA', 'founded_year' => 1990]);
        Label::create(['name' => 'A&M Records', 'slug' => 'a-m-records', 'country' => 'USA', 'founded_year' => 1962]);

        // Tags
        $seattleSound = Tag::create(['name' => 'Seattle Sound', 'slug' => 'seattle-sound', 'is_approved' => true]);
        $supergroup = Tag::create(['name' => 'Supergroup', 'slug' => 'supergroup', 'is_approved' => true]);
        $nineties = Tag::create(['name' => '90s Icons', 'slug' => '90s-icons', 'is_approved' => true]);
        Tag::create(['name' => 'One Hit Wonder', 'slug' => 'one-hit-wonder', 'is_approved' => false]);
        Tag::create(['name' => 'Hall of Fame', 'slug' => 'hall-of-fame', 'is_approved' => true]);

        // Seed photos — Wikimedia Commons CC images
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

        $bands = Band::all();
        foreach ($bands as $band) {
            $band->update([
                'photo' => $this->photoUrl($bandImages[$band->slug] ?? null, $band->slug),
                'hero_image' => $this->photoHero($bandImages[$band->slug] ?? null, $band->slug),
            ]);
        }

        $artists = Artist::all();
        foreach ($artists as $artist) {
            $artist->update([
                'photo' => $this->photoUrl($artistImages[$artist->slug] ?? null, $artist->slug),
            ]);
        }

        // Get band references
        $nirvana = Band::whereSlug('nirvana')->first();
        $fooFighters = Band::whereSlug('foo-fighters')->first();
        $pearlJam = Band::whereSlug('pearl-jam')->first();
        $soundgarden = Band::whereSlug('soundgarden')->first();
        $audioslave = Band::whereSlug('audioslave')->first();
        $rageMachine = Band::whereSlug('rage-against-the-machine')->first();
        $dgc = Label::whereSlug('dgc-records')->first();
        $interscope = Label::whereSlug('interscope-records')->first();
        $epic = Label::whereSlug('epic-records')->first();
        $aandm = Label::whereSlug('a-m-records')->first();

        // Assign labels
        if ($nirvana) {
            $nirvana->label()->associate($dgc);
            $nirvana->save();
        }
        if ($fooFighters) {
            $fooFighters->label()->associate($interscope);
            $fooFighters->save();
        }
        if ($pearlJam) {
            $pearlJam->label()->associate($epic);
            $pearlJam->save();
        }
        if ($soundgarden) {
            $soundgarden->label()->associate($aandm);
            $soundgarden->save();
        }
        if ($audioslave) {
            $audioslave->label()->associate($epic);
            $audioslave->save();
        }
        if ($rageMachine) {
            $rageMachine->label()->associate($epic);
            $rageMachine->save();
        }

        // Attach tags
        if ($nirvana) {
            $nirvana->tags()->attach([$seattleSound->id, $nineties->id]);
        }
        if ($fooFighters) {
            $fooFighters->tags()->attach([$nineties->id]);
        }
        if ($pearlJam) {
            $pearlJam->tags()->attach([$seattleSound->id, $nineties->id]);
        }
        if ($soundgarden) {
            $soundgarden->tags()->attach([$seattleSound->id, $nineties->id]);
        }
        if ($audioslave) {
            $audioslave->tags()->attach([$supergroup->id]);
        }
        if ($rageMachine) {
            $rageMachine->tags()->attach([$nineties->id, $supergroup->id]);
        }

        // Attach tags to artists
        $daveGrohl = Artist::whereSlug('dave-grohl')->first();
        $kurtCobain = Artist::whereSlug('kurt-cobain')->first();
        if ($daveGrohl) {
            $daveGrohl->tags()->attach([$nineties->id]);
        }
        if ($kurtCobain) {
            $kurtCobain->tags()->attach([$nineties->id, $seattleSound->id]);
        }

        // Albums with real CC photos via picsum (album art is fair-use, using CC photos instead)
        if ($nirvana) {
            Album::create(['band_id' => $nirvana->id, 'title' => 'Bleach', 'slug' => 'bleach', 'release_year' => 1989, 'cover_art' => $this->coverUrl('bleach')]);
            Album::create(['band_id' => $nirvana->id, 'title' => 'Nevermind', 'slug' => 'nevermind', 'release_year' => 1991, 'cover_art' => $this->coverUrl('nevermind'), 'tracklist' => ['Smells Like Teen Spirit', 'Come as You Are', 'Lithium']]);
            Album::create(['band_id' => $nirvana->id, 'title' => 'In Utero', 'slug' => 'in-utero', 'release_year' => 1993, 'cover_art' => $this->coverUrl('in-utero')]);
        }
        if ($pearlJam) {
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Ten', 'slug' => 'ten', 'release_year' => 1991, 'cover_art' => $this->coverUrl('ten'), 'tracklist' => ['Alive', 'Even Flow', 'Jeremy']]);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Vs.', 'slug' => 'vs', 'release_year' => 1993, 'cover_art' => $this->coverUrl('vs')]);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Vitalogy', 'slug' => 'vitalogy', 'release_year' => 1994, 'cover_art' => $this->coverUrl('vitalogy')]);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Yield', 'slug' => 'yield', 'release_year' => 1998, 'cover_art' => $this->coverUrl('yield')]);
        }
        if ($soundgarden) {
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Badmotorfinger', 'slug' => 'badmotorfinger', 'release_year' => 1991, 'cover_art' => $this->coverUrl('badmotorfinger')]);
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Superunknown', 'slug' => 'superunknown', 'release_year' => 1994, 'cover_art' => $this->coverUrl('superunknown'), 'tracklist' => ['Black Hole Sun', 'Spoonman', 'Fell on Black Days']]);
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Down on the Upside', 'slug' => 'down-on-the-upside', 'release_year' => 1996, 'cover_art' => $this->coverUrl('down-on-the-upside')]);
        }
        if ($fooFighters) {
            Album::create(['band_id' => $fooFighters->id, 'title' => 'Foo Fighters', 'slug' => 'foo-fighters', 'release_year' => 1995, 'cover_art' => $this->coverUrl('foo-fighters')]);
            Album::create(['band_id' => $fooFighters->id, 'title' => 'The Colour and the Shape', 'slug' => 'the-colour-and-the-shape', 'release_year' => 1997, 'cover_art' => $this->coverUrl('the-colour-and-the-shape')]);
        }
        if ($audioslave) {
            Album::create(['band_id' => $audioslave->id, 'title' => 'Audioslave', 'slug' => 'audioslave', 'release_year' => 2002, 'cover_art' => $this->coverUrl('audioslave')]);
            Album::create(['band_id' => $audioslave->id, 'title' => 'Out of Exile', 'slug' => 'out-of-exile', 'release_year' => 2005, 'cover_art' => $this->coverUrl('out-of-exile')]);
        }
        if ($rageMachine) {
            Album::create(['band_id' => $rageMachine->id, 'title' => 'Rage Against the Machine', 'slug' => 'rage-against-the-machine', 'release_year' => 1992, 'cover_art' => $this->coverUrl('rage-against-the-machine'), 'tracklist' => ['Killing in the Name', 'Bombtrack', 'Bullet in the Head']]);
            Album::create(['band_id' => $rageMachine->id, 'title' => 'Evil Empire', 'slug' => 'evil-empire', 'release_year' => 1996, 'cover_art' => $this->coverUrl('evil-empire')]);
        }

        // Blog posts
        Post::create([
            'title' => 'The Seattle Sound: A Grunge Explosion',
            'slug' => 'seattle-sound-grunge',
            'excerpt' => 'How a rainy city in the Pacific Northwest changed rock music forever.',
            'body' => "## The Birth of a Movement\n\nIn the late 1980s, Seattle's underground music scene gave birth to a genre that would dominate the early 1990s. **Grunge** was more than just music — it was a cultural shift.\n\nBands like **Nirvana**, **Pearl Jam**, **Soundgarden**, and **Alice in Chains** created a raw, unpolished sound that resonated with a generation.\n\n### Key Albums\n\n- **Nevermind** (Nirvana, 1991)\n- **Ten** (Pearl Jam, 1991)\n- **Superunknown** (Soundgarden, 1994)\n\n> \"Grunge was about stripping away the excess and playing from the heart.\"\n\nThe influence of the Seattle scene continues to be felt today, with new bands citing these pioneers as major influences.",
            'author' => 'LISTA',
            'is_published' => true,
            'published_at' => now()->subDays(3),
            'featured_image' => $this->photoHero(self::WIKI.'/b/bf/Pearl_Jam_1991.jpg', 'seattle-sound'),
        ]);

        Post::create([
            'title' => 'Band Genealogy: Tracing Musical Families',
            'slug' => 'band-genealogy-families',
            'excerpt' => 'Explore how bands are connected through shared members across decades of music history.',
            'body' => "## The Web of Connections\n\nOne of the most fascinating aspects of music history is how bands are connected through their members. **Supergroups**, **side projects**, and **lineup changes** create a rich web of relationships.\n\n### The Pearl Jam Family Tree\n\nPearl Jam emerged from the ashes of **Mother Love Bone**, which itself formed from **Green River** — one of the first grunge bands.\n\nSoundgarden's Matt Cameron later joined Pearl Jam, connecting the two iconic bands.\n\n### The Dave Grohl Effect\n\nFrom Nirvana to Foo Fighters, Dave Grohl's career spans multiple genres and decades. He also played in **Them Crooked Vultures** with Josh Homme and John Paul Jones.\n\n> \"Every band is connected by six degrees of separation.\"\n\nUse our interactive [genealogy graph](/genealogy) to explore these connections yourself.",
            'author' => 'LISTA',
            'is_published' => true,
            'published_at' => now()->subDays(1),
            'featured_image' => $this->photoHero(self::WIKI.'/thumb/c/cb/Glasto2023.jpg/500px-Glasto2023.jpg', 'band-genealogy'),
        ]);
    }
}
