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
    private const MOCK = 'https://placehold.co';

    private function mockPhoto(string $name, string $bg = '6b6b6b', string $fg = 'fff', int $size = 400): string
    {
        return self::MOCK . "/{$size}x{$size}/{$bg}/{$fg}?text=" . urlencode($name) . '&font=inter';
    }

    private function mockHero(string $name, string $bg = '2a2a2a', string $fg = 'cccccc'): string
    {
        return self::MOCK . "/1200x400/{$bg}/{$fg}?text=" . urlencode($name) . '&font=inter';
    }

    private function mockCover(string $title): string
    {
        return self::MOCK . '/400x400/333333/aaaaaa?text=' . urlencode($title) . '&font=inter';
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

        // Seed photos — clean mocks with brand colors
        $bands = Band::all();
        $bandColors = [
            'nirvana' => ['555555', 'f5f5f5'], 'foo-fighters' => ['606060', 'fafafa'],
            'pearl-jam' => ['4a4a4a', 'eeeeee'], 'soundgarden' => ['404040', 'e5e5e5'],
            'audioslave' => ['555555', 'f2f2f2'], 'rage-against-the-machine' => ['666666', 'fcfcfc'],
            'green-river' => ['606060', 'f5f5f5'], 'mother-love-bone' => ['4a4a4a', 'eeeeee'],
            'temple-of-the-dog' => ['808080', '222222'],
        ];
        foreach ($bands as $band) {
            $c = $bandColors[$band->slug] ?? ['8f4a37', 'f9e8e0'];
            $band->update([
                'photo' => $this->mockPhoto($band->name, $c[0], $c[1]),
                'hero_image' => $this->mockHero($band->name, '1a1a18', $c[0]),
            ]);
        }

        $artists = Artist::all();
        foreach ($artists as $artist) {
            $artist->update([
                'photo' => $this->mockPhoto($artist->name, '7a7a7a', 'fff'),
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
        if ($nirvana) { $nirvana->label()->associate($dgc); $nirvana->save(); }
        if ($fooFighters) { $fooFighters->label()->associate($interscope); $fooFighters->save(); }
        if ($pearlJam) { $pearlJam->label()->associate($epic); $pearlJam->save(); }
        if ($soundgarden) { $soundgarden->label()->associate($aandm); $soundgarden->save(); }
        if ($audioslave) { $audioslave->label()->associate($epic); $audioslave->save(); }
        if ($rageMachine) { $rageMachine->label()->associate($epic); $rageMachine->save(); }

        // Attach tags
        if ($nirvana) { $nirvana->tags()->attach([$seattleSound->id, $nineties->id]); }
        if ($fooFighters) { $fooFighters->tags()->attach([$nineties->id]); }
        if ($pearlJam) { $pearlJam->tags()->attach([$seattleSound->id, $nineties->id]); }
        if ($soundgarden) { $soundgarden->tags()->attach([$seattleSound->id, $nineties->id]); }
        if ($audioslave) { $audioslave->tags()->attach([$supergroup->id]); }
        if ($rageMachine) { $rageMachine->tags()->attach([$nineties->id, $supergroup->id]); }

        // Attach tags to artists
        $daveGrohl = Artist::whereSlug('dave-grohl')->first();
        $kurtCobain = Artist::whereSlug('kurt-cobain')->first();
        if ($daveGrohl) { $daveGrohl->tags()->attach([$nineties->id]); }
        if ($kurtCobain) { $kurtCobain->tags()->attach([$nineties->id, $seattleSound->id]); }

        // Albums with mock covers
        if ($nirvana) {
            Album::create(['band_id' => $nirvana->id, 'title' => 'Bleach', 'slug' => 'bleach', 'release_year' => 1989, 'cover_art' => $this->mockCover('Bleach')]);
            Album::create(['band_id' => $nirvana->id, 'title' => 'Nevermind', 'slug' => 'nevermind', 'release_year' => 1991, 'cover_art' => $this->mockCover('Nevermind'), 'tracklist' => ['Smells Like Teen Spirit', 'Come as You Are', 'Lithium']]);
            Album::create(['band_id' => $nirvana->id, 'title' => 'In Utero', 'slug' => 'in-utero', 'release_year' => 1993, 'cover_art' => $this->mockCover('In Utero')]);
        }
        if ($pearlJam) {
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Ten', 'slug' => 'ten', 'release_year' => 1991, 'cover_art' => $this->mockCover('Ten'), 'tracklist' => ['Alive', 'Even Flow', 'Jeremy']]);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Vs.', 'slug' => 'vs', 'release_year' => 1993, 'cover_art' => $this->mockCover('Vs.')]);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Vitalogy', 'slug' => 'vitalogy', 'release_year' => 1994, 'cover_art' => $this->mockCover('Vitalogy')]);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Yield', 'slug' => 'yield', 'release_year' => 1998, 'cover_art' => $this->mockCover('Yield')]);
        }
        if ($soundgarden) {
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Badmotorfinger', 'slug' => 'badmotorfinger', 'release_year' => 1991, 'cover_art' => $this->mockCover('Badmotorfinger')]);
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Superunknown', 'slug' => 'superunknown', 'release_year' => 1994, 'cover_art' => $this->mockCover('Superunknown'), 'tracklist' => ['Black Hole Sun', 'Spoonman', 'Fell on Black Days']]);
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Down on the Upside', 'slug' => 'down-on-the-upside', 'release_year' => 1996, 'cover_art' => $this->mockCover('Down on the Upside')]);
        }
        if ($fooFighters) {
            Album::create(['band_id' => $fooFighters->id, 'title' => 'Foo Fighters', 'slug' => 'foo-fighters', 'release_year' => 1995, 'cover_art' => $this->mockCover('Foo Fighters')]);
            Album::create(['band_id' => $fooFighters->id, 'title' => 'The Colour and the Shape', 'slug' => 'the-colour-and-the-shape', 'release_year' => 1997, 'cover_art' => $this->mockCover('The Colour and the Shape')]);
        }
        if ($audioslave) {
            Album::create(['band_id' => $audioslave->id, 'title' => 'Audioslave', 'slug' => 'audioslave', 'release_year' => 2002, 'cover_art' => $this->mockCover('Audioslave')]);
            Album::create(['band_id' => $audioslave->id, 'title' => 'Out of Exile', 'slug' => 'out-of-exile', 'release_year' => 2005, 'cover_art' => $this->mockCover('Out of Exile')]);
        }
        if ($rageMachine) {
            Album::create(['band_id' => $rageMachine->id, 'title' => 'Rage Against the Machine', 'slug' => 'rage-against-the-machine', 'release_year' => 1992, 'cover_art' => $this->mockCover('Rage Against the Machine'), 'tracklist' => ['Killing in the Name', 'Bombtrack', 'Bullet in the Head']]);
            Album::create(['band_id' => $rageMachine->id, 'title' => 'Evil Empire', 'slug' => 'evil-empire', 'release_year' => 1996, 'cover_art' => $this->mockCover('Evil Empire')]);
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
            'featured_image' => $this->mockHero('The Seattle Sound', '000000', 'ffffff'),
        ]);

        Post::create([
            'title' => 'Band Genealogy: Tracing Musical Families',
            'slug' => 'band-genealogy-families',
            'excerpt' => 'Explore how bands are connected through shared members across decades of music history.',
            'body' => "## The Web of Connections\n\nOne of the most fascinating aspects of music history is how bands are connected through their members. **Supergroups**, **side projects**, and **lineup changes** create a rich web of relationships.\n\n### The Pearl Jam Family Tree\n\nPearl Jam emerged from the ashes of **Mother Love Bone**, which itself formed from **Green River** — one of the first grunge bands.\n\nSoundgarden's Matt Cameron later joined Pearl Jam, connecting the two iconic bands.\n\n### The Dave Grohl Effect\n\nFrom Nirvana to Foo Fighters, Dave Grohl's career spans multiple genres and decades. He also played in **Them Crooked Vultures** with Josh Homme and John Paul Jones.\n\n> \"Every band is connected by six degrees of separation.\"\n\nUse our interactive [genealogy graph](/genealogy) to explore these connections yourself.",
            'author' => 'LISTA',
            'is_published' => true,
            'published_at' => now()->subDays(1),
            'featured_image' => $this->mockHero('Band Genealogy', '000000', 'ffffff'),
        ]);
    }
}
