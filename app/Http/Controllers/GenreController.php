<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Services\BandService;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function __invoke(string $slug, Request $request, BandService $service)
    {
        $genre = Genre::where('slug', $slug)->firstOrFail();

        $bands = $service->getPaginated(
            filters: ['genre' => $slug] + $request->only(['year', 'origin', 'search', 'label']),
            sort: $request->get('sort', 'name'),
            dir: $request->get('dir', 'asc'),
        );

        return view('bands.index', [
            'bands' => $bands,
            'genres' => $service->getGenres(),
            'labels' => $service->getLabels(),
            'origins' => $service->getOrigins(),
            'filters' => ['genre' => $slug],
            'sort' => $request->get('sort', 'name'),
            'dir' => $request->get('dir', 'asc'),
            'genreName' => $genre->name,
        ]);
    }
}
