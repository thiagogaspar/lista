<?php

namespace App\Http\Controllers;

use App\Services\ArtistService;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function index(Request $request, ArtistService $service)
    {
        $sort = $request->get('sort', 'name');
        $dir = $request->get('dir', 'asc');

        return view('artists.index', [
            'artists' => $service->getPaginated($request->only(['search']), sort: $sort, dir: $dir),
            'filters' => $request->only(['search']),
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function show(string $slug, ArtistService $service)
    {
        return view('artists.show', [
            'artist' => $service->getBySlug($slug),
        ]);
    }
}
