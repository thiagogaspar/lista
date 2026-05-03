<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Band;
use App\Models\BandArtist;
use App\Models\BandRelationship;
use App\Models\Label;
use App\Services\ArtistService;
use App\Services\BandService;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __invoke(BandService $bands, ArtistService $artists)
    {
        return view('home', [
            'featuredBands' => $bands->getFeatured(),
            'featuredArtists' => $artists->getFeatured(),
            'heroBand' => Band::with('genres')->inRandomOrder()->first(),
            'featuredLabels' => Label::withCount('bands')->orderBy('bands_count', 'desc')->limit(6)->get(),
            'stats' => Cache::remember('home.stats', 600, fn () => [
                'bands' => Band::count(),
                'artists' => Artist::count(),
                'memberships' => BandArtist::count(),
                'relationships' => BandRelationship::count(),
            ]),
        ]);
    }
}
