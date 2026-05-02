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
        $data = Cache::remember('home.data', 600, fn () => [
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

        return view('home', $data);
    }
}
