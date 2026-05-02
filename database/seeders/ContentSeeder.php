<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Band;
use App\Models\Label;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ContentSeeder extends Seeder
{
    private const PICSUM = 'https://picsum.photos/seed';

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

        // Seed photos
        $bands = Band::all();
        foreach ($bands as $band) {
            $band->update([
                'photo' => self::PICSUM . '/' . $band->slug . '/400/400',
                'hero_image' => self::PICSUM . '/' . $band->slug . '-hero/1200/400',
            ]);
        }

        $artists = Artist::all();
        foreach ($artists as $artist) {
            $artist->update([
                'photo' => self::PICSUM . '/' . $artist->slug . '/400/400',
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

        // Albums with cover art
        if ($nirvana) {
            Album::create(['band_id' => $nirvana->id, 'title' => 'Bleach', 'slug' => 'bleach', 'release_year' => 1989, 'cover_art' => self::PICSUM . '/bleach/400/400']);
            Album::create(['band_id' => $nirvana->id, 'title' => 'Nevermind', 'slug' => 'nevermind', 'release_year' => 1991, 'cover_art' => self::PICSUM . '/nevermind/400/400', 'tracklist' => ['Smells Like Teen Spirit', 'Come as You Are', 'Lithium']]);
            Album::create(['band_id' => $nirvana->id, 'title' => 'In Utero', 'slug' => 'in-utero', 'release_year' => 1993, 'cover_art' => self::PICSUM . '/in-utero/400/400']);
        }
        if ($pearlJam) {
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Ten', 'slug' => 'ten', 'release_year' => 1991, 'cover_art' => self::PICSUM . '/pearl-jam-ten/400/400', 'tracklist' => ['Alive', 'Even Flow', 'Jeremy']]);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Vs.', 'slug' => 'vs', 'release_year' => 1993, 'cover_art' => self::PICSUM . '/pearl-jam-vs/400/400']);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Vitalogy', 'slug' => 'vitalogy', 'release_year' => 1994, 'cover_art' => self::PICSUM . '/vitalogy/400/400']);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Yield', 'slug' => 'yield', 'release_year' => 1998, 'cover_art' => self::PICSUM . '/yield/400/400']);
        }
        if ($soundgarden) {
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Badmotorfinger', 'slug' => 'badmotorfinger', 'release_year' => 1991, 'cover_art' => self::PICSUM . '/badmotorfinger/400/400']);
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Superunknown', 'slug' => 'superunknown', 'release_year' => 1994, 'cover_art' => self::PICSUM . '/superunknown/400/400', 'tracklist' => ['Black Hole Sun', 'Spoonman', 'Fell on Black Days']]);
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Down on the Upside', 'slug' => 'down-on-the-upside', 'release_year' => 1996, 'cover_art' => self::PICSUM . '/down-on-the-upside/400/400']);
        }
        if ($fooFighters) {
            Album::create(['band_id' => $fooFighters->id, 'title' => 'Foo Fighters', 'slug' => 'foo-fighters', 'release_year' => 1995, 'cover_art' => self::PICSUM . '/foo-fighters-s/t/400/400']);
            Album::create(['band_id' => $fooFighters->id, 'title' => 'The Colour and the Shape', 'slug' => 'the-colour-and-the-shape', 'release_year' => 1997, 'cover_art' => self::PICSUM . '/the-colour-and-the-shape/400/400']);
        }
        if ($audioslave) {
            Album::create(['band_id' => $audioslave->id, 'title' => 'Audioslave', 'slug' => 'audioslave', 'release_year' => 2002, 'cover_art' => self::PICSUM . '/audioslave-s/t/400/400']);
            Album::create(['band_id' => $audioslave->id, 'title' => 'Out of Exile', 'slug' => 'out-of-exile', 'release_year' => 2005, 'cover_art' => self::PICSUM . '/out-of-exile/400/400']);
        }
        if ($rageMachine) {
            Album::create(['band_id' => $rageMachine->id, 'title' => 'Rage Against the Machine', 'slug' => 'rage-against-the-machine', 'release_year' => 1992, 'cover_art' => self::PICSUM . '/rage-s/t/400/400', 'tracklist' => ['Killing in the Name', 'Bombtrack', 'Bullet in the Head']]);
            Album::create(['band_id' => $rageMachine->id, 'title' => 'Evil Empire', 'slug' => 'evil-empire', 'release_year' => 1996, 'cover_art' => self::PICSUM . '/evil-empire/400/400']);
        }
    }
}
