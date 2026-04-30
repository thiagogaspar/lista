<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Label;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@lista.site'],
            [
                'name' => 'Admin',
                'password' => Hash::make('1234'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        // Labels
        $subPop = Label::create(['name' => 'Sub Pop', 'slug' => 'sub-pop', 'country' => 'USA', 'founded_year' => 1988]);
        $dgc = Label::create(['name' => 'DGC Records', 'slug' => 'dgc-records', 'country' => 'USA', 'founded_year' => 1990]);
        $epic = Label::create(['name' => 'Epic Records', 'slug' => 'epic-records', 'country' => 'USA', 'founded_year' => 1953]);
        $interscope = Label::create(['name' => 'Interscope Records', 'slug' => 'interscope-records', 'country' => 'USA', 'founded_year' => 1990]);
        $aandm = Label::create(['name' => 'A&M Records', 'slug' => 'a-m-records', 'country' => 'USA', 'founded_year' => 1962]);

        // Tags
        $seattleSound = Tag::create(['name' => 'Seattle Sound', 'slug' => 'seattle-sound', 'is_approved' => true]);
        $supergroup = Tag::create(['name' => 'Supergroup', 'slug' => 'supergroup', 'is_approved' => true]);
        $nineties = Tag::create(['name' => '90s Icons', 'slug' => '90s-icons', 'is_approved' => true]);
        Tag::create(['name' => 'One Hit Wonder', 'slug' => 'one-hit-wonder', 'is_approved' => false]);
        Tag::create(['name' => 'Hall of Fame', 'slug' => 'hall-of-fame', 'is_approved' => true]);

        // Get bands (assuming DemoDataSeeder ran first)
        $nirvana = Band::whereSlug('nirvana')->first();
        $fooFighters = Band::whereSlug('foo-fighters')->first();
        $pearlJam = Band::whereSlug('pearl-jam')->first();
        $soundgarden = Band::whereSlug('soundgarden')->first();
        $audioslave = Band::whereSlug('audioslave')->first();
        $rageMachine = Band::whereSlug('rage-against-the-machine')->first();

        // Assign labels to bands
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

        // Attach tags to bands
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

        // Albums
        if ($nirvana) {
            Album::create(['band_id' => $nirvana->id, 'title' => 'Bleach', 'slug' => 'bleach', 'release_year' => 1989]);
            Album::create(['band_id' => $nirvana->id, 'title' => 'Nevermind', 'slug' => 'nevermind', 'release_year' => 1991, 'tracklist' => ['Smells Like Teen Spirit', 'Come as You Are', 'Lithium']]);
            Album::create(['band_id' => $nirvana->id, 'title' => 'In Utero', 'slug' => 'in-utero', 'release_year' => 1993]);
        }
        if ($pearlJam) {
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Ten', 'slug' => 'ten', 'release_year' => 1991, 'tracklist' => ['Alive', 'Even Flow', 'Jeremy']]);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Vs.', 'slug' => 'vs', 'release_year' => 1993]);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Vitalogy', 'slug' => 'vitalogy', 'release_year' => 1994]);
            Album::create(['band_id' => $pearlJam->id, 'title' => 'Yield', 'slug' => 'yield', 'release_year' => 1998]);
        }
        if ($soundgarden) {
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Badmotorfinger', 'slug' => 'badmotorfinger', 'release_year' => 1991]);
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Superunknown', 'slug' => 'superunknown', 'release_year' => 1994, 'tracklist' => ['Black Hole Sun', 'Spoonman', 'Fell on Black Days']]);
            Album::create(['band_id' => $soundgarden->id, 'title' => 'Down on the Upside', 'slug' => 'down-on-the-upside', 'release_year' => 1996]);
        }
        if ($fooFighters) {
            Album::create(['band_id' => $fooFighters->id, 'title' => 'Foo Fighters', 'slug' => 'foo-fighters', 'release_year' => 1995]);
            Album::create(['band_id' => $fooFighters->id, 'title' => 'The Colour and the Shape', 'slug' => 'the-colour-and-the-shape', 'release_year' => 1997]);
        }
        if ($audioslave) {
            Album::create(['band_id' => $audioslave->id, 'title' => 'Audioslave', 'slug' => 'audioslave', 'release_year' => 2002]);
            Album::create(['band_id' => $audioslave->id, 'title' => 'Out of Exile', 'slug' => 'out-of-exile', 'release_year' => 2005]);
        }
        if ($rageMachine) {
            Album::create(['band_id' => $rageMachine->id, 'title' => 'Rage Against the Machine', 'slug' => 'rage-against-the-machine', 'release_year' => 1992, 'tracklist' => ['Killing in the Name', 'Bombtrack', 'Bullet in the Head']]);
            Album::create(['band_id' => $rageMachine->id, 'title' => 'Evil Empire', 'slug' => 'evil-empire', 'release_year' => 1996]);
        }
    }
}
