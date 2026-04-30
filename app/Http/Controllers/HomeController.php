<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Band;
use App\Models\BandArtist;
use App\Models\BandRelationship;
use App\Models\Label;
use App\Services\ArtistService;
use App\Services\BandService;

class HomeController extends Controller
{
    public function __invoke(BandService $bands, ArtistService $artists)
    {
        return view('home', [
            'featuredBands' => $bands->getFeatured(),
            'featuredArtists' => $artists->getFeatured(),
            'heroBand' => Band::with('genres')->inRandomOrder()->first(),
            'featuredLabels' => Label::withCount('bands')->orderBy('bands_count', 'desc')->limit(6)->get(),
            'stats' => [
                'bands' => Band::count(),
                'artists' => Artist::count(),
                'memberships' => BandArtist::count(),
                'relationships' => BandRelationship::count(),
            ],
        ]);
    }
}
