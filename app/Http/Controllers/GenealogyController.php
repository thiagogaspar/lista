<?php

namespace App\Http\Controllers;

use App\Models\Band;
use App\Models\Genre;

class GenealogyController extends Controller
{
    public function __invoke()
    {
        $bands = Band::with('genres')->orderBy('name')->get();

        return view('genealogy.index', [
            'bandsJson' => $bands->map(fn ($b) => [
                'id' => "band_{$b->id}",
                'label' => $b->name,
                'slug' => $b->slug,
                'genre' => $b->genres->first()?->slug,
                'formed_year' => $b->formed_year,
                'dissolved_year' => $b->dissolved_year,
                'origin' => $b->origin,
            ])->values(),
            'genres' => Genre::orderBy('name')->pluck('name', 'slug')->all(),
        ]);
    }
}
