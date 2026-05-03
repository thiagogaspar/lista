<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Band;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json(['bands' => [], 'artists' => []]);
        }

        return response()->json([
            'bands' => Band::where('name', 'like', "%{$q}%")
                ->limit(5)->get(['id', 'name', 'slug', 'genre']),
            'artists' => Artist::where('name', 'like', "%{$q}%")
                ->limit(5)->get(['id', 'name', 'slug', 'origin']),
        ]);
    }
}
